<?php
namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclBaseRecordModel;

interface RecordsOfDataCollectionViewExtendMiddleware {
    public static function new():RecordsOfDataCollectionViewExtendMiddleware;
    public function process(array $record_data, ilDclBaseRecordModel $record);
}