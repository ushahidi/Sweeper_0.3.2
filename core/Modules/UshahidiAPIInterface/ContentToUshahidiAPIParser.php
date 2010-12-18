<?php
namespace Swiftriver\UshahidiAPIInterface;
class ContentToUshahidiAPIParser {
    /**
     * Given a Content items, this function parses the properties of the
     * content item into the format required by the Ushahidi API.
     *
     * @param \Swiftriver\Core\ObjectModel\Content $content
     * @return string[] parameters
     */
    public function ParseContentItemToUshahidiAPIFormat($content) {
        //Extract the apropriate Ushahidi categories form the content item
        $categories = $this->ExtractUshahidiCategoriesFromContent($content);

        //Extract the date from the content item
        $date = $this->ExtractDateFromContent($content);

        //Extract the GIS Data location from the content item
        $location = $this->ExtractLocationDataFromContent($content);

        //Extract the location name form the content item
        $locationName = $this->ExtractLocationNameFromContent($content);

        //Extract the text
        $description = $this->ExtractDescriptionFromContent($content);

        //Use the data above and others to build the parameters array
        $parameters = array(
            "task" => "swiftriver",
            "incident_title" => $content->text[0]->title,
            "incident_description" => $description,
            "incident_date" => date("m/d/Y", $date),
            "incident_hour" => date("h", $date),
            "incident_minute" => date("i", $date),
            "incident_ampm" => (date("H", $date) <= 12) ? "am" : "pm",
            "incident_category" => $categories,
        );

        if(isset($content->gisData->longitude)) {
            $parameters["longitude"] = $content->gisData->longitude;
        }
        else {
            $parameters["longitude"] = "0";
        }

        if(isset($content->gisData->latitude)) {
            $parameters["latitude"] = $content->gisData->latitude;
        }
        else {
            $parameters["latitude"] = "0";
        }

        if(isset($content->gisData->name)) {
            $parameters["location_name"] = $content->gisData->name; 
        }
        else {
            $parameters["location_name"] = "None";
        }

        //TODO: We have to find a way to get categories from sweeper and into Ushahidi

        $parameters["incident_category"] = "Trusted Reports";

        //TODO: Until riverId is online and the data schema has been decided, user name and address are not implemnted.

        //return the encoded query
        return $parameters;
    }

    /**
     * Returns the comma seporated list of Ushahidi categories
     *
     * @param \Swiftriver\Core\ObjectModel\Content $content
     * @return string
     */
    private function ExtractUshahidiCategoriesFromContent($content) {
        //set up the return variable
        $return = "";

        //if there are no tags then just return the defult
        if(!isset($content->tags) || !is_array($content->tags)) {
            return $return;
        }

        //loop through all the content tags
        foreach($content->tags as $tag) {

            //We are only interested in what tags
            if($tag->type == "what") {

                //Add the what tag text to the return string
                $return .= $tag->text . ",";
            }
        }

        //Chop the trailing comma off the end of the return string
        $return = substr($return, 0, strlen($return) - 1);

        //Return the categories as a string
        return $return;
    }

    /**
     * Extract the incident date from the content item
     *
     * @param \Swiftriver\Core\ObjectModel\Content $content
     * @return time
     */
    private function ExtractDateFromContent($content) {
        return (isset($content->date) && $content->date != null)
                ? $content->date
                : \time();
    }

    /**
     * Returns the most appropriate long and latt from the
     * given Content item.
     *
     * @param \Swiftriver\Core\ObjectModel\Content $content
     * @return \Swiftriver\Core\ObjectModel\GisData
     */
    private function ExtractLocationDataFromContent($content) {
        return (!isset($content->gisData) || !is_array($content->gisData))
                ? new \Swiftriver\Core\ObjectModel\GisData(0, 0)
                : reset($content->gisData);
    }

    /**
     * Given a content item, this function extracts the first
     * where tag from its tags collection or returns 'unknown'
     * 
     * @param \Swiftriver\Core\ObjectModel\Content $content
     * @return string
     */
    private function ExtractLocationNameFromContent($content) {
        
        //if there are no tags then just return the defult
        if(!isset($content->tags) || !is_array($content->tags)) 
            return "unknown";
        

        //Set up the return variable with the defaul value
        $return = "";

        //loop through the content tags
        foreach($content->tags as $tag) {

            //we are only interested in the where tag
            if($tag->type == "where") {

                //set the location
                $return .= $tag->text . ", ";
            }
        }

        //If no where tags, return unknown
        if (strlen($return) == 0)
            return "unknown";

        //trim the extra comma and space off the end
        $return = rtrim($return, " ,");

        //return the location
        return $return;
    }

    private function ExtractDescriptionFromContent($content) {
        $return = "";
        if(isset($content->text) && is_array($content->text)) {
            foreach($content->text as $text) {
                $return .= "$text->title ";
                if(isset($text->text))
                    foreach($text->text as $innerText)
                        $return .= "$innerText ";
            }
        }
        return $return;
    }
}
?>
