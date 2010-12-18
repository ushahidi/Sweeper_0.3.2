<?php
class Channels_API {

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

    public function activate_channel($json_encoded_parameters)
    {
        $workflow = new \Swiftriver\Core\Workflows\ChannelServices\ActivateChannel();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function add_channel($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\ChannelServices\AddChannel();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function desctivate_channel($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\ChannelServices\DeactivateChannel();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function delete_channel($json_encoded_parameters)
    {
        $workflow = new \Swiftriver\Core\Workflows\ChannelServices\DeleteChannel();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function get_all_channels()
    {
        $workflow = new Swiftriver\Core\Workflows\ChannelServices\GetAllChannels();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }

    public function get_channel($json_encoded_parameters)
    {
        $workflow = new \Swiftriver\Core\Workflows\ChannelServices\GetChannel();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function list_available_channel_types()
    {
        $workflow = new \Swiftriver\Core\Workflows\ChannelServices\ListAvailableChannelTypes();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }

    public function save_channel($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\ChannelServices\SaveChannel();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }
}