<?php
namespace Swiftriver\Core\DAL\Repositories;
/**
 * The Repository for the APIKeys system
 * @author mg[at]swiftly[dot]org
 */
class APIKeyRepository
{

    /**
     * The fully qualified type of the IAPIKeyDataContext implemting
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
    public function __construct($dataContext = null) 
    {
        //if the data context is null ten set it to the one in config
        if(!isset($dataContext))
            $dataContext = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;

        $classType = (string) $dataContext;
        $this->dataContext = new $classType();
    }

    /**
     * Checks that the provided Key is registed against this install
     * of the Core
     * Returns true on sucess
     *
     * @param string $key
     * @return bool
     */
    public function IsRegisterdCoreAPIKey($key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::APIKeyRepository::IsRegisterdCoreAPIKey [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;

        $isreg = $dc::IsRegisterdCoreAPIKey($key);

        $logger->log("Core::DAL::Repositories::APIKeyRepository::IsRegisterdCoreAPIKey [Method finished]", \PEAR_LOG_DEBUG);

        return $isreg;
    }

    /**
     * Adds a new API key to the list of registered API Keys
     * Returns true on sucess
     *
     * @param string $key
     * @return bool
     */
    public function AddRegisteredAPIKey($key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::APIKeyRepository::AddRegisteredAPIKey [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;

        $dc::AddRegisteredCoreAPIKey($key);

        $logger->log("Core::DAL::Repositories::APIKeyRepository::AddRegisteredAPIKey [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Removes a registered API key
     * Returns true on sucess
     *
     * @param string $key
     * @return bool
     */
    public function RemoveRegisteredAPIKey($key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::DAL::Repositories::APIKeyRepository::RemoveRegisteredAPIKey [Method invoked]", \PEAR_LOG_DEBUG);

        $dc = $this->dataContext;

        $dc::RemoveRegisteredCoreAPIKey($key);

        $logger->log("Core::DAL::Repositories::APIKeyRepository::RemoveRegisteredAPIKey [Method finished]", \PEAR_LOG_DEBUG);
    }
}
?>
