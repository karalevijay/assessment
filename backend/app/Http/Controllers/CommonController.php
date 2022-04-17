<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AddressDetails;
use App\Http\Controllers\Controller;
use App\Helpers\Utility;
use DB;
use Log;

class CommonController extends Controller {

    public function __construct(JWTAuth $jwt) {
        
    }

    public function loadAllUsers(Request $request) {
        $column = 'users.fname, users.lname, users.id as user_id, users.mobile, users.email,
            address_details.address_one, address_details.address_two, address_details.city,
            address_details.id as address_detail_id, address_details.country';
        $obj = User::select($column)
                ->leftJoin('address_details', 'address_details.user_id', '=', 'users.id')
                ->where('users.status', 'A')
                ->orderBy('users.id', 'ASC');
        $return_data['data'] = $obj->get();
        Utility::log("DSFASDFa",$return_data);
        return Utility::genSuccessResp('listSuccess', $return_data, false);
    }

}
