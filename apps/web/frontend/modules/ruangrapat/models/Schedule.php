<?php

namespace frontend\modules\ruangrapat\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "schedule".
 *
 * @property int $id
 * @property int $room_id
 * @property string $start_time
 * @property string $end_time
 * @property string|null $peminjam
 * @property string|null $keterangan
 * @property string|null $created_at
 * @property string|null $status
 */
class Schedule extends ActiveRecord
{
    public static function tableName()
    {
        return 'schedule';
    }

    public function rules()
    {
        return [
            [['room_id', 'start_time', 'end_time'], 'required'],
            [['room_id'], 'integer'],
            [['start_time', 'end_time', 'created_at'], 'safe'],
            [['keterangan'], 'string'],
            [['peminjam'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => ['pending', 'confirmed', 'cancelled']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'ID Ruangan',
            'start_time' => 'Waktu Mulai',
            'end_time' => 'Waktu Selesai',
            'peminjam' => 'Peminjam',
            'keterangan' => 'Keterangan',
            'created_at' => 'Dibuat Pada',
            'status' => 'Status',
        ];
    }

    public function getRoom()
    {
        return $this->hasOne(Room::class, ['id' => 'room_id']);
    }

    public function getNama_kegiatan()
    {
        return $this->keterangan;
    }

    public function getPenanggung_jawab()
    {
        return $this->peminjam;
    }
}