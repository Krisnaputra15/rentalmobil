<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transaksi extends Model
{
    protected $table="transaksi";
    protected $fillable=['tanggal_transaksi','id_pelanggan','id_petugas','tanggal_kembali','tanggal_dikembalikan','id_detail'];
    public $timestamps = false;
}
