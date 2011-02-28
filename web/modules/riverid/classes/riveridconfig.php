<?php
class RiverIdConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'swiftriver';
    public static $password = 'KU93uT';
    public static $database = 'swiftriver';

    public static $createsql = "CREATE TABLE IF NOT EXISTS users ( username VARCHAR(2000), password VARCHAR(2000), role VARCHAR(2000) ) TYPE=innodb";
}