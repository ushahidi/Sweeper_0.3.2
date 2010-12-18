<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Template_Modal extends Controller_Template
{
    public $template = 'template/modal';

    public function before()
    {
        parent::before();

        $this->template->title = "modal title";
        $this->template->content = '';
    }

}

