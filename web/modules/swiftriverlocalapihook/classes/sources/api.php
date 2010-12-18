<?php
class Sources_API {

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

    public function activate_source($json_encoded_parameters)
    {
        $workflow = new \Swiftriver\Core\Workflows\SourceServices\ActivateSource();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function add_source($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\SourceServices\AddSource();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function desctivate_source($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\SourceServices\DeactivateSource();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function delete_source($json_encoded_parameters)
    {
        $workflow = new \Swiftriver\Core\Workflows\SourceServices\DeleteSource();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function get_all_sources()
    {
        $workflow = new Swiftriver\Core\Workflows\SourceServices\GetAllSources();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }

    public function get_source($json_encoded_parameters)
    {
        $workflow = new \Swiftriver\Core\Workflows\SourceServices\GetSource();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function list_available_source_types()
    {
        $workflow = new \Swiftriver\Core\Workflows\SourceServices\ListAvailableSourceTypes();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }

    public function save_source($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\SourceServices\SaveSource();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }
}