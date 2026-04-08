# PromotionalMaterial Module

## Overview

The Promotional Material module lets internal users create and publish marketing assets (description + document upload). **Partner** users can see **all published** items in the list, open any published record, and **download** attachments. They are **not** allowed to create, edit, or delete promotional material records.

**Recommended:** configure **Settings → Workflows** on **PromotionalMaterial** (e.g. when **Promotional status** is **Published**) and add **Invoke custom function** tasks:

| Method name (dropdown) | Behaviour |
|------------------------|-----------|
| **PMNotifyPartners** | VDNotifier Pro notification to all users with the Partner role; default title includes the record **Title**. |
| **PMSendPartnerEmail** | Sends email to each Partner user using the email template named **`PMPartnerEmailTemplate`** with **module = Users** (your field mapping). Subject/body are merged per recipient user. Optional placeholders: **`###PM_DETAIL_URL###`**, **`###PM_RECORD_TITLE###`**. |

Registration: **`PromotionalMaterial::registerWorkflowEntityMethods()`** runs on module **post-install** and **enable** and inserts rows into **`com_vtiger_workflowtasks_entitymethod`**. If methods are missing, disable and re-enable the module under **Settings → Module Manager**, or call that static method once from a small script.

**Remove old custom task types** (if you added them earlier): run **`modules/PromotionalMaterial/workflow/sql/delete_pm_workflow_tasktypes.sql`**. Replace any workflow steps that still used **PMPartnerNotificationTask** / **PMPartnerEmailTask** with **Invoke custom function** tasks first.

Optional **`.tpl`** files under **`layouts/v7/modules/Settings/Workflows/Tasks/PMPartner*.tpl`** are kept for possible future use; they are **not** required for **Invoke custom function**.

The legacy **`PromotionalMaterialEventHandler`** is **disabled by default** (so you do not double-notify). To re-enable it, define `PM_PROMOTIONAL_USE_LEGACY_EVENT_NOTIFIER` as `true` in `config.inc.php` (or another file loaded before events).

---

## Partner vs admin behaviour

| Capability | Admin (non-partner) | Partner (role `H6` by default) |
|------------|---------------------|----------------------------------|
| List view | All records (subject to profile/sharing) | **Only** rows with `promotional_status = Published` |
| Detail / download | As per CRM permissions | **Published** records only (any owner) |
| Create | Yes (if profile allows) | **No** (`CreateView` denied) |
| Edit / Save / Delete | Yes (if profile allows) | **No** on entity record |
| Notifications on publish | N/A (they are recipients) | As defined in **Workflows** (or legacy event handler if enabled) |

---

## Technical implementation (what was implemented)

### 1. List view — all published materials for partners

- **`models/ListView.php`** (`PromotionalMaterial_ListView_Model`): For partner users, the list SQL adds  
  `AND vtiger_promotionalmaterial.promotional_status = 'Published'`.
- **`PromotionalMaterial.php`**: **`getNonAdminAccessControlQuery()`** — for the partner role, the private-sharing owner temp-table join is skipped so partners are not limited to “my records only”. Visibility is then enforced by the list filter + `isPermitted` (below).

### 2. Record access — view / download only for partners

- **`include/utils/UserInfoUtil.php`** (`isPermitted`):
  - Partner + empty record: **`CreateView`** → denied (before the generic “empty record = yes” shortcut).
  - Partner + record id: load **`promotional_status`** from **`vtiger_promotionalmaterial`**. If not **`Published`** → denied. If **`Published`**, deny **`EditView`**, **`Delete`**, **`Save`**; allow other actions such as **`DetailView`** / **`index`** (so standard attachment download, which checks **`DetailView`**, works).

### 3. Notifications (workflows + optional legacy event)

- **Workflows** — **Invoke custom function** (**`PMNotifyPartners`**, **`PMSendPartnerEmail`**) implement partner notification and mail; see the overview above.
- **Legacy** **`PromotionalMaterialEventHandler.php`**: still present but **returns immediately** unless **`PM_PROMOTIONAL_USE_LEGACY_EVENT_NOTIFIER`** is set to **`true`**. When enabled, it uses **`VTEntityDelta`** on **`promotional_status`** and sends VDNotifier + email on transition to Published (same behaviour as before).

### 4. Web service handler

- **`handlers/PromotionalMaterialHandler.php`**:
  - Uses column **`promotional_status`** (not `status`).
  - **Update** blocked for partners only when the record is **Published** (admins were previously blocked incorrectly).

### 5. Entity class helpers

- **`PromotionalMaterial.php`**:
  - **`getPartnerRoleId()`** — single place to change role id if not `H6`.
  - **`currentUserIsPartnerRole()`** — used by the ListView model.
  - Removed the old **`insertIntoAttachment()`** stub that called **`die()`** (it broke saves/uploads).

### 6. Installer / event registration

- **`install_promotional_material_db.php`** — **`registerEventHandler()`**:
  - Deletes any row with **`handler_class = 'PromotionalMaterialEventHandler'`**.
  - Inserts one row: **`event_name = 'vtiger.entity.aftersave`**, path **`modules/PromotionalMaterial/PromotionalMaterialEventHandler.php`**.

Older installs may still have the wrong event name (`vtiger.entity.aftersave.PromotionalMaterial`), which **never fires** in core. Fix with the SQL in **Maintenance** below.

### 7. Language

- **`languages/en_us/PromotionalMaterial.php`**: **`LBL_PROMOTIONAL_MATERIAL_PUBLISHED`** — used in notifier title / email subject.

---

## Database schema (reference)

Main entity table fields include (see installer / DB for full list):

- **`promotionalmaterialid`** (PK)
- **`title`**, **`description`**
- **`promotional_status`** — picklist (e.g. Draft, Published, Archived); **this** is the field used everywhere in code (not a column named `status` on this table in the typical install)
- **`publisheddate`**, **`promotional_document`**, etc.

Supporting tables: **`vtiger_promotionalmaterialcf`**, optional **`vtiger_promotionalmaterial_documents`** (if used).

---

## Maintenance: fix event handler row (existing CRMs)

If notifications never appeared after publish, run:

```sql
DELETE FROM vtiger_eventhandlers WHERE handler_class = 'PromotionalMaterialEventHandler';
INSERT INTO vtiger_eventhandlers (event_name, handler_path, handler_class)
VALUES (
  'vtiger.entity.aftersave',
  'modules/PromotionalMaterial/PromotionalMaterialEventHandler.php',
  'PromotionalMaterialEventHandler'
);
```

Then clear vtiger cache if your deployment uses static event caches.

---

## Configuration checklist

1. **VDNotifierPro** module active.
2. **Outgoing mail** configured if email notifications are required.
3. **Partner role id** in DB matches **`PromotionalMaterial::getPartnerRoleId()`** (default **`H6`**). If your Partner role id differs, update:
   - `PromotionalMaterial.php` → **`getPartnerRoleId()`**
   - `include/utils/UserInfoUtil.php` → **`$pmPartnerRoleId`**
   - `handlers/PromotionalMaterialHandler.php` → partner checks that compare to **`'H6'`**

---

## Files touched by this behaviour

| File | Role |
|------|------|
| `PromotionalMaterial.php` | Partner role id, list-query bypass, file upload, **`registerWorkflowEntityMethods()`** on enable |
| `workflow/PromotionalMaterialWorkflowMethods.php` | **`pmwf_notifyPartnerUsers`**, **`pmwf_sendPartnerEmail`** for Invoke custom function |
| `PromotionalMaterialEventHandler.php` | Optional legacy aftersave notifier (off unless `PM_PROMOTIONAL_USE_LEGACY_EVENT_NOTIFIER`) |
| `models/ListView.php` | Partner list = published only |
| `handlers/PromotionalMaterialHandler.php` | Webservice read/update rules for partners |
| `views/Detail.php`, `views/Edit.php`, `actions/Save.php` | Standard vtiger overrides (minimal) |

Root installer: **`install_promotional_material_db.php`** (module + event registration).

---

## Usage (short)

1. **Staff**: Create material, set **`promotional_status`** to **Published**, save → workflows run (**Invoke custom function** as configured).
2. **Partners**: Open **Promotional Material** app → list shows published items only → open record → download attachments; no create/edit/delete of the promotional record.

---

## Notes

- Draft / non-published rows are **not** listed for partners and **not** openable via normal permission checks.
- Requirement “mentions in comments” for other modules is separate (e.g. **VTAtomCommentsMentions** + **VDNotifierPro**); this module only covers promotional publish + partner read/download rules described above.
