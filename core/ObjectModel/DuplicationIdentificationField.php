<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Duplication Identification Fields’ (DIFs) are a
 * Swiftriver standard mechanism for helping to
 * identify duplicated data.  Populating these fields is
 * the responsibility of the parser associated with the
 * channel  type. For example, if the  channel  is twitter
 * then the unique_tweet_id can be used as one DIF so
 * can the content of the tweet parsed to remove
 * common additions – such as r/t. 
 *
 * @author mg[at]swiftly[dot]org
 */
class DuplicationIdentificationField
{
    /**
     * The type of this DIF
     * @var string
     */
    public $type;
    
    /**
     * The value of this DIF
     * @var string
     */
    public $value;

    public function  __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}
?>
