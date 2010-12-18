<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
class FrontlineSMSParser implements IParser {
    /**
     * Implementation of IParser::GetAndParse
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @return Swiftriver\Core\ObjectModel\Content[] contentItems
     */
    public function GetAndParse($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Create the Content array
        $contentItems = array();

        switch($channel->subType) {
            case "Remote":
                $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [START: Parsing SMS items from Remote API]", \PEAR_LOG_DEBUG);
                
                $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

                //Extract the Server URL
                $serverURL = $channel->parameters["ServerURL"];

                if(!isset($serverURL)) {
                    $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [the parameter 'ServerURL' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
                    $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
                    return null;
                }

                $contentItems = $this->getRemoteContentItems($channel, $logger, $serverURL);

                $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [END: Parsing SMS items from Remote API]", \PEAR_LOG_DEBUG);
                break;
            case "Local":
                $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [START: Parsing SMS items from Local DB]", \PEAR_LOG_DEBUG);

                $contentItems = $this->getLocalContentItems($channel, $logger);

                $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::GetAndParse [END: Parsing SMS items from Local DB]", \PEAR_LOG_DEBUG);
                break;
        }



        //return the content array
        return $contentItems;
    }

    /**
     * Gets content items from the server
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @param string $serverURL
     *
     * @return array()
     */

    private function getRemoteContentItems($channel, $logger, $serverURL) {
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getRemoteContentItems [Preparing URL]", \PEAR_LOG_DEBUG);

        $endServerURL = "index.php";

        if(isset($channel->lastSucess)) {
            $endServerURL.="?lastmessagedate=".date('U', $channel->lastSucess)."000";
        }

        $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getRemoteContentItems [Connecting to server: ".$serverURL.$endServerURL."]", \PEAR_LOG_DEBUG);
        
        $json_response = file_get_contents($serverURL.$endServerURL);
        $json_decoded = json_decode($json_response, true);

        $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getRemoteContentItems [Extracting message]", \PEAR_LOG_DEBUG);

        if(is_array($json_decoded)) {
            $num_messages = count($json_decoded["messages"]);

            $json_decoded = $json_decoded["messages"];

            $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getRemoteContentItems [Processing $num_messages messages]", \PEAR_LOG_DEBUG);

            for($message_index = 0; $message_index < $num_messages; $message_index ++) {
                $source_name = "";

                // Embed source name and source number
                $source_name = $json_decoded[$message_index]['senderMsisdn'];

                $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);

                $source->name = $source_name;
                $source->parent = $channel->id;
                $source->type = $channel->type;
                $source->subType = $channel->subType;

                //Create a new Content item
                $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

                $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                        "", //here we set null as we dont know the language yet
                        "", //the keyword can be used as a subject
                        array($json_decoded[$message_index]['textContent'], $json_decoded[$message_index]['binaryMessageContent'])); //the message

                $item->link = null;
                $item->date = time();

                $contentItems[] = $item;
            }
            
            return $contentItems;
        }
        else {
            return null;
        }

    }

    /**
     * Gets content items from the server
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @param string $serverURL
     *
     * @return array()
     */

    private function getLocalContentItems($channel, $logger) {
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getLocalContentItems [Preparing to read from DB]", \PEAR_LOG_DEBUG);

        $lastSuccess = "0";

        if(isset($channel->lastSucess)) {
            $lastSuccess = date('U', $channel->lastSucess)."000";
        }

        if($channel->parameters["Database"] == "" && $channel->parameters[""] == "UserName") {
            $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getLocalContentItems [Database and UserName parameters not set, exiting]", \PEAR_LOG_DEBUG);

            return null;
        }

        $my_connection = $this->localDBOpen("localhost", $channel->parameters["UserName"], $channel->parameters["Password"], $channel->parameters["Database"]);
        $my_messages = $this->localDBReturnMessages($my_connection, "message", $lastSuccess);
        $this->localDBClose($my_connection);

        if(is_array($my_messages)) {
            $num_messages = count($my_messages);

            $logger->log("Core::Modules::SiSPS::Parsers::FrontlineSMSParser::getContentItems [Processing $num_messages messages]", \PEAR_LOG_DEBUG);

            for($message_index = 0; $message_index < $num_messages; $message_index ++) {
                $source_name = "";

                // Embed source name and source number
                $source_name = $my_messages[$message_index]['senderMsisdn'];

                $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);

                $source->name = $source_name;
                $source->parent = $channel->id;
                $source->type = $channel->type;
                $source->subType = $channel->subType;

                //Create a new Content item
                $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

                $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                        "", //here we set null as we dont know the language yet
                        "", //the keyword can be used as a subject
                        array($my_messages[$message_index]['textContent'], $my_messages[$message_index]['binaryMessageContent'])); //the message

                $item->link = null;
                $item->date = time();

                $contentItems[] = $item;
            }

            return $contentItems;
        }
        else {
            return null;
        }

    }

    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse. For example
     * the FrontlineParser may return array("SMS");
     *
     * @return string[]
     */
    public function ListSubTypes() {
        return array(
            "Remote",
            "Local"
        );
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the FeedsParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType() {
        return "FrontlineSMS";
    }

    /**
     * This method returns an array of the required parameters that
     * are necessary to run this parser. The Array should be in the
     * following format:
     * array(
     *  "SubType" => array ( ConfigurationElements )
     * )
     *
     * @return array()
     */
    public function ReturnRequiredParameters(){
        $return = array();
        foreach($this->ListSubTypes() as $subType){
            if($subType == "Remote") {
                $return[$subType] = array(
                    new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "ServerURL",
                        "string",
                        "The Server's URL - This will contain the API"
                    )
                );
            }
            else {
                $return[$subType] = array(
                    new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "Database",
                        "string",
                        "FrontlineSMS database name"
                    ),
                    new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "UserName",
                        "string",
                        "User name for database"
                    ),
                    new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "Password",
                        "string",
                        "Password for database"
                    )
                );
            }
        }
        return $return;
    }

    function localDBOpen($host, $user, $password, $database) {
        // Open database connection and return resource to the connection
        $mysqlConnection = mysql_connect($host, $user, $password, $database);
        mysql_select_db($database, $mysqlConnection);

        return $mysqlConnection;
    }

    function localDBClose($myConnection) {
        // Close an open resource
        if($myConnection) {
            mysql_close($myConnection);
        }
    }

    function localDBReturnMessages($myConnection, $messageTable = 'message', $lastMessageDate = 0) {
        // Return an array of messages

        if(!$myConnection) {
            return null;
        }

        $whereQuery = "";

        if($lastMessageDate > 0) {
            $whereQuery = " WHERE `$messageTable`.`date` > $lastMessageDate";
        }

        $sqlQuery = "SELECT * FROM $messageTable".$whereQuery;

        $myResult = mysql_query($sqlQuery, $myConnection);
        $myArray = array();

        if($myResult) {
            $currentArrayIndex = 0;

            while($myItemArray = mysql_fetch_array($myResult)) {
                $myArray[$currentArrayIndex] = $myItemArray;
                $currentArrayIndex ++;
            }

            return $myArray;
        }
        else {
            return null;
        }
    }
}
?>