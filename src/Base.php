<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilAbstractSoapMethod;
use ilDataCollectionSOAPServicesPlugin;
use ilSoapPluginException;

/**
 * Class Base
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class Base extends ilAbstractSoapMethod
{

    /**
     * @inheritdoc
     */
    const TYPE_INT_ARRAY = 'tns:intArray';
    const TYPE_STRING = 'xsd:string';
    const TYPE_INT = 'xsd:int';
    const TYPE_DOUBLE_ARRAY = 'tns:doubleArray';
    const SID = 'sid';
    const REF_ID = 'ref_id';
    const OBJ_TYPE = "dcl";


    /**
     * @inheritdoc
     */
    public function getServiceNamespace()
    {
        return 'urn:' . ilDataCollectionSOAPServicesPlugin::PLUGIN_NAME;
    }


    /**
     * @return array
     */
    protected abstract function getAdditionalInputParams();


    /**
     * @inheritdoc
     */
    final public function getInputParams()
    {
        return array_merge(
            array(
                self::SID => self::TYPE_STRING,
                self::REF_ID => self::TYPE_INT
            ), $this->getAdditionalInputParams()
        );
    }


    /**
     * @param array $params
     *
     * @return mixed
     */
    abstract protected function run(array $params);


    /**
     * @param array $params
     *
     * @return mixed
     * @throws ilSoapPluginException
     */
    public function execute(array $params)
    {
        $this->checkParameters($params);
        $session_id = (isset($params[0])) ? $params[0] : '';
        $this->init($session_id);

        // Check Permissions
        global $DIC;
        if (!$DIC->access()->checkAccessOfUser($DIC->user()->getId(), 'write', '', $params[1])) {
            $this->error('Permission denied');
        }

        $clean_params = array();
        $i = 2;
        foreach ($this->getAdditionalInputParams() as $key => $type) {
            $clean_params[$key] = $params[$i];
            $i++;
        }

        return $this->run($clean_params);
    }


    /**
     * @param $message
     *
     * @throws ilSoapPluginException
     */
    protected function error($message)
    {
        throw new ilSoapPluginException($message);
    }


    /**
     * @param $session_id
     *
     * @throws ilSoapPluginException
     */
    private function init($session_id)
    {
        $this->initIliasAndCheckSession($session_id); // Throws exception if session is not valid
    }
}
