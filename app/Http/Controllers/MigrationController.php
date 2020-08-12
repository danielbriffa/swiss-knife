<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Managers\SqlManager;

class MigrationController extends Controller
{
    function create(Request $request)
    {
        //get sql
        $sql = $request->get('sql');

        //parse and get output
        $sqlManager = new SqlManager($sql);
        $migration = $sqlManager->getSqlMigration();
        $seeder = $sqlManager->getSqlSeeder();
    }
}
