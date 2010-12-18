<?php
namespace Swiftriver\Core\DAL\Repositories;
/**
 * The Repository for the Channels
 * @author mg[at]swiftly[dot]org
 */
class ChannelRepository
{
    /**
     * The fully qualified type of the IChannelDataContext implemting
     * data context for this repository
     * @var \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext
     */
    private $dataContext;

    /**
     * The constructor for this repository
     * Accepts the fully qulaified type of the IDataContext implemting
     * data context for this repository
     *
     * @param string $dataContext
     */
    public function __construct($dataContext = null)
    {
        if(!isset($dataContext))
            $dataContext = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;

        $classType = (string) $dataContext;

        $this->dataContext = new $classType();
    }

    /**
     * Given the IDs of Channels, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public function GetChannelsById($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ChannelRepository::GetChannelsById [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;
        
        $channels = $dc::GetChannelsById($ids);

        $logger->log("Core::DAL::Repositories::ChannelRepository::GetChannelsById [Method Finished]", \PEAR_LOG_DEBUG);
        
        return $channels;
    }

    /**
     * Adds a list of new Channels to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Channel[] $channels
     */
    public function SaveChannels($channels)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ChannelRepository::SaveChannels [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;
        
        $dc::SaveChannels($channels);

        $logger->log("Core::DAL::Repositories::ChannelRepository::SaveChannels [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a list of IDs this method removes the Channels from
     * the data store.
     *
     * @param string[] $ids
     */
    public function RemoveChannels($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ChannelRepository::RemoveChannels [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;

        $dc::RemoveChannels($ids);

        $logger->log("Core::DAL::Repositories::ChannelRepository::RemoveChannels [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a date time, this function returns the next due
     * Channel.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Channel
     */
    public function SelectNextDueChannel($time)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ChannelRepository::SelectNextDueChannel [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;
        
        $channel = $dc::SelectNextDueChannel($time);

        $logger->log("Core::DAL::Repositories::ChannelRepository::SelectNextDueChannel [Method Finished]", \PEAR_LOG_DEBUG);
        
        return $channel;
    }

    /**
     * Lists all the current Channel in the core
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public function ListAllChannels()
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ChannelRepository::ListAllChannels [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;

        $channels = $dc::ListAllChannels();

        $logger->log("Core::DAL::Repositories::ChannelRepository::ListAllChannels [Method Finished]", \PEAR_LOG_DEBUG);

        return $channels;
    }
}
?>
