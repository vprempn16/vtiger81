<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Whatsapp_Record_Model extends Settings_Vtiger_Record_Model
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule($moduleModel)
    {
        $this->module = $moduleModel;
        return $this;
    }

    public function getEditViewUrl()
    {
        return 'index.php?module=' . $this->getModule()->getName() . '&parent=' . $this->getModule()->getParentName() . '&view=Edit&record=' . $this->getId();
    }

    public function getDeleteUrl()
    {
        return 'index.php?module=' . $this->getModule()->getName() . '&parent=' . $this->getModule()->getParentName() . '&action=ActionAjax&mode=delete&record=' . $this->getId();
    }

    public function getViewTemplatesUrl()
    {
        return 'index.php?module=' . $this->getModule()->getName() . '&parent=' . $this->getModule()->getParentName() . '&view=Templates&channel_id=' . $this->getId();
    }

    public function getSyncTemplatesUrl()
    {
        return 'index.php?module=' . $this->getModule()->getName() . '&parent=' . $this->getModule()->getParentName() . '&action=ActionAjax&mode=syncTemplates&record=' . $this->getId();
    }

    public function getRecordLinks()
    {
        $links = array();
        $recordLinks = array(
            array(
                'linktype' => 'LISTVIEWRECORD',
                'linklabel' => 'LBL_VIEW_TEMPLATES',
                'linkurl' => $this->getViewTemplatesUrl(),
                'linkicon' => 'fa fa-eye'
            ),
            array(
                'linktype' => 'LISTVIEWRECORD',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => $this->getEditViewUrl(),
                'linkicon' => 'fa fa-pencil'
            ),
            array(
                'linktype' => 'LISTVIEWRECORD',
                'linklabel' => 'LBL_DELETE',
                'linkurl' => "javascript:Settings_Whatsapp_List_Js.triggerDelete(event, '" . $this->getDeleteUrl() . "');",
                'linkicon' => 'fa fa-trash'
            )
        );
        foreach ($recordLinks as $recordLink) {
            $linkModel = new Vtiger_Base_Model($recordLink);
            $links[] = $linkModel;
        }

        return $links;
    }

    public function save()
    {
        $db = PearDatabase::getInstance();
        $id = $this->getId();
        $params = array(
            $this->get('name'),
            $this->get('description'),
            $this->get('app_id'),
            $this->get('app_secret'),
            $this->get('phone_number_id'),
            $this->get('business_id'),
            $this->get('access_token'),
            $this->get('is_active') ? 1 : 0
        );

        if ($id) {
            $query = 'UPDATE vtiger_whatsapp_channels SET name=?, description=?, app_id=?, app_secret=?, phone_number_id=?, business_id=?, access_token=?, is_active=? WHERE id=?';
            $params[] = $id;
        } else {
            $query = 'INSERT INTO vtiger_whatsapp_channels(name, description, app_id, app_secret, phone_number_id, business_id, access_token, is_active) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
        }
        $db->pquery($query, $params);
        if (!$id) {
            $id = $db->getLastInsertID('vtiger_whatsapp_channels');
        }
        return $id;
    }

    static public function getInstanceById($recordId, $qualifiedModuleName)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_whatsapp_channels WHERE id = ?', array($recordId));

        if ($db->num_rows($result)) {
            $moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
            $rowData = $db->query_result_rowdata($result, 0);

            $recordModel = new self();
            $recordModel->setData($rowData)->setModule($moduleModel);

            return $recordModel;
        }
        return false;
    }

    static public function getCleanInstance($qualifiedModuleName)
    {
        $recordModel = new self();
        $moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
        return $recordModel->setModule($moduleModel);
    }

    static public function getAll($qualifiedModuleName)
    {
        $db = PearDatabase::getInstance();
        $moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
        $result = $db->pquery('SELECT * FROM vtiger_whatsapp_channels', array());
        $records = array();
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $rowData = $db->query_result_rowdata($result, $i);
            $recordModel = new self();
            $recordModel->setData($rowData)->setModule($moduleModel);
            $records[$rowData['id']] = $recordModel;
        }
        return $records;
    }

    static public function getAllChannels()
    {
        return self::getAll('Settings:Whatsapp');
    }
}
