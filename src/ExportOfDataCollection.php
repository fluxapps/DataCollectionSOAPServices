<?php

namespace srag\Plugins\DataCollectionSOAPServices;

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
        $a_entity = "dcl";
        $a_class = ilImportExportFactory::getExporterClass($a_entity);
        $a_comp = ilImportExportFactory::getComponentForExport($a_entity);
        $a_target_release = "";
        $a_id = $params["obj_id"];

        $exp = new ilExportCopy();
        return $exp->processExporter($a_comp, $a_class, $a_entity, $a_target_release, $a_id);
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