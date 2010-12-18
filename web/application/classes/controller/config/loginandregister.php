<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_LoginAndRegister extends Controller_Template_Modal
{
    public function action_login()
    {
        $this->template->title = "Login";
        $this->template->content = new View('config/login');
    }

    public function action_register()
    {
        $this->template->title = "Register a new user";
        $this->template->content = new View('config/registernewuser');
    }
}
