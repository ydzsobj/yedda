<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    protected $table = 'message';
    protected $primaryKey ='message_id';
    public $timestamps=false;

    /**
     * 创建新消息
     * @param $request
     * @param $phone     //结束短息号码
     * @param $goods_id  //商品ID
     * @param $order_id  //订单ID
     * @param $text      //发送短信内容
     * @param $num       //短信验证码
     * @param $message_status //发送状态 0：成功 1：失败
     * @return bool
     */
    public static function CreateMessage($request,$phone,$goods_id,$order_id,$text,$num,$message_status,$messaga_remark){
       $message = new Message();
       $message->message_ip = $request->getClientIp();
       $message->message_gettime = date('Y-m-d H:i:s');
       $message->message_goods_id = $goods_id;
       $message->message_order_msg = serialize($request->all());
       $message->messaga_content = $text;
       $message->messaga_code = $num;
       $message->message_order_id = $order_id;
       $message->message_status = $message_status;
       $message->message_mobile_num = $phone;
       $message->messaga_remark = $messaga_remark;
       $message->message_marking = 0;
       if($message->save()){
            return true;
       }else{
           return false;
       }
    }

    /** 地区区号处理
     * @param $blade_id //商品模板id
     * @param $phone    //电话号码
     * @return string
     */
    public static function AreaCode($blade_id,$phone)
    {
        switch ($blade_id) {
            case '0': //台湾模板
                if('886' == substr($phone,0,3)){
                    $tel = substr($phone,3);
                    $phones = '886'.ltrim($tel,'0');
                }else{
                    $phones = '886'.ltrim($phone,'0');
                }
                break;
            case '1': //简体模板
                if('86' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '86'.ltrim($tel,'0');
                }else{
                    $phones = '86'.ltrim($phone,'0');
                }
                break;
            case '2': //阿联酋模板
                if('971' == substr($phone,0,3)){
                    $tel = substr($phone,3);
                    $phones = '971'.ltrim($tel,'0');
                }else{
                    $phones = '971'.ltrim($phone,'0');
                }
                break;
            case '3': //马来西亚
                if('60' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '60'.ltrim($tel,'0');
                }else{
                    $phones = '60'.ltrim($phone,'0');
                }
                break;
            case '4': //泰国
                if('66' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '66'.ltrim($tel,'0');
                }else{
                    $phones = '66'.ltrim($phone,'0');
                }
                break;
            case '5': //日本
                if('81' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '81'.ltrim($tel,'0');
                }else{
                    $phones = '81'.ltrim($phone,'0');
                }
                break;
            case '6': //印度尼西亚
                if('62' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '62'.ltrim($tel,'0');
                }else{
                    $phones = '62'.ltrim($phone,'0');
                }
                break;
            case '7': //菲律宾
                if('63' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '63'.ltrim($tel,'0');
                }else{
                    $phones = '63'.ltrim($phone,'0');
                }
                break;
            case '8': //英国
                if('44' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '44'.ltrim($tel,'0');
                }else{
                    $phones = '44'.ltrim($phone,'0');
                }
                break;
            case '9': //英国
                if('44' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '44'.ltrim($tel,'0');
                }else{
                    $phones = '44'.ltrim($phone,'0');
                }
                break;
            case '10': //美国
                if('1' == substr($phone,0,1)){
                    $tel = substr($phone,1);
                    $phones = '1'.ltrim($tel,'0');
                }else{
                    $phones = '1'.ltrim($phone,'0');
                }
                break;
            case '11': //越南
                if('84' == substr($phone,0,2)){
                    $tel = substr($phone,2);
                    $phones = '84'.ltrim($tel,'0');
                }else{
                    $phones = '84'.ltrim($phone,'0');
                }
                break;
            default: //沙特、卡塔尔、中东地区
                $phones = $phone;
                break;
        }
        return $phones;
    }
}
