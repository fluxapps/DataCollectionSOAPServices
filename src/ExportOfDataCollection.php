<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilExport;
use ilXmlWriter;

class ExportOfDataCollection extends Base
{

    /**
     * @return array
     */
    protected function getAdditionalInputParams()
    {
        return array();
    }


    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function run(array $params)
    {
        $export = new ilExport();
        $result = $export->exportObject("dcl", 280);


        // write export.xml file
        $export_writer = new ilXmlWriter();
        $export_writer->xmlHeader();

        $sv = $exp->determineSchemaVersion($a_entity, $a_target_release);
        $this->log->debug("schema version for entity: $a_entity, target release: $a_target_release");
        $this->log->debug("...is: ".$sv["schema_version"].", namespace: ".$sv["namespace"].
            ", xsd file: ".$sv["xsd_file"].", uses_dataset: ".((int)$sv["uses_dataset"]));

        $attribs = array("InstallationId" => IL_INST_ID,
                         "InstallationUrl" => ILIAS_HTTP_PATH,
                         "Entity" => $a_entity, "SchemaVersion" => $sv["schema_version"], "TargetRelease" => $a_target_release,
                         "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
                         "xmlns:exp" => "http://www.ilias.de/Services/Export/exp/4_1",
                         "xsi:schemaLocation" => "http://www.ilias.de/Services/Export/exp/4_1 ".ILIAS_HTTP_PATH."/xml/ilias_export_4_1.xsd"
        );
        if ($sv["namespace"] != "" && $sv["xsd_file"] != "")
        {
            $attribs["xsi:schemaLocation"].= " ".$sv["namespace"]." ".
                ILIAS_HTTP_PATH."/xml/".$sv["xsd_file"];
            $attribs["xmlns"] = $sv["namespace"];
        }
        if ($sv["uses_dataset"])
        {
            $attribs["xsi:schemaLocation"].= " ".
                "http://www.ilias.de/Services/DataSet/ds/4_3 ".ILIAS_HTTP_PATH."/xml/ilias_ds_4_3.xsd";
            $attribs["xmlns:ds"] = "http://www.ilias.de/Services/DataSet/ds/4_3";
        }


        $export_writer->xmlStartTag('exp:Export', $attribs);

        $dir_cnt = 1;
        foreach ($a_id as $id)
        {
            $exp->setExportDirectories($set_dir_relative."/expDir_".$dir_cnt,
                $set_dir_absolute."/expDir_".$dir_cnt);
            $export_writer->xmlStartTag('exp:ExportItem', array("Id" => $id));
            //$xml = $exp->getXmlRepresentation($a_entity, $a_target_release, $id);
            $xml = $exp->getXmlRepresentation($a_entity, $sv["schema_version"], $id);
            $export_writer->appendXml($xml);
            $export_writer->xmlEndTag('exp:ExportItem');
            $dir_cnt++;
        }

        $export_writer->xmlEndTag('exp:Export');
        $export_writer->xmlDumpFile($set_dir_absolute."/export.xml", false);
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