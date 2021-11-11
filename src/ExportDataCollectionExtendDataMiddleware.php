<?php
namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclBaseRecordModel;
use ilDclTable;
use ilExcel;

interface ExportDataCollectionExtendDataMiddleware {

    public function process(ilDclTable $table, ilExcel $worksheet, ilDclBaseRecordModel $record, &$row, &$col, $field_id);


}