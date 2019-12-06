<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilObject;
use ilSoapPluginException;

/**
 * Class ViewsOfDataCollectionTable
 *
 * @package srag\Plugins\DataCollectionSOAPServices
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ViewsOfDataCollectionTable extends Base
{

    const NAME = "getViewsOfDataCollectionTable";
    const DESCRIPTION = "Returns all view IDs and their respective title";
    const ERR_TABLE_NOT_FOUND = "Table with id '%s' not found";
    const ERR_TABLE_NOT_CONNECTED_TO_REF_ID = "Specified table id '%s' is not linked to ref id '%s'";


    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array("dcl_table_id" => Base::TYPE_INT);
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

        // Check if table exists
        $result = $ilDB->queryF('SELECT * FROM il_dcl_table WHERE id = %s',
            array("integer"),
            array($params["dcl_table_id"])
        );

        if ($result->rowCount() === 0) {
            throw new ilSoapPluginException(sprintf(self::ERR_TABLE_NOT_FOUND, $params["dcl_table_id"]));
        }

        if ($result->fetchAssoc()["obj_id"] != $obj_id) {
            throw new ilSoapPluginException(sprintf(self::ERR_TABLE_NOT_CONNECTED_TO_REF_ID, $params["dcl_table_id"], $ref_id));
        }

        $result = $ilDB->queryF('SELECT * FROM il_dcl_tableview WHERE table_id = %s',
            array("integer"),
            array($params["dcl_table_id"])
        );

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