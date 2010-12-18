<?php
class RiverId
{
    /**
     * This function checks that the current user is logged in.
     * The return value is an associative array in the following
     * format:
     * array(
     *  "IsLoggedIn" => bool,
     *  "Role" => string
     * );
     * @return array(bool,role)
     */
    public static function is_logged_in()
    {
        $key = array_key_exists("authkey", $_SESSION) ? $_SESSION["authkey"] : null;

        $username = array_key_exists("username", $_SESSION) ? $_SESSION["username"] : null;

        if($key == null || $key == "" || $username == null || $username == "") 
            return array(
                "IsLoggedIn" => false,
                "Role" => "user"
            );

        $result = self::get_hashes_password_and_role($username);

        if($result === false)
            return array(
                "IsLoggedIn" => false,
                "Role" => "user"
            );

        $password = $result["password"];

        if($password != $key)
            return array(
                "IsLoggedIn" => false,
                "Role" => "user"
            );

        $role = $result["role"];

        $return = array(
            "IsLoggedIn" => true,
            "Role" => $role
        );

        return $return;
    }

    public static function log_in($username, $password)
    {
        $hashedPasswordResult = self::get_hashes_password_and_role($username);

        $hashedPassword = $hashedPasswordResult["password"];

        $password = md5($password);

        if($hashedPassword != $password)
            return false;

        $_SESSION["username"] = $username;

        $_SESSION["authkey"] = $password;

        $_SESSION["role"] = $hashedPasswordResult["role"];

        return true;
    }

    public static function log_out()
    {
        $_SESSION["authkey"] = null;
    }

    public static function register($username, $password, $role)
    {
        $username = mysql_escape_string($username);

        $password = md5($password);

        if($role != "sweeper" && $role != "editor" && $role != "admin")
            $role = "sweeper";

        $sql = "INSERT INTO users VALUES('$username', '$password', '$role');";

        $con = mysql_connect(
                RiverIdConfig::$databaseurl,
                RiverIdConfig::$username,
                RiverIdConfig::$password);

        mysql_select_db(RiverIdConfig::$database, $con);

        mysql_query(RiverIdConfig::$createsql, $con);

        $result = mysql_query($sql, $con);
    }

    private static function get_hashes_password_and_role($username)
    {
        $con = mysql_connect(
                RiverIdConfig::$databaseurl,
                RiverIdConfig::$username,
                RiverIdConfig::$password);

        mysql_select_db(RiverIdConfig::$database, $con);

        mysql_query(RiverIdConfig::$createsql, $con);

        $username = mysql_escape_string($username);

        $sql = "SELECT * FROM users WHERE username = '" . $username . "';";

        $results = mysql_query($sql, $con);

        if($results == false)
            return false;

        $row = mysql_fetch_assoc($results);

        if($row == false)
            return false;

        $role = $row["role"];

        $password = $row["password"];

        $hashedPassword = $password;

        $return = array(
            "password" => $hashedPassword,
            "role" => $role
        );

        return $return;
    }
}
?>
