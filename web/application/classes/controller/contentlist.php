<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ContentList extends Controller_Template_Master
{
    public function action_index($state)
    {
        $loggedinstatus = RiverId::is_logged_in();
        if(!$loggedinstatus["IsLoggedIn"])
            $state = "accurate";
        else if($loggedinstatus["Role"] == "sweeper")
            $state = "new_content";
        else if($loggedinstatus["Role"] == "editor")
            $state = "new_content";
        else if($loggedinstatus["Role"] == "admin")
            $state = "new_content";
        $this->action_get($state);
    }

    public function action_get($state)
    {
        $this->state = $state;
        $_SESSION["nav_state"] = $state;
        $this->set_content();
        $this->set_menu();
    }
} 
