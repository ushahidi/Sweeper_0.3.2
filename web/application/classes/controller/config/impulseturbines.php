<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_ImpulseTurbines extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "Configure Swiftriver Impulse Turbines";
        $this->template->content = new View("config/impulseturbines");
        $return = API::preprocessing_steps_api()->list_all_preprocessing_steps();
        $object = json_decode($return);
        $this->template->content->turbines = $object->data->steps;
    }

    public function action_activate()
    {
        $object->name = $_POST["name"];
        $json_encodedParameters = json_encode($object);
        $json = API::preprocessing_steps_api()->activate_preprocessing_step($json_encodedParameters);
        $this->request->response = $json;
    }

    public function action_deactivate()
    {
        $object->name = $_POST["name"];
        $json_encodedParameters = json_encode($object);
        $json = API::preprocessing_steps_api()->deactivate_preprocessing_step($json_encodedParameters);
        $this->request->response = $json;
    }

    public function action_save()
    {
        $object->name = $_POST["name"];
        $object->data = $_POST["data"];
        $json_encoded_parameters = json_encode($object);
        $json = API::preprocessing_steps_api()->save_preprocessing_step($json_encoded_parameters);
        $this->request->response = $json;
    }
}