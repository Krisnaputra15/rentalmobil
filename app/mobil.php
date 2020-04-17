<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mobil extends Model
{
    protected $table="mobil";
    protected $fillable=['nama_mobil','plat_nomer','kondisi','status','id_jenis'];
    public $timestamps = false;
}
