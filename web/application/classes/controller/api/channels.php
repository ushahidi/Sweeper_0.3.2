<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Channels extends Controller
{
    public function action_add()
    {
        //Get the json from the post
        //TODO: Check and clean the data posted here
        $json_encoed_channel = $_POST["channel"];

        //Call the API
        $json = API::channel_api()->add_channel($json_encoed_channel);

        //Return the API message
        $this->request->response = $json;
    }

    public function action_save()
    {
        //Get the json from the post
        //TODO: Check and clean the data posted here
        $json_encoed_channel = $_POST["channel"];

        //Call the API
        $json = API::channel_api()->save_channel($json_encoded_parameters);

        //Return the API message
        $this->request->response = $json;
    }

    public function action_activateChannel($id)
    {
        //Create a paraeters object to hold the params
        $parameters->id = $id;

        //json encode the params
        $json_encoded_parameters = json_encode($parameters);

        //call the API
        $json = API::channel_api()->activate_channel($json_encoded_parameters);

        //return the json from the api call
        $this->request->response = $json;
    }

    public function action_deactivateChannel($id)
    {
        //Create a paraeters object to hold the params
        $parameters->id = $id;

        //json encode the params
        $json_encoded_parameters = json_encode($parameters);

        //call the API
        $json = API::channel_api()->desctivate_channel($json_encoded_parameters);

        //return the json from the api call
        $this->request->response = $json;
    }

    public function action_deleteChannel($id)
    {
        //Create a paraeters object to hold the params
        $parameters->id = $id;

        //json encode the params
        $json_encoded_parameters = json_encode($parameters);

        //call the API
        $json = API::channel_api()->delete_channel($json_encoded_parameters);

        //return the json from the api call
        $this->request->response = $json;
    }

    public function action_getChannel($id)
    {
        //Create a paraeters object to hold the params
        $parameters->id = $id;

        //json encode the params
        $json_encoded_parameters = json_encode($parameters);

        //call the API
        $json = API::channel_api()->get_channel($json_encoded_parameters);

        //return the json from the api call
        $this->request->response = $json;
    }

    public function action_listallavailableChanneltypes()
    {
        //call the api
        $json = API::channel_api()->list_available_channel_types();

        //return the json
        $this->request->response = $json;
    }

    public function action_getall()
    {
        $json = API::channel_api()->get_all_channels();

        //return the json
        $this->request->response = $json;
    }
}
