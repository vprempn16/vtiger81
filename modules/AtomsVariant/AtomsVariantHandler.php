<?php
class AtomsVariantHandler extends VTEventHandler{
    function handleEvent($eventName, $entityData) {
        global $adb;
        $recordid = $entityData->getId();

        if ($eventName == 'vtiger.entity.aftersave') {
            $moduleName = $entityData->getModuleName();
            $requestData = $_POST;
            if (in_array($moduleName, ['Invoice', 'SalesOrder', 'Quotes'])) {
                $totalProductsCount = $requestData['totalProductCount'];
                $variantData = [];
                $productVariants = [];

                foreach ($requestData as $key => $value) {
                    if (preg_match('/^hdnProductId(\d+)$/', $key, $matches) && $key != "hdnProductId0") {
                        $productVariants[] = $value;
                    }
                }
                $variantData = [];
                $variantInfoList = [];
                $variantIdList = [];

                foreach ($requestData as $key => $value) {
                    if (preg_match('/^atomsvariantinfo(\d+)$/', $key, $matches)) {
                        $variantInfoList[] = $value; 
                    }
                    if (preg_match('/^atomsvariantid(\d+)$/', $key, $matches)) {
                        $variantIdList[] = $value; 
                    }
                }

                foreach ($productVariants as $index => $productId) {
                    $variantData[] = [
                        'productid' => $productId,
                        'variantid' => $variantIdList[$index] ?? '', 
                        'variantinfo' => $variantInfoList[$index] ?? ''
                    ];
                }
                $query = "SELECT lineitem_id, productid, sequence_no FROM vtiger_inventoryproductrel WHERE id = ? ORDER BY sequence_no";
                $result = $adb->pquery($query, [$recordid]);

                $variantIndex = 0; 

                while ($row = $adb->fetch_array($result)) {
                    $lineItemId = $row['lineitem_id'];
                    $productId = $row['productid'];
                    $sequenceNo = $row['sequence_no'];

                    if (!isset($variantData[$variantIndex])) {
                        continue;
                    }

                    $variant = $variantData[$variantIndex]; 

                    if ($variant['productid'] == $productId) {  
                        $variantId = $variant['variantid'];
                        $variantInfo = $variant['variantinfo'];
                        $variantName = Vtiger_Util_Helper::getRecordName($variantId);

                        $updateQuery = "UPDATE vtiger_inventoryproductrel SET atomsvariantid = ?, atomsvariantinfo = ?, atomvariantname = ? WHERE id = ? AND lineitem_id = ?";
                        $adb->pquery($updateQuery, [$variantId, $variantInfo, $variantName, $recordid, $lineItemId]);
                        $variantIndex++; 
                    }
                } 
            }
        }
    }
}
