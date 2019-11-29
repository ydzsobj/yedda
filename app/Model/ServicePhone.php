<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePhone extends Model
{
    use SoftDeletes;

    protected $tables = 'service_phones';

    protected $fillable = [
        'name',
        'phone',
        'area_code',
        'round',
        'disabled_at'
    ];

    public static function round_phone($order_id){

        $service_phones = self::whereNull('disabled_at')->get();

        if(!$service_phones){
            return false;
        }

        if($service_phones->count() == 1){
            $s_phone = $service_phones->first();
            return $s_phone->area_code. $s_phone->phone;
        }else{
            //多个手机号时 计算应该发给谁
            $cnt = $service_phones->count();
            $round = intval($order_id%$cnt + 1);//取模
            $s_phone = self::where('round', $round)->whereNull('disabled_at')->first();

            if($s_phone){
                return $s_phone->area_code. $s_phone->phone;
            }else{
                return false;
            }
        }
    }

    /**
     * 检查有没有可用的
     */
    public static function check_available(){
        return self::whereNull('disabled_at')->count();
    }
}
