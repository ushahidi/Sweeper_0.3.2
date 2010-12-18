<?php
namespace Swiftriver\GoogleLanguageServiceInterface;
class LanguageDetectionInterface {
    private $text;
    private $referer;
    public function __construct($text, $referer) {
        $this->text = $text;
        $this->referer = $referer;
    }

    public function GetLanguageCode() {
        $context = stream_context_create(
            array(
                'http' => array(
                    "Referer: ".$this->referer."\r\n",
                ),
            ));
        $uri = "http://ajax.googleapis.com/ajax/services/language/detect?v=1.0&q=".urlencode($this->text);
        $returnData = file_get_contents($uri, false, $context);
        $object = json_decode($returnData);
        $languageCode = $object->responseData->confidence > 0.1
                ? $object->responseData->language
                : null;
        return $languageCode;
    }
}
?>
