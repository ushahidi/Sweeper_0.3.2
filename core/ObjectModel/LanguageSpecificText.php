<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Object to hold text in a given language
 * @author mg[at]swiftly[dot]org
 */
class LanguageSpecificText
{
    /**
     * The ISO 639-1 two letter language code
     * ref: http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     * @var string
     */
    public $languageCode;

    /**
     * The title in the language denoted by
     * $languageCode
     * @var string
     */
    public $title;

    /**
     * A none-associative array of text in the
     * language denoted by $languageCode
     * @var string[]
     */
    public $text;

    /**
     * Constructor for LanguageSpecificText objetc
     * @param string $languageCode the ISO 639-1 language code
     * @param string $title the title of the content
     * @param string[] $text the none-assositave array of the content text
     */
    public function __construct($languageCode, $title, $text)
    {
        $this->languageCode = $languageCode;
        $this->title = $title;
        $this->text = $text;
    }
}
?>
