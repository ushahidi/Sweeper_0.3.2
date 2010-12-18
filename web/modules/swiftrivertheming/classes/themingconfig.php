<?php
class ThemingConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'sweeper_nightly';
    public static $password = 'sweeper_nightly';
    public static $database = 'sweeper_nightly';

    public static $createsql = "CREATE TABLE IF NOT EXISTS theming ( theme VARCHAR(2000) ) TYPE=innodb";
}
?>