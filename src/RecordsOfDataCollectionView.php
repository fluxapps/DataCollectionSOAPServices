<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclCache;
use ilDclTableView;

class RecordsOfDataCollectionView extends Base
{

    protected $filter = [];

    /**
     * @return array
     */
    protected function getAdditionalInputParams()
    {
        return array(
            "obj_id" => Base::TYPE_INT,
            "dcl_view_id" => Base::TYPE_INT
        );
    }


    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function run(array $params)
    {
        // Load records
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
     * Get the name of the method. Used as endpoint for SOAP requests.
     * Note that this name must be unique in combination with the service namespace.
     *
     * @return string
     */
    public function getName()
    {
        return "getRecordsOfDataCollectionView";
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