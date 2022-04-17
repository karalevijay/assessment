<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class AddressDetails extends Model {

    use SoftDeletes;

    protected $table = 'address_details';
    protected $dates = ['deleted_at'];

}
