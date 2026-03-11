<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Whatsapp_Template_Model extends Settings_Vtiger_Record_Model
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('template_name');
    }

    static public function getInstanceById($recordId)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_whatsapp_templates WHERE id = ?', array($recordId));

        if ($db->num_rows($result)) {
            $rowData = $db->query_result_rowdata($result, 0);
            $recordModel = new self();
            $recordModel->setData($rowData);
            return $recordModel;
        }
        return false;
    }

    static public function getAllByChannel($channelId)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_whatsapp_templates WHERE whatsapp_channel_id = ?', array($channelId));
        $records = array();
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $rowData = $db->query_result_rowdata($result, $i);
            $recordModel = new self();
            $recordModel->setData($rowData);
            $records[] = $recordModel;
        }
        return $records;
    }

    public function save()
    {
        $db = PearDatabase::getInstance();
        $id = $this->getId();
        $params = array(
            $this->get('business_id'),
            $this->get('template_id'),
            $this->get('whatsapp_channel_id'),
            $this->get('module'),
            $this->get('template_name'),
            $this->get('language'),
            $this->get('format'),
            $this->get('status'),
            $this->get('components'),
            $this->get('category'),
            $this->get('created_by')
        );

        if ($id) {
            $query = 'UPDATE vtiger_whatsapp_templates SET business_id=?, template_id=?, whatsapp_channel_id=?, module=?, template_name=?, language=?, format=?, status=?, components=?, category=?, created_by=? WHERE id=?';
            $params[] = $id;
        } else {
            $query = 'INSERT INTO vtiger_whatsapp_templates(business_id, template_id, whatsapp_channel_id, module, template_name, language, format, status, components, category, created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?)';
        }
        $db->pquery($query, $params);
        if (!$id) {
            $id = $db->getLastInsertID('vtiger_whatsapp_templates');
        }
        return $id;
    }
}
