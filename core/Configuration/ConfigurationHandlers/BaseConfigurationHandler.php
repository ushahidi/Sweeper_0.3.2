<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
/**
 * Base Configuration handler that provides functions that can
 * be utalised by an inheriting class
 * @author mg[at]swiftly[dot]org
 */
class BaseConfigurationHandler
{
    /**
     * Given the file path and then name of the collection
     * element, this function will open the xml config file
     * if it already exists or will create an empty file
     * and then open it if it dosent.
     * 
     * @param string $filePath
     * @param string $collectionElement
     * @return \SimpleXMLElement 
     */
    public function SaveOpenConfigurationFile($filePath, $collectionElement)
    {
        //Check to see if the file is there
        if(!file_exists($filePath))
        {
            //If not then create a new XML structure
            $root = new \SimpleXMLElement("<configuration></configuration>");

            //Add the collection element to the XML structure
            $root->addChild($collectionElement);

            //Write out the XML to the file
            $root->asXML($filePath);
        }

        //Load the xml into a variable
        $xml = simplexml_load_file($filePath);

        //return the simple xml element
        return $xml;
    }
}
?>
