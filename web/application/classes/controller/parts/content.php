<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Parts_Content extends Controller_Template
{
    public $template = "/parts/contentitem";

    public function action_render()
    {
        //Very strange to have to encode then decoe but we want objects
        //at the end of the day not arrays and this is the easiest way
        //to get one!
        $content = json_decode(json_encode($_POST["content"]));

        //do some work on organisng the tags
        $tags = array("general" => array(), "who" => array(), "what" => array(), "where" => array());
        if(isset($content->tags)) {
            foreach($content->tags as $tag) {
                switch($tag->type) {
                    case "General" : $tags["general"][] = $tag->text; break;
                    case "who" : $tags["who"][] = $tag->text; break;
                    case "what" : $tags["what"][] = $tag->text; break;
                    case "where" : $tags["where"][] = $tag->text; break;
                }
            }
        }
        $content->tags = $tags;

        $this->template->content = $content;

        $logincheck = RiverId::is_logged_in();

        $this->template->enableActions = $logincheck["IsLoggedIn"];
    }
}
