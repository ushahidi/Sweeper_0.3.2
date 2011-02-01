<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_ContentSelection extends Controller
{
    public function action_index()
    {
        if(!isset($_POST) || \count($_POST) < 1)
            return;

        $strings = array ("state", "type", "subType", "source", "orderBy");

        $ints = array ("minVeracity", "maxVeracity", "pageSize", "pageStart", "time");
        
        $params = array();

        foreach($strings as $name)
            if(\key_exists($name, $_POST) &&  $_POST[$name] != null && $_POST[$name] != "null")
                $params[$name] = $_POST[$name];

        foreach($ints as $name)
            if(\key_exists($name, $_POST) && $_POST[$name] != null && $_POST[$name] != "null")
                $params[$name] = (int) $_POST[$name];

        //Deal with the | seporated tags
        if(\key_exists("tags", $_POST) && $_POST["tags"] != null && $_POST["tags"] != "null")
        {
            $tags = \explode("|", \trim($_POST["tags"], "|"));

            $params["tags"] = $tags;
        }

        $json_encoded_parameters = json_encode($params);
        $json = API::content_api()->get_content_list($json_encoded_parameters);
        $this->request->response = $json;
    }

    /**
     * Depricated - please use the post method above
     */
    public function action_get($state, $minVeracity, $maxVeracity, $type, $subType, $source, $pageSize, $pageStart, $orderBy)
    {
        $params = array();

        if($state != null && $state != "null")                  $params["state"] = $state;
        if($minVeracity != null && $minVeracity != "null")      $params["minVeracity"] = (int)$minVeracity;
        if($maxVeracity != null && $maxVeracity != "null")      $params["maxVeracity"] = (int)$maxVeracity;
        if($type != null && $type != "null")                    $params["type"] = $type;
        if($subType != null && $subType != "null")              $params["subType"] = $subType;
        if($source != null && $source != "null")                $params["source"] = $source;
        if($pageSize != null && $pageSize != "null")            $params["pageSize"] = (int)$pageSize;
        if($pageStart != null && $pageStart != "null")          $params["pageStart"] = (int)$pageStart;
        if($orderBy != null && $orderBy != "null")              $params["orderBy"] = $orderBy;

        $json_encoded_parameters = json_encode($params);
        $json = API::content_api()->get_content_list($json_encoded_parameters);
        $this->request->response = $json;
    }
}