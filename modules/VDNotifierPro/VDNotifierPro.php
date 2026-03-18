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
	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
    
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
		$moduleName = 'VDNotifierPro';
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
 		if($eventType == 'module.postinstall') {
                   
                    $fieldid = $adb->getUniqueID('vtiger_settings_field');
                    $blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
                    $seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid=$blockid");
                    $seq = 1;
                    if ($adb->num_rows($seq_res) > 0)
						{
						$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
				
						if ($cur_seq != null)
						{
							$seq = $cur_seq + 1;
						}
					}
					
               
				
                    $adb->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, description, linkto, sequence,active) VALUES (?,?,?,?,?,?,?)',
				array
				($fieldid,
				$blockid,
				vtranslate($moduleName, $moduleName),
				'LBL_'.strtoupper($moduleName).'_DESCRIPTION',
				'index.php?module='.$moduleName.'&view=Index&parent=Settings',
				$seq,
				0
				)
			);
			
                $adb->pquery("INSERT INTO vtiger_vdnotifierpro_seq (id) value ('1')", array());
                $EventManager = new VTEventsManager($adb);
                $createEvent = 'vtiger.entity.aftersave';
                $deleteEVent = 'vtiger.entity.beforedelete';
                $restoreEvent = 'vtiger.entity.afterrestore';
                $handler_path = 'modules/VDNotifierPro/VDNotifierProHandler.php';
                $className = 'VDNotifierProHandler';
               
                $EventManager->registerHandler($createEvent, $handler_path, $className);
                $EventManager->registerHandler($deleteEVent, $handler_path, $className);
                $EventManager->registerHandler($restoreEvent, $handler_path, $className);

                $moduleInstance->addLink('HEADERSCRIPT', 'VDNotifierScript', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js');
            $moduleInstance->addLink('HEADERCSS', 'VDNotifierPnotifyCSS', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.default.css');
            $moduleInstance->addLink('HEADERSCRIPT', 'PNotify', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js');
        } else if($eventType == 'module.disabled') {
            $moduleInstance->deleteLink('HEADERSCRIPT', 'VDNotifierScript', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js');
            $moduleInstance->deleteLink('HEADERCSS', 'VDNotifierPnotifyCSS', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.default.css');
            $moduleInstance->deleteLink('HEADERSCRIPT', 'PNotify', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js');
        } else if($eventType == 'module.enabled') {
            $moduleInstance->addLink('HEADERSCRIPT', 'VDNotifierScript', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js');
            $moduleInstance->addLink('HEADERCSS', 'VDNotifierPnotifyCSS', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.default.css');
            $moduleInstance->addLink('HEADERSCRIPT', 'PNotify', 'layouts/v7/modules/Settings/VDNotifierPro/resources/pnotify/jquery.pnotify.js');
        }
		else if($eventType == 'module.preuninstall') {
					require_once('vtlib/Vtiger/Link.php');

					$tabid = getTabId($moduleName);
					Vtiger_Link::deleteAll($tabid);
					$EventManager = new VTEventsManager($adb);
                    $className = 'VDNotifierProHandler';
                    $EventManager->unregisterHandler($className);
					$adb->pquery('DELETE FROM vtiger_settings_field WHERE name = ?', array($moduleName));
					$adb->pquery('DROP TABLE vtiger_vdnotifierpro', array());
                    $adb->pquery('DROP TABLE vtiger_vdnotifierpro_seq', array());
                } else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
 	}
        
        
    }
