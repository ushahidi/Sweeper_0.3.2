<?php
class Content_API
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

    public function get_content_list($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\ContentServices\GetContent();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function mark_content_as_accurate($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\ContentServices\MarkContentAsAcurate();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function mark_content_as_inaccurate($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\ContentServices\MarkContentAsInacurate();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function mark_content_as_irrelevant($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\ContentServices\MarkContentAsIrrelevant();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function mark_content_as_cross_talk($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\ContentServices\MarkContentAsChatter();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function update_content_tags($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\ContentServices\UpdateContentTagging();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;    
    }
}
?>
