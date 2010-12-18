<?php
namespace Swiftriver\SiLCCInterface;
class ContentFromJSONParser {
    /**
     * The original Content Items
     * @var \Swiftriver\Core\ObjectModel\Content
     */
    private $content;

    /**
     * The JSON string returned from the service
     * @var string
     */
    private $json;

    /**
     * Constructor
     * @param \Swiftriver\Core\ObjectModel\Content $content
     * @param string $json
     */
    public function __construct($content, $json) {
        $this->content = $content;
        $this->json = $json;
    }

    /**
     * Using the constructor parameters, this method
     * decodes the JSON and applies any tags that could be
     * extracted from the content. It returns the content
     * with taggs.
     *
     * @return \Swiftriver\Core\ObjectModel\Content
     */
    public function GetTaggedContent() {
        //Validity Checks
        if(!isset($this->content))
            return null;
        if(!isset($this->json))
            return $this->content;
        if($this->json == "")
            return $this->content;

        //decode the JSON string
        $objects = json_decode($this->json, true);

        //Check for malformed JSON
        if(!isset($objects))
            return $this->content;

        //Array to hold the tags for this content
        $tags = array();

        foreach($objects as $tag)
            $tags[] = new \Swiftriver\Core\ObjectModel\Tag($tag);

        //Add the tags to the content
        $this->content->tags = $tags;

        //return the tagged content
        return $this->content;
    }

}
?>
