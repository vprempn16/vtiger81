# Partner Role Implementation - Complete Guide

**Created**: March 31, 2026  
**Vtiger Version**: 8.1  
**Project**: Partner Functionality Enhancement  

---

## Executive Summary

This document provides a complete implementation plan and guide for enabling Partner role functionality in Vtiger CRM. The implementation includes:

1. **Partner Role Verification** - Validate existing Partner (H6) role configuration
2. **Promotional Material Module** - New module for sharing marketing content with Partners
3. **Notification System** - Automated notifications for Partner events
4. **Database Setup** - Proper table creation and module registration

---

## Current Status

### ✅ Completed Components

1. **Partner Role (H6)** - Already created and configured
   - User: `premtest` (ID: 6)
   - Role ID: `H6`
   - Email: nathprem529@gmail.com
   - Admin: Off

2. **VTAtomCommentsMentions Module** - Already implementing @mention notifications
   - Location: `modules/VTAtomCommentsMentions/`
   - Features: Parse @mentions, send email notifications, VDNotifierPro integration
   - Status: Functional and integrated

3. **VDNotifierPro Module** - Notification framework available
   - Location: `modules/VDNotifierPro/`
   - Used by: VTAtomCommentsMentions and other modules

### ⚠️ Components Requiring Verification

1. **Projects Module Access** - Need to verify Partner can create/manage projects
   - Check if TabID 37 (Projects) is in Partner's profileTabsPermission
   - File to verify: `user_privileges/user_privileges_6.php`

2. **Partner Data Visibility** - Verify own-record-only restrictions
   - Leads: Must see only own leads
   - Contacts: Must see only own contacts
   - Accounts: Must see only associated accounts
   - File to verify: `user_privileges/sharing_privileges_6.php`

### ❌ Components to Be Implemented

1. **Promotional Material Module** - NEW MODULE
   - Status: Created (files ready)
   - Action: Install and enable via database script

2. **Event Notification System** - Record assignment and update notifications
   - Partial: @mention notifications already working
   - To Add: Record assignment, record update, and project collaboration notifications

---

## Created Files Overview

### Module Files

#### Core Module Files
```
modules/PromotionalMaterial/
├── PromotionalMaterial.php                 (Main module class)
├── PromotionalMaterialEventHandler.php     (Event handler for notifications)
├── schema.xml                              (Database schema definition)
└── README.md                               (Module documentation)
```

#### Handler Files
```
modules/PromotionalMaterial/handlers/
└── PromotionalMaterialHandler.php          (WebServices/REST API handler)
```

#### Directory Structure (Ready for Views/Actions)
```
modules/PromotionalMaterial/
├── views/                                  (For Edit, Detail, List views)
└── actions/                                (For custom actions)
```

### Installation & Verification Scripts
```
└── install_promotional_material_db.php     (Database setup script - tab ID 58)
└── verify_partner_role.php                 (Partner role verification script)
```

---

## Database Tables Structure

### PromotionalMaterial Module Tables

#### 1. vtiger_promotionalmaterial
```sql
CREATE TABLE `vtiger_promotionalmaterial` (
  `promotionalmaterialid` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  `status` VARCHAR(25) DEFAULT 'Draft',    -- Draft, Published, Archived
  `publisheddate` DATETIME,
  INDEX `idx_title` (`title`),
  INDEX `idx_status` (`status`)
)
```

#### 2. vtiger_promotionalmaterial_documents
```sql
CREATE TABLE `vtiger_promotionalmaterial_documents` (
  `documentid` INT AUTO_INCREMENT PRIMARY KEY,
  `promotionalmaterialid` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(500),
  `filetype` VARCHAR(50),
  `filesize` INT,
  `createdtime` DATETIME,
  FOREIGN KEY (`promotionalmaterialid`) REFERENCES `vtiger_promotionalmaterial`
)
```

#### 3. vtiger_promotionalmaterialcf
```sql
CREATE TABLE `vtiger_promotionalmaterial_custom_fields` (
  `promotionalmaterialid` INT PRIMARY KEY,
  -- Custom field columns will be added dynamically
)
```

### Module Registration Tables

#### vtiger_entityname Entry
```sql
INSERT INTO vtiger_entityname 
  (tabid, modulename, tablename, displayname_fields, identifier_field, table_primarykey)
VALUES
  (58, 'PromotionalMaterial', 'vtiger_promotionalmaterial', 'title', 'promotionalmaterialid', 'promotionalmaterialid')
```

#### vtiger_ws_entity Entry
```sql
INSERT INTO vtiger_ws_entity 
  (id, name, handler_path, handler_class, ismodule)
VALUES
  (58, 'PromotionalMaterial', 'modules/PromotionalMaterial/handlers/PromotionalMaterialHandler.php', 'PromotionalMaterialHandler', 1)
```

---

## Implementation Steps

### Step 1: Verify Partner Role Permissions ⚠️ (REQUIRED)

**Purpose**: Ensure Partner role has correct permissions for required modules

**Command**: Access `verify_partner_role.php` from Vtiger root
```
https://your-vtiger-instance/verify_partner_role.php
```

**Checklist**:
- [ ] Leads module accessible ✓
- [ ] Accounts module accessible ✓
- [ ] Contacts module accessible ✓
- [ ] Project module accessible (⚠️ VERIFY)
- [ ] Own-record-only restrictions enforced ✓
- [ ] Delete/Export disabled ✓

**Expected Output**:
```
✓ Partner Role Found (H6)
✓ Partner Users Found: 1 (premtest)
✓ Accessible Modules: Leads, Accounts, Contacts, Project...
✓ Action Permissions: Create, Read, Edit allowed; Delete, Export denied
```

---

### Step 2: Install Promotional Material Module ⚠️ (REQUIRED)

**Command**: Access `install_promotional_material_db.php` from Vtiger root
```
https://your-vtiger-instance/install_promotional_material_db.php
```

**Script Actions**:
1. Create `vtiger_promotionalmaterial` table
2. Create `vtiger_promotionalmaterial_documents` table
3. Create `vtiger_promotionalmaterialcf` custom fields table
4. Register in `vtiger_entityname` (TabID: 58)
5. Register in `vtiger_ws_entity` (ID: 58)
6. Create tab entry in `vtiger_tab`
7. Add default fields (Title, Description, Status, Published Date)
8. Configure Partner role permissions
9. Register event handler for notifications
10. Create status picklist (Draft, Published, Archived)

**Expected Output**:
```
✓ Created vtiger_promotionalmaterial table
✓ Created vtiger_promotionalmaterial_documents table
✓ Created vtiger_promotionalmaterialcf table
✓ Registered module in vtiger_entityname (tabid: 58)
✓ Registered module in vtiger_ws_entity (id: 58)
✓ Created tab in vtiger_tab
✓ Added 4 default fields
✓ Added PromotionalMaterial to Partner profile
✓ Registered PromotionalMaterialEventHandler
✓ Created status picklist
```

---

### Step 3: Clear Cache & Enable Module

**In Vtiger Admin Panel**:

1. Navigate to: **Admin > System > Maintenance > Clear All Cache**
   - Click "Clear Cache" button
   - Wait for completion

2. Navigate to: **Admin > Modules and Packages > Module Manager**
   - Find "PromotionalMaterial" in the list
   - Check if status shows as "Inactive" or "Active"
   - If "Inactive", click checkbox and click "Enable"

3. Verify Tab is visible:
   - Log out and log back in
   - Should see "Promotional Material" tab in tab list
   - Check Partner user also sees the tab

---

### Step 4: Configure Partner Role Permissions

**Navigation**: **Admin > Users > Roles > Partner (H6)**

**Tab Permissions**:
- [ ] PromotionalMaterial - Enabled for Partner

**Module Action Permissions**:
```
PromotionalMaterial:
- Create: Disabled (Partners should not create, only view published)
- Read: Enabled
- Edit: Disabled
- Delete: Disabled
- Download: Enabled (Custom - implement in handler)
```

**Data Sharing**:
- Create rule: Partners can only view Published records
- Cannot edit or delete any records
- Can download documents from published records only

---

### Step 5: Verify Notification System

**Components to Verify**:

#### A. @Mention Notifications (Already Working)
**Module**: `modules/VTAtomCommentsMentions/`

Test:
1. Navigate to any record (e.g., Project)
2. Go to Comments section
3. Add comment with `@premtest` mention
4. Execute event handlers
5. Check if email notification is sent to partner@email.com

#### B. Record Assignment Notifications (To Implement)

**Current Status**: Not implemented yet  
**Implementation**: Need to add event handler for:
- When Partner is assigned to a Project
- When Project owner changes
- Post-assignment notification

#### C. Record Update Notifications (To Implement)

**Current Status**: Not implemented yet  
**Implementation**: Need to add event handler for:
- When Project is updated
- Notify all involved team members
- Use VDNotifierPro notification system

---

### Step 6: Test Promotional Material Workflow

**As Admin**:
1. Navigate to **Promotional Material** tab
2. Click **New** to create a record
3. Fill in:
   - Title: "Q1 Product Launch"
   - Description: "New product launch materials for partners"
   - Status: Draft
4. Save
5. Update the record and change Status to "Published"
6. Check if Partner user receives notification

**As Partner** (premtest user):
1. Login as premtest
2. Navigate to Promotional Material tab
3. Should see only Published materials
4. Should NOT see Draft materials
5. Click on a published record
6. Should see "Download Document" button (if documents attached)
7. Should NOT see Edit or Delete buttons

**Expected Notifications**:
- Email sent to Partner: "New Promotional Material Published"
- VDNotifierPro notification in admin panel
- Internal notification in user's notification area

---

### Step 7: Configure Project Assignment Notifications

**Requirement**: Partner gets notified when assigned to a Project

**Implementation Steps**:

1. Create file: `modules/Project/ProjectAssignmentHandler.php`
   - Listen to: `vtiger.entity.aftersave` on Project
   - Check if smownerid (assigned user) is a Partner
   - Send notification

2. Register handler in: `modules/Project/Project.php`
   - Add event hook registration

3. Test by:
   - Creating a Project
   - Assigning to Partner user
   - Check notification received

---

### Step 8: Configure Comment Mention Notifications

**Status**: Already Implemented ✓
**Module**: `modules/VTAtomCommentsMentions/`

**How it works**:
1. ModComments fires `vtiger.entity.aftersave` event
2. VTAtomCommentsMentions catches the event
3. Parses @mentions from comment text
4. Sends email & VDNotifierPro notifications to mentioned users

**Testing**:
1. Open any Project record
2. Add comment: `@premtest Please review this project status`
3. Save comment
4. Check Partner email for "@mention" notification
5. Check VDNotifierPro for notification

---

## Permission Matrix Summary

### Partner Role (H6) Permissions

| Module | Create | Read | Update | Delete | Download |
|--------|--------|------|--------|--------|----------|
| Leads | ✓ | ✓* | ✓* | ✗ | ✗ |
| Contacts | ✓ | ✓* | ✓* | ✗ | ✗ |
| Accounts | ✗ | ✓* | ✗ | ✗ | ✗ |
| Project | ✓ | ✓* | ✓* | ✗ | ✗ |
| PromotionalMaterial | ✗ | ✓** | ✗ | ✗ | ✓ |
| ModComments | ✓ | ✓ | ✓ | ✓ | ✗ |

**Notes**:
- `*` = Own records only (Leads/Contacts created by Partner, Projects assigned to Partner)
- `**` = Published records only
- Default org sharing level: 2 (Limited)

---

## File Locations Reference

### Created Module Files
```
modules/PromotionalMaterial/
├── PromotionalMaterial.php                     Main module class
├── PromotionalMaterialEventHandler.php         Notification event handler
├── schema.xml                                  Database schema
├── README.md                                   Module documentation
└── handlers/
    └── PromotionalMaterialHandler.php          WebServices handler
```

### Installation Scripts
```
install_promotional_material_db.php             Complete installation (DB + registration)
verify_partner_role.php                         Partner role verification
```

### Existing Important Files
```
modules/VTAtomCommentsMentions/                 @Mention notification module
├── CommentMentionSendMail.php                 Event handler for mentions
└── README.md                                   @Mention documentation

modules/VDNotifierPro/                          Notification framework
└── VDNotifierPro.php                          Core notification class

user_privileges/
├── user_privileges_6.php                       Partner user configuration
└── sharing_privileges_6.php                    Partner data sharing rules
```

---

## Troubleshooting Guide

### Issue: PromotionalMaterial tab not showing after installation

**Solutions**:
1. Clear cache: Admin > System > Maintenance > Clear All Cache
2. Log out and log back in
3. Check module is enabled: Admin > Modules and Packages > Modules Manager
4. Check user role has permission: Admin > Users > Roles > Partner

### Issue: Partner can see all records (not just published)

**Solutions**:
1. Check handler permission logic
2. Verify status field has correct values
3. Review PromotionalMaterialHandler.php checkPartnerPermission() method
4. Test with SQL: `SELECT * FROM vtiger_promotionalmaterial WHERE status = 'Published'`

### Issue: Notifications not sending to Partner

**Solutions**:
1. Check VDNotifierPro module is enabled
2. Verify Partner user has valid email in user_privileges_6.php
3. Check event handler is registered: `vtiger_eventhandlers` table
4. Review cron job for email delivery: `modules/VDNotifierPro/SendNotifications.php`
5. Check logs: `logs/` directory for error messages

### Issue: @Mention notifications not working

**Solutions**:
1. Verify VTAtomCommentsMentions module is enabled
2. Check event handler: `modules/VTAtomCommentsMentions/CommentMentionSendMail.php`
3. Verify email configuration: Config > Settings > Email
4. Test with admin user first to isolate partner-specific issues

---

## Implementation Checklist

### Pre-Implementation
- [ ] Backup database
- [ ] Backup Vtiger files
- [ ] Verify Partner (H6) role exists
- [ ] Verify at least one Partner user exists (premtest)

### Installation Phase
- [ ] Run `verify_partner_role.php` to check current state
- [ ] Review verification report
- [ ] Run `install_promotional_material_db.php`
- [ ] Review installation output for errors

### Post-Installation
- [ ] Clear Vtiger cache
- [ ] Verify PromotionalMaterial module appears in tab list
- [ ] Enable module if not auto-enabled
- [ ] Configure Partner role permissions
- [ ] Create test promotional material
- [ ] Test Partner access (create, edit, delete restrictions)

### Testing Phase
- [ ] Test as Admin: Create, edit, delete promotional materials
- [ ] Test as Partner: View published only, download documents
- [ ] Test notifications: @mentions in comments
- [ ] Test record assignment: Partner assigned to project
- [ ] Test email delivery: Check partner inbox for notifications

### Final Verification
- [ ] Partner can create Leads ✓
- [ ] Partner can create Contacts ✓
- [ ] Partner can create Projects ✓
- [ ] Partner can only see own records ✓
- [ ] Partner can view published promotional materials ✓
- [ ] Partner receives notifications on @mentions ✓
- [ ] Partner receives notifications on record assignment ✓
- [ ] Partner can download documents from published materials ✓
- [ ] Partner cannot edit/delete records ✓

---

## Performance Considerations

### Index Strategy
- Added index on `vtiger_promotionalmaterial.status` for faster published material filtering
- Added index on `vtiger_promotionalmaterial_documents.promotionalmaterialid` for faster document lookup
- Consider additional indexes if performance issues arise

### Query Optimization
- Partner permission checks use indexed status field
- Document queries use foreign key relationship
- Consider caching published materials list (TTL: 1 hour)

### Notification Scaling
- Event handler may add latency during record save
- Consider implementing queued notifications for large partner base
- Use cron job for batch notification delivery

---

## Future Enhancements

1. **Bulk Actions**
   - Bulk publish promotional materials
   - Bulk assign to partner groups
   - Bulk delete archived materials

2. **Advanced Permissions**
   - Partner-specific material visibility (not all partners see same materials)
   - Material expiration dates
   - Content ratings/versions

3. **Analytics**
   - Track downloads by partner
   - View count per material
   - Partner engagement metrics

4. **Mobile App**
   - PromoMaterial module support in mobile app
   - Offline download capability
   - Push notifications on publish

5. **Integration**
   - PDF generation of promotional materials
   - Email campaign integration
   - Portal access for external partners

---

## Support & References

### Vtiger Documentation
- Module Development: https://www.vtiger.com/
- Event Handlers: vtlib_handler() in CRMEntity
- WebServices: VTEntityHandler class

### Created Module Documentation
- See: `modules/PromotionalMaterial/README.md`
- See: `modules/VTAtomCommentsMentions/README.md` (for @mention reference)

### Database References
- Main tables: vtiger_entityname, vtiger_ws_entity
- Event system: vtiger_eventhandlers
- Permissions: vtiger_profile2tab, vtiger_user2role

---

## PROJECT IMPLEMENTATION - Partner Workflow & Notifications

### Overview

The Project module has been enhanced to support Partner role with:

1. **Partner Project Access** - Partners can create and manage Projects they are assigned to or created by
2. **Project Assignment Notifications** - Users notified when assigned to a Project
3. **Project Update Notifications** - Team members notified when project status/info changes
4. **Comment Mentions** - @mention functionality for project collaboration

---

### Feature 4: Partner can create/update Project records

**Configuration File**: `install_project_notification_db.php`

**What it does**:
1. Configures Partner (H6) role permissions on Project module
   - Create: ✓ Allowed
   - Read: ✓ Allowed (own projects only)
   - Update: ✓ Allowed (own projects only)
   - Delete: ✗ Not allowed
   - Export: ✗ Not allowed
   - Assign: ✓ Allowed (can assign to other users)

2. Registers event handlers for notifications
3. Sets up VDNotifierPro integration

**Installation**:
1. Open browser: `https://your-instance/install_project_notification_db.php`
2. Click "Start Installation"
3. Wait for completion message
4. Check logs: `storage/project_notification_install.log`

**Expected Output**:
```
[STEP 1] Configuring Partner permissions for Projects...
  ✓ Found Project TabID: 37
  ✓ Found Partner Role: H6
  ✓ Found Partner Profile: 5
  ✓ Added Project tab to Partner profile
  ✓ Set action permissions: Create=1, Read=1, Edit=1, Delete=0, Export=0, Assign=1

[STEP 2] Registering Project Assignment Notification Handler...
  ✓ Registered ProjectAssignmentNotificationHandler

[STEP 3] Registering Project Update Notification Handler...
  ✓ Registered ProjectUpdateNotificationHandler

=== Project Installation Completed Successfully ===
```

**Permission Model**:
- Partners can create new Projects
- Partners can only edit Projects they created or are assigned to
- Partners can assign Projects they created to other users
- Partners cannot delete Projects
- Own-record-only restriction applies to Partner view

---

### Feature 5: Notifications for Project Communication

#### 5a. @Mention Notifications in Comments

**Status**: ✅ Already Implemented (VTAtomCommentsMentions module)

**How it works**:
1. User adds comment to Project: `@username Please review this`
2. System parses @mentions using regex
3. Mentioned user receives:
   - Email notification with comment details
   - VDNotifierPro internal notification
   - Link to the project record

**Testing**:
```
1. Open a Project record
2. Add comment with mention: @premtest Review this status update
3. Check Partner email for notification
4. Check VDNotifierPro notification panel
```

**Files Involved**:
- `modules/VTAtomCommentsMentions/CommentMentionSendMail.php` - Parse & send notifications
- `modules/VDNotifierPro/models/Record.php` - Create notifications

---

#### 5b. Record Assignment Notifications

**Status**: ✅ Newly Implemented

**Handler File**: `modules/Project/handlers/ProjectAssignmentNotificationHandler.php`

**What it does**:
1. Monitors when a Project is assigned to a user (smownerid changes)
2. Detects change by comparing audit trail
3. Sends notification to newly assigned user:
   - **Title**: "You have been assigned to Project: [ProjectName]"
   - **Email**: Detailed notification with project link
   - **VDNotifierPro**: Internal notification with clickable link
   - **Priority**: High (assignment is important)

**Sample Notification**:
```
Subject: You have been assigned to Project: Website Redesign
Body:
  Admin User has assigned you to the project: Website Redesign
  
  You can view the project details by clicking the link below:
  https://your-instance/index.php?module=Project&view=Detail&record=123
```

**Testing**:
```
1. As Admin, create or edit a Project
2. Change "Assigned To" to Partner user (premtest)
3. Save
4. Check Partner user's:
   - Email inbox for assignment notification
   - VDNotifierPro notifications
   - Internal notification bell
```

---

#### 5c. Record Update Notifications

**Status**: ✅ Newly Implemented

**Handler File**: `modules/Project/handlers/ProjectUpdateNotificationHandler.php`

**What it does**:
1. Monitors when a Project record is updated
2. Detects changed fields: projectname, projectstatus, duedate, startdate, type
3. Identifies all affected team members:
   - Current project owner
   - All users with related ProjectTasks
4. Sends notification to each team member (except the person who made the change):
   - **Title**: "Project Updated: [ProjectName]"
   - **Description**: Lists which fields changed
   - **Link**: Direct link to project
   - **Priority**: Normal (informational)

**Sample Notification**:
```
Title: Project Updated: Q2 Campaign
Description: Admin User updated the project "Q2 Campaign".
Changed fields: projectstatus, duedate
```

**Changes Tracked**:
- projectname - Project name
- projectstatus - Current status (Initiated, In Progress, Completed, On Hold, Cancelled)
- duedate - Project deadline
- startdate - Project start date
- type - Project type

**Testing**:
```
1. As Admin, open a Project
2. Change status: Draft → In Progress
3. Save
4. Check all team members receive update notification
5. Verify only changed fields are listed
```

---

### Implementation Architecture

#### Event Flow Diagram

```
Project Record Save
        ↓
vtiger.entity.aftersave event triggered
        ↓
    ├─→ ProjectAssignmentNotificationHandler
    │   ├─→ Detect if smownerid changed
    │   ├─→ Get assigned user details
    │   ├─→ Create VDNotifierPro notification
    │   └─→ Send email notification
    │
    └─→ ProjectUpdateNotificationHandler
        ├─→ Get affected team members
        ├─→ Detect changed fields
        ├─→ Create VDNotifierPro notifications
        └─→ Send to all team members
```

#### Notification Priority System

| Event Type | Priority | Delivery | Action |
|-----------|----------|----------|--------|
| Assignment | High | Immediate | Open Project |
| @Mention | High | Immediate | View Comment |
| Status Update | Normal | Queue | Open Project |
| Field Change | Low | Daily Digest | View Details |

---

### Database Changes for Project Module

No new tables created. Uses existing Project module structure:
- `vtiger_project` - Main project records
- `vtiger_crmentity` - Core metadata (smownerid, createdtime, modifiedtime)
- `vtiger_audit` - Change tracking (for assignment detection)
- `vtiger_projecttask` - Related tasks (for team member identification)

**New Event Handlers Registered**:
```sql
INSERT INTO vtiger_eventhandlers 
  (eventname, classname, classfile) 
VALUES 
  ('vtiger.entity.aftersave', 'ProjectAssignmentNotificationHandler', 
   'modules/Project/handlers/ProjectAssignmentNotificationHandler.php'),
  ('vtiger.entity.aftersave', 'ProjectUpdateNotificationHandler', 
   'modules/Project/handlers/ProjectUpdateNotificationHandler.php');
```

---

### Testing Scenarios

#### Scenario 1: Partner Creates Project

```
1. Login as Partner (premtest)
2. Go to Project module
3. Click New
4. Fill in:
   - Name: "Marketing Campaign Q2"
   - Status: Initiated
   - Due Date: 2026-06-30
5. Click Save
6. As Admin, verify project appears in list
7. Verify Owner = premtest
```

**Expected Result**: ✓ Project created, Partner is owner

---

#### Scenario 2: Project Assignment Notification

```
1. Login as Admin
2. Open Partner's project
3. Change "Assigned To": Partner User → Team Lead
4. Click Save
5. Check Team Lead's:
   - VDNotifierPro notifications
   - Email inbox
   - Notification bell
6. Verify notification says "assigned to"
7. Click notification link → opens project
```

**Expected Result**: ✓ Notification received, link works

---

#### Scenario 3: Project Update Notification

```
1. Login as Admin
2. Open Project assigned to multiple users
3. Change Status: Initiated → In Progress
4. Change Due Date: 2026-06-30 → 2026-06-15
5. Click Save
6. Check all team members' notifications
7. Verify each sees "projectstatus, duedate" changed
```

**Expected Result**: ✓ All team members notified, correct fields shown

---

#### Scenario 4: @Mention in Project Comments

```
1. Open Project record
2. Go to Comments tab
3. Add comment: "Status update needed. @premtest please review"
4. Click Save
5. Check Partner (premtest) email
6. Verify email contains comment text + project link
7. Click link → opens project detail
```

**Expected Result**: ✓ @mention parsed, email sent, link functional

---

### Troubleshooting Project Implementation

#### Issue: Partner can't create Projects

**Solution**:
1. Check installation completed: `storage/project_notification_install.log`
2. Verify Partner has Create permission:
   ```sql
   SELECT * FROM vtiger_profile2standardpermission 
   WHERE profileid=5 AND tabid=37 AND operation=0
   ```
3. Re-run installer: `install_project_notification_db.php`
4. Clear cache: Admin > System > Maintenance

---

#### Issue: Assignment notifications not sending

**Solution**:
1. Verify handler registered:
   ```sql
   SELECT * FROM vtiger_eventhandlers 
   WHERE classname = 'ProjectAssignmentNotificationHandler'
   ```
2. Check event handler file exists: `modules/Project/handlers/ProjectAssignmentNotificationHandler.php`
3. Verify VDNotifierPro enabled: Admin > Modules > Module Manager
4. Check email config: Admin > Settings > Email

---

#### Issue: Partner sees all projects (not just owned)

**Solution**:
1. Check sharing privileges:
   ```sql
   SELECT * FROM vtiger_user2role WHERE userid = 6
   SELECT * FROM sharing_privileges_6.php
   ```
2. Verify role has 'own' restriction
3. Check if Partner is assigned as secondary owner
4. Review field-level permissions

---

### Files Created/Modified for Project Feature

```
Newly Created:
├── modules/Project/handlers/
│   ├── ProjectAssignmentNotificationHandler.php    (Assignment notifications)
│   └── ProjectUpdateNotificationHandler.php      (Update notifications)
├── install_project_notification_db.php           (Installation script)

Existing Modified:
└── vtiger_eventhandlers table                     (Handler registration)
```

---

**End of Implementation Guide**

---

*For questions or issues, refer to the troubleshooting section above or consult Vtiger documentation.*
