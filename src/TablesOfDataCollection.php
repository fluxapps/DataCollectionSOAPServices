<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilObject;

/**
 * Class TablesOfDataCollection
 *
 * @package srag\Plugins\DataCollectionSOAPServices
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TablesOfDataCollection extends Base
{

    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array("ref_id" => Base::TYPE_INT);
    }


    /**
     * @inheritDoc
     */
    protected function run(array $params)
    {
        // Possible errors: obj_id doesn't exist
        global $DIC;
        $ilDB = $DIC['ilDB'];

        $ref_id = $params["ref_id"];
        $obj_id = ilObject::_lookupObjectId($ref_id);

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
        return "getTablesOfDataCollection";
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
        return "Returns all table IDs and their respective title";
    }
}