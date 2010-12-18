<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Rating extends Controller_Template_Modal
{
    public function action_rating()
    {
        $this->template->title = "Confirm rating";
        $this->template->content = new View('config/rating');
    }
}
