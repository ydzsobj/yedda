<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->increments('sms_id');
            $table->string('mobile',20)->comment('接收短信手机号');
            $table->string('sms_text')->comment('发送内容');
            $table->dateTime('send_time')->nullable()->comment('发布时间');
            $table->tinyInteger('code')->nullable()->comment('接收返回值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms');
    }
}
