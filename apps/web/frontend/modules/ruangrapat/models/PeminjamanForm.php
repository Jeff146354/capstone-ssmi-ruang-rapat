<?php

namespace frontend\modules\ruangrapat\models;

use yii\base\Model;

class PeminjamanForm extends Model
{
    public $nama_peminjam;
    public $tanggal_pinjam;
    public $jam_mulai;
    public $jam_selesai;
    public $surat_peminjaman;

    public function rules()
    {
        return [
            [['nama_peminjam', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai'], 'required'],
            [['tanggal_pinjam'], 'date', 'format' => 'php:Y-m-d'],
            [['jam_mulai', 'jam_selesai'], 'string'],
            [['surat_peminjaman'], 'file', 'extensions' => ['pdf', 'doc', 'docx']],
        ];
    }
}
