<?php
namespace Swiftriver\Core\DAL\Repositories;
/**
 * The Repository for the Sources
 * @author mg[at]swiftly[dot]org
 */
class SourceRepository
{
    /**
     * The fully qualified type of the ISourceDataContext implemting
     * data context for this repository
     * @var \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext
     */
    private $dataContext;

    /**
     * The constructor for this repository
     * Accepts the fully qulaified type of the IAPIKeyDataContext implemting
     * data context for this repository
     *
     * @param string $dataContext
     */
    public function __construct($dataContext = null) {
        if(!isset($dataContext))
            $dataContext = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;
        $classType = (string) $dataContext;
        $this->dataContext = new $classType();
    }

    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public function GetSourcesById($ids) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::GetSourcesById [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $sources = $dc::GetSourcesById($ids);
        $logger->log("Core::DAL::Repositories::SourceRepository::GetSourcesById [Method Finished]", \PEAR_LOG_DEBUG);
        return $sources;
    }

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public function ListAllSources(){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::DAL::Repositories::SourceRepository::ListAllSources [Method invoked]", \PEAR_LOG_DEBUG);
        $dc = $this->dataContext;
        $sources = $dc::ListAllSources();
        $logger->log("Core::DAL::Repositories::SourceRepository::ListAllSources [Method Finished]", \PEAR_LOG_DEBUG);
        return $sources;
    }
}
?>
