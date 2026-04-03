# Partner Visibility Enhancement for Project Module

**Date**: April 1, 2026  
**Purpose**: Allow Partner users to see Projects they created, even after assigning to other users  
**Location**: `modules/Project/models/`  

---

## Overview

By default, Vtiger CRM restricts Partner users to see only:
- Records they **own** (are currently assigned to)
- Records in their **organization** level (if org sharing is enabled)

This creates a problem: **When a Partner creates a Project and assigns it to another user, they can no longer see it.**

This implementation fixes that by allowing Partner users to see:
- ✅ Projects **assigned to them** (current owner)
- ✅ Projects **they created** (regardless of current owner)
- ❌ Projects created by other Partners (protected)

---

## Files Modified

### 1. `modules/Project/models/ListView.php`

**What Changed**: Extended `Project_ListView_Model` class

**New Method**: `getListViewEntries($pageNumber)`
- Detects if current user is a Partner role (H6)
- For Partner users, calls custom `getPartnerListViewEntries()` method
- For non-Partner users, uses standard Vtiger behavior

**Custom Query Logic**:
```php
WHERE vtiger_crmentity.deleted = 0
AND (
    vtiger_crmentity.smownerid = ? 
    OR vtiger_crmentity.createdby = ?
)
```

This SQL shows projects where user is **EITHER**:
- Current owner (smownerid)
- **OR** original creator (createdby)

**Result**: Partner list view shows all projects they created OR are assigned to

---

### 2. `modules/Project/models/Record.php`

**What Changed**: Extended `Project_Record_Model` class  

**New Method**: `isPermitted($action)`
- Overrides parent's permission check
- Detects if current user is Partner
- For Partners, allows `read/view/edit/list` actions if:
  - User is the **creator** (createdby)
  - **OR** user is the **current owner** (smownerid)

**Result**: Partner can open and edit records they created

---

## How It Works

### Scenario: Partner Creates & Assigns Project

```
1. Partner user (premtest) opens Project module
   ↓
   ListView.php → getListViewEntries() → isPartnerUser() → returns TRUE
   ↓
2. Custom query executes with Partner-specific WHERE clause
   ↓
   SELECT projects WHERE
     - Project assigned TO premtest (smownerid = 6)
     - OR project CREATED BY premtest (createdby = 6)
   ↓
3. Partner sees both:
   - Projects assigned to them now
   - Projects they created earlier (even if assigned elsewhere)
   ↓
4. Partner clicks on a project they created
   ↓
   Record.php → isPermitted('read') → isPartnerUser() → returns TRUE
   ↓
   CHECK: createdby = 6 OR smownerid = 6 → TRUE
   ↓
5. Record opens successfully
```

---

## SQL Behavior Comparison

### Default Vtiger Behavior (without customization)

```sql
-- Partner sees only records assigned to them
SELECT * FROM vtiger_project
INNER JOIN vtiger_crmentity ON crmentity.crmid = project.projectid
WHERE smownerid = 6  -- Only current owner
```

**Result**: Partner can't see projects after assigning them away

### With Our Customization

```sql
-- Partner sees records they own OR created
SELECT * FROM vtiger_project
INNER JOIN vtiger_crmentity ON crmentity.crmid = project.projectid
WHERE (
    smownerid = 6        -- Current owner
    OR createdby = 6     -- Creator
)
```

**Result**: Partner can see all their projects, past and present

---

## Permission Levels

### What Partner CAN Do

✅ **List View**:
- See all projects they created OR are assigned to
- Filter and search those projects
- Click to open

✅ **Detail View**:
- View full project details
- See assigned tasks
- View comments
- Edit fields (if edit permission = 1)

✅ **Edit View**:
- Modify project details
- Change status, dates, etc.
- Reassign to other users
- Add comments

### What Partner CANNOT Do

❌ **Delete**: Projects cannot be deleted by Partner  
❌ **Export**: Project list cannot be exported by Partner  
❌ **See Others' Projects**: Only see projects they created or own  
❌ **Manage Team**: Cannot access other Partners' projects  

---

## Configuration

This enhancement is **automatic** after modifying the two model files. No configuration needed.

However, to activate it ensure:

1. **Partner role exists** (H6) - Check: Admin > Users > Roles
2. **Project module enabled** - Check: Admin > Modules & Packages
3. **Partner has Project permissions** - Check via `install_project_notification_db.php`
   - Create = 1
   - Read = 1
   - Edit = 1
   - Delete = 0
   - Export = 0
   - Assign = 1

---

## Testing Guide

### Test 1: Partner Creates and Assigns Project

```
1. Login as Partner (premtest)
2. Go to Project module
3. Click "New"
4. Fill in:
   - Project Name: "Test Marketing"
   - Status: "Initiated"
   - Start Date: Today
5. Save → Project created with Partner as owner
6. Edit project
7. Change "Assigned To": premtest → Admin User
8. Save
9. Refresh list view
   ✓ Project still visible in Partner's list
   ✓ Project now shows "Admin User" as owner
10. Click on project to open
    ✓ Record opens (Partner is creator)
    ✓ Partner can still edit
```

### Test 2: Verify "Created By" Field

```
1. Partner creates project and assigns to Admin
2. Admin opens the same project
3. Check "Created By" field → shows "premtest"
4. Admin can see who created it
5. Partner can edit because createdby=6 matches current user
```

### Test 3: Partner Cannot See Others' Projects

```
1. Login as Partner User 1 (premtest)
2. Project list shows only their projects
3. Login as Partner User 2 (different partner)
4. Project list shows ONLY their projects
5. Cannot see Partner 1's projects
✓ Data isolation maintained - Partners don't spy on each other
```

### Test 4: Admin Can See All Projects

```
1. Login as Admin
2. Project list shows ALL projects
3. Admin can see projects created by anyone
4. No restriction for Admin role
✓ Admin visibility unchanged
```

---

## Database Impact

No new tables created. Uses existing Vtiger tables:

- `vtiger_project` - Project records
- `vtiger_crmentity` - Core fields including:
  - `smownerid` - Current owner (manager)
  - `createdby` - Original creator

---

## Performance Considerations

### Query Performance

- **Before**: Simple query (only smownerid check)
- **After**: OR condition (smownerid OR createdby)

**Impact**: Minimal - both are indexed columns
**Index Used**: `idx_crmentity_smownerid`, `idx_crmentity_createdby`

### Pagination

- Custom method handles pagination correctly
- Uses `$pageLimit` and `$pageNumber`
- No performance degradation

---

## Compatibility

- ✅ Vtiger 8.1
- ✅ PHP 7.x and 8.x
- ✅ MySQL 5.7+
- ✅ Works with VDNotifierPro
- ✅ Works with ProjectTask module
- ✅ Works with ProjectMilestone module

---

## Troubleshooting

### Issue: Partner still can't see their created projects

**Solution**:
1. Verify Partner role is H6:
   ```
   SELECT * FROM vtiger_role WHERE name = 'Partner'
   ```
2. Verify user assigned to Partner role:
   ```
   SELECT * FROM vtiger_user2role WHERE userid = 6
   ```
3. Clear Vtiger cache:
   - Admin > System > Maintenance > Clear All Cache
4. Check logs: `logs/` directory

### Issue: Partner can see all projects (not filtered)

**Solution**:
1. Verify `createdby` field is populated:
   ```
   SELECT projectid, createdby FROM vtiger_crmentity WHERE crmid IN (SELECT projectid FROM vtiger_project)
   ```
2. Check if Partner flag is working:
   - Open browser console (F12)
   - Create test project
   - Verify `isPartnerUser()` returns true
3. Review ListView.php isPartnerUser() method

### Issue: Permission denied when opening project

**Solution**:
1. Check Record.php is loading:
   ```
   grep -n "isPartnerUser" modules/Project/models/Record.php
   ```
2. Verify fields are being retrieved:
   ```
   echo $recordModel->get('created_by');
   echo $recordModel->get('smownerid');
   ```
3. Clear cache and try again

---

## Future Enhancements

1. **Group Access**: Allow Partners to see projects assigned to their group
2. **Department View**: Show related projects from department
3. **Audit Trail**: Log when Partner accesses created projects
4. **Notifications**: Alert Partner when their created projects are modified
5. **Archive**: Keep archive of all Partner-created projects for reporting

---

## Support

For issues or questions:
1. Check logs: `storage/project_notification_install.log`
2. Review this README
3. Test with non-Partner user first (should work normally)
4. Verify database queries manually

---

**Files Affected**: 
- `/modules/Project/models/ListView.php`
- `/modules/Project/models/Record.php`

**No core Vtiger files modified** - All changes in Project module folder only.
