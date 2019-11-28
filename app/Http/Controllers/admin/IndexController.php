<?php

namespace App\Http\Controllers\admin;

use App\admin;
use App\channel\smsAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ServicePhone;
use Illuminate\Support\Facades\Auth;
class IndexController extends Controller
{
    public function index(Request $request){

        // $phone = ServicePhone::round_phone(1002);
        // dd($phone);
        // $sms = new smsAPI();
        // $msg = 'ID:999999,Ada pesanan baru masuk, silakan proses';
        // dd($sms->send($msg, $phone));


    	$data=getclientcity($request);
    	$hcoun=\App\order::where(function($query){
            $query->whereIn('order_type',[0,11]);
            $query->where('is_del','0');
        })
        ->where(function($query){
//            if(Auth::user()->is_root!='1'){
//                        $query->whereIn('order.order_goods_id',\App\goods::get_selfid(Auth::user()->admin_id));
//              }
                          $query->whereIn('order.order_goods_id',admin::get_goods_id());
        })
        ->count();
        view()->share('hcoun',$hcoun);
    	return view('admin.father.app')->with(compact('data'));
    }
    public function welcome(Request $request){
        $data=getclientcity($request);
		$url = $_SERVER['SERVER_NAME'];        
    	return view('admin.index.index')->with(compact('data','url'));
    }

}
