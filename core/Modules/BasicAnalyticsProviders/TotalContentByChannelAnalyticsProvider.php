\<?php
namespace Swiftriver\AnalyticsProviders;
include_once(\dirname(__FILE__)."/BaseAnalyticsClass.php");
class TotalContentByChannelAnalyticsProvider
    extends BaseAnalyticsClass
    implements \Swiftriver\Core\Analytics\IAnalyticsProvider
{
    /**
     * Function that should return the name of the
     * given AnalyticsProvider.
     *
     * @return string
     */
    public function ProviderType()
    {
        return "TotalContentByChannelAnalyticsProvider";
    }

    /**
     * Function that when implemented by a derived
     * class should return an object that can be
     * json encoded and returned to the UI to
     * provide analytical information about the
     * underlying data.
     *
     * @param \Swiftriver\Core\Analytics\AnalyticsRequest $parameters
     * @return \Swiftriver\Core\Analytics\AnalyticsRequest
     */
    public function ProvideAnalytics($request)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Swiftriver::AnalyticsProviders::TotalContentByChannelAnalyticsProvider::ProvideAnalytics [Method Invoked]", \PEAR_LOG_DEBUG);

        $parameters = $request->Parameters;

        $yearDay = (int) \date('z');

        $timeLimit = 5;

        if(\is_array($parameters))
            if(\key_exists("TimeLimit", $parameters))
                $timeLimit = (int) $parameters["TimeLimit"];

        $date = \strtotime("-$timeLimit days");

        $sql =
            "SELECT
                count(c.id) as numberofcontentitems,
                ch.id as channelId,
                ch.type as channelType,
                ch.subType as channelSubType
            FROM
                SC_Content c JOIN SC_Sources s ON c.sourceId = s.id
                JOIN SC_Channels ch ON s.channelId = ch.id
            WHERE
                c.date > $date
            GROUP BY
                channelId";

        try
        {
            $db = parent::PDOConnection($request);

            if($db == null)
                return $request;

            $statement = $db->prepare($sql);

            $result = $statement->execute();

            if($result == false)
            {
                $logger->log("Swiftriver::AnalyticsProviders::TotalContentByChannelAnalyticsProvider::ProvideAnalytics [An exception was thrown]", \PEAR_LOG_ERR);

                $errorCollection = $statement->errorInfo();

                $logger->log("Swiftriver::AnalyticsProviders::TotalContentByChannelAnalyticsProvider::ProvideAnalytics [" . $errorCollection[2] . "]", \PEAR_LOG_ERR);

                return $request;
            }

            $request->Result = array();

            foreach($statement->fetchAll() as $row)
            {
                $entry = array(
                    "numberofcontentitems" => $row["numberofcontentitems"],
                    "channelId" => $row["channelId"],
                    "channelType" => $row["channelType"],
                    "channelSubType" => $row["channelSubType"]);

                $request->Result[] = $entry;
            }
        }
        catch(\PDOException $e)
        {
            $logger->log("Swiftriver::AnalyticsProviders::TotalContentByChannelAnalyticsProvider::ProvideAnalytics [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Swiftriver::AnalyticsProviders::TotalContentByChannelAnalyticsProvider::ProvideAnalytics [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Swiftriver::AnalyticsProviders::TotalContentByChannelAnalyticsProvider::ProvideAnalytics [Method finished]", \PEAR_LOG_DEBUG);

        return $request;
    }

    /**
     * Function that returns an array containing the
     * fully qualified types of the data content's
     * that the deriving Analytics Provider can work
     * against
     *
     * @return string[]
     */
    public function DataContentSet()
    {
        return array("\Swiftriver\Core\Modules\DataContext\MySql_V2\DataContext");
    }
}
?>
