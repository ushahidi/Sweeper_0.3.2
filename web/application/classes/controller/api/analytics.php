<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Analytics extends Controller
{
    public function action_totalcontentbychannel()
    {
        $json = API::analytics_api()->run_analytics_query('{"RequestType":"TotalContentByChannelAnalyticsProvider"}');

        //Return the API message
        $this->request->response = $json;
    }

    public function action_contentbychannelovertime($timelimit = 100)
    {
        $json = API::analytics_api()->run_analytics_query('{"RequestType":"ContentByChannelOverTimeAnalyticsProvider","Parameters":{"TimeLimit":'.$timelimit.'}}');

        //Return the API message
        $this->request->response = $json;
    }

    public function action_accumulatedcontentovertime()
    {
        $json = API::analytics_api()->run_analytics_query('{"RequestType":"AccumulatedContentOverTimeAnalyticsProvider"}');

        //Return the API message
        $this->request->response = $json;
    }

    public function action_totaltagpopularity($limit = 20)
    {
        $json = API::analytics_api()->run_analytics_query('{"RequestType":"TotalTagPopularityAnalyticsProvider","Parameters":{"Limit":'.$limit.'}}');

        //Return the API message
        $this->request->response = $json;
    }
}