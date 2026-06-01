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
            [['capacity'], 'default', 'value' => 0],
            [['room', 'name'], 'required'],
            [['description'], 'string'],
            [['capacity'], 'integer'],
            [['room', 'name', 'fr_img', 'location', 'contact'], 'string', 'max' => 255],
            // Unique room code — exclude self on update
            [['room'], 'unique', 'targetAttribute' => 'room',
                'filter' => function ($query) {
                    if (!$this->isNewRecord) {
                        $query->andWhere(['not', ['id' => $this->id]]);
                    }
                }
            ],
            // Allow file instance for fr_img (handled in controller, stored as string)
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

}
