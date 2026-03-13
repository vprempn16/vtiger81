# Vtiger WhatsApp Integration - Development Progress & Architecture

**Last Updated Date:** March 2026

This document summarizes everything developed for the native Vtiger CRM WhatsApp integration so far. It can serve as a reference point for continuing development after updates or environment resets.

---

## 1. Database Schema & Tables
We established the core back-end tables to store WhatsApp configurations and imported Meta templates:
* **`vtiger_whatsapp_channels`**: Stores Meta App credentials (App ID, Secret, Permanent Access Token, Phone Number ID, Business Account ID) and the **`default_country_code`** (for per-channel local number handling).
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
  * Complete CRUD UI implemented for Channels.
  * **Default Country Code Setting**: Added a user-editable field to define the local prefix (e.g., `91` for India, `1` for USA) per channel. This ensures 10-digit numbers stored in the CRM are correctly formatted before sending.
  * Securely stores credentials and adheres to Vtiger V7 standards.

## 3. WhatsApp Settings UI: Templates List
* **Features**:
  * Shows a list of templates filtered by Channel.
  * **Sync Logic**: 
    - Downloads approved templates from Meta.
    - **Alphanumeric Variable Parsing**: Identifies dynamic placeholders like `{{1}}` (positional) AND `{{any_name}}` (named), creating mapping entries for all unique variables found in Header, Body, and Buttons.

## 4. Template Field Mapping Modal
* **Location**: Accessed via the "Map" button on the Templates list table.
* **Features**:
  1. **Select2 Stability**: Reliable module switching without element duplication.
  2. **Named Variable Support**: Supports Meta's newer templates utilizing named placeholders (e.g., `{{doctor}}`, `{{date}}`).
  3. **Meta Example Fetching**: UI displays variables alongside actual example data from Meta.

---

## 5. Intelligent Phone Number Normalization
Implemented in `WhatsAppApiService::formatPhoneNumber`, this multi-level engine ensures Meta always receives a full E.164-style number:
1. **Manual Override**: Always respects numbers starting with `+` as full international numbers.
2. **Record-Level Mapping**: If a Contact/Lead has a `Country` or `Mailing Country`, the system automatically looks up the dialing code (e.g., USA -> 1).
3. **Channel Fallback**: If no country is found on the record, it prepends the Channel's **Default Country Code**.
4. **Clean Logging**: Stores the final normalized number in the CRM message list for complete audit trails.

## 6. Send WhatsApp Modal (List & Detail Views)
* **Features**:
  * **Live Template Preview**: Renders beautiful HTML previews from raw template JSON.
  * **Strict Validation**: Highlights invalid or missing CRM fields in red and prevents sending if variable mappings are incomplete.

## 7. Unified Sending & Message Logging
* **Backend Execution**:
  * **Media Upload**: Detects attachments, uploads to Meta Media API, and converts text to media messages.
  * **Unified Logging**: Every message creates a record in the `Whatsapp` module with `whatsapp_status`, `whatsapp_no` (normalized), and raw `message_id`.
  * **Extended Tracking**: Captures full Meta error contexts (e.g., "recipient not on allowed list") for debugging.

---

## Next Steps Remaining
1.  **Messaging Components UI**: Build widget views / chat interface within Contact/Lead summary views.
2.  **Webhook Integration**: Expose a secure endpoint to ingest real-time replies/status receipts.
3.  **Interactive Messages**: Extend logic to handle List and Reply Button interactive payloads.
4.  **Final Installation Package**: Compile changes into a `manifest.xml` package.
