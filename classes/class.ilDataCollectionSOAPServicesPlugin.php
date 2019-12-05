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
            new \srag\Plugins\DataCollectionSOAPServices\TablesOfDataCollection(),
            new \srag\Plugins\DataCollectionSOAPServices\ViewsOfDataCollectionTable(),
            new \srag\Plugins\DataCollectionSOAPServices\RecordsOfDataCollectionView(),
            new \srag\Plugins\DataCollectionSOAPServices\ExportOfDataCollection()
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