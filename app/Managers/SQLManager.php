<?php

namespace App\Managers;

use PHPSQLParser\PHPSQLParser;

class SqlManager
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
        $parsed = $parser->parse($_sql, true);

        if ($parsed == false)
        {
            throw new Exception('Invalid SQL');
        }
        
        return $parsed;
    }

    /**
     * Is the SQL provides a CREATE, SELECT, INSERT ?
     */
    private function getSqlType()
    {
        if (isset($this->parsedSql['INSERT']))
        {
            return 'INSERT';
        }
        else if (isset($this->parsedSql['CREATE']))
        {
            return 'CREATE';
        }
        else 
        {
            throw new Exception('Invalid SQL - ONLY CREATE OR INSERT STATEMENTS ARE ACCEPTED');
        }
    }

    private function getColumnDefinitionOptions($_colDefinitions)
    {
        $tempArr = [];

        foreach($_colDefinitions as $def)
        {
            switch ($def['expr_type'])
            {
                case 'colref':
                    $tempArr['name'] = $def['base_expr'];
                    break;

                case 'column-type':
                    $tempArr['unique'] = $def['unique'];
                    $tempArr['nullable'] = $def['nullable'];
                    $tempArr['auto_inc'] = $def['auto_inc'];
                    $tempArr['primary'] = $def['primary'];
                    
                    //get datatype information
                    foreach($def['sub_tree'] as $type)
                    {
                        switch ($type['expr_type'])
                        {
                            case 'data-type':
                                $tempArr['data_type'] = [];
                                $tempArr['data_type']['type'] = $type['base_expr'];
                                $tempArr['data_type']['unsigned'] = $type['unsigned'] ?? null;
                                $tempArr['data_type']['zerofill'] = $type['zerofill'] ?? null;
                                $tempArr['data_type']['length'] = $type['length'] ?? null;
                                break;
                        }
                    }
                    break;
            }        
        }
        
        return $tempArr;
    }

    private function getPrimaryKeyOptions($_primaryKey)
    {
        $tempArr = [];

        foreach($_primaryKey as $def)
        {
            switch ($def['expr_type'])
            {
                case 'column-list':
                    foreach($def['sub_tree'] as $column)
                    {
                        switch ($column['expr_type'])
                        {
                            case 'index-column':
                                array_push($tempArr, $column['name']);
                            break;
                        }
                    }

                    break;
            }
        }

        return $tempArr;
    }

    private function extractTableInfoFromCreate()
    {
        $data = [];
        $data['name'] = $this->parsedSql['TABLE']['name'];
        $data['config'] = [];
        $data['config']['if_not_exists'] = $this->parsedSql['CREATE']['not-exists'] ?? false;
        $data['columns'] = [];
        $data['primary-key'] = [];
        
        $columnOptions = $this->parsedSql['TABLE']['create-def']['sub_tree'];
        foreach($columnOptions as $option)
        {
            $tempArr = [];
            switch($option['expr_type'])
            {
                case 'column-def':
                    $tempArr = $this->getColumnDefinitionOptions($option['sub_tree']);
                    array_push($data['columns'], $tempArr);
                break;

                case 'primary-key':
                    $tempArr = $this->getPrimaryKeyOptions($option['sub_tree']);
                    $data['primary-key'] = $tempArr;
                break;
            }
        }
        

    }

    private function extractTableInfoFromInsert()
    {
        
    }


    public function getSqlMigration()
    {
        $data = [];

        switch($this->getSqlType())
        {
            case 'CREATE':
                return $this->extractTableInfoFromCreate();
                break;
            case 'INSERT':
                break;
            default:
                throw new Exception('Invalid SQL - ONLY CREATE OR INSERT STATEMENTS ARE ACCEPTED');
                break;
        }
    }

    public function getSqlSeeder()
    {

    }
}