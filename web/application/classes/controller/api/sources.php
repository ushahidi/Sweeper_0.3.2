<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Sources extends Controller
{
    public function action_getsource($id)
    {
        //Create a paraeters object to hold the params
        $parameters->id = $id;

        //json encode the params
        $json_encoded_parameters = json_encode($parameters);

        //call the API
        $json = API::sources_api()->get_source($json_encoded_parameters);

        //return the json from the api call
        $this->request->response = $json;
    }
}
