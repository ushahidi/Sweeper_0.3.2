<?php
class RiverIdConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'sweeper';
    public static $password = 'sweeper';
    public static $database = 'sweeper';

    public static $createsql = "CREATE TABLE IF NOT EXISTS users ( username VARCHAR(2000), password VARCHAR(2000), role VARCHAR(2000) ) ENGINE=innodb";
}