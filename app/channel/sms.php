<?php
namespace App\channel;

include_once __DIR__.'/../Utils/InterSms/ChuanglanSmsApi.php';

class sms{
    private static $phones=['8613937102140','8615639069121'];
    /**
     * 发送消息
     * @param $request
     * @param $text
     * @param $num
     * @return bool
     */
    public static function send($request,$phone,$text,$num)
    {
        $clapi = new \ChuanglanSmsApi();
        $code = mt_rand(100000,999999);
        $result = $clapi->sendInternational($phone, $text.$code);
        if(!is_null(json_decode($result))){

            $output=json_decode($result,true);
            if(isset($output['code'])  && $output['code']=='0'){

                return '短信发送成功！' ;
            }else{
                return $output['error'];
            }
        }else{
            return $result;
        }
    }


}