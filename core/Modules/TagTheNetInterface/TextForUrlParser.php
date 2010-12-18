<?php
namespace Swiftriver\TagTheNetInterface;
class TextForUrlParser {
    /**
     * The content item to extract uri lext from
     * @var \Swiftriver\Core\ObjectModel\Content
     */
    private $content;

    /**
     * The constructor for this object. Takes in
     * the Content item that text is to be extracted
     * from
     * @param \Swiftriver\Core\ObjectModel\Content; $content
     */
    public function __construct($content) {
        $this->content = $content;
    }

    public function GetUrlText() {
        //Validation checks
        if(!isset($this->content))
            return null;
        
        //Get the title for the content
        $urlText = $this->content->text[0]->title;

        //Get the lines of text
        $textLines = $this->content->text[0]->text;

        //Loop through all text from the content
        if(isset($textLines)) {
            foreach($textLines as $text) {
                $urlText .= " ".$text;
            }
        }

        //encode the text ready for transmission
        $urlText = utf8_encode($urlText);
        $urlText = urlencode($urlText);

        //If the text is long, truncate it to the apache standard length (2000)
        if(strlen($urlText) > 2000) {
            $urlText = substr($urlText, 0, 2000);
        }

        //Return the url text
        return $urlText;
    }
}
?>
