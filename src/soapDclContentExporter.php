<?php
include_once "./Customizing/global/plugins/Services/Cron/CronHook/LearningObjectiveSuggestions/vendor/autoload.php";

require_once "./Modules/DataCollection/classes/Content/class.ilDclContentExporter.php";

class soapDclContentExporter extends ilDclContentExporter {

    /**
     * @var string
     */
    protected $extend_data_middleware_fqdn_class_name;

    /**
     * soapDclContentExporter constructor.
     *
     * @param        $ref_id
     * @param null   $table_id
     * @param array  $filter
     * @param string $extend_data_middleware_fqdn_class_name
     */
    public function __construct($ref_id, $table_id = null, $filter = array(),string $extend_data_middleware_fqdn_class_name = "")
    {
        parent::__construct($ref_id, $table_id, $filter);

        $this->extend_data_middleware_fqdn_class_name = $extend_data_middleware_fqdn_class_name;
    }

    /**
     * Fill a excel row
     *
     * @param ilDclTable           $table
     * @param ilExcel              $worksheet
     * @param ilDclBaseRecordModel $record
     * @param                      $row
     */
    protected function fillRowExcel(ilDclTable $table, ilExcel $worksheet, ilDclBaseRecordModel $record, $row)
    {
        $col = 0;
        foreach ($table->getFields() as $field) {
            if ($field->getExportable()) {
                $record->fillRecordFieldExcelExport($worksheet, $row, $col, $field->getId());
            }

            if(strlen($this->extend_data_middleware_fqdn_class_name) > 0) {
                $middleware = new $this->extend_data_middleware_fqdn_class_name();
                $middleware->process($record, $worksheet, $record, $row, $col, $field->getId());
            }
        }
    }
}