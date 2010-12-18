<?php
namespace Swiftriver\Core\ObjectModel\ObjectFactories;
/**
 * Factory object used to build source objects
 * @author mg[at]swiftly[dot]org
 */
class SourceFactory
{
    /**
     * Creats a new Source object from a unique id
     *
     * @param string $identifier
     * @return \Swiftriver\Core\ObjectModel\Source
     */
    public static function CreateSourceFromIdentifier($identifier, $trusted = false)
    {
        $source = new \Swiftriver\Core\ObjectModel\Source();
        
        $source->id = md5($identifier);

        $repository = new \Swiftriver\Core\DAL\Repositories\SourceRepository();

        $sources = $repository->GetSourcesById(array($source->id));

        if(\count($sources) < 1)
        {
            if($trusted)
                $source->score = 100;

            $source->date = \time();
        }
        else
        {
            $source = \reset($sources);
        }

        return $source;
    }

    /**
     * Returns a new Source object from the JSON encoded string
     * of a Source object
     * 
     * @param JSON $json
     * @return \Swiftriver\Core\ObjectModel\Source 
     */
    public static function CreateSourceFromJSON($json)
    {
        //decode the json
        $object = json_decode($json);

        //If there is an error in the JSON
        if(!$object || $object == null)
            throw new \Exception("There was an error in the JSON passed in to the SourceFactory.");

        //create a new source
        $source = new \Swiftriver\Core\ObjectModel\Source();

        //set the basic properties
        $source->id =               isset($object->id) ? $object->id : md5(uniqid(rand(), true));
        $source->score =            isset($object->score) ? $object->score : null;
        $source->date =             isset($object->date) ? $object->date : \time();
        $source->name =             isset($object->name) ? $object->name : null;
        $source->type =             isset($object->type) ? $object->type : null;
        $source->subType =          isset($object->subType) ? $object->subType : null;
        $source->email =            isset($object->email) ? $object->email : null;
        $source->link =             isset($object->link) ? $object->link : null;
        $source->parent =           isset($object->parent) ? $object->parent : null;

        //Sort out the GIS Data
        if(\property_exists($object, "gisData"))
        {
            $gisData = $object->gisData;

            if($gisData != null && is_array($gisData))
            {
                foreach($gisData as $gis)
                {
                    $long = $gis->longitude;
                    $lat = $gis->latitude;
                    $name = $gis->name;
                    
                    if($long == null || (!\is_int($long) && !\is_float($long)))
                        continue;

                    if($lat == null || (!\is_int($lat) && !\is_float($lat)))
                        continue;

                    $source->gisData[] = new \Swiftriver\Core\ObjectModel\GisData($long, $lat, $name);
                }
            }
        }
try {        //sort out the profile images
        if($object->applicationProfileImages != null)
            foreach($object->applicationProfileImages as $key => $value)
                $source->applicationProfileImages[$key] = $value;

        //sort out the application ids
        if($object->applicationIds != null)
            foreach($object->applicationIds as $key => $value)
                $source->applicationIds[$key] = $value;
}catch(\Exception $e)
{

}
        //return the source
        return $source;
    }
}
?>
