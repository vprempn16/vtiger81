<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Whatsapp_Module_Model extends Settings_Vtiger_Module_Model
{

    var $baseTable = 'vtiger_whatsapp_channels';
    var $baseIndex = 'id';
    var $listFields = array('name' => 'Name', 'phone_number_id' => 'Phone Number ID', 'is_active' => 'Active');
    var $name = 'Whatsapp';

    public function getCreateRecordUrl()
    {
        return 'index.php?module=' . $this->getName() . '&parent=' . $this->getParentName() . '&view=Edit';
    }

    public function getListUrl()
    {
        return 'index.php?module=' . $this->getName() . '&parent=' . $this->getParentName() . '&view=Settings';
    }

    public function getTemplatesUrl()
    {
        return 'index.php?module=' . $this->getName() . '&parent=' . $this->getParentName() . '&view=Templates';
    }

    public function getDefaultViewName()
    {
        return 'Settings';
    }
}
