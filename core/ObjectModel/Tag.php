<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Tag object
 * @author mg[at]swiftly[dot]org
 */
class Tag
{
    /**
     * The type of the tag. 
     * @var string (who|what|where)
     */
    public $type = "General";

    /**
     * The text of the tag.
     * @var string
     */
    public $text;

    public function __construct($text, $type = "General")
    {
        $this->type = $type;
        $this->text = $text;
    }
}
?>