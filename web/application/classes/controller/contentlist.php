<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ContentList extends Controller_Template_Master
{
    private $state;

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

    private function set_menu()
    {
        $loggedinstatus = RiverId::is_logged_in();
        if(!$loggedinstatus["IsLoggedIn"])
            $this->template->menu = new View("contentmenu/user");
        else if($loggedinstatus["Role"] == "sweeper")
            $this->template->menu = new View("contentmenu/sweeper");
        else if($loggedinstatus["Role"] == "editor")
            $this->template->menu = new View("contentmenu/editor");
        else if($loggedinstatus["Role"] == "admin")
            $this->template->menu = new View("contentmenu/editor");

        $this->template->menu->state = $this->state;
        $this->template->menu->new_content_class = ($this->state == "new_content") ? "selected" : "";
        $this->template->menu->accurate_class = ($this->state == "accurate") ? "selected" : "";
        $this->template->menu->inaccurate_class = ($this->state == "inaccurate") ? "selected" : "";
        $this->template->menu->irrelevant_class = ($this->state == "irrelevant") ? "selected" : "";
        $this->template->menu->chatter_class = ($this->state == "chatter") ? "selected" : "";
    }

    private function set_content()
    {
        $this->template->content = new View("pages/contentlist");
        $this->template->content->state = $this->state;
    }
} 
