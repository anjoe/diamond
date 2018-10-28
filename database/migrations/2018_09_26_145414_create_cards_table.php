<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id'); // 卡片Id
            $table->char('familiarity', 4)->default(0); // 熟悉度
            $table->unsignedInteger('user_id'); // 卡片创建者Id
            $table->unsignedInteger('package_id'); // 卡片所在卡包
            $table->char('type', 1); // 卡片类型，0表示普通，1表示选择，2表示填空
            $table->char('is_active', 4)->default(1); // 卡片是否有效
            $table->string('cover')->nullable(); // 卡片正面的图片
            $table->string('description')->nullable(); // 卡片正面的文字
            $table->char('flag_front', 2)->default(0); // 低位标志文字，高位标志图片，0表无，1表示有
            // 背面类型标志，最高位表示语音，次高位表示图片，低位表示文字，1表示有，0表示无
            $table->char('flag_back', 3)->default(0);
            $table->string('word')->nullable();
            $table->string('picture')->nullable();
            $table->string('voice')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
