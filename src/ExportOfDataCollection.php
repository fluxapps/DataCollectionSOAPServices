<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilExport;

class ExportOfDataCollection extends Base
{

    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array(
            "obj_id" => Base::TYPE_INT,
        );
    }


    /**
     * @inheritDoc
     */
    protected function run(array $params)
    {
        $type = "dcl";
        $id = $params["obj_id"];

        $exp = new ilExport();
        $exp->exportObject($type, $id);
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