<?php
namespace Swiftriver\Core\Workflows;
/**
 * @author mg@swiftly.com
 */
class WorkflowBase
{
    /**
     * Returns the given error in standard JSON format
     * @param string $error
     * @return string
     */
    protected function FormatErrorMessage($error)
    {
        return '{"message":"'.str_replace('"', '\'', $error).'"}';
    }

    /**
     * Returns the given message in standard JSON format
     * @param string $message
     * @return string
     */
    protected function FormatMessage($message)
    {
        return '{"message":"'.str_replace('"', '\'', $message).'"}';
    }

    protected function FormatReturn($json)
    {
        return '{"message":"OK","data":'.$json.'}';
    }

    /**
     * Checks to see if the API key provided matches the configured
     * API Keys for this Core install
     * @param string $key
     * @return bool
     */
    public function CheckKey($key) 
    {
        //BETA - accept all dev calls
        return $key == "swiftriver_dev";
    }
}
?>
