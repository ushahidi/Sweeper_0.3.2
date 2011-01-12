<?php
class ThemingConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'sweeper';
    public static $password = 'sweeper';
    public static $database = 'sweeper';

    public static $createsql = "CREATE TABLE IF NOT EXISTS theming ( theme VARCHAR(2000) ) TYPE=innodb";
}
?>