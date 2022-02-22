<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class LoanTermDetails extends Model {

    use SoftDeletes;

    protected $table = 'loan_term_details';
    protected $dates = ['deleted_at'];

}
