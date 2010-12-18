<?php
namespace Swiftriver\SiCDSInterface;
class Parser {
    /**
     * Given a array of content items, this function will parse
     * the diff collections of each item to JSON formtted for the
     * SiCDS cloud service interface.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $items
     * @param string $apiKey
     * @return string
     */
    public function ParseToRequestJson($items, $apiKey) {
        //set up the array to hold the item dtos
        $jsonReadyItems = array();

        //loop through the content and only take the bits we need
        foreach($items as $item) {
            $i->id = $item->id;
            $i->difcollections = $item->difs;
            $jsonReadyItems[] = $i;
            unset($i);
        }

        //Add the json header information
        $object->key = $apiKey;

        //Add the content item dtos
        $object->contentItems = $jsonReadyItems;

        //to json the object
        $json = json_encode($object);

        //return the json
        return $json;
    }

    public function ParseItemToRequestJson($item, $apiKey) {
        $i->id = $item->id;
        $i->difcollections = $item->difs;

        //Add the json header information
        $object->key = $apiKey;

        //Add the content item dto
        $object->contentItems = array($i);

        //to json the object
        $json = json_encode($object);

        //return the json
        return $json;
    }

    /**
     * Given the JSON returned from the SiCDS, this function attempts
     * to parse and return an array of content item ID's that have been
     * classified as unique.
     * 
     * @param string $json
     * @return string[]
     */
    public function ParseResponseFromJsonToUniqueIds($json) {
        //decode the json param
        $array = json_decode($json, true);

        //Null and not array check
        if($array == null || !is_array($array))
            throw new \InvalidArgumentException("The json was not well formed");

        //Ensure required property 'results' is there and is array
        if(!key_exists("results", $array) || !is_array($array["results"]))
            throw new \InvalidArgumentException("The json did not contain the required property 'results'");

        //get the results array
        $results = $array["results"];

        //set up the return array
        $return = array();

        //Loop through the resulsts looking for unique content item ids
        foreach($results as $result) {
            if($result["result"] == "unique") {
                $return[] = $result["id"];
            }
        }

        //return the array
        return $return;
    }

    public function ContentIsUnique($json, $id) {
        //decode the json param
        $array = json_decode($json, true);

        //Null and not array check
        if($array == null || !is_array($array))
            throw new \InvalidArgumentException("The json was not well formed");

        //Ensure required property 'results' is there and is array
        if(!key_exists("results", $array) || !is_array($array["results"]))
            throw new \InvalidArgumentException("The json did not contain the required property 'results'");

        //get the results array
        $results = $array["results"];

        //set up the return array
        $return = array();

        //Loop through the results looking for unique content item ids
        foreach($results as $result) {
            if($result["id"] == $id) {
                return ($result["result"] == "unique");
            }
        }

        //return default
        return true;
    }
}
?>
