<?php
namespace Swiftriver\Core\DAL\DataContextInterfaces;
interface ITrustLogDataContext {
    /**
     * This method redords the fact that a marker (sweeper) has changed the score
     * of a source by marking a content items as either 'acurate', 'chatter' or
     * 'inacurate'
     *
     * @param string $sourceId
     * @param string $markerId
     * @param string|null $reason
     * @param int $change
     */
    public static function RecordSourceScoreChange($sourceId, $markerId, $change, $reason = null);
}
?>
