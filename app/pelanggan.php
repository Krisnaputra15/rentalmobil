<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pelanggan extends Model
{
    protected $table= "pelanggan";
    protected $fillable = ['nama_pelanggan','alamat','telp','nik','foto_ktp','username','password'];
    public $timestamps = false;
}
