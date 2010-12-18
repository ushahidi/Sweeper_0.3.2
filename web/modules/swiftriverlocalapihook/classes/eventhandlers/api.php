<?php
class EventHandlers_API {
    /**
     * The core API key
     * @var string Guid
     */
    private $apiKey;

    /**
     * Constructor method used to include the core setup file
     * before any of the api functions can be called
     */
    public function __construct($apiKey)
    {
        //Localise the api key
        $this->apiKey = $apiKey;

        //include the core one
        include_once(DOCROOT."../core/Setup.php");
    }

    public function list_all_event_handlers()
    {
        //Instanciate the workflow
        $workflow = new Swiftriver\Core\Workflows\EventHandlers\ListAllEventHandlers();

        //run the workflow
        $json = $workflow->RunWorkflow($this->apiKey);

        //return the json
        return $json;
    }

    public function activate_event_handler($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new Swiftriver\Core\Workflows\EventHandlers\ActivateEventHandler();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function deactivate_event_handler($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\EventHandlers\DeactivateEventHandler();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function save_event_handler($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new Swiftriver\Core\Workflows\EventHandlers\SaveEventHandler();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }
}
?>