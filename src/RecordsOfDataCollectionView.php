<?php

namespace srag\Plugins\DataCollectionSOAPServices;

use ilDclCache;
use ilDclTableView;
use ilObject;
use ilSoapPluginException;


class RecordsOfDataCollectionView extends Base
{

    const NAME = "getRecordsOfDataCollectionView";
    const DESCRIPTION = "Returns the data collection records of a specific applied view";
    const ERR_VIEW_NOT_FOUND = "View with id '%s' not found";
    const ERR_VIEW_NOT_CONNECTED_TO_REF_ID = "Specified view id '%s' is not linked to ref id '%s'";


    /**
     * @inheritDoc
     */
    protected function getAdditionalInputParams()
    {
        $arr=  array(
            "dcl_view_id" => Base::TYPE_INT,
            "extend_records_middleware_fqdn_class_name" => Base::TYPE_STRING
        );

        return $arr;
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
            throw new ilSoapPluginException(sprintf(self::ERR_VIEW_NOT_FOUND, $params["dcl_view_id"]));
        }

        $this->checkIsViewLinkedToRef($params, $ilDB);

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

            if(class_exists($params["extend_records_middleware_fqdn_class_name"])) {
                $middleware = $params["extend_records_middleware_fqdn_class_name"];
                $record_data = $middleware::new()->process($record_data,$record);
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
        return self::NAME;
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
        return self::DESCRIPTION;
    }


    /**
     * @param array $params
     * @param       $ilDB
     *
     * @throws ilSoapPluginException
     */
    protected function checkIsViewLinkedToRef(array $params, $ilDB)
    {
        $ref_id = $params[self::REF_ID];
        $obj_id = ilObject::_lookupObjectId($ref_id);

        // Check if specified ref_id/obj_id has a connection to the requested view
        $result = $ilDB->queryF('SELECT obj_id FROM il_dcl_table WHERE id = (SELECT table_id FROM il_dcl_tableview WHERE id = %s)',
            array("integer"),
            array($params["dcl_view_id"]));

        $obj_id_record = $result->fetchAssoc()["obj_id"];

        if (is_null($obj_id_record) || $obj_id_record == false || $obj_id_record != $obj_id) {
            throw new ilSoapPluginException(sprintf(self::ERR_VIEW_NOT_CONNECTED_TO_REF_ID, $params["dcl_view_id"], $ref_id));
        }
    }
}