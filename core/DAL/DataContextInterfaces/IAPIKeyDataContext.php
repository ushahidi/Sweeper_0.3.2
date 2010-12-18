<?php
namespace Swiftriver\Core\DAL\DataContextInterfaces;
interface IAPIKeyDataContext {
    /**
     * Checks that the given API Key is registed for this
     * Core install
     * @param string $key
     * @return bool
     */
    public static function IsRegisterdCoreAPIKey($key);

    /**
     * Given a new APIKey, this method adds it to the
     * data store or registered API keys.
     * Returns true on sucess
     * 
     * @param string $key
     * @return bool
     */
    public static function AddRegisteredCoreAPIKey($key);

    /**
     * Given an APIKey, this method will remove it from the
     * data store of registered API Keys
     * Returns true on sucess
     *
     * @param string key
     * @return bool
     */
    public static function RemoveRegisteredCoreAPIKey($key);
}
?>
