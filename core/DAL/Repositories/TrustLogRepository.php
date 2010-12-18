<?php
namespace Swiftriver\Core\DAL\Repositories;
/**
 * The Repository for the Trustlog system
 * @author mg[at]swiftly[dot]org
 */
class TrustLogRepository
{
    /**
     * The fully qualified type of the ITrustLogDataContext implemting
     * data context for this repository
     *
     * @var \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext
     */
    private $dataContext;

    /**
     * The constructor for this repository
     * Accepts the fully qulaified type of the ITrustLogDataContext implemting
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
     * This method redords the fact that a marker (sweeper) has changed the score
     * of a source by marking a content items as either 'acurate', 'chatter' or
     * 'inacurate'
     *
     * @param string $sourceId
     * @param string $markerId
     * @param int $change
     */
    public function RecordSourceScoreChange($sourceId, $markerId, $change, $reason = null)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::TrustLogRepository::RecordSourceScoreChange [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = new $this->dataContext();

        $dc::RecordSourceScoreChange($sourceId, $markerId, $change, $reason);

        $logger->log("Core::DAL::Repositories::TrustLogRepository::RecordSourceScoreChange [Method Finished]", \PEAR_LOG_DEBUG);
    }
}
?>
