# Vtiger WhatsApp Integration - Development Progress & Architecture

**Last Updated Date:** March 2026

This document summarizes everything developed for the native Vtiger CRM WhatsApp integration so far. It can serve as a reference point for continuing development after updates or environment resets.

---

## 1. Database Schema & Tables
We established the core back-end tables to store WhatsApp configurations and imported Meta templates:
* **`vtiger_whatsapp_channels`**: Stores Meta App credentials (App ID, Secret, Permanent Access Token, Phone Number ID, Business Account ID).
* **`vtiger_whatsapp_templates`**: Holds the raw templates synced from Meta (Template Name, Language, Category, Format, and raw Components JSON).
* **`vtiger_whatsapp_channel_template_rel`**: Relates templates to specific WhatsApp Business Channels.
* **`vtiger_whatsapp_template_map`**: Stores user mapping preferences (which template variable maps to which exact Vtiger CRM Module/Field combination).

## 2. WhatsApp Settings UI: Channels
* **Location**: Vtiger **Settings > Other Settings > WhatsApp Channels**
* **Features**: 
  * Complete CRUD (Create, Read, Update, Delete) UI implemented for Channels.
  * Utilizes Vtiger's `Settings_Whatsapp_Record_Model` and customized List/Edit views.
  * Securely stores the `Permanent Access Token` and other Meta API parameters.
  * Clean styling adhering to Vtiger V7 standards.

## 3. WhatsApp Settings UI: Templates List
* **Features**:
  * Shows a list of templates filtered by Channel.
  * **Sync action**: Implemented "Sync Templates", which routes via `ActionAjax` to `WhatsAppApiService`.
  * **Sync Logic**: 
    - Downloads approved templates for the selected Meta Business Account.
    - Parses the raw `components` array and identifies dynamic variables (`{{1}}`, `{{name}}`, etc.).
    - Inserts unmapped rows into `vtiger_whatsapp_template_map` for each unique variable found in HEADER, BODY, and BUTTONS contexts (assigning unique keys like `BUTTONS_1` to handle conflicting parameters across components).

## 4. Template Field Mapping Modal
* **Location**: Accessed via the "Map" button on the Templates list table.
* **User Flow**:
  1. The user selects an applicable Vtiger CRM Module (e.g., Contacts, Leads).
  2. The Modal extracts the dynamic variables from the template.
  3. The user picks specific CRM fields to replace the template variables.
* **Advanced Technical Fixes Implemented**:
  * **Module Changing via AJAX**: Built custom JS logic securely destroying and re-initializing Vtiger's `Select2` elements instances when the CRM Module changes.
  * **Dynamic Variable Extraction**: Parses template JSON directly on load, correctly merging strings for both `POSITIONAL` (`{{1}}`) and `NAMED` (`{{name}}`) parameters.
  * **Meta Example Fetching**: UI displays variables alongside actual example data pulled from the Meta Payload (e.g., `accountnumber → 818181818`).
  * **Complex Button Routing**: Explicit mapping interface tracking dynamic URL variables embedded in WhatsApp Interactive buttons (distinguishes the first button's `.url` parameter from the second's).
  * **Message Preview**: Built a Smarty view to render the entire WhatsApp message template (Headers, Body strings, Footer, interactive Action Buttons) cleanly inside the modal UI.

## 5. Backend Logic & Controllers Created
* `layouts/v7/modules/Settings/Whatsapp/resources/Templates.js`: Handles advanced mapping modal DOM updates, AJAX population, and layout refresh.
* `modules/Settings/Whatsapp/actions/ActionAjax.php`: Handles saving field maps to DB, deleting channels, and executing external Meta API Sync queries.
* `modules/Settings/Whatsapp/views/MappingModal.php` (View Controller): Prepares grouped variable data, parses components for the mapping template context, formats variable texts.
* `modules/Settings/Whatsapp/Services/WhatsAppApiService.php`: A standalone service helper wrapping Guzzle/cURL to communicate precisely with the Meta endpoint graph syntax.
* Various Vtiger Model overrides (`Settings_Whatsapp_Template_Record_Model`, `Settings_Whatsapp_Record_Model`) extending `Settings_Vtiger_Record_Model`.

---

## Next Steps Remaining
1.  **Sending Service Implementation**: Write backend observers or workflow tasks logic to automatically format actual DB-mapped field values and dispatch messages.
2.  **Messaging Components UI**: Build widget views / chat interface within Contact/Lead summary views to visualize sent/received logs directly.
3.  **Webhook Integration**: Expose a secure endpoint to ingest real-time replies/status receipts from the Meta Webhooks service and document them in the CRM.
4.  **Final Installation Package**: Compile changes into a `manifest.xml` package script to provide a direct module upload zip.
