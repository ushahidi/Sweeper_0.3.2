<?php
class UserCreation implements IInstallStep
{
    private $firsttime = true;
    private $errors = array();

    public function GetName()
    {
        return "User Accounts";
    }

    public function GetDescription()
    {
        return "Here you can specify the password for the ".
               "administrative account to use with this ".
               "instance of Swiftriver. Once you create one ".
               "here, you can go ahead and create others later.";
    }

    public function RunChecks($postVar)
    {
        $this->firsttime = count($postVar) == 0;

        if($this->firsttime)
            return null;

        if(!key_exists("password1", $postVar) || strlen($postVar["password1"]) == 0)
            $this->errors[] = "You have to enter a password in the first box.";


        if(!key_exists("password2", $postVar) || strlen($postVar["password2"]) == 0)
            $this->errors[] = "You have to enter a password in the second box.";

        if(count($this->errors) > 0)
            return false;

        $password1 = $postVar["password1"];
        $password2 = $postVar["password2"];

        if($password1 != $password2)
            $this->errors[] = "The passwords you entered did not match.";

        if(count($this->errors) > 0)
            return false;

        if(strlen($password1) < 6)
            $this->errors[] = "The password you choose must be at lease 6 characters long.";

        if(count($this->errors) > 0)
            return false;

        try
        {
            include_once(dirname(__FILE__)."/../../web/modules/riverid/classes/riverid.php");
            include_once(dirname(__FILE__)."/../../web/modules/riverid/classes/riveridconfig.php");
            RiverId::register("admin", $password1, "admin");
            return true;
        }
        catch(\Exception $e)
        {
            $this->errors[] = "There was a problem registering the admin username and password: $e";
            return false;
        }
    }

    public function Render()
    {
        if(!$this->firsttime && count($this->errors) == 0)
            return "<div class='message'>" .
                    "<p>Thats great, all that worked out with no problems.</p>".
                    "<p>When you have finished installing me, click the login ".
                    "button and then use the username <strong>'admin'</strong> and the password ".
                    "you just set!</p>".
                   "</div>";


        $return = "";

        if(count($this->errors) > 0)
        {
            $return .= "<div class='alert'><ul>";
            foreach($this->errors as $error)
                $return .= "<li>" . $error . "</li>";
            $return .= "</ul></div>";
        }

        if($this->firsttime || count($this->errors) > 0)
            $return .= "<form class='users' action='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "' method='post'>".
                        "<div class='form-row'>" .
                            "<label>Please enter the password for the admin account:</label>".
                            "<input type='password' name='password1' />".
                        "</div>".
                        "<div class='form-row'>" .
                            "<label>Please re-enter the password:</label>".
                            "<input type='password' name='password2' />".
                        "</div>".
                        "<div class='form-action'>" .
                            "<input type='submit' value='Go and set this password!' />".
                        "</div>".
                       "</form>";

        return $return;
    }
}
?>
