<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Sources extends Controller_Template_Modal
{
    public function action_index()
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
                $st->configurationProperties = $channelType->configurationProperties;
                foreach($channelsArray as $source)
                {
                    if($source->type != $t->type || $source->subType != $st->type)
                        continue;

                    $c->name = $source->name;
                    $c->id = $source->id;
                    $c->active = $source->active;
                    $st->sources[] = $c;
                    unset($c);
                }
                $t->subTypes[] = $st;
                unset($st);
            }
            $return->channelTypes[] = $t;
            unset($t);
        }

        $this->template->title = "Content Sources";
        $this->template->content = new View("config/sources");
        $this->template->content->channels = $return;
    }

    public function action_add()
    {
        $data->parameters = array();
        foreach($_POST as $key => $value) {
            if($key == "name" || $key == "type" || $key == "subType" || $key == "updatePeriod") {
                $data->$key = $value;
            }
            else {
                $data->parameters[$key] = $value;
            }
        }
        $json_encoded_parameters = json_encode($data);
        $json = API::channel_api()->add_channel($json_encoded_parameters);
    }

    public function action_activate()
    {
        $object->id = $_POST["id"];
        $json_encoded_parameters = json_encode($object);
        $json = API::channel_api()->activate_channel($json_encoded_parameters);
    }

    public function action_deactivate()
    {
        $object->id = $_POST["id"];
        $json_encoded_parameters = json_encode($object);
        $json = API::channel_api()->desctivate_channel($json_encoded_parameters);
    }
}