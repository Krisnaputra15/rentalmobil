<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jenis_mobil extends Model
{
    protected $table="jenis";
    protected $fillable=['nama_jenis'];
    public $timestamps = false;
}
