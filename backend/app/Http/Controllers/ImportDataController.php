<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Helpers\Utility;
use App\Models\User;

class ImportDataController {

    public function __construct() {
//
    }

    public function importData() {
        Try {
            DB::beginTransaction();

            $userData = config('config.demo_users');
            foreach ($userData as $key => $value) {
                $userData[$key]['password'] = app('hash')->make($value['password']);
            }
            User::insert($userData);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            \App\Helpers\Utility::log($ex);
            echo 'Exception at excel file level:-' . $ex->getMessage() . PHP_EOL;
        }
    }

}
