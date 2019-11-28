<?php

namespace App\Jobs;

use App\channel\smsAPI;
use App\Model\ServicePhone;
use App\order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class sendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $send_msg = sprintf("ID: %d, Ada pesanan baru masuk, silakan proses", $this->order->order_id);
        $phone = ServicePhone::round_phone($this->order->order_id);

        if($phone){
            $sms = new smsAPI();
            $result = $sms->send($send_msg, $phone);

            if($result->code == 0){
                echo '发送成功';
            }else{
                $error_msg = prinf('发送失败，错误code:%d,error:%s', $result->code, $result->error);
                Log::info($error_msg);
                echo $error_msg;
            }
        }
        
    }
}