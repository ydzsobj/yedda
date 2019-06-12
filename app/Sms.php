<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    //关联短信表
    protected $table="sms";
    protected $primaryKey="id";
    public $timestamps=false;
}
