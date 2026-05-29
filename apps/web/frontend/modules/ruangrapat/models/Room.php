<?php
namespace frontend\modules\ruangrapat\models;

use yii\db\ActiveRecord;

class Room extends ActiveRecord
{
    public $peminjaman_count; // Tambahan properti supaya bisa dipanggil tanpa error

    public static function tableName()
    {
        return 'room';
    }

    public function rules()
    {
        return [
            [['name', 'capacity'], 'required'],
            [['capacity'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Nama Ruangan',
            'capacity' => 'Kapasitas',
            'description' => 'Deskripsi',
        ];
    }
    
    public function getFasilitasList()
    {
        return $this->facilities ? array_map('trim', explode(',', $this->facilities)) : [];
    }
}
