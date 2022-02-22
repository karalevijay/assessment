<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class LoanDetails extends Model {

    use SoftDeletes;

    protected $table = 'loan_details';
    protected $dates = ['deleted_at'];

}
