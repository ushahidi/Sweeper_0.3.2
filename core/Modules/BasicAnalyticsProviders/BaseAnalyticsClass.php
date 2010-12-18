<?php
namespace Swiftriver\AnalyticsProviders;
class BaseAnalyticsClass
{
    /**
     * This function returns an initilised \PDO object that
     * can be used by the calling class to access the data
     * store. The implementation of generating the \PDO
     * object is switched on the DataContentType property
     * of the $request parameter
     * 
     * @param \Swiftriver\Core\Analytics\AnalyticsRequest $request
     * @return \PDO
     */
    public function PDOConnection($request)
    {
        switch ($request->DataContextType)
        {
            case "\Swiftriver\Core\Modules\DataContext\MySql_V2\DataContext":
                return \Swiftriver\Core\Modules\DataContext\MySql_V2\DataContext::PDOConnection();
            default :
                return null;
        }
    }
}
?>
