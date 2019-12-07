<?php

namespace App\channel;

use \GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class smsAPI
{

    private $url;

    private $account;

    private $password;

    public function __construct()
    {

        $this->url = env('SMS_URL',false);
        $this->account = env('SMS_ACCOUNT',false);
        $this->password = env('SMS_PASSWORD',false);
        
    }

    public function send($msg, $phone){

        $client = new \GuzzleHttp\Client([
            'timeout'  => 2.0,
        ]);

        try {
            $response = $client->request('POST', $this->url, [
                'json' => [
                    'account' => $this->account,
                    'password' => $this->password,
                    'msg' => $msg,
                    'mobile' => $phone,
                ]
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            
                Log::info('请求'.$this->url.'失败');
                return false;
            
        }

        $result = json_decode($response->getBody());

        return $result;

    }

}