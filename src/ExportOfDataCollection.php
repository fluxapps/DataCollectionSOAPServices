<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclContentExporter;
use ilDclException;
use ilExport;
use ilObject;
use ilSoapPluginException;

class ExportOfDataCollection extends Base
{

    const NAME = "createExportOfDataCollection";
    const DESCRIPTION = "Creates a downloadable export file of a specific data collection";
    const ERR_FORMAT_NOT_FOUND = "Format '%s' not found, only the following formats are allowed: [%s]";
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
            "export_format" => Base::TYPE_STRING,
            "extend_data_middleware_fqdn_class_name" => Base::TYPE_STRING
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
        $extend_data_middleware = strtolower($params["extend_data_middleware_fqdn_class_name"]);

        if (!in_array($requested_format, self::AVAILABLE_EXPORT_FORMATS)) {
            throw new ilSoapPluginException(sprintf(
                self::ERR_FORMAT_NOT_FOUND,
                $requested_format,
                implode(", ", self::AVAILABLE_EXPORT_FORMATS)
            ));
        }

        $ref_id = $params[self::REF_ID];
        $obj_id = ilObject::_lookupObjectId($ref_id);
        $type = ilObject:: _lookupType($obj_id);

        if ($requested_format === "xlsx") {
            $exporter = new ilDclContentExporter($ref_id);
            $exporter->exportAsync();
        } else {
            if ($requested_format === "xml") {
                $exp = new ilExport();
                $exp->exportObject($type, $obj_id);
            }
        }
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
        return [];
    }


    /**
     * @inheritDoc
     */
    public function getDocumentation()
    {
        return self::DESCRIPTION;
    }
}