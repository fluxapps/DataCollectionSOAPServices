<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilAbstractSoapMethod;
use ilDataCollectionSOAPServicesPlugin;
use ilObject;
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
    const ERR_OBJ_NOT_FOUND = "Object with ref id '%s' not found";
    const ERR_OBJ_INVALID_TYPE = "Object with ref id '%s' has invalid type: '%s' found, '%s' required";
    const INPUT_PARAMS
        = [
            self::SID    => self::TYPE_STRING,
            self::REF_ID => self::TYPE_INT,
        ];


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
            self::INPUT_PARAMS,
            $this->getAdditionalInputParams()
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

        // Check target ref_id
        $ref_id = $params[1];
        $obj_id = ilObject::_lookupObjectId($ref_id);
        $type = ilObject:: _lookupType($obj_id);

        if (is_null($type)) {
            throw new ilSoapPluginException(sprintf(self::ERR_OBJ_NOT_FOUND, $ref_id));
        }

        if ($type !== self::OBJ_TYPE) {
            throw new ilSoapPluginException(sprintf(self::ERR_OBJ_INVALID_TYPE, $ref_id, $type, self::OBJ_TYPE));
        }

        // Check Permissions
        global $DIC;
        if (!$DIC->access()->checkAccessOfUser($DIC->user()->getId(), 'write', '', $params[1])) {
            $this->error('Permission denied');
        }

        $clean_params = array();
        $i = 0;

        foreach (self::INPUT_PARAMS as $key => $type) {
            $clean_params[$key] = $params[$i];
            $i++;
        }

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
