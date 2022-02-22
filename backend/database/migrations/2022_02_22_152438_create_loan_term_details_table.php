<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanTermDetailsTable extends Migration {

    public function up() {
        Schema::create('loan_term_details', function (Blueprint $table) {
            $table->id();
            $table->date('payment_date')->nullable();
            $table->double('amount')->defalut(0);
            $table->string('payment_status')->default('P')->comment('P=>Pending,C=>Completed');
            $table->bigInteger('loan_details_id')->unsigned()->nullable();
            $table->foreign('loan_details_id')->references('id')->on('loan_details');            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('loan_term_details');
    }

}
