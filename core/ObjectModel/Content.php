<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Content object
 * @author mg[at]swiftly[dot]org
 */
class Content
{
    /**
     * The unique Id of the content
     * @var sytring
     */
    public $id;

    /**
     * The current state of the content
     * @var string
     */
    public $state;

    /**
     * A none-assisative array of language specific
     * text associated with the content. Each element
     * of the array is an instance of the
     * Core\ObjectModel\LanguageSpecificText class
     * @var LanguageSpecificText[]
     */
    public $text = array();

    /**
     * The hyperlink to the original content
     * @var string
     */
    public $link;

    /**
     * the publish date of the content
     * @var timestamp
     */
    public $date;

    /**
     * An array of tags for the content
     * @var \Swiftriver\Core\ObjectModel\Tag[]
     */
    public $tags = array();

    /**
     * The source of the content
     * @var \Swiftriver\Core\ObjectModel\Source
     */
    public $source;

    /**
     * The array of DIFs
     * @var \Swiftriver\Core\ObjectModel\DuplicationIdentificationFieldCollection[]
     */
    public $difs = array();

    /**
     * The global positioning data for this content
     * @var GisData;
     */
    public $gisData = array();

    /**
     * This is the primary mechnisum for extending the product item class,
     * You can use this associative array in any way you can imagine! All
     * you need to be aware of is how the data will be used later on.
     * @var array
     */
    public $extensions = array();
}
?>
