<?php
namespace src;

use \src\Config;

class Database{
    
    public static function getInstance(){
        
        return new \PDO(Config::DB_DRIVER.":dbname=".Config::DB_DATABASE.";host=".Config::DB_HOST, Config::DB_USER, Config::DB_PASS); 

    }
    public static function getPdo(){
        return self::getInstance();
    }
}