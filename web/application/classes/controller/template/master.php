<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Template_Master extends Controller_Template
{
    public $template = 'template/master';

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

}

