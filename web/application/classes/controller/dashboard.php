<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dashboard extends Controller_Template_Master
{
    public function action_index()
    {
        $this->state = "dashboard";
        $this->template->rightbar = View::factory("pages/dashboard");
        $this->set_menu();
    }
}