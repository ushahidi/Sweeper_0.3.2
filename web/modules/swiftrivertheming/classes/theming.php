<?php
class Theming
{
    public static function collect_themes()
    {
        $themes = array();

        try
        {
            $dir = DOCROOT."/themes";
            $dirItterator = new \DirectoryIterator($dir);
            foreach($dirItterator as $directory) {
                if($directory->isDir()) {
                    $dirname = $directory->getFilename();
                    $innerItterator = new DirectoryIterator($directory->getPathname());
                    foreach($innerItterator as $file) {
                        if($file->isFile()) {
                            $filePath = $file->getPathname();
                            $fileName = $file->getFilename();
                            if($fileName == "style.css") {
                                $theme->cssFilePath = url::base()."themes/".$dirname."/style.css";
                                $theme->thumbnail = url::base()."themes/".$dirname."/thumbnail.png";//str_replace("style.css", "thumbnail.png", $filePath);
                                $theme->title = "";
                                $theme->description = "";
                                $theme->author = "";
                                $theme->email = "";
                                $theme->url = "";
                                $theme->notes = "";
                                $file = file($filePath);
                                foreach($file as $line) {
                                    if(strpos($line, "@title") != 0) {
                                        $theme->title = trim(substr($line, strpos($line, "@title") + 6));
                                    }
                                    else if (strpos($line, "@description") != 0) {
                                        $theme->description = trim(substr($line, strpos($line, "@description") + 12));
                                    }
                                    else if (strpos($line, "@author") != 0) {
                                        $theme->author = trim(substr($line, strpos($line, "@author") + 7));
                                    }
                                    else if (strpos($line, "@email") != 0) {
                                        $theme->email = trim(substr($line, strpos($line, "@email") + 6));
                                    }
                                    else if (strpos($line, "@url") != 0) {
                                        $theme->url = trim(substr($line, strpos($line, "@url") + 4));
                                    }
                                    else if (strpos($line, "@notes") != 0) {
                                        $theme->notes = trim(substr($line, strpos($line, "@notes") + 6));
                                    }
                                }
                                $themes[] = $theme;
                                unset($theme);
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $e)
        {

        }

        return $themes;
    }

    public static function set_theme($themePath)
    {
        $con = mysql_connect(
                ThemingConfig::$databaseurl,
                ThemingConfig::$username,
                ThemingConfig::$password);

        mysql_select_db(ThemingConfig::$database, $con);

        mysql_query(ThemingConfig::$createsql, $con);
        
        $deleteSql = "DELETE FROM theming;";
        
        mysql_query($deleteSql, $con);
        
        $insertSql = "INSERT INTO theming values('$themePath');";
        
        mysql_query($insertSql, $con);
        
        Cookie::set("theme", $themePath);
    }
    
    public static function get_theme()
    {
        $themePath = Cookie::get("theme");
        
        if($themePath != null)
            return $themePath;
        
        $con = mysql_connect(
                ThemingConfig::$databaseurl,
                ThemingConfig::$username,
                ThemingConfig::$password);

        mysql_select_db(ThemingConfig::$database, $con);

        mysql_query(ThemingConfig::$createsql, $con);
        
        $selectSql = "SELECT theme FROM theming LIMIT 1;";
        
        $result = mysql_query($selectSql, $con);
        
        if($result == false)
        {
            Cookie::set("theme", "default");
            return "default";
        }
        
        $row = mysql_fetch_assoc($result);
        
        if($row == false)
        {
            Cookie::set("theme", "default");
            return "default";
        }
        
        $theme = $row["theme"];
        
        if($theme == null || $theme < " ")
        {
            Cookie::set("theme", "default");
            return "default";
        }
        
        Cookie::set("theme", $theme);
        return $theme;
    }
}
?>