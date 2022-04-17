<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Controllers\ImportDataController;

class DatabaseSeeder extends Seeder {

    public function run() {
        $obj = new ImportDataController();
        $obj->importData();
    }

}
