<?php
/* * *******************************************************************************
 * The content of this file is subject to the VD Notifier Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */

include_once('vtlib/Vtiger/Module.php');
require 'include/events/include.inc';

class VDNotifierPro {
    
    private $moduleName = 'VDNotifierPro';
    private $handlerPath = 'modules/VDNotifierPro/VDNotifierProHandler.php';
    private $handlerClass = 'VDNotifierProHandler';
    private $events = array(
        'vtiger.entity.aftersave',
        'vtiger.entity.beforedelete',
        'vtiger.entity.afterrestore'
    );
    private $headerLinks = array(
        'HEADERSCRIPT' => array(
            'VDNotifierScript' => 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js',
            'PNotify' => 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js'
        ),
        'HEADERCSS' => array(
            'VDNotifierPnotifyCSS' => 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.default.css'
        )
    );

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        $this->moduleName = $moduleName;
        
        switch ($eventType) {
            case 'module.postinstall':
                $this->handlePostInstall();
                break;
            case 'module.postupdate':
                $this->handlePostUpdate();
                break;
            case 'module.enabled':
                $this->handleEnabled();
                break;
            case 'module.disabled':
                $this->handleDisabled();
                break;
            case 'module.preuninstall':
                $this->handlePreUninstall();
                break;
            case 'module.preupdate':
                $this->handlePreUpdate();
                break;
        }
    }

    /**
     * Handle module post installation
     */
    private function handlePostInstall() {
        global $adb;
        
        $this->createSettingsField();
        $this->initializeSequenceTable();
        $this->registerEventHandlers();
        $this->addHeaderLinks();
    }

    /**
     * Handle module post update
     */
    private function handlePostUpdate() {
        $this->registerEventHandlers();
        $this->addHeaderLinks();
    }

    /**
     * Handle module enabled
     */
    private function handleEnabled() {
        $this->registerEventHandlers();
        $this->addHeaderLinks();
    }

    /**
     * Handle module disabled
     */
    private function handleDisabled() {
        $this->unregisterEventHandlers();
        $this->removeHeaderLinks();
    }

    /**
     * Handle module pre uninstallation
     */
    private function handlePreUninstall() {
        global $adb;
        
        require_once('vtlib/Vtiger/Link.php');
        
        $tabid = getTabId($this->moduleName);
        Vtiger_Link::deleteAll($tabid);
        
        $this->unregisterEventHandlers();
        $this->removeSettingsField();
        $this->dropTables();
    }

    /**
     * Handle module pre update
     */
    private function handlePreUpdate() {
        // TODO: Handle actions before this module is updated.
    }

    /**
     * Create settings field entry
     */
    private function createSettingsField() {
        global $adb;
        
        $fieldid = $adb->getUniqueID('vtiger_settings_field');
        $blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
        
        // Get next sequence number
        $seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid=$blockid");
        $seq = 1;
        if ($adb->num_rows($seq_res) > 0) {
            $cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
            if ($cur_seq != null) {
                $seq = $cur_seq + 1;
            }
        }
        
        $adb->pquery(
            'INSERT INTO vtiger_settings_field(fieldid, blockid, name, description, linkto, sequence,active) VALUES (?,?,?,?,?,?,?)',
            array(
                $fieldid,
                $blockid,
                vtranslate($this->moduleName, $this->moduleName),
                'LBL_' . strtoupper($this->moduleName) . '_DESCRIPTION',
                'index.php?module=' . $this->moduleName . '&view=Index&parent=Settings',
                $seq,
                0
            )
        );
    }

    /**
     * Initialize sequence table
     */
    private function initializeSequenceTable() {
        global $adb;
        
        $adb->pquery("INSERT INTO vtiger_vdnotifierpro_seq (id) value ('1')", array());
    }

    /**
     * Register event handlers
     */
    private function registerEventHandlers() {
        global $adb;
        
        $EventManager = new VTEventsManager($adb);
        
        foreach ($this->events as $event) {
            $EventManager->registerHandler($event, $this->handlerPath, $this->handlerClass);
        }
    }

    /**
     * Unregister event handlers
     */
    private function unregisterEventHandlers() {
        global $adb;
        
        $EventManager = new VTEventsManager($adb);
        $EventManager->unregisterHandler($this->handlerClass);
    }

    /**
     * Add header links
     */
    private function addHeaderLinks() {
        $moduleInstance = Vtiger_Module::getInstance($this->moduleName);
        
        if ($moduleInstance) {
            foreach ($this->headerLinks as $type => $links) {
                foreach ($links as $linkName => $linkPath) {
                    $moduleInstance->addLink($type, $linkName, $linkPath);
                }
            }
        }
    }

    /**
     * Remove header links
     */
    private function removeHeaderLinks() {
        $moduleInstance = Vtiger_Module::getInstance($this->moduleName);
        
        if ($moduleInstance) {
            foreach ($this->headerLinks as $type => $links) {
                foreach ($links as $linkName => $linkPath) {
                    $moduleInstance->deleteLink($type, $linkName, $linkPath);
                }
            }
        }
    }

    /**
     * Remove settings field
     */
    private function removeSettingsField() {
        global $adb;
        
        $adb->pquery('DELETE FROM vtiger_settings_field WHERE name = ?', array($this->moduleName));
    }

    /**
     * Drop module tables
     */
    private function dropTables() {
        global $adb;
        
        $adb->pquery('DROP TABLE vtiger_vdnotifierpro', array());
        $adb->pquery('DROP TABLE vtiger_vdnotifierpro_seq', array());
    }
}
