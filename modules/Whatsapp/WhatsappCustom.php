<?php

class WhatsappCustom
{
    public function postInstall()
    {
        $this->createFields();
        $this->createTables();
        $this->settingsLink();
    }

    public function postDisable()
    {
        $this->removeSettingsLink();
    }

    public function postEnable()
    {
        $this->settingsLink();
    }

    public function postUpdate()
    {
        $this->createFields();
        $this->createTables();
        $this->settingsLink();
    }

    public function createFields()
    {
        $moduleName = 'Whatsapp';
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        if (!$moduleInstance) {
            echo "Module instance not found for $moduleName<br>";
            return;
        }


        // Defensive check: Ensure basetable is set
        if (empty($moduleInstance->basetable)) {
            $moduleInstance->basetable = 'vtiger_whatsapp';
        }
        echo "Creating fields for module: $moduleName on table: {$moduleInstance->basetable}<br>";

        $blocklabel = 'LBL_WHATSAPP_INFORMATION';
        $blockInstance = Vtiger_Block::getInstance($blocklabel, $moduleInstance);
        if (!$blockInstance) {
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blocklabel;
            $moduleInstance->addBlock($blockInstance);
        }

        $fields = array(
            'whatsapp_channel_id' => array('label' => 'WhatsApp Channel ID', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(100)'),
            'direction' => array('label' => 'Direction', 'uitype' => 16, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)', 'picklistvalues' => array('Incoming', 'Outgoing')),
            'type' => array('label' => 'Type', 'uitype' => 16, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)', 'picklistvalues' => array('Text', 'Image', 'Video', 'Template', 'Interactive', 'Media')),
            'message_id' => array('label' => 'Message ID', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(255)'),
            'message' => array('label' => 'Message', 'uitype' => 21, 'typeofdata' => 'V~O', 'columntype' => 'TEXT'),
            'crm_module' => array('label' => 'CRM Module', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)'),
            'crm_field' => array('label' => 'CRM Field', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)'),
            'crm_field_value' => array('label' => 'CRM Field Value', 'uitype' => 11, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)'),
            'related_module' => array('label' => 'Related Module', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)'),
            'related_id' => array('label' => 'Related ID', 'uitype' => 10, 'typeofdata' => 'I~O', 'columntype' => 'INT(19)', 'relatedmodules' => array('Contacts', 'Leads')),
            'conversation_key' => array('label' => 'Conversation Key', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(100)'),
            'media_id' => array('label' => 'Media ID', 'uitype' => 1, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(255)'),
            'whatsapp_status' => array('label' => 'Whatsapp Status', 'uitype' => 16, 'typeofdata' => 'V~O', 'columntype' => 'VARCHAR(50)', 'picklistvalues' => array('Sent', 'Delivered', 'Read', 'Failed', 'Received')),
            'whatsapp_info' => array('label' => 'Whatsapp Info', 'uitype' => 21, 'typeofdata' => 'V~O', 'columntype' => 'TEXT')
        );

        foreach ($fields as $fieldName => $fieldInfo) {
            try {
                $fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
                if (!$fieldInstance) {
                    $fieldInstance = new Vtiger_Field();
                    $fieldInstance->name = $fieldName;
                    $fieldInstance->label = $fieldInfo['label'];
                    $fieldInstance->table = $moduleInstance->basetable;
                    $fieldInstance->column = $fieldName;
                    $fieldInstance->columntype = $fieldInfo['columntype'];
                    $fieldInstance->uitype = $fieldInfo['uitype'];
                    $fieldInstance->typeofdata = $fieldInfo['typeofdata'];
                    $blockInstance->addField($fieldInstance);

                    if (isset($fieldInfo['picklistvalues'])) {
                        $fieldInstance->setPicklistValues($fieldInfo['picklistvalues']);
                    }
                    if (isset($fieldInfo['relatedmodules'])) {
                        $fieldInstance->setRelatedModules($fieldInfo['relatedmodules']);
                    }
                    echo "Field $fieldName created successfully.<br>";
                }
            } catch (Exception $e) {
                echo "Error creating field $fieldName: " . $e->getMessage() . "<br>";
            }
        }
    }

    public function createTables()
    {
        global $adb;
        $tables = array(
            'vtiger_whatsapp_channels' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_channels (
                id INT(19) AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                description TEXT,
                app_id VARCHAR(255),
                app_secret VARCHAR(255),
                phone_number_id VARCHAR(255),
                business_id VARCHAR(255),
                access_token TEXT,
                is_active TINYINT(1) DEFAULT 1,
                created_by INT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            'vtiger_whatsapp_templates' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_templates (
                id INT(19) AUTO_INCREMENT PRIMARY KEY,
                business_id VARCHAR(100),
                template_id VARCHAR(100),
                whatsapp_channel_id INT(19),
                module VARCHAR(50),
                template_name VARCHAR(255),
                language VARCHAR(50),
                format VARCHAR(50),
                status VARCHAR(50),
                components LONGTEXT,
                category VARCHAR(100),
                created_by INT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            'vtiger_whatsapp_media' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_media (
                id VARCHAR(100) PRIMARY KEY,
                whatsapp_channel_id VARCHAR(100),
                media_id VARCHAR(255),
                mime_type VARCHAR(100),
                file_name VARCHAR(255),
                local_path VARCHAR(255),
                created_by INT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            'vtiger_whatsapp_channel_template_rel' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_channel_template_rel (
                id VARCHAR(100) PRIMARY KEY,
                whatsapp_channel_id VARCHAR(100),
                whatsapp_template_id VARCHAR(100)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            'vtiger_whatsapp_template_map' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_template_map (
                id INT AUTO_INCREMENT PRIMARY KEY,
                template_id INT,
                template_language VARCHAR(50),
                template_variable VARCHAR(100),
                component_type VARCHAR(50),
                button_index INT,
                crm_module VARCHAR(50),
                crm_field VARCHAR(50)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            'vtiger_whatsapp_interactives' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_interactives (
                id VARCHAR(100) PRIMARY KEY,
                whatsapp_channel_id VARCHAR(100),
                name VARCHAR(255),
                type VARCHAR(50),
                body TEXT,
                crm_module VARCHAR(50),
                trigger_event VARCHAR(255),
                is_active TINYINT(1) DEFAULT 1,
                created_by INT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            'vtiger_whatsapp_interactive_items' => "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_interactive_items (
                id VARCHAR(100) PRIMARY KEY,
                interactive_id VARCHAR(100),
                item_type VARCHAR(50),
                item_key VARCHAR(100),
                title VARCHAR(255),
                description TEXT,
                section VARCHAR(255),
                sort_order INT DEFAULT 0,
                next_action_type VARCHAR(50),
                next_action_value VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );

        foreach ($tables as $tableName => $sql) {
            $adb->pquery($sql, array());
        }
    }

    public function settingsLink()
    {
        global $adb;
        //$adb->setDebug(true);
        $name = "WhatsApp Channels Setup";
        $description = "Configure WhatsApp Channels and Meta API credentials";
        $linkto = "index.php?module=Whatsapp&parent=Settings&view=Settings";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?", array($name));
        if ($adb->num_rows($result) == 0) {
            $otherSettingsBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_OTHER_SETTINGS'));
            if ($adb->num_rows($otherSettingsBlock) > 0) {
                $blockid = $adb->query_result($otherSettingsBlock, 0, 'blockid');
                $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_field WHERE blockid=?", array($blockid));
                $sequence = 0;
                if ($adb->num_rows($sequenceResult)) {
                    $sequence = $adb->query_result($sequenceResult, 0, 'sequence') + 1;
                }
                $fieldid = $adb->getUniqueID('vtiger_settings_field');
                $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active , pinned) VALUES(?,?,?,?,?,?,?,?,?)", array($fieldid, $blockid, $name, '', $description, $linkto, $sequence, 0, 1));
            }
        }
    }

    public function removeSettingsLink()
    {
        global $adb;
        $name = "WhatsApp Channels Setup";
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE name = ?", array($name));
    }
}
?>