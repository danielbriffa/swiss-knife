<?php

namespace App\Managers;

use PHPSQLParser\PHPSQLParser;

class SQLManager
{
    protected $sql;
    protected $parsedSql;

    function __construct($_sql)
    {
        $this->sql = $_sql;
        $this->parsedSql = $this->parseSql($_sql);
    }

    /**
     * Try to parse SQL.
     * If valid, return parsed
     * else throw exception
     */
    private function parseSql($_sql)
    {
        $parser = new PHPSQLParser();
        $parsed = $parser->parse($sql, true);

        if ($parsed == false)
        {
            throw new Exception('Invalid SQL');
        }
        
        return $parsed;
    }


    public function getSqlMigration()
    {

    }

    public function getSqlSeeder()
    {
        
    }
}