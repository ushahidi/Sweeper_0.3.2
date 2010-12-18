<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
/**
 * @author ultimateprogramer@gmail.com
 */
class IMAPParser implements IParser {
    /**
     * Gets IMAP content
     *
     * @param string $imapHost
     * @param string $imapUser
     * @param string $imapPassword
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     *
     * @return $contentItems[]
     */
    private function GetIMAPContent($imapHost, $imapUser, $imapPassword, $channel) {
        $imapResource = imap_open("{".$imapHost."}INBOX", $imapUser, $imapPassword);
        //Open up unseen messages

        $search = ($channel->lastSuccess == null)
            ? "UNSEEN"
            : "UNSEEN SINCE " . \date("Y-m-d", $channel->lastSuccess);

        $imapEmails = imap_search($imapResource, $search);

        $contentItems = array();

        if($imapEmails) {
            //Put newest emails on top
            rsort($imapEmails);

            foreach($imapEmails as $Email) {
                //Loop through each email and return the content
                $email_overview = imap_fetch_overview($imapResource, $Email, 0);

                if(strtotime(reset($email_overview)->date) < $channel->lastSuccess)
                    continue;

                $email_header_info = imap_header($imapResource, $Email);
                $email_message = imap_fetchbody($imapResource, $Email, 1);

                $source_name = \reset($email_overview)->from;
                $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);

                $source->name = $source_name;
                $source->parent = $channel->id;
                $source->type = $channel->type;
                $source->subType = $channel->subType;

                $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

                $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                        null, //here we set null as we dont know the language yet
                        $email_overview[0]->subject, //email subject
                        array($email_message)); //the message

                $item->link = null;
                $item->date = $email_header_info->udate;

                $contentItems[] = $item;
            }
        }

        imap_close($imapResource);

        return $contentItems;
    }

    /**
     * Implementation of IParser::GetAndParse
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     */
    public function GetAndParse($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the IMAP parameters

        if(\array_key_exists("Host", $channel->parameters))
            $imapHostName = $channel->parameters["Host"];
        elseif ($channel->subType == "Gmail")
            $imapHostName = "imap.gmail.com:993/imap/ssl/novalidate-cert";
        else
        {
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [the parameter 'IMAPHostName' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $imapUserName = $channel->parameters["UserName"];
        $imapPassword = $channel->parameters["Password"];

        if(!isset($imapHostName) || ($imapHostName == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [the parameter 'IMAPHostName' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        if(!isset($imapUserName) || ($imapUserName == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [the parameter 'IMAPUserName' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        if(!isset($imapPassword) || ($imapPassword == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [the parameter 'IMAPPassword' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [START: Parsing IMAP items]", \PEAR_LOG_DEBUG);

        //Get information regarding the source

        $contentItems = $this->GetIMAPContent($imapHostName, $imapUserName, $imapPassword, $channel);

        $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [END: Parsing IMAP items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::EmailParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
    }

    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse.
     *
     * @return string[]
     */
    public function ListSubTypes() {
        return array(
            "Gmail",
            "Any Other Mail Account"
        );
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the FeedsParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType() {
        return "Email";
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
        return array (
            "Gmail" => array(
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "UserName",
                        "string",
                        "Your Gmail user name (including the @gmail bit)"),
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "Password",
                        "string",
                        "The password you use to log into this account")),
            "Any Other Mail Account" => array (
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "Host",
                    "string",
                    "Host URL (you may need to look this up, for example, GMail's URL is: imap.gmail.com:993/imap/ssl/novalidate-cert - not so intuitive, right)"),
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "UserName",
                    "string",
                    "IMAP login user name"),
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "Password",
                    "string",
                    "IMAP login password")));
    }
}
?>