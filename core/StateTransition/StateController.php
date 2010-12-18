<?php
namespace Swiftriver\Core\StateTransition;
/**
 * Class providing static strongly typed access to the
 * list of alowable states and the transition of states
 * of an item of content
 *
 * @author mg[at]swiftly[dot]org
 */
class StateController
{

    /**
     * the default state of a content item
     * @var int
     */
    public static $defaultState = "new_content";

    /**
     * public list of all states
     * @var array
     */
    private static $states = array
    (
        "new_content",
        "accurate",
        "inaccurate",
        "chatter",
        "irrelevant"
    );

    /**
     * Given a content item, this function marks it as ACCURATE
     * @param \Swiftriver\Core\ObjectModel\Content $content
     */
    public static function MarkContentAcurate($content)
    {
        $content->state = "accurate";
        return $content;
    }

    /**
     * Given a content item, this function marks it as INACCURATE
     * @param \Swiftriver\Core\ObjectModel\Content $content
     */
    public static function MarkContentInaccurate($content)
    {
        $content->state = "inaccurate";
        return $content;
    }

    /**
     * Given a content item, this function marks it as CHATTER
     * @param \Swiftriver\Core\ObjectModel\Content $content
     */
    public static function MarkContentChatter($content)
    {
        $content->state = "chatter";
        return $content;
    }

    /**
     * Given a content item, this function marks it as IRRELEVANT
     * @param \Swiftriver\Core\ObjectModel\Content $content
     */
    public static function MarkContentIrrelevant($content)
    {
        $content->state = "irrelevant";
        return $content;
    }

    /**
     * Given a string, this function checks if it is a valid reason for
     * marking a content item as inaccurate.
     * 
     * @param string $reason
     * @return bool
     */
    public static function IsValidInacurateReason($reason)
    {
        return ($reason == "falsehood" || $reason == "inaccuracy" || $reason == "biased");
    }

}
?>
