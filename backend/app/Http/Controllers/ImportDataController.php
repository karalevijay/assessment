<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Helpers\Utility;
use App\Models\User;
use App\Models\AddressDetails;

class ImportDataController {

    public function __construct() {
//
    }

    public function importData() {
        Try {
            DB::beginTransaction();
            $userDetail = [];
            $userData = config('config.demo_users');
            foreach ($userData as $key => $value) {
                $userDetail = $value;
                $userDetail['user']['password'] = app('hash')->make($value['user']['password']);
                User::insert($userDetail['user']);
                $userDetail['address']['user_id'] = DB::getPdo()->lastInsertId();
                AddressDetails::insert($userDetail['address']);
            }


            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            \App\Helpers\Utility::log($ex);
            echo 'Exception at excel file level:-' . $ex->getMessage() . PHP_EOL;
        }
    }

}
