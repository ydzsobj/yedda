<?php
namespace App\channel;

include_once __DIR__.'/../Utils/InterSms/ChuanglanSmsApi.php';
use Illuminate\Support\Facades\DB;

class sms{
    private static $phones=['8613937102140','8615639069121'];
    /**
     * 发送消息
     * @param $request
     * @param $text
     * @param $num
     * @return bool
     */
    public static function send()
    {
        $clapi = new \ChuanglanSmsApi();
        $code = mt_rand(100000,999999);
        $content = 'test'.$code;
        $phone_num = array_rand(self::$phones);
        $phone = self::$phones[$phone_num];
        $result = $clapi->sendInternational($phone,$content);
        if(!is_null(json_decode($result))){

            $output=json_decode($result,true);
            if(isset($output['code'])  && $output['code']=='0'){
                DB::table('sms')->insert(['mobile'=>$phone,'sms_text'=>$content,'send_time'=>date('Y-m-d H:i:s',time()),'code'=>$output['code']]);
                return '短信发送成功！' ;
            }else{
                DB::table('sms')->insert(['mobile'=>$phone,'sms_text'=>$content,'send_time'=>date('Y-m-d H:i:s',time()),'code'=>$output['code']]);
                return $output['error'];
            }
        }else{
            return $result;
        }
    }


}