<?php

namespace App\Http\Controllers\admin;

use App\channel\sms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SmsController extends Controller
{
    //sms_send
    public function send(){
        //sms::send(0,'40176');
    }

    public function index()
    {
        $data = \App\Sms::orderBy('id','desc')->take(1000)->get();
        return view('admin.sms.index')->with(compact('data'));
    }
}
