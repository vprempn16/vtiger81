{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{include file="modules/Settings/Vtiger/IndexViewPreProcess.tpl"|@vtemplate_path:$QUALIFIED_MODULE}

<div class="settingsIndexPage">
	<div class="settingsActionsContainer">
		<div class="row-fluid">
            <div class="span12">
                <ul class="nav nav-tabs massEditTabs">
                    <li {if $CURRENT_TAB eq 'Channels'}class="active"{/if}>
                        <a href="index.php?module=Whatsapp&parent=Settings&view=Settings"><strong>{vtranslate('LBL_CHANNELS', $QUALIFIED_MODULE)}</strong></a>
                    </li>
                    <li {if $CURRENT_TAB eq 'Templates'}class="active"{/if}>
                        <a href="index.php?module=Whatsapp&parent=Settings&view=Templates"><strong>{vtranslate('LBL_TEMPLATES', $QUALIFIED_MODULE)}</strong></a>
                    </li>
                </ul>
            </div>
		</div>
	</div>
    <div class="tab-content" style="padding-top: 10px;">
