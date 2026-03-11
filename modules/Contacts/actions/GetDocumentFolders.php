<?php
require_once "modules/Documents/models/Module.php";
require_once "modules/Documents/models/Folder.php";

class Contacts_GetDocumentFolders_Action extends Vtiger_Action_Controller {
    public function checkPermission(Vtiger_Request $request) {
        return true;
    }

    public function validateRequest(Vtiger_Request $request) {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $folders = Documents_Module_Model::getAllFolders();
        $folderData = [];
        
        foreach ($folders as $folder) {
            $folderData[] = [
                'id' => $folder->getId(),
                'name' => $folder->getName()
            ];
        }

        $response = new Vtiger_Response();
        $response->setResult(['folders' => $folderData]);
        $response->emit();
    }
}
