<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Parts_AddChannel extends Controller_Template_Modal
{
    public function action_index($type, $subType)
    {
        $this->template->title = "Add a new channel - $type / $subType";
        $this->template->content = new View("parts/addchannel");
        $this->template->content->type = $type;
        $this->template->content->subType = $subType;
        $json = API::channel_api()->list_available_channel_types();
        $channelTypesContainer = json_decode($json);
        $channelTypes = $channelTypesContainer->data->channelTypes;
        foreach($channelTypes as $channelType) {
            if($channelType->type == $type)
                $this->template->content->channel = $channelType;
        }
    }
}

