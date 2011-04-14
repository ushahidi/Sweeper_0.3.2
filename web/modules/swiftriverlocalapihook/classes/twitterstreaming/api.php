<?php
class Twitterstreaming_API
{
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

    public function start_streaming($json_encoded_parameters)
    {
        $workflow = new Swiftriver\Core\Workflows\TwitterStreamingServices\StartTwitterStreamer();
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);
        return $json;
    }

    public function stop_streaming()
    {
        $workflow = new Swiftriver\Core\Workflows\TwitterStreamingServices\StopTwitterStreamer();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }
    public function get_config()
    {
        $workflow = new \Swiftriver\Core\Workflows\TwitterStreamingServices\GetTwiterStreamConfig();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }

    public function get_isactive()
    {
        $workflow = new \Swiftriver\Core\Workflows\TwitterStreamingServices\IsActive();
        $json = $workflow->RunWorkflow($this->apiKey);
        return $json;
    }
}