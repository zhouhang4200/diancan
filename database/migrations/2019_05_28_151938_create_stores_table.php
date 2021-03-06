<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->default(0)->comment('父级门店：默认0');
            $table->tinyInteger('status')->default(0)->comment('审核：0审核中，1通过，2不通过');
            $table->string('name')->comment('店名');
            $table->integer('merchant_id')->comment('店长');
            $table->string('logo', 100)->default('')->comment('门店照');
            $table->string('address')->default('')->comment('地址');
            $table->string('license_number')->default('')->comment('营业执照号');
            $table->string('legal_person')->default('')->comment('法人姓名');
            $table->string('legal_phone')->default('')->comment('法人电话');
            $table->string('banner1', 100)->default('')->comment('banner图片');
            $table->string('banner2', 100)->default('')->comment('banner图片');
            $table->string('banner3', 100)->default('')->comment('banner图片');
            $table->string('banner4', 100)->default('')->comment('banner图片');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
