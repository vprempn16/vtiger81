<?php
require_once 'data/CRMEntity.php';
require_once 'modules/Documents/Documents.php';

class Contacts_UploadContactDocuments_Action extends Vtiger_Action_Controller {

    public function process(Vtiger_Request $request) {

        // recordIds comes as JSON array from JS
        $recordIds = $request->get('recordIds');
	//$recordIds = json_decode($recordIds, true);
	$recordIds = is_array($request->get('recordIds')) ? $request->get('recordIds') : json_decode($request->get('recordIds'), true);
	$parentModule = $request->get('parentModule');


        if (empty($recordIds)) {
            echo json_encode(['success'=>false,'message'=>'No record IDs']);
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
                'name'     => $files['name'][$index],
                'type'     => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error'    => $files['error'][$index],
                'size'     => $files['size'][$index]
            ];

            if ($fileDetails['error'] !== 0) {
                continue;
            }

            // Create Document
            $documentId = $this->createDocumentFromFile($fileDetails);

            if ($documentId) {
                $createdDocumentIds[] = $documentId;

                // Relate document with each selected parent record
                foreach ($recordIds as $parentId) {

		    $parentModuleModel = Vtiger_Module_Model::getInstance($parentModule);
                    $relatedModuleModel = Vtiger_Module_Model::getInstance('Documents');

                    $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModuleModel);
                    $relationModel->addRelation($parentId, $documentId);
                }
            }
        }

        echo json_encode([
            'success' => true,
            'documents' => $createdDocumentIds
        ]);
    }


    // ===== Document creation logic reused from senior code =====

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

        // Move uploaded file
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

        // ---- Create Attachment Records ----

        $db->pquery(
            "INSERT INTO vtiger_crmentity
            (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$attachId, $userId, $userId, $userId, "Documents Attachment",
             $fileName, $formattedDate, $formattedDate, 1, 0]
        );

        $db->pquery(
            "INSERT INTO vtiger_attachments
            (attachmentsid, name, description, type, path, storedname)
            VALUES (?, ?, ?, ?, ?, ?)",
            [$attachId, $fileName, $fileName, $mimeType, $uploadPath, $encryptFileName]
        );

        // ---- Create Document Record ----

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

        // ---- Link attachment to document ----

        $db->pquery(
            "INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid)
             VALUES(?,?)",
            [$documentId, $attachId]
        );

        return $documentId;
    }
}
?>
