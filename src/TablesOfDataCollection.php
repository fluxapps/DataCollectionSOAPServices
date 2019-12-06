<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilObject;
use ilSoapPluginException;

/**
 * Class TablesOfDataCollection
 *
 * @package srag\Plugins\DataCollectionSOAPServices
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TablesOfDataCollection extends Base
{

    const NAME = "getTablesOfDataCollection";
    const DESCRIPTION = "Returns all table IDs and their respective title";
    const ERR_OBJ_NOT_FOUND = "Object with ref id '%s' not found";
    const ERR_OBJ_INVALID_TYPE = "Object with ref id '%s' has invalid type: '%s' found, '%s' required";


    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array();
    }


    /**
     * @inheritDoc
     * @throws ilSoapPluginException
     */
    protected function run(array $params)
    {
        global $DIC;
        $ilDB = $DIC['ilDB'];

        $ref_id = $params[self::REF_ID];
        $obj_id = ilObject::_lookupObjectId($ref_id);
        $type = ilObject:: _lookupType($obj_id);

        if (is_null($type)) {
            throw new ilSoapPluginException(sprintf(self::ERR_OBJ_NOT_FOUND, $ref_id));
        }

        if ($type !== self::OBJ_TYPE) {
            throw new ilSoapPluginException(sprintf(
                    self::ERR_OBJ_INVALID_TYPE,
                    $ref_id, $type,
                    self::OBJ_TYPE)
            );
        }

        $result = $ilDB->queryF('SELECT * FROM il_dcl_table WHERE obj_id = %s',
            array("integer"),
            array($obj_id));

        $end_result = [];
        while ($row = $result->fetchAssoc()) {
            array_push($end_result, ["id" => $row["id"], "title" => $row["title"]]);
        }

        return json_encode($end_result);
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::NAME;
    }


    /**
     * @inheritDoc
     */
    public function getOutputParams()
    {
        return array('json' => Base::TYPE_STRING);
    }


    /**
     * @inheritDoc
     */
    public function getDocumentation()
    {
        return self::DESCRIPTION;
    }
}