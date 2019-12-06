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
            "export_format" => Base::TYPE_STRING
        );
    }


    /**
     * @inheritDoc
     * @throws ilDclException
     * @throws ilSoapPluginException
     */
    protected function run(array $params)
    {
        $requested_format = strtolower($params["export_format"]);

        if (!in_array($requested_format, self::AVAILABLE_EXPORT_FORMATS)) {
            throw new ilSoapPluginException(sprintf("Format '%s' not found, only the following formats are allowed: [%s]",
                $requested_format,
                implode(", ", self::AVAILABLE_EXPORT_FORMATS)));
        }

        $ref_id = $params[self::REF_ID];
        $obj_id = ilObject::_lookupObjectId($ref_id);
        $type = ilObject:: _lookupType($obj_id);

        if (is_null($type)) {
            throw new ilSoapPluginException(sprintf("Object with ref id '%s' not found", $ref_id));
        }

        if ($type !== self::OBJ_TYPE) {
            throw new ilSoapPluginException(sprintf("Object with ref id '%s' has invalid type: '%s' found, '%s' required", $ref_id, $type, self::OBJ_TYPE));
        }

        if ($requested_format === "xlsx") {
            $exporter = new ilDclContentExporter($ref_id);
            $exporter->exportAsync();
        } else if ($requested_format === "xml") {
            $exp = new ilExport();
            $exp->exportObject($type, $obj_id);
        }
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