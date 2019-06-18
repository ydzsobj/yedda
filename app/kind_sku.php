<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class kind_sku extends Model
{
    //
    protected $table="kind_sku";
    protected $primaryKey='id';
    protected $fillable=['id','num'];
    public $timestamps=false;
}
