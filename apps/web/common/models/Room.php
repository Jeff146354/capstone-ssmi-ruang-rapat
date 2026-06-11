<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $room
 * @property string $name
 * @property string|null $description
 * @property string|null $fr_img
 * @property string|null $location
 * @property string|null $contact
 * @property int|null $capacity
 * @property bool $is_active
 *
 * @property Reservation[] $reservations
 */
class Room extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'fr_img', 'location', 'contact'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => true],
            [['room', 'name'], 'required'],
            [['description'], 'string'],
            [['capacity'], 'integer', 'min' => 1, 'max' => 10000,
                'tooSmall' => 'Kapasitas harus minimal 1 orang.',
                'tooBig'   => 'Kapasitas tidak realistis.',
            ],
            [['capacity'], 'required', 'message' => 'Kapasitas wajib diisi.'],
            [['is_active'], 'boolean'],
            [['room', 'name', 'location'], 'string', 'max' => 255],
            [['contact'], 'string', 'max' => 255],
            [['contact'], 'match', 'pattern' => '/^.+\s[\d\+\-\s]{8,}$/',
                'message' => 'Format kontak: Nama diikuti nomor HP (contoh: Nugraha 081234567890)',
                'when' => function ($model) { return !empty($model->contact); }
            ],
            // Unique room code — use raw query to bypass find() scope
            [['room'], 'unique', 'targetClass' => '\common\models\Room',
                'filter' => function ($query) {
                    // Bypass the is_active scope for uniqueness check
                    $query->where(['room' => $this->room]);
                    if (!$this->isNewRecord) {
                        $query->andWhere(['not', ['id' => $this->id]]);
                    }
                },
                'message' => 'ID Ruangan "{value}" sudah digunakan.',
            ],
            // fr_img handled in controller (file upload → string filename)
            [['fr_img'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID database',
            'room' => 'ID Ruangan',
            'name' => 'Nama Ruangan',
            'description' => 'Deskripsi',
            'fr_img' => 'Gambar Ruangan',
            'location' => 'Lokasi ',
            'contact' => 'Kontak',
            'capacity' => 'Kapasitas',
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['reservation_count']);
    }

    /**
     * Gets query for [[Reservations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservations()
    {
        return $this->hasMany(Reservation::class, ['room_id' => 'id']);
    }

    /**
     * Returns the public URL for the room image.
     * Falls back to a placeholder if no image is set.
     */
    public function getImageUrl(string $fallback = 'https://placehold.co/400x300?text=No+Image'): string
    {
        if (!$this->fr_img) {
            return $fallback;
        }
        // If it's already a full URL (e.g. external), return as-is
        if (str_starts_with($this->fr_img, 'http')) {
            return $this->fr_img;
        }
        return \yii\helpers\Url::base(true) . '/uploads/' . $this->fr_img;
    }

    /**
     * Soft-delete: mark room as inactive instead of deleting.
     * Preserves all reservation history.
     */
    public function softDelete(): bool
    {
        // Block deletion if there are future approved reservations
        $hasFutureBookings = Reservation::find()
            ->where(['room_id' => $this->id, 'status' => Reservation::STATUS_APPROVED])
            ->andWhere(['>=', 'date', date('Y-m-d')])
            ->exists();

        if ($hasFutureBookings) {
            return false; // caller should check this and show an error
        }

        $this->is_active = false;
        return $this->save(false);
    }

    /**
     * Default scope: only active rooms in normal queries.
     * Use Room::findUnscoped() for admin views that need all rooms.
     */
    public static function find(): \yii\db\ActiveQuery
    {
        return parent::find()->andWhere(['{{%room}}.is_active' => true]);
    }

    /**
     * Find without the is_active filter (for admin, edit, delete operations).
     */
    public static function findUnscoped(): \yii\db\ActiveQuery
    {
        return parent::find();
    }

    /**
     * Find a room by ID, bypassing the is_active scope.
     * Use this in controllers where admin needs to access any room.
     */
    public static function findById(int $id): ?self
    {
        return parent::find()->where(['id' => $id])->one();
    }

}
