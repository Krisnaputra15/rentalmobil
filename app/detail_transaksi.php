<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class detail_transaksi extends Model
{
    protected $table="detail_transaksi";
    protected $fillable=['id_trans','id_mobil','harga_sewa_per_hari','total_harga','denda','total_keterlambatan','total_denda','status'];
    public $timestamps = false;
}
