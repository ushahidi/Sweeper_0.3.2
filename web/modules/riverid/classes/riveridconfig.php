<?php
class RiverIdConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'sweeper_nightly';
    public static $password = 'sweeper_nightly';
    public static $database = 'sweeper_nightly';

    public static $createsql = "CREATE TABLE IF NOT EXISTS users ( username VARCHAR(2000), password VARCHAR(2000), role VARCHAR(2000) ) TYPE=innodb";
}