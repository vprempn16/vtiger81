# Quick Implementation Guide - Promotional Material Module

## 🚀 Quick Start (5 Minutes)

### Step 1: Verify Partner Role
```
Open in browser:
https://your-vtiger-instance/verify_partner_role.php
```
**Check Output**:
- ✓ Partner Role (H6) found
- ✓ Partner user (premtest) found
- ✓ Required modules: Leads, Contacts, Project accessible
- ✓ Own-record-only restrictions in place

### Step 2: Install Promotional Material Module
```
Open in browser:
https://your-vtiger-instance/install_promotional_material_db.php
```
**Expected Output**:
```
✓ Created vtiger_promotionalmaterial table
✓ Created vtiger_promotionalmaterial_documents table
✓ Registered module in vtiger_entityname (tabid: 58)
✓ Registered module in vtiger_ws_entity (id: 58)
✓ Installation Complete
```

### Step 3: Clear Cache in Vtiger Admin
```
1. Go to: Admin > System > Maintenance > Clear All Cache
2. Click "Clear Cache" button
3. Wait for "All caches cleared successfully"
4. Log out and log back in
```

### Step 4: Enable Module (if not auto-enabled)
```
1. Go to: Admin > Modules and Packages > Module Manager
2. Find "PromotionalMaterial" in the list
3. If status is "Inactive", click checkbox and then "Enable"
4. Refresh page and verify "Active" status
```

### Step 5: Verify Tab Appears
```
1. Should see "Promotional Material" in tab bar at top
2. Click on it to access the module
3. Try creating a test promotional material
```

---

## ✅ What Was Created

### Module Files (in `/modules/PromotionalMaterial/`)
```
✓ PromotionalMaterial.php               - Main module class
✓ PromotionalMaterialEventHandler.php   - Handles notifications on publish
✓ schema.xml                            - Database table definitions
✓ README.md                             - Module documentation
✓ handlers/PromotionalMaterialHandler.php - REST API handler
```

### Installation/Verification Scripts (in Vtiger root)
```
✓ install_promotional_material_db.php   - One-click installation script
✓ verify_partner_role.php               - Partner role verification
```

### Documentation
```
✓ PARTNER_IMPLEMENTATION_GUIDE.md       - Complete implementation guide
✓ QUICK_REFERENCE_GUIDE.md              - This file
```

---

## 📋 Database Tables Created

### Three new tables in database:
1. **vtiger_promotionalmaterial** - Main promotional material records
   - Fields: promotionalmaterialid, title, description, status, publisheddate
   
2. **vtiger_promotionalmaterial_documents** - Document attachments
   - Fields: documentid, promotionalmaterialid, filename, filepath, filetype, filesize
   
3. **vtiger_promotionalmaterialcf** - Custom fields support
   - Extensible for future custom fields

---

## 🔐 Partner Permissions Configured

### Partner (H6) Role Permissions for PromotionalMaterial
```
✓ Can VIEW published materials
✓ Can DOWNLOAD document attachments
✗ Cannot CREATE promotional materials
✗ Cannot EDIT promotional materials
✗ Cannot DELETE promotional materials
```

---

## 🧪 Quick Test Cases

### Test 1: As Admin - Create & Publish Material
```
1. Log in as Admin
2. Click "Promotional Material" tab
3. Click "New" button
4. Fill in:
   - Title: "Test Material"
   - Description: "This is a test"
   - Status: Draft
5. Click Save
6. Open record again, change Status to "Published"
7. Click Save
8. Check Partner email for notification
```

### Test 2: As Partner - View Published Material
```
1. Log in as Partner (premtest)
2. Click "Promotional Material" tab
3. Should see the material you published
4. Should NOT see Draft materials
5. Click on material to view details
6. Should see download button (if documents attached)
7. Should NOT see Edit or Delete buttons
```

### Test 3: @Mention Notification
```
1. Log in as Admin
2. Open any Project record
3. Go to Comments section
4. Add comment: "@premtest Please review this"
5. Save comment
6. Check Partner email for mention notification
```

---

## ⚙️ Configuration Checklist

- [ ] Run verify_partner_role.php and verify all checks pass
- [ ] Run install_promotional_material_db.php and verify success
- [ ] Clear Vtiger cache (Admin > System > Maintenance)
- [ ] Verify PromotionalMaterial tab appears in main navigation
- [ ] Verify Partner can see PromotionalMaterial tab
- [ ] Test creating/publishing promotional material
- [ ] Test Partner can view published material only
- [ ] Test Partner cannot create/edit/delete materials
- [ ] Test @mention notifications work
- [ ] Test email notifications are sent

---

## 🐛 Troubleshooting Quick Links

### PromotionalMaterial tab not showing?
- Clear cache: Admin > System > Maintenance > Clear All Cache
- Check module enabled: Admin > Modules and Packages
- Restart browser session

### Partner seeing all records instead of published only?
- Check PromotionalMaterialHandler.php permissions logic
- Verify status field has "Draft" or "Published" values
- Test SQL: `SELECT * FROM vtiger_promotionalmaterial WHERE status = 'Published'`

### Notifications not sending?
- Verify email config: Config > Settings > Email
- Check VDNotifierPro is enabled
- Review logs in `/logs/` directory
- Check Partner user has valid email address

### Tab ID already exists?
- Edit install_promotional_material_db.php
- Change `$tabid = 58` to next available ID
- Re-run installation script

---

## 📚 Next Steps After Installation

1. **Configure Advanced Permissions** (Optional)
   - Set specific partners to see specific materials
   - Configure material expiration dates
   - Set content version control

2. **Set Up Analytics** (Optional)
   - Track downloads by partner
   - Monitor material views
   - Generate partner engagement reports

3. **Mobile App** (Optional)
   - Enable PromotionalMaterial in mobile app
   - Test on partner mobile device
   - Configure push notifications

4. **Additional Notification Types** (Optional)
   - Record assignment notifications (Project)
   - Record update notifications
   - Bulk publish notifications

---

## 📞 Support

### If you encounter issues:

1. **Check the Complete Guide**:
   - Read: `PARTNER_IMPLEMENTATION_GUIDE.md`
   - Full details and troubleshooting

2. **Verify Installation**:
   - Run: `verify_partner_role.php`
   - Run: Manual tests from "Quick Test Cases"

3. **Review Logs**:
   - Check: `logs/` directory for errors
   - Check: Web server error log

4. **Database Verification**:
   - Run database queries to verify tables exist
   - Check module is registered in vtiger_entityname
   - Check module is registered in vtiger_ws_entity

---

## ℹ️ Module Information

- **Module Name**: PromotionalMaterial
- **Tab ID**: 58
- **WebService ID**: 58
- **Main Table**: vtiger_promotionalmaterial
- **Handler Class**: PromotionalMaterialHandler
- **Event Handler**: PromotionalMaterialEventHandler
- **Role**: Partner (H6)
- **Status**: Ready for Installation

---

**Implementation Time**: ~15-20 minutes  
**Difficulty Level**: Intermediate  
**Risk Level**: Low (uses standard Vtiger patterns)

*Last Updated: March 31, 2026*
