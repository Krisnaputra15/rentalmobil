<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mobil_detail extends Model
{
    protected $table="mobil_detail";
    protected $fillable=['id_mobil','id_jenis'];
    public $timestamps = false;
}
