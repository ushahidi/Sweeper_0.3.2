<?php
namespace Swiftriver\Core\Workflows\PreProcessingSteps;
/**
 * @author mg[at]swiftly[dot]org
 */
class PreProcessingStepsBase extends \Swiftriver\Core\Workflows\WorkflowBase
{
    public function ParseStepsToJson($steps)
    {
        $modulesConfig = \Swiftriver\Core\Setup::DynamicModuleConfiguration();

        $return;

        $return->steps = array();
        
        foreach($steps as $step)
        {
            $s;

            $s->name = $step->Name();

            $s->description = $step->Description();

            $s->configurationProperties = $step->ReturnRequiredParameters();

            if(array_key_exists($s->name, $modulesConfig->Configuration))
            {
                $configuration = $modulesConfig->Configuration[$s->name];
                
                if($configuration != null)
                    foreach($s->configurationProperties as $property) 
                        foreach($configuration as $key => $config) 
                            if($property->name == $key) 
                                $property->value = $config->value;
            }

            $s->active = isset($step->active);

            $return->steps[] = $s;

            unset($s);
        }

        return json_encode($return);
    }

    public function ParseJsonToPreProcessingStepName($json)
    {
        $result = json_decode($json);

        if(!$result || $result == null)
            throw new \InvalidArgumentException("The json was malformed");

        if(!isset($result->name) || !is_string($result->name))
            throw new \InvalidArgumentException("The JSON did not contain the required 'name' string");

        return $result->name;
    }

    public function ParseJsonToPreProcessingStepConfiguration($json)
    {
        $array = json_decode($json, true);

        if(!$array || $array == null) 
            throw new \InvalidArgumentException("The json was malformed");

        $return = array();

        $config = $array["data"];

        if($config == null)
            return $return;

        foreach($config as $key => $value)
            $return[$key] = $value;

        return $return;
    }
}
?>
