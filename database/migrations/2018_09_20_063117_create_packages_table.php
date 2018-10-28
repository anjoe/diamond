<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable(); // 卡包名称
            $table->string('cover')->nullable(); // 卡包封面
            $table->unsignedInteger('user_id'); // 卡包所有者id
            $table->integer('card_num')->default(0); // 卡包所有者id
            $table->tinyInteger('type'); // 卡包类型
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
        Schema::dropIfExists('package');
    }
}
