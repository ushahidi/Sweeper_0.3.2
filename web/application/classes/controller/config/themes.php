<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Themes extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "Choose a theme";
        $this->template->content = new View("config/themes");
        $this->template->content->themes = $this->collect_themes();
    }

    public function action_select()
    {
        Theming::set_theme($_POST['cssfile']);
        $this->request->redirect("");
    }

    private function collect_themes()
    {
        return Theming::collect_themes();
    }
}
