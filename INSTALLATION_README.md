VTIGER CRM MASTER MODULE INSTALLATION INSTRUCTIONS
===================================================

This document provides complete configuration instructions for your system after installing the MyMassInstaller.

The MyMassInstaller.zip package will automatically extract and install four underlying modules simultaneously:
1. PromotionalMaterial
2. VDNotifierPro
3. VTAtomCommentsMentions
4. BranchUsers

--------------------------------------------------------------------------------
PART 1: MODULE INSTALLATION
--------------------------------------------------------------------------------

Step 1: Uploading the Master Installer
1. Navigate to Settings (gear icon) > Module Manager
2. Click the Import Module button
3. Upload the MyMassInstaller.zip file
4. Follow the installation wizard and click finish.
5. Note: Behind the scenes, the installer will unpack and install your 4 modules, and then it will cleanly delete itself from the system!

--------------------------------------------------------------------------------
PART 2: CORE CONFIGURATION
--------------------------------------------------------------------------------

Step 2: Outgoing Server Configuration
1. Navigate to Settings > Outgoing Server
2. Configure SMTP settings (Server name, Port, Username, Password, and Authentication method)
3. Test email configuration with Send Test Email
4. Ensure "Default Outgoing Server" is selected

Step 3: Partner Role Configuration
1. Navigate to Settings > Roles
2. Create the "Partner" role.
3. Note: There is no need to update any backend files. The system will automatically detect the correct role ID using the "Partner" role name.
4. Disable the necessary modules precisely as shown in the system configuration.

--------------------------------------------------------------------------------
PART 3: AUTOMATION & PERMISSIONS
--------------------------------------------------------------------------------

Step 4: Email Template Configuration
1. Navigate to Settings > Templates
2. Create a new email template:
   - Template Name: PMPartnerEmailTemplate
   - Module: Users
   - Subject: New Promotional Material Available: ###PM_RECORD_TITLE###
   - Body: 
     Dear Partner,
     A new promotional material has been published:
     Title: ###PM_RECORD_TITLE###
     View Details: ###PM_DETAIL_URL###
     Please log in to view and download the materials.

Step 5: Workflow Creation for Partner Notifications
1. Navigate to Settings > Workflows
2. Create a new workflow for PromotionalMaterial:
   - Condition: When Promotional Status equals "Published"
3. Add Two Invoke Custom Function tasks:
   - Task 1: PMNotifyPartners (sends real-time notifications via VDNotifierPro)
   - Task 2: PMSendPartnerEmail (sends previously created email template to Partners)

Step 6: Partner Profile Permissions
1. Navigate to Settings > Profiles
2. Configure permissions specifically for Partner profiles:
   - Allow: PromotionalMaterial module access
   - Allow: DetailView (to view published materials)
   - Allow: Download attachments
   - Deny: CreateView, EditView, Delete

Step 7: Sharing Rules Configuration
1. Navigate to Settings > Sharing Rules
2. Establish the exact sharing access rules for the Partner Role regarding the PromotionalMaterial and BranchUsers modules.

--------------------------------------------------------------------------------
PART 4: BRANCH & PROJECT SETUP (BRANCHUSERS MODULE)
--------------------------------------------------------------------------------

Step 8: Branch & User Hierarchy Configuration
1. Branch Creation: Navigate to the BranchUsers module and create branches. Assign a "Branch Manager" and Branch Code to each.
2. User Assignment: In User Management, ensure every user is mapped to their appropriate Branch.
3. Hierarchy Mapping: Establish Parent-Child relationships. Branch managers should be the "Parent User" for their respective staff to ensure correct data visibility and reporting.

Step 9: Advanced Project Security & Mentions
1. Branch-Based Isolation: The system automatically filters the Project List View. Users will only see projects that belong to their own branch or branches they manage.
2. Mention-Based Access:
   - Notification: When a user is @mentioned in a project comment, they receive an immediate notification via VDNotifierPro.
   - Dynamic Access: Mentioning a user automatically grants them access to see that specific project in both the List View and Detail View, bypassing standard branch restrictions.
3. Permission Restrictions (Security Model):
   - View-Only Access: Users granted access via a Mention are restricted to "Read-Only". They can view details but cannot use EditView, Delete, or Save.
   - Management Access: Only the Record Owner (Assigned To) and the Record Creator retain full permissions to edit or delete the record.
   - Inline Editing: Inline editing in the List View is automatically blocked for users who only have mention-based access.

