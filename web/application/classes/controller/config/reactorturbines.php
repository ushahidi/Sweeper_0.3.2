<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_ReactorTurbines extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "Configure Swiftriver Reactor Turbines";
        $this->template->content = new View("config/reactorturbines");
        $return = API::event_handlers_api()->list_all_event_handlers();
        $object = json_decode($return);
        $this->template->content->turbines = $object->data->handlers;
    }

    public function action_activate()
    {
        $object->name = $_POST["name"];
        $json_encodedParameters = json_encode($object);
        $json = API::event_handlers_api()->activate_event_handler($json_encodedParameters);
        $this->template->content = $json;
    }

    public function action_deactivate()
    {
        $object->name = $_POST["name"];
        $json_encodedParameters = json_encode($object);
        $json = API::event_handlers_api()->deactivate_event_handler($json_encodedParameters);
        $this->template->content = $json;
    }

    public function action_save()
    {
        $object->name = $_POST["name"];
        $object->data = $_POST["data"];
        $json_encoded_parameters = json_encode($object);
        $json = API::event_handlers_api()->save_event_handler($json_encoded_parameters);
        $this->template->content = $json;
    }
}