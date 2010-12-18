<?php
/**
 * ParserFactory is responsible for returning 
 * an instance of an object that implements the
 * IParser interface.
 */
namespace Swiftriver\Core\Modules\SiSPS;
class ParserFactory{
    /**
     * Expects a string representing the class
     * name of an object that implements the
     * SiSPS\IParser interface. The param $type
     * must not include the word 'Parser'. For
     * example, supplying the $type Email will
     * return an instance of the EmailParser
     * object.
     *
     * @param string $type
     * @return SiSPS\Parsers\IParser $parser
     */
    public static function GetParser($type) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::ParserFactory::GetParser [Method invoked]", \PEAR_LOG_DEBUG);

        //Append the word Parser to the type
        $type = \str_replace(" ", "", $type) . "Parser";

        //If the class is not defined, return null
        $type = "\\Swiftriver\\Core\\Modules\\SiSPS\\Parsers\\".$type;
        if(!class_exists($type)) {
            $logger->log("Core::Modules::SiSPS::ParserFactory::GetParser [Class $type not found. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::ParserFactory::GetParser [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::ParserFactory::GetParser [Method finished]", \PEAR_LOG_DEBUG);

        //Finally, return a new Parser
        return new $type();
    }

    public static function ReturnAllAvailableParsers(){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::ParserFactory::ReturnAllAvailableParsers [Method invoked]", \PEAR_LOG_DEBUG);

        $parsers = array();
        
        $logger->log("Core::Modules::SiSPS::ParserFactory::ReturnAllAvailableParsers [START: Directory Itteration]", \PEAR_LOG_DEBUG);
        
        $dirItterator = new \RecursiveDirectoryIterator(dirname(__FILE__)."/Parsers/");
        $iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach($iterator as $file) {
            if($file->isFile()) {
                $filePath = $file->getPathname();
                if(strpos($filePath, ".php") && !strpos($filePath, "IParser")) {
                    try{
                        $typeString = "\\Swiftriver\\Core\\Modules\\SiSPS\\Parsers\\".$file->getFilename();
                        $type = str_replace(".php", "", $typeString);
                        $object = new $type();
                        if($object instanceof Parsers\IParser) {
                            $logger->log("Core::Modules::SiSPS::ParserFactory::ReturnAllAvailableParsers [Adding type $type]", \PEAR_LOG_DEBUG);
                            $parsers[] = $object;
                        }
                    }
                    catch(\Exception $e) {
                        $logger->log("Core::Modules::SiSPS::ParserFactory::ReturnAllAvailableParsers [error adding type $type]", \PEAR_LOG_DEBUG);
                        $logger->log("Core::Modules::SiSPS::ParserFactory::ReturnAllAvailableParsers [$e]", \PEAR_LOG_ERR);
                        continue;
                    }
                }
            }
        }

        $logger->log("Core::Modules::SiSPS::ParserFactory::ReturnAllAvailableParsers [END: Directory Itteration]", \PEAR_LOG_DEBUG);

        return $parsers;
    }
}
?>
