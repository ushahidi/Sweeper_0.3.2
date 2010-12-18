<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Content extends Controller_Template_Modal
{
    public function action_content()
    {
        $this->template->title = "Content details";
        $this->template->content = new View('config/content');
    }
}
