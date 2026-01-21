<?php
require_once 'data/CRMEntity.php';
require_once 'modules/Documents/Documents.php';

class Contacts_UploadContactDocuments_Action extends Vtiger_Action_Controller {

    public function process(Vtiger_Request $request) {

        $recordId = $request->get('record');
        if (empty($recordId)) {
            echo json_encode(['success'=>false,'message'=>'No Contact ID']);
            return;
        }

        if (!isset($_FILES['documents'])) {
            echo json_encode(['success'=>false,'message'=>'No files uploaded']);
            return;
        }

        $files = $_FILES['documents'];
        $createdDocumentIds = [];

        foreach ($files['name'] as $index => $name) {

            $fileDetails = [
                'name' => $files['name'][$index],
                'type' => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error' => $files['error'][$index],
                'size' => $files['size'][$index]
            ];

            if ($fileDetails['error'] !== 0) continue;

            $documentId = $this->createDocumentFromFile($fileDetails);
            if ($documentId) {
                $createdDocumentIds[] = $documentId;
		 $parentModuleModel = Vtiger_Module_Model::getInstance('Contacts');
		$relatedModule = Vtiger_Module_Model::getInstance('Documents');
                // Relate document to contact
                $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
                $relationModel->addRelation($recordId, $documentId);
            }
        }

        echo json_encode(['success'=>true,'documents'=>$createdDocumentIds]);
    }


    // ======= REUSED from your senior code =======

    private function createDocumentFromFile($fileDetails) {

        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUserModel->id;

        $uploadPath = decideFilePath();
        $binFile = sanitizeUploadFileName($fileDetails['name'], vglobal('upload_badext'));
        $fileName = ltrim(basename(" " . $binFile));

        $attachId = $db->getUniqueId('vtiger_crmentity');
        $encryptFileName = Vtiger_Util_Helper::getEncryptedFileName($binFile);
        $targetPath = $uploadPath . $attachId . "_" . $encryptFileName;

        if (!move_uploaded_file($fileDetails['tmp_name'], $targetPath)) {
            return false;
        }

        $fileSize = filesize($targetPath);
        $mimeType = vtlib_mime_content_type($targetPath);
        if (empty($mimeType)) {
            $mimeType = $fileDetails['type'];
        }

        $dateVar = date("Y-m-d H:i:s");
        $formattedDate = $db->formatDate($dateVar, true);

        // Insert attachment records
        $db->pquery(
            "INSERT INTO vtiger_crmentity 
            (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$attachId, $userId, $userId, $userId, "Documents Attachment", $fileName, $formattedDate, $formattedDate, 1, 0]
        );

        $db->pquery(
            "INSERT INTO vtiger_attachments
            (attachmentsid, name, description, type, path, storedname)
            VALUES (?, ?, ?, ?, ?, ?)",
            [$attachId, $fileName, $fileName, $mimeType, $uploadPath, $encryptFileName]
        );

        // Create document record
        $document = new Documents();
        $document->column_fields['notes_title'] = $fileName;
        $document->column_fields['filename'] = $fileName;
        $document->column_fields['filestatus'] = 1;
        $document->column_fields['filelocationtype'] = 'I';
        $document->column_fields['folderid'] = 1;
        $document->column_fields['filesize'] = $fileSize;
        $document->column_fields['assigned_user_id'] = $userId;
        $document->save('Documents');

        $documentId = $document->id;

        // Link attachment to document
        $db->pquery(
            "INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",
            [$documentId, $attachId]
        );

        return $documentId;
    }
}
?>
