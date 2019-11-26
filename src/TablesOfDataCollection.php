<?php

namespace srag\Plugins\DataCollectionSOAPServices;

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
     * @return array
     */
    protected function getAdditionalInputParams()
    {
        return array("obj_id" => Base::TYPE_INT);
    }


    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function run(array $params)
    {
        global $DIC;
        $ilDB = $DIC['ilDB'];

        $result = $ilDB->queryF('SELECT * FROM il_dcl_table WHERE obj_id = %s',
            array("integer"),
            array($params["obj_id"]));

        $end_result = [];
        while ($row = $result->fetchAssoc()) {
            array_push($end_result, ["id" => $row["id"], "title" => $row["title"]]);
        }

        return json_encode($end_result);
    }


    /**
     * Get the name of the method. Used as endpoint for SOAP requests.
     * Note that this name must be unique in combination with the service namespace.
     *
     * @return string
     */
    public function getName()
    {
        return "getTablesOfDataCollection";
    }


    /**
     * Get the output parameters in the same format as the input parameters
     *
     * @return array
     */
    public function getOutputParams()
    {
        return array('json' => Base::TYPE_STRING);
    }


    /**
     * Get the documentation of this method
     *
     * @return string
     */
    public function getDocumentation()
    {
        return "TODO";
    }
}