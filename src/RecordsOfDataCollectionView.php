<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclCache;
use ilDclTableView;
use ilSoapPluginException;

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
     * @throws ilSoapPluginException
     */
    protected function run(array $params)
    {
        global $DIC;
        $ilDB = $DIC['ilDB'];

        $tableview = ilDclTableView::find($params["dcl_view_id"]);

        if (is_null($tableview)) {
            throw new ilSoapPluginException(sprintf("View with id '%s' not found", $params["dcl_view_id"]));
        }

        $records = array();

         $result = $ilDB->queryF('SELECT id FROM il_dcl_record WHERE table_id = (SELECT table_id FROM il_dcl_tableview WHERE id = %s)',
            array("integer"),
            array($params["dcl_view_id"]));

        while ($rec = $result->fetchAssoc()) {
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