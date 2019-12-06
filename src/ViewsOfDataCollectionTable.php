<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclTable;
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

        // Check if table exists
        $result = $ilDB->queryF('SELECT * FROM il_dcl_table WHERE id = %s',
            array("integer"),
            array($params["dcl_table_id"])
        );

        if ($result->rowCount() === 0) {
            throw new ilSoapPluginException(sprintf("Table with id '%s' not found", $params["dcl_table_id"]));
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
        return "getViewsOfDataCollectionTable";
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
        return "Returns all view IDs and their respective title";
    }
}