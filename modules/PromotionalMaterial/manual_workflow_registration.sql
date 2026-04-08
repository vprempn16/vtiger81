-- Manual registration of PromotionalMaterial workflow methods
-- Run this SQL on your server if workflow methods are missing

DELETE FROM com_vtiger_workflowtasks_entitymethod WHERE module_name = 'PromotionalMaterial';

INSERT INTO com_vtiger_workflowtasks_entitymethod (module_name, method_name, function_path, function_name) VALUES
('PromotionalMaterial', 'PMNotifyPartners', 'modules/PromotionalMaterial/workflow/PromotionalMaterialWorkflowMethods.php', 'pmwf_notifyPartnerUsers'),
('PromotionalMaterial', 'PMSendPartnerEmail', 'modules/PromotionalMaterial/workflow/PromotionalMaterialWorkflowMethods.php', 'pmwf_sendPartnerEmail');
