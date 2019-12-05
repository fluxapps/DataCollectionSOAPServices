<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclContentExporter;
use ilDclException;
use ilExport;
use ilObject;
use ilSoapPluginException;

class ExportOfDataCollection extends Base
{

    const AVAILABLE_EXPORT_FORMATS
        = [
            "xlsx",
            "xml",
        ];


    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array(
            "ref_id"        => Base::TYPE_INT,
            "export_format" => Base::TYPE_STRING,
        );
    }


    /**
     * @inheritDoc
     * @throws ilDclException
     */
    protected function run(array $params)
    {
        $requested_format = strtolower($params["export_format"]);

        if (!in_array($requested_format, self::AVAILABLE_EXPORT_FORMATS)) {
            //throw new ilSoapPluginException("Could not Read the XML File");
        }

        $type = "dcl";
        $ref_id = $params["ref_id"];
        $obj_id = ilObject::_lookupObjectId($ref_id);

        if ($requested_format === "xlsx") {
            $exporter = new ilDclContentExporter($ref_id);
            $exporter->exportAsync();
        } else if ($requested_format === "xml") {
            $exp = new ilExport();
            $exp->exportObject($type, $obj_id);
        }

        // Possible errors: obj_id doesn't exist, various export errors


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