<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilExport;
use ilImportExportFactory;
use srag\Plugins\DataCollectionSOAPServices\DuplicateClasses\ilExportCopy;

class ExportOfDataCollection extends Base
{

    /**
     * @return array
     */
    protected function getAdditionalInputParams()
    {
        return array(
            "obj_id" => Base::TYPE_INT,
        );
    }


    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function run(array $params)
    {
        $type = "dcl";
        $id = $params["obj_id"];

        $exp = new ilExport();
        $exp->exportObject($type, $id);
    }


    /**
     * Get the name of the method. Used as endpoint for SOAP requests.
     * Note that this name must be unique in combination with the service namespace.
     *
     * @return string
     */
    public function getName()
    {
        return "getExportOfDataCollection";
    }


    /**
     * Get the output parameters in the same format as the input parameters
     *
     * @return array
     */
    public function getOutputParams()
    {
        return [];
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