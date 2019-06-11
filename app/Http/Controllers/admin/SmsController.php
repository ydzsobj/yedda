<?php

namespace App\Http\Controllers\home;

use App\channel\sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    //sms_send
    public function send(){
        $sms = sms::send(0,'8613973849571');
    }
}
