# PromotionalMaterial Module

## Overview
The Promotional Material module allows administrators to create and manage promotional content that can be shared with partners. Once published, all partners can view and download promotional materials with notifications.

## Features

### Admin Functionality
- Create and edit promotional materials
- Upload documents/attachments
- Publish/Draft status management
- Assign promotional materials to specific users or share with partners
- Notify partners when new materials are published

### Partner Functionality
- View published promotional materials
- Download attached documents
- Receive notifications when new materials are published
- View only - no edit/delete capabilities

## Database Tables

### Main Tables
1. **vtiger_promotionalmaterial**
   - promotionalmaterialid (PK)
   - title (VARCHAR 255)
   - description (TEXT)
   - status (VARCHAR 25) - Published/Draft
   - publisheddate (DATETIME)

2. **vtiger_promotionalmaterial_documents**
   - documentid (PK)
   - promotionalmaterialid (FK)
   - filename (VARCHAR 255)
   - filepath (VARCHAR 500)
   - filetype (VARCHAR 50)
   - filesize (INT)
   - createdtime (DATETIME)

3. **vtiger_promotionalmaterialcf**
   - Custom fields table for extensibility
   - promotionalmaterialid (PK, FK)

## Web Services Registration
Module must be registered in:
- `vtiger_ws_entity` - For REST API support
- `vtiger_entityname` - For module identification

## Event Handlers

### PromotionalMaterialEventHandler.php
Handles events for:
- **Event**: `vtiger.entity.aftersave` on PromotionalMaterial
- **Action**: Send notifications to Partners when status changes to "Published"
- **Notification Method**: VDNotifierPro + Email

## Implementation
See `install_promotional_material_db.php` for database setup and registration.

## Permissions

### Partner Role
- **Create**: Yes
- **Read**: Published records only
- **Update**: Own records only (if not published)
- **Delete**: No
- **Download**: Published documents only

### Admin/Manager Role
- **Create**: Yes
- **Read**: All
- **Update**: All
- **Delete**: Yes
- **Manage**: Status, Publishing, Sharing

## Notification Flow

1. Admin publishes a Promotional Material
2. System triggers `vtiger.entity.aftersave` event on PromotionalMaterial module
3. PromotionalMaterialEventHandler catches the event and checks if status is "Published"
4. Extract all Partner users via role-based query
5. Send notification to each Partner via:
   - VDNotifierPro notification stream
   - Email notification
6. Partners receive notification and can view/download material

## Usage

### Creating Promotional Material
1. Navigate to Promotional Material module
2. Click "Create" or "New"
3. Fill in Title, Description
4. Upload document(s)
5. Set Status to Draft
6. Save

### Publishing Material
1. Open existing Promotional Material
2. Click "Edit"
3. Change Status to "Published"
4. Save - This triggers partner notifications

### Accessing as Partner
1. Login as Partner user
2. Navigate to Promotional Material module
3. View only published materials
4. Download attached documents (view-only)

## Configuration

### Vtiger Settings Integration
- Accessible under: Settings > Tools > Promotional Material Manager
- Allows admins to bulk publish/manage materials
- Configure notification preferences

## Notes
- Promotional Materials inherit CRM Entity standard features (audit trail, custom fields, etc.)
- All download activities are logged for audit purposes
- Partners can only see materials marked as "Published"
- Draft materials are visible only to creators and admins
