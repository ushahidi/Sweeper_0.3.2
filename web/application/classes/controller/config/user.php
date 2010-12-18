<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_User extends Controller
{
    public function action_login()
    {
        $username = $_POST["username"];

        $password = $_POST["password"];

        $return->result = RiverId::log_in($username, $password);

        $this->request->response = json_encode($return);
    }

    public function action_logout()
    {
        RiverId::log_out();

        $this->request->redirect("");
    }

    public function action_register()
    {
        $username = $_POST["username"];

        $password = $_POST["password"];

        $role = $_POST["role"];

        $return->result = RiverId::register($username, $password, $role);

        $this->request->response = json_encode($return);
    }
}