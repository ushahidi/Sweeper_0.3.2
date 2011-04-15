<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Twitterstreaming extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "";
        $this->template->content = new View("config/twitterstreaming");

        $isactive = API::twitterstreaming_api()->get_isactive();
        $this->template->content->isactive = json_decode($isactive)->data->IsActive;
        $twitterstreamingconfigjson = API::twitterstreaming_api()->get_config();
        $twitterstreamingconfig = json_decode($twitterstreamingconfigjson);
        if(isset($twitterstreamingconfig->data))
        {
            $this->template->content->TwitterUsername = $twitterstreamingconfig->data->TwitterUsername;
            $this->template->content->TwitterPassword = $twitterstreamingconfig->data->TwitterPassword;
            $this->template->content->SearchTerms = implode(' ' , $twitterstreamingconfig->data->SearchTerms);
        }
        else
        {
            $this->template->content->TwitterUsername = "";
            $this->template->content->TwitterPassword = "";
            $this->template->content->SearchTerms = "";
        }

    }
}