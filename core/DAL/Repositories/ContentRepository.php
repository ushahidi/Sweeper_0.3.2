<?php
namespace Swiftriver\Core\DAL\Repositories;
/**
 * The Repository for the Content
 * @author mg[at]swiftly[dot]org
 */
class ContentRepository {
    /**
     * The fully qualified type of the IContentDataContext implemting
     * data context for this repository
     *
     * @var \Swiftriver\Core\DAL\DataContextInterfaces\IDataContext
     */
    private $dataContext;

    /**
     * The constructor for this repository
     * Accepts the fully qulaified type of the IContentDataContext implemting
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
     * Given a set of content items, this method will persist
     * them to the data store, if they already exists then this
     * method should update the values in the data store.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public function SaveContent($content)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ContentRepository::SaveContent [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = new $this->dataContext();

        $dc::SaveContent($content);

        $logger->log("Core::DAL::Repositories::ContentRepository::SaveContent [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given an array of content is's, this function will
     * fetch the content objects from the data store.
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public function GetContent($ids)
    {
        try
        {
            $logger = \Swiftriver\Core\Setup::GetLogger();

            $logger->log("Core::DAL::Repositories::ContentRepository::GetContent [Method invoked]", \PEAR_LOG_DEBUG);

            $dc = new $this->dataContext();

            $content = $dc::GetContent($ids);

            $logger->log("Core::DAL::Repositories::ContentRepository::GetContent [Method finished]", \PEAR_LOG_DEBUG);

            return $content;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }

    /**
     * Given an array of content items, this method removes them
     * from the data store.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public function DeleteContent($content)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::ContentRepository::DeleteContent [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = new $this->dataContext();

        $dc::DeleteContent($content);

        $logger->log("Core::DAL::Repositories::ContentRepository::DeleteContent [Method finshed]", \PEAR_LOG_DEBUG);
    }

    /**
     * Returns an array of Swiftriver\ObjectModel\Content items based on the
     * supplied parameters.
     *
     * @param string[] $parameters
     */
    public function GetContentList($parameters)
    {
        try
        {
            $logger = \Swiftriver\Core\Setup::GetLogger();

            $logger->log("Core::DAL::Repositories::ContentRepository::GetContentList [Method invoked]", \PEAR_LOG_DEBUG);

            $dc = new $this->dataContext();

            $content = $dc::GetContentList($parameters);

            $logger->log("Core::DAL::Repositories::ContentRepository::GetContentList [Method finished]", \PEAR_LOG_DEBUG);

            return $content;
        }
        catch (\Exception $e)
        {
            return array();
        }
    }
}
?>
