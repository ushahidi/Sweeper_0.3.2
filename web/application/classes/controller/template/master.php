<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Template_Master extends Controller_Template
{
    public $template = 'template/master';

    protected $state;

    public function before()
    {
        parent::before();
        
        $this->template->title = 'Swiftriver';
        $this->template->theme = Theming::get_theme();
        $this->template->header = new View('defaults/header');
        $this->template->content = '';
        $this->template->rightbar = new View('defaults/rightbar');
        $this->template->footer = new View('defaults/footer');
        

        $loggedinstatus = RiverId::is_logged_in();
        if(!$loggedinstatus["IsLoggedIn"])
            $this->template->admin = new View("adminbar/user");
        else if($loggedinstatus["Role"] == "sweeper")
            $this->template->admin = new View("adminbar/sweeper");
        else if($loggedinstatus["Role"] == "editor")
            $this->template->admin = new View("adminbar/editor");
        else if($loggedinstatus["Role"] == "admin")
            $this->template->admin = new View("adminbar/admin");
    }

    protected function set_menu()
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
        $this->template->menu->dashboard_class = ($this->state == "dashboard") ? "selected" : "";
    }

    protected function set_content()
    {
        $this->template->content = new View("pages/contentlist");
        $this->template->content->state = $this->state;
    }
}

