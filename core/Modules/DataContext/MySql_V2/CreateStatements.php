<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V2;
class CreateStatements
{
    public function GetChannelTableSql()
    {
        return "CREATE TABLE 'swiftriver_temp'.'sc_channel' ( " .
                    "'id' VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , ".
                    "'type' VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , ".
                    "'subType' VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , ".
                    "'active' BIT( 1 ) NOT NULL , ".
                    "'inProcess' BIT( 1 ) NOT NULL , ".
                    "'nextRun' INT NOT NULL , ".
                    "'json' TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , ".
                    "PRIMARY KEY ( 'id' )  ".
                ") ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
    }
}
?>
