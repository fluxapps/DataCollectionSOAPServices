<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class ilDataCollectionSOAPServicesPlugin
 */
class ilDataCollectionSOAPServicesPlugin extends ilSoapHookPlugin
{

    const PLUGIN_NAME = 'DataCollectionSOAPServices';


    /**
     * @return string
     */
    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritdoc
     */
    public function getSoapMethods()
    {
        return array(
            new \srag\Plugins\DataCollectionSOAPServices\PositionIds(),
            new \srag\Plugins\DataCollectionSOAPServices\PositionTitle(),
            new \srag\Plugins\DataCollectionSOAPServices\SuperiorPositionId(),
            new \srag\Plugins\DataCollectionSOAPServices\EmployeePositionId(),
            new \srag\Plugins\DataCollectionSOAPServices\UserIdsOfPosition(),
            new \srag\Plugins\DataCollectionSOAPServices\UserIdsOfPositionAndOrgUnit(),
            new \srag\Plugins\DataCollectionSOAPServices\AddUserIdToPositionInOrgUnit(),
            new \srag\Plugins\DataCollectionSOAPServices\RemoveUserIdFromPositionInOrgUnit(),
            new \srag\Plugins\DataCollectionSOAPServices\OrgUnitTree(),
            new \srag\Plugins\DataCollectionSOAPServices\ImportOrgUnitTree(),
        );
    }


    /**
     * @inheritdoc
     */
    public function getWsdlTypes()
    {
        return array();
    }
}