<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressDetailsTable extends Migration {

    public function up() {
        Schema::create('address_details', function (Blueprint $table) {
            $table->id();
            $table->string('address_one');
            $table->string('address_two')->nullable();
            $table->string('city', 30);
            $table->string('country', 30);
            $table->string('post_title', 20);
            $table->string('post_body')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('loan_details');
    }

}
