<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclCache;
use ilDclTableView;

class RecordsOfDataCollectionView extends Base
{

    protected $filter = [];

    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        return array(
            "dcl_view_id" => Base::TYPE_INT
        );
    }


    /**
     * @inheritDoc
     */
    protected function run(array $params)
    {
        // Possible errors: View doesn't exist, obj_id doesn't exist
        global $DIC;
        $ilDB = $DIC['ilDB'];

        $tableview = ilDclTableView::find($params["dcl_view_id"]);

        $records = array();
        $query = "SELECT id FROM il_dcl_record WHERE table_id = (SELECT table_id FROM il_dcl_tableview WHERE id = "
            . $ilDB->quote($params["dcl_view_id"], "integer")
            . ")";
        $set = $ilDB->query($query);

        while ($rec = $ilDB->fetchAssoc($set)) {
            $records[$rec['id']] = ilDclCache::getRecordCache($rec['id']);
        }

        $data = array();

        foreach ($records as $record) {
            $record_data = array();

            foreach ($tableview->getVisibleFields() as $field) {
                $title = $field->getTitle();
                $record_data[$title] = $record->getRecordFieldExportValue($field->getId());
            }

            $data[] = $record_data;
        }

        return json_encode($data);
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "getRecordsOfDataCollectionView";
    }


    /**
     * @inheritDoc
     */
    public function getOutputParams()
    {
        return array('json' => Base::TYPE_STRING);
    }


    /**
     * @inheritDoc
     */
    public function getDocumentation()
    {
        return "Returns the data collection records of a specific applied view";
    }
}