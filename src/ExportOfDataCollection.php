<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilExport;
use ilObject;

class ExportOfDataCollection extends Base
{

    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array(
            "ref_id" => Base::TYPE_INT,
        );
    }


    /**
     * @inheritDoc
     */
    protected function run(array $params)
    {
        // Possible errors: obj_id doesn't exist, various export errors
        $type = "dcl";
        $ref_id = $params["ref_id"];
        $obj_id = ilObject::_lookupObjectId($ref_id);

        $exp = new ilExport();
        $exp->exportObject($type, $obj_id);
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "getExportOfDataCollection";
    }


    /**
     * @inheritDoc
     */
    public function getOutputParams()
    {
        return [];
    }


    /**
     * @inheritDoc
     */
    public function getDocumentation()
    {
        return "Creates a downloadable export file of a specific data collection";
    }
}