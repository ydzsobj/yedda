<?php
namespace App\channel;

include_once __DIR__.'/../Utils/InterSms/ChuanglanSmsApi.php';

use App\order;
use Illuminate\Support\Facades\DB;

class sms{
    private static $phones=['62081223529301','62081212610733'];
    /**
     * 发送消息
     * @param $request
     * @param $text
     * @param $num
     * @return bool
     */
    public static function send($text_id,$order_id)
    {
        $order_info = order::find($order_id);
        $sms_info = $order_info;
        $sms_info['order_goods_name'] = $order_info->goods->goods_name;
        $clapi = new \ChuanglanSmsApi();
        $content = self::send_text($text_id,$sms_info);
        $phone_num = array_rand(self::$phones);
        $phone = self::$phones[$phone_num];
        $result = $clapi->sendInternational($phone,$content);
        if(!is_null(json_decode($result))){

            $output=json_decode($result,true);
            if(isset($output['code'])  && $output['code']=='0'){
                DB::table('sms')->insert(['mobile'=>$phone,'sms_text'=>$content,'send_time'=>date('Y-m-d H:i:s',time()),'code'=>$output['code'],'code_msg'=>'短信发送成功']);
                return true;
            }else{
                DB::table('sms')->insert(['mobile'=>$phone,'sms_text'=>$content,'send_time'=>date('Y-m-d H:i:s',time()),'code'=>$output['code'],'code_msg'=>$output['error']]);
                return true;
            }
        }else{
            DB::table('sms')->insert(['mobile'=>$phone,'sms_text'=>$content,'send_time'=>date('Y-m-d H:i:s',time()),'code'=>'','code_msg'=>$result]);
            return true;
        }
    }


    /**
     * 发送短信内容
     * @param $text_id
     * @param $tel
     * @return text|string
     */
    public static function send_text($text_id,$info)
    {
        $code = mt_rand(100000,999999);
        $order_name = $info['order_name']?$info['order_name']:'name is null';
        $order_tel = $info['order_tel']?$info['order_tel']:'tel is null';
        $order_price = $info['order_price']?$info['order_price']:'price is null';
        $order_goods_name = $info['order_goods_name']?$info['order_goods_name']:'goodsname is null';
        $order_city = $info['order_city']?$info['order_city']:'city is null';
        $order_add = $info['order_add']?$info['order_add']:'add is null';
        $order_remark = $info['order_remark']?$info['order_remark']:'null';
        $order_id = $info['order_id']?$info['order_id']:'null';
        switch ($text_id) {
            case '0':
                //$text="anda ada orderan baru yang akan di konfirmasi  nama penerima ".$order_name." nomor telepon ".$order_tel." nama barang ".$order_goods_name." harga barang ".$order_price." alamat penerima ".$order_city.$order_add." tinggalkan pesan ".$order_remark;
                $text="anda ada orderan baru yang akan di konfirmasi（".$order_id ."）, nama penerima:".$order_name.",nomor telepon:".$order_tel.",nama barang:".$order_goods_name.",harga barang:".$order_price.",alamat penerima:".$order_city." ".$order_add.",tinggalkan pesan".$order_remark;
                break;
            case '1':
                $text="你有新订单（".$order_id ."）待沟通确认。收货人：".$order_name."电话号码：".$order_tel."商品名称：".$order_goods_name."商品价格：".$order_price."收货地址：".$order_city.$order_add."留言信息".$order_remark;
                break;
            default:
                $text='You have a new order to deal with.'.$code;
                break;
        }
        return $text;
    }


    }