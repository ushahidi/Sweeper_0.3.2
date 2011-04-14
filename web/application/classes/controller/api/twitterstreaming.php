<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Twitterstreaming extends Controller
{
    public function action_start()
    {
        $json = \json_encode($_POST['json']);

        API::twitterstreaming_api()->start_streaming($json);
    }

    public function action_stop()
    {
        API::twitterstreaming_api()->stop_streaming();
    }

    public function action_getconfig()
    {
        $this->request->response = API::twitterstreaming_api()->get_config();
    }
}
