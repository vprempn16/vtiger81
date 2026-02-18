<?php
require_once "data/CRMEntity.php";
require_once "modules/Documents/Documents.php";

class Contacts_UploadContactDocuments_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        // recordIds may come as array or JSON
        $recordIds = $request->get("recordIds");
        $recordIds = is_array($recordIds) ? $recordIds : json_decode($recordIds, true);

        $parentModule = $request->get("parentModule"); // Contacts | Calendar

        if (empty($recordIds) || !is_array($recordIds)) {
            echo json_encode([
                "success" => false,
                "message" => "No records selected",
            ]);
            return;
        }

        if (!isset($_FILES["documents"])) {
            echo json_encode([
                "success" => false,
                "message" => "No files uploaded",
            ]);
            return;
        }

        $files = $_FILES["documents"];
        $createdDocumentIds = [];

        foreach ($files["name"] as $index => $name) {

            $fileDetails = [
                "name" => $files["name"][$index],
                "type" => $files["type"][$index],
                "tmp_name" => $files["tmp_name"][$index],
                "error" => $files["error"][$index],
                "size" => $files["size"][$index],
            ];

            if ($fileDetails["error"] !== 0) {
                continue;
            }

            // Create document ONCE per file
            $documentId = $this->createDocumentFromFile($fileDetails);
            if (!$documentId) {
                continue;
            }

            $createdDocumentIds[] = $documentId;

            /* ===============================
             * CONTACTS MODULE LOGIC (UNCHANGED)
             * =============================== */
            if ($parentModule === "Contacts") {

                $parentModuleModel = Vtiger_Module_Model::getInstance("Contacts");
                $documentsModuleModel = Vtiger_Module_Model::getInstance("Documents");
                $relationModel = Vtiger_Relation_Model::getInstance(
                    $parentModuleModel,
                    $documentsModuleModel
                );

                foreach ($recordIds as $contactId) {
                    $relationModel->addRelation($contactId, $documentId);
                }
            }

            /* ==========================================
             * EVENTS / CALENDAR MODULE (NEW CORRECT LOGIC)
             * ========================================== */
            if ($parentModule === "Events") {

                foreach ($recordIds as $eventId) {

                    // Get "Related To" records of the Event
                    $res = $db->pquery(
                        "SELECT crmid FROM vtiger_seactivityrel WHERE activityid = ?",
                        [$eventId]
                    );

                    if ($db->num_rows($res) === 0) {
                        continue; // Event has no Related To
                    }

                    while ($row = $db->fetchByAssoc($res)) {

                        $parentId = $row["crmid"];
                        if (empty($parentId)) {
                            continue;
                        }

                        $parentEntityModule = getSalesEntityType($parentId);
                        if (empty($parentEntityModule)) {
                            continue;
                        }

                        // For Events uploads, relate documents ONLY to Accounts
                        if ($parentEntityModule !== "Accounts") {
                            continue;
                        }

                        $parentModuleModel = Vtiger_Module_Model::getInstance($parentEntityModule);
                        $documentsModuleModel = Vtiger_Module_Model::getInstance("Documents");

                        $relationModel = Vtiger_Relation_Model::getInstance(
                            $parentModuleModel,
                            $documentsModuleModel
                        );

                        $relationModel->addRelation($parentId, $documentId);
                    }
                }
            }
        }

        echo json_encode([
            "success" => true,
            "documents" => $createdDocumentIds,
        ]);
    }

    /* =========================================
     * DOCUMENT CREATION (UNCHANGED, STABLE)
     * ========================================= */
    private function createDocumentFromFile($fileDetails)
    {
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUserModel->id;

        $uploadPath = decideFilePath();
        $binFile = sanitizeUploadFileName(
            $fileDetails["name"],
            vglobal("upload_badext")
        );
        $fileName = ltrim(basename(" " . $binFile));

        $attachId = $db->getUniqueId("vtiger_crmentity");
        $encryptFileName = Vtiger_Util_Helper::getEncryptedFileName($binFile);
        $targetPath = $uploadPath . $attachId . "_" . $encryptFileName;

        if (!move_uploaded_file($fileDetails["tmp_name"], $targetPath)) {
            return false;
        }

        $fileSize = filesize($targetPath);
        $mimeType = vtlib_mime_content_type($targetPath);
        if (empty($mimeType)) {
            $mimeType = $fileDetails["type"];
        }

        $dateVar = date("Y-m-d H:i:s");
        $formattedDate = $db->formatDate($dateVar, true);

        // Attachment entity
        $db->pquery(
            "INSERT INTO vtiger_crmentity
            (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $attachId,
                $userId,
                $userId,
                $userId,
                "Documents Attachment",
                $fileName,
                $formattedDate,
                $formattedDate,
                1,
                0,
            ]
        );

        $db->pquery(
            "INSERT INTO vtiger_attachments
            (attachmentsid, name, description, type, path, storedname)
            VALUES (?, ?, ?, ?, ?, ?)",
            [
                $attachId,
                $fileName,
                $fileName,
                $mimeType,
                $uploadPath,
                $encryptFileName,
            ]
        );

        // Document record
        $document = new Documents();
        $document->column_fields["notes_title"] = $fileName;
        $document->column_fields["filename"] = $fileName;
        $document->column_fields["filestatus"] = 1;
        $document->column_fields["filelocationtype"] = "I";
        $document->column_fields["folderid"] = 1;
        $document->column_fields["filesize"] = $fileSize;
        $document->column_fields["assigned_user_id"] = $userId;
        $document->save("Documents");

        $documentId = $document->id;

        // Link attachment to document
        $db->pquery(
            "INSERT INTO vtiger_seattachmentsrel (crmid, attachmentsid)
             VALUES (?, ?)",
            [$documentId, $attachId]
        );

        return $documentId;
    }
}
?>

