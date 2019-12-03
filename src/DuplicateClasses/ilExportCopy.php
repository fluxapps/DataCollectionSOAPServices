<?php
namespace srag\Plugins\DataCollectionSOAPServices\DuplicateClasses;
use ilExport;
use ilExportException;
use ilLogger;
use ilLoggerFactory;
use ilUtil;
use ilXmlWriter;

/**
 * Class ilExportCopy
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilExportCopy extends ilExport
{

    /**
     * Process exporter
     *
     * @param string $a_comp e.g. "Modules/Forum"
     * @param string $a_class
     * @param string $a_entity e.g. "frm"
     * @param string $a_target_release e.g. "5.1.0"
     * @param string $a_id id of entity (e.g. object id)
     * @return bool success true/false
     * @throws ilExportException
     */
    function processExporter($a_comp, $a_class, $a_entity, $a_target_release, $a_id)
    {
        $success = true;

        $this->log->debug("process exporter, comp: ".$a_comp.", class: ".$a_class.", entity: ".$a_entity.
            ", target release ".$a_target_release.", id: ".$a_id);

        if (!is_array($a_id))
        {
            if ($a_id == "")
            {
                return;
            }
            $a_id = array($a_id);
        }

        // get exporter object
        if(!class_exists($a_class))
        {
            $export_class_file = "./".$a_comp."/classes/class.".$a_class.".php";
            if (!is_file($export_class_file))
            {
                include_once("./Services/Export/exceptions/class.ilExportException.php");
                throw new ilExportException('Export class file "'.$export_class_file.'" not found.');
            }
            include_once($export_class_file);
        }

        $exp = new $a_class();
        $exp->setExport($this);
        if (!isset($this->cnt[$a_comp]))
        {
            $this->cnt[$a_comp] = 1;
        }
        else
        {
            $this->cnt[$a_comp]++;
        }
        $set_dir_relative = $a_comp."/set_".$this->cnt[$a_comp];
        $set_dir_absolute = $this->export_run_dir."/".$set_dir_relative;
        ilUtil::makeDirParents($set_dir_absolute);
        $this->log->debug("dir: ".$set_dir_absolute);

        $this->log->debug("init exporter");
        $exp->init();

        // process head dependencies
        $this->log->debug("process head dependencies for ".$a_entity);
        $sequence = $exp->getXmlExportHeadDependencies($a_entity, $a_target_release, $a_id);
        foreach ($sequence as $s)
        {
            $comp = explode("/", $s["component"]);
            $exp_class = "il".$comp[1]."Exporter";
            $s = $this->processExporter($s["component"], $exp_class,
                $s["entity"], $a_target_release, $s["ids"]);
            if (!$s)
            {
                $success = false;
            }
        }

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
        return $export_writer->xmlStr;
    }
}
?>
