<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanDetailsTable extends Migration {

    public function up() {
        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->double('loan_amount')->defalut('0');
            $table->double('loan_term')->default('1')->comment('loan_term_in_weeks');
            $table->string('loan_status',1)->default('A')->comment('P=>Pending,A=>Approved,R=>Reject');
            $table->timestamp('loan_approve_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->bigInteger('loan_borrower_id')->unsigned()->nullable();
            $table->foreign('loan_borrower_id')->references('id')->on('users');            
            $table->bigInteger('loan_approver_id')->unsigned()->nullable();
            $table->foreign('loan_approver_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('loan_details');
    }

}
