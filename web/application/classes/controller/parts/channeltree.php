<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Parts_channelTree extends Controller_Template
{
    public $template = "parts/channeltree";

    public function action_render()
    {
        $availableChannelTypesJson = API::channel_api()->list_available_channel_types();
        $availableChannelTypes = json_decode($availableChannelTypesJson);
        $channelTypesArray = $availableChannelTypes->data->channelTypes;

        $channelsJson = API::channel_api()->get_all_channels();
        $channels = json_decode($channelsJson);
        $channelsArray = $channels->data->channels;
        
        $return;
        $return->channelTypes = array();
        foreach($channelTypesArray as $channelType)
        {
            $t->type = $channelType->type;
            $t->subTypes = array();
            foreach($channelType->subTypes as $subType)
            {
                $st->type = $subType;
                $st->sources = array();
                foreach($channelsArray as $source)
                {
                    if($source->type != $t->type || $source->subType != $st->type)
                        continue;

                    $c->name = $source->name;
                    $c->id = $source->id;
                    $st->sources[] = $c;
                    unset($c);
                }
                $t->subTypes[] = $st;
                unset($st);
            }
            $return->channelTypes[] = $t;
            unset($t);
        }

        $this->template->channels = $return;
    }
}