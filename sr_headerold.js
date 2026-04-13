$(function() {
    var moduleName = app.getModuleName();
    var moduleView = app.getViewName();
    var mode = $('input[name="module"]').val();

    // Update Subject based on select field
    if (moduleName == 'Calendar' && moduleView == 'Edit' && mode == 'Events') {
        $(document).on('change', 'select[name="cf_1027"]', function() {
            $('#Events_editView_fieldName_subject').val($(this).val());
        });
    }

    if (moduleName == 'Invoice' && moduleView == 'Edit') {
        jQuery('input[name="account_id"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data) {
            fetchAccountFieldInfo(data.record);
        });
        if (jQuery('input[name="account_id"]').val()) {
            fetchAccountFieldInfo(jQuery('input[name="account_id"]').val());
        }
    }

});

// Add pencil icon for date section
function addInlineEdit(field_name) {
    var fieldValue = $('#Events_detailView_fieldValue_' + field_name).find('.value').text();

    $('#Events_detailView_fieldValue_' + field_name).append('<span class="hide edit pull-left"><input type="hidden" class="fieldBasicData" data-name="' + field_name + '" data-type="date" data-displayvalue="" data-value="' + fieldValue.trim() + '"></span><span class="action pull-right"><a href="#" onclick="return false;" class="sr-inline-edit fa fa-pencil"></a></span>');
}


let oldCf1025Value = $("#Events_editView_fieldName_cf_1025").val();

$(document).on('focus', '#Events_editView_fieldName_cf_1023, [name="cf_1023"]', function() {
    // Capture value before user changes
    //oldCf1025Value = $("#Events_editView_fieldName_cf_1025").val();
});

// Update End Date
$(document).on('change', '#Events_editView_fieldName_cf_1023 ,[name="cf_1023"]', function() {
    var startDate = $(this).val();
    var subject = $('#Events_editView_fieldName_subject').val();
console.log('cf_1025.sa',oldCf1025Value);

    
	if (!startDate || startDate.trim() === '') {
        $("#Events_editView_fieldName_cf_1025").val(oldCf1025Value);

        if (app.getViewName() == 'Detail') {
            $('#Events_detailView_fieldValue_cf_1025')
                .find('span.value')
                .text(oldCf1025Value);
        }
        return;
    }
	
    // ===== FIX 2: Validate format =====
    let parts = startDate.split('-'); // Split the date
    if (parts.length !== 3) {
        console.warn("Invalid start date format");
        return;
    }

    let day = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10) - 1; // Month index starts from 0
    let year = parseInt(parts[2], 10);

    let date = new Date(year, month, day); // Create a Date object

    // ===== FIX 3: Stop if invalid date =====
    if (isNaN(date.getTime())) {
        console.warn("Invalid Date object");
        return;
    }


    if (subject == '' || subject == undefined && app.getViewName() == 'Detail') {
        subject = $('#Events_detailView_fieldValue_subject').text().trim();
    }


    if (subject.indexOf('PREPOSTO') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('SOCCORSO') != -1) {
        date.setFullYear(date.getFullYear() + 3);
    } else if (subject.indexOf('soccorso') != -1) {
        date.setFullYear(date.getFullYear() + 3);
    } else if (subject.indexOf('FARMACIE') != -1) {
        date.setFullYear(date.getFullYear() + 1);
    } else if (subject.indexOf('HACCP') != -1) {
        date.setFullYear(date.getFullYear() + 3);
    } else if (subject.indexOf('RLS') != -1) {
        date.setFullYear(date.getFullYear() + 1);
    } else if (subject.indexOf('emissioni') != -1) {
        date.setFullYear(date.getFullYear() + 4);
    } else if (subject.indexOf('ponteggi') != -1) {
        date.setFullYear(date.getFullYear() + 4);
    } else if (subject.indexOf('stress') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('BIENNALE') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('ANNUALE') != -1) {
        date.setFullYear(date.getFullYear() + 1);
    } else if (subject.indexOf('SICUREZZA') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('VALUTAZIONE') != -1) {
        date.setFullYear(date.getFullYear() + 4);
    } else if (subject.indexOf('DL/RSPP') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('PRIMO SOCCORSO') != -1) {
        date.setFullYear(date.getFullYear() + 3);
    } else if (subject.indexOf('ANTINCENDIO') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('CARRELLI') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('DPI') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('PLE') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('GRU') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('CARROPONTE') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else if (subject.indexOf('STRESS') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('VERIFICA MESSA A TERRA BIENNALI') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('RUMORE') != -1) {
        date.setFullYear(date.getFullYear() + 4);
    } else if (subject.indexOf('VIBRAZIONI') != -1) {
        date.setFullYear(date.getFullYear() + 4);
    } else if (subject.indexOf('CHIMICO') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('EMISSIONI') != -1) {
        date.setFullYear(date.getFullYear() + 2);
    } else if (subject.indexOf('VERIFICA MESSA A TERRA QUINQUENNALI') != -1) {
        date.setFullYear(date.getFullYear() + 5);
    } else {
        date.setFullYear(date.getFullYear() + 5);
    }

    let newDate = date.toLocaleDateString('en-GB').replace(/\//g, '-');

    updateFieldValue('cf_1025', newDate);

    // Set the value to the field
    $("#Events_editView_fieldName_cf_1025").val(newDate);
    if (app.getViewName() == 'Detail') {
        $('#Events_detailView_fieldValue_cf_1025').find('span.value').text(newDate)

    }
});


// Update field value
function updateFieldValue(name, value) {
    var record = $('input[name="record_id"]').val();
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
            'value': value,
            'field': name,
            'record': record,
            'module': 'Events',
            'action': 'SaveAjax'
        },
        dataType: 'JSON',
        success: function(response) {
            console.log('response', response);
        }
    });
}


// Show input box
$(document).on('click', '.sr-inline-edit', function(e) {
    var currentTarget = jQuery(e.currentTarget);
    currentTarget.hide();
    var currentTdElement = currentTarget.closest('td.fieldValue');
    var detailViewValue = jQuery('.value', currentTdElement);
    var editElement = jQuery('.edit', currentTdElement);

    var fieldBasicData = jQuery('.fieldBasicData', editElement);
    var fieldName = fieldBasicData.data('name');
    var fieldType = fieldBasicData.data('type');
    var rawValue = fieldBasicData.data('value');

    console.log(fieldBasicData, rawValue);

    var fieldInfo = {
        column: fieldName,
        "date-format": "mm-dd-yyyy",
        defaultvalue: false,
        label: "",
        mandatory: false,
        masseditable: true,
        name: fieldName,
        presence: true,
        quickcreate: false,
        type: "date",
        validator: [],
        value: rawValue,
    };


    var fieldObject = Vtiger_Field_Js.getInstance(fieldInfo);
    var fieldModel = fieldObject.getUiTypeModel();

    var ele = jQuery('<div class="input-group editElement"></div>');
    var actionButtons = '<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlineAjaxSave"><i class="fa fa-check"></i></span>';
    actionButtons += '<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlineAjaxCancel"><i class="fa fa-close"></i></span>';

    //wrapping action buttons with class called input-save-wrap
    var inlineSaveWrap = jQuery('<div class="input-save-wrap"></div>');
    inlineSaveWrap.append(actionButtons);

    // we should have atleast one submit button for the form to submit which is required for validation
    ele.append(fieldModel.getUi()).append(inlineSaveWrap);
    ele.find('.inputElement').addClass('form-control');
    editElement.append(ele);

    detailViewValue.css('display', 'none');
    editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();
    var contentHolder = getDetailViewContainer();
    var vtigerInstance = Vtiger_Index_Js.getInstance();
    vtigerInstance.registerAutoCompleteFields(contentHolder);
    vtigerInstance.referenceModulePopupRegisterEvent(contentHolder);

    jQuery('.sr-inline-edit').addClass('hide');

});

function getDetailViewContainer() {

    detailViewContainer = jQuery('.detailViewContainer');
    return detailViewContainer;
}


// Mass Edit action
jQuery(document).ready(function(e) {

    function addEditButton() {
        if (jQuery('.listViewActionsContainer #Calendar_listView_massAction_LBL_EDIT').length === 0) {
            var editButton = '<button type="button" class="btn btn-default" id="Calendar_listView_massAction_LBL_EDIT" href="javascript:void(0);" onclick="Vtiger_List_Js.triggerMassEdit(\'index.php?module=Events&view=MassActionAjax&mode=showMassEditForm\');" title="Edit" disabled><i class="fa fa-pencil"></i></button>';

            jQuery('.listViewActionsContainer #Calendar_listView_massAction_LBL_DELETE').before(editButton);
        }
    }

    if (app.getModuleName() == "Calendar" && app.getViewName() == "List") {
        addEditButton();
    }

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.hasOwnProperty("url")) {
            var url = settings.url;
            if (url != '') {
                var params = new URLSearchParams(url.split('?')[1]);
                var module = params.get('module');
                var view = params.get('view');
                if (module == "Calendar" && view == "List") {
                    addEditButton();
                }
            }
        }
    });

});


// Update Account custom field value to Invoice
function fetchAccountFieldInfo(recordId) {
    $.ajax({
        url: 'index.php?module=Accounts&view=AccountInfo&record=' + recordId,
        type: 'GET',
        dataType: 'JSON',
        success: function(response) {
            $('select[name="cf_1037"]').val(response.customFieldValue).trigger('change');
        }
    });
}


// Show/Hide report module folders 
jQuery.Class("ReporstHiddenHeader", {}, {
    registerAddMenu: function() {
        if (app.getModuleName() !== 'Reports') return;

        jQuery(document).on('shown.bs.popover', '[rel="popover"]', function() {
            var $trigger = jQuery(this);
            var folderId = $trigger.data('id');
            setTimeout(function() {
                var $popover = jQuery('.popover:visible');
                if (!$popover.length) return;

                if ($popover.find('.hideFilter').length === 0) {
                    var $delete = $popover.find('.deleteFilter');
                    if ($delete.length) {
                        var $hide = jQuery(`
							   <li role="presentation" class="hideFilter" data-id="${folderId}">
								   <a role="menuitem" href="#"><i class="fa fa-eye-slash"></i>&nbsp;Hide</a>
							   </li>
						   `);
                        $delete.after($hide);
                    }
                }
            }, 100);
        });
        jQuery(document).on('click', '.hideFilter a', function(e) {
            e.preventDefault();
            var $li = jQuery(this).closest('.listViewFilter');
            var folderId = jQuery(this).closest('li').data('id');

            var message = app.vtranslate('Are you sure want to hide the folder?');
            app.helper.showConfirmationBox({
                'message': message
            }).then(function(data) {
                    jQuery('.listViewFilter').has(`[data-filter-id="${folderId}"]`).hide();
                    jQuery('[rel="popover"]').popover('hide').removeClass('rotate activePopover');
                    var params = {};
                    params['module'] = 'Reports';
                    params['action'] = 'SaveHiddenFolder';
                    params['mode'] = 'add';
                    params['folderid'] = folderId;
                    app.request.get({
                        data: params
                    }).then(function(err, data) {
                        if (err === null && data) {

                        } else {

                        }
                    });
                },
                function(error, err) {});

        });
        jQuery(document).on('click', '.show-folder', function() {
            var message = app.vtranslate('Are you sure want to show hidden folder?');
            var ele = jQuery(this);
            app.helper.showConfirmationBox({
                'message': message
            }).then(function(data) {
                    var folderId = ele.attr('data-id');
                    var params = {};
                    params['module'] = 'Reports';
                    params['action'] = 'SaveHiddenFolder';
                    params['mode'] = 'UnhideFolder';
                    params['folderid'] = folderId;
                    app.request.get({
                        data: params
                    }).then(function(err, data) {
                        jQuery(`a[data-filter-id="${folderId}"]`).closest('li').show();
                        jQuery(`span[data-id="${folderId}"]`).closest('tr').remove();
                    });
                },
                function(error, err) {});
        });
    },
    hideSavedFolders: function() {
        var params = {};
        params['module'] = 'Reports';
        params['action'] = 'SaveHiddenFolder';
        params['mode'] = 'get';
        app.request.get({
            data: params
        }).then(function(err, data) {
            var data = JSON.parse(data);
            if (Array.isArray(data)) {
                data.forEach(function(id) {
                    jQuery('.listViewFilter').has(`[data-filter-id="${id}"]`).hide();
                });
            }
        });
    },
    appendHiddenReportsButton: function() {
        let $container = jQuery('.list-group');
        if ($container.length && !$container.find('#Reports_listView_hiddenreports').length) {
            let $deleteBtn = $container.find('.toggleFilterSize');
            let $hiddenBtn = jQuery(`
					   <span class="toggleFilterSize" id="Reports_listView_hiddenreports" style="float: right;cursor: pointer;font-size: 11px;padding-right: 20px;color: #15c;"> Hidden Folders </span>
				   `);
            $deleteBtn.after($hiddenBtn);
        }
    },
    showModalForHiddenFolders: function() {
        var thisInstance = this;
        jQuery(document).on('click', '#Reports_listView_hiddenreports', function() {
            var params = {
                module: 'Reports',
                view: 'GetHiddenFolders',
                mode: 'get'
            };
            app.request.get({
                data: params
            }).then(function(err, data) {
                if (err === null && data) {
                    var response = JSON.parse(data);
                    folders = response.data;
                    if (Object.keys(folders).length > 0) {
                        thisInstance.showHiddenFoldersModal(folders);
                    } else {
                        app.helper.showAlertNotification({
                            message: 'No hidden folders found.'
                        });
                    }
                }
            });
        });
    },
    showHiddenFoldersModal: function(folders) {
        let tableRows = '';
        jQuery.each(folders, function(id, label) {
            tableRows += `
			   <tr>
				   <td>${label}</td>
				   <td><span class="show-folder" style="cursor: pointer;color: blue; float:right;" data-id="${id}">Show</span></td>
			   </tr>
		   `;
        });

        const modalHTML = `
		   <div class="modal fade" id="hiddenFoldersModal" tabindex="-1" role="dialog">
			 <div class="modal-dialog" role="document">
			   <div class="modal-content">
				 <div class="modal-header ">
				   <h4 class="modal-title col-md-11">Hidden Report Folders</h4>
				   <button type="button" class="close" data-dismiss="modal">&times;</button>
				 </div>
				 <div class="modal-body">
				   <table class="table">
	   					<thead>
	   						<tr>
	   							<th>Folder Name</th>
	   							<th style="float: right;"> Action</th>
	   						</tr>
	   					</thead>
	   					
					   <tbody>${tableRows}</tbody>
				   </table>
				 </div>
			   </div>
			 </div>
		   </div>`;


        jQuery('#hiddenFoldersModal').remove();
        jQuery('body').append(modalHTML);
        jQuery('#hiddenFoldersModal').modal('show');
    },
    checkIfInHiddenFolder: function() {
        var params = new URLSearchParams(window.location.search);
        var viewname = params.get('viewname');
        if (!viewname || viewname === '') {
            viewname = jQuery('input[name="folder"]').val();
        }
        if (viewname !== null && viewname !== '') {
            var requestParams = {
                module: 'Reports',
                action: 'SaveHiddenFolder',
                mode: 'get'
            };

            app.request.get({
                data: requestParams
            }).then(function(err, data) {
                try {
                    var hiddenIds = JSON.parse(data);
                    if (Array.isArray(hiddenIds)) {
                        if (hiddenIds.includes(viewname)) {
                            params.set('viewname', 'All');
                            window.location.search = params.toString();
                        }
                    }
                } catch (e) {
                    console.error('Failed to parse hidden folders response', e);
                }
            });
        }
    },
    registerEvents: function() {
        var thisInstance = this;
        thisInstance.registerAddMenu();
        thisInstance.hideSavedFolders();
        thisInstance.appendHiddenReportsButton();
        thisInstance.showModalForHiddenFolders();
        thisInstance.checkIfInHiddenFolder();
    }
});

jQuery(document).ready(function(e) {
    var instance = new ReporstHiddenHeader();
    instance.registerEvents();
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.hasOwnProperty("url")) {
            var url = settings.url;
            if (url != '') {
                var params = new URLSearchParams(url.split('?')[1]);
                var view = params.get('view');
                if (view == 'List' && params.has('viewname')) {
                    instance.appendHiddenReportsButton();
                    instance.checkIfInHiddenFolder();
                }
            }
        }
    });
});


// ***********************	    Bulk Comment    ***********************
// Add bulk comment button in list view
$(document).on('change', '.listViewEntriesCheckBox, .listViewEntriesMainCheckBox', function() {
    var moduleName = app.getModuleName();
    var moduleView = app.getViewName();
    if (moduleView == 'List' && (moduleName == 'Calendar')) {
        var checkedCount = $('.listViewEntriesCheckBox:checked').length;
        if (checkedCount > 0) {
            if ($('#add_bulk_comment').length == 0) {
                $('.listViewMassActions .dropdown-menu').append('<li><a href="javascript:void(0);" class="shareEvent" id="add_bulk_comment">Aggiungi Commento</a></li>');
            }
        } else {
            $('.listViewMassActions .dropdown-menu').find('#add_bulk_comment').remove();
        }
    }
});

// Check if at least one record is selected to add comment and show modal to add comment
$(document).on('click', '#add_bulk_comment', function() {
    var checkedCount = $('.listViewEntriesCheckBox:checked').length;
    if (checkedCount <= 0) {
        app.helper.showErrorNotification({
            message: "Please select at least one record to add comment."
        });
        return false;
    }

    // Create modal HTML with textarea and submit button
    var modalHTML = '<div class="modal-dialog">' +
        '<div class="modal-content">' +
        '<form class="form-horizontal" id="sr_bulk_comment_form" method="post">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
        '<h4 class="modal-title">Add Comment</h4>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="container-fluid">' +
        '<div class="row">' +
        '<textarea class="col-lg-12 form-control" name="commentcontent" id="sr_commentcontent" rows="5" placeholder="Write your comment here..." data-rule-required="true"></textarea>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="modal-footer">' +
        '<center>' +
        '<button class="btn btn-success" type="submit" name="saveButton" id="sr_submit_comment"><strong>Submit</strong></button>' +
        '<a href="#" class="cancelLink" type="reset" data-dismiss="modal">Cancel</a>' +
        '</center>' +
        '</div>' +
        '</form>' +
        '</div>' +
        '</div>';

    // Show modal using app.helper.showModal
    app.helper.showModal(modalHTML, {
        'cb': function(container) {
            // Register submit button click event
            container.find('#sr_submit_comment').on('click', function(e) {
                e.preventDefault();
                var commentText = container.find('#sr_commentcontent').val();
                if (commentText.trim() == '') {
                    app.helper.showErrorNotification({
                        message: "Please enter a comment."
                    });
                    return false;
                }

                // Get all checked record IDs
                var selectedIds = [];
                $('.listViewEntriesCheckBox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                // Show progress				
                app.helper.showProgress();

                // Add bulk comment
                app.request.post({
                    url: 'index.php',
                    data: {
                        module: 'Events',
                        action: 'AddBulkCommentAjax',
                        selected_ids: selectedIds,
                        commentcontent: commentText
                    }
                }).then(function(err, data) {
                    app.helper.hideProgress();

                    if (err) {
                        app.helper.showErrorNotification({
                            message: "Error saving comment."
                        });
                    } else {
                        app.helper.showSuccessNotification({
                            message: "Comment added successfully."
                        });
                        app.helper.hideModal();
                    }
                });

            });
        }
    });
});
// ******************   Bulk comment End    **********************

// ******************		Apply Account Info on Contact quick create form on Events module 		******************

// Store account info when parent_id changes (handles both user interaction and programmatic updates)
window.accountInfo = {};
jQuery(document).ready(function(e) {
    // Function to update account info
    var updateAccountInfo = function($field) {
        if (!$field || $field.length === 0) return;

        var moduleName = app.getModuleName();
        var moduleView = app.getViewName();

        if (moduleView === 'Edit' && (moduleName === 'Calendar' || moduleName === 'Events')) {
            var $fieldContainer = $field.closest('.referencefield-wrapper, .fieldValue, td');
            var parentModule = $fieldContainer.find('input[name="popupReferenceModule"]').val();

            if (parentModule === 'Accounts') {
                window.accountInfo = {
                    parentId: $fieldContainer.find('input[name="parent_id"]').val() || $('input[name="parent_id"]').val(),
                    recordName: $field.val()
                };
            }
        }
    };
});
// Handle change and input events (user interaction)
$(document).on('change input', '#parent_id_display', function() {
    updateAccountInfo($(this));
});

// Polling mechanism to detect programmatic value changes
jQuery(document).ready(function(e) {
    var lastParentIdValue = '';
    var parentIdPollInterval = setInterval(function() {
        var moduleName = app.getModuleName();
        var moduleView = app.getViewName();

        // Only poll when we're in Edit view of Calendar/Events
        if (moduleView === 'Edit' && (moduleName === 'Calendar' || moduleName === 'Events')) {
            var $field = $('#parent_id_display');
            if ($field.length > 0) {
                var currentValue = $field.val() || '';
                if (currentValue !== lastParentIdValue) {
                    lastParentIdValue = currentValue;
                    updateAccountInfo($field);
                }
            }
        }
    }, 300);
});

// Listen for postajaxready event (vtiger's AJAX ready event)
$(document).on('postajaxready', function() {
    var $field = $('#parent_id_display');
    if ($field.length > 0) {
        lastParentIdValue = $field.val() || '';
        updateAccountInfo($field);
    }
});

// Apply account info to contact form when create button is clicked
$(document).on('click', '#Events_editView_fieldName_contact_id_create, .createReferenceRecord', function(e) {
    var $target = $(e.target);
    if ($target.attr('id') !== 'Events_editView_fieldName_contact_id_create' &&
        $target.closest('.createReferenceRecord').find('#Events_editView_fieldName_contact_id_create').length === 0) {
        return;
    }

    var moduleName = app.getModuleName();
    var moduleView = app.getViewName();

    if (moduleView !== 'Edit' || (moduleName !== 'Calendar' && moduleName !== 'Events')) {
        return;
    }

    var applyAccountInfo = function() {
        if (!window.accountInfo || !window.accountInfo.recordName) return false;

        var $modal = $('#QuickCreate, .modal:visible, .myModal');
        var $accountDisplay = $modal.find('input[name="account_id_display"], #account_id_display, input[id*="account_id_display"]');

        if ($accountDisplay.length === 0) return false;

        $accountDisplay.val(window.accountInfo.recordName).trigger('change');
        $modal.find('input[name="account_id"], input[id*="account_id"][type="hidden"]').val(window.accountInfo.parentId);
        $accountDisplay.closest('.referencefield-wrapper, .fieldValue, td, .row')
            .find('input[name="popupReferenceModule"]').val('Accounts');

        // Clear account info after successful application
        window.accountInfo = {};

        return true;
    };

    var tryApply = function() {
        if (!applyAccountInfo()) {
            setTimeout(applyAccountInfo, 500);
        }
    };

    $(document).one('ajaxComplete', function(event, xhr, settings) {
        if (settings.type && settings.type.toUpperCase() === 'POST' && settings.data) {
            var postData = typeof settings.data === 'string' ?
                (settings.data.indexOf('module=Contacts') !== -1 && settings.data.indexOf('view=QuickCreateAjax') !== -1 ?
                    settings.data.split('&').reduce(function(obj, param) {
                        var parts = param.split('=');
                        if (parts.length === 2) obj[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
                        return obj;
                    }, {}) : {}) :
                (typeof settings.data === 'object' && !(settings.data instanceof FormData) ? settings.data : {});

            if (postData.view === 'QuickCreateAjax' && postData.module === 'Contacts') {
                setTimeout(tryApply, 200);
                $(document).one('postajaxready', tryApply);
            }
        }
    });
});

// ******************		Apply Account Info Script End		******************

// **********************    MULTIPLE FILE UPLOADED   **********************

jQuery(document).ready(function() {
    var module = app.getModuleName();
    var view = app.getViewName();

    var btn = `
	<button class="btn btn-primary" id="customUploadBtn">
	    <i class="fa fa-upload"></i> Upload Documents
	</button>`;

    // Contacts → Detail View
    if (module === 'Contacts' && view === 'Detail') {
        jQuery('.detailview-header .btn-toolbar').prepend(btn);
    }

    // Calendar → List View

    if ((module === 'Calendar' || module === 'Accounts') && view === 'List') {
        var btn = `
	<button class="btn addButton btn-default module-buttons" id="customUploadBtn">
	    <i class="fa fa-upload"></i> Upload Documents
	</button>`;
        var addEventBtn = jQuery('#Calendar_listView_basicAction_LBL_ADD_EVENT, #Accounts_listView_basicAction_LBL_ADD_RECORD');
        if (addEventBtn.length) {
            addEventBtn.first().before(btn);
        } else {
            // fallback if selector changes in theme
            jQuery('.listViewActions').prepend(btn);
        }
    }
});

jQuery(document).on('click', '#customUploadBtn', function() {
	let module = app.getModuleName();
    let view = app.getViewName();
	// Check record selected
	if ((module === 'Calendar' || module === 'Accounts') && view === 'List') {
        var listInstance = Vtiger_List_Js.getInstance();
        var selectedRecordCount = listInstance.getSelectedRecordCount();
		
        if (selectedRecordCount === 0) {
            app.helper.showErrorNotification({
                message: "Please select records"
            });
            return;
        }
    }
    // Check if modal already exists to avoid duplicates
    if (jQuery('#docUploadModal').length > 0) {
        // Reset the file input and display text
        jQuery('#docFiles').val('');
        jQuery('#dropZone p').text('Choose an image file.');
        jQuery('#docUploadModal').modal('show');
        return;
    }

    let modalHtml = `
    <div class="modal fade" id="docUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" style="font-weight: 500; color: #555;">Upload Documents</h4>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div class="upload-container">
						<div class="form-group" style="margin-bottom: 20px;">
                            <label style="font-weight: bold; color: #666; display: block; margin-bottom: 10px;">Select Folder</label>
                            <select id="docFolderSelect" class="form-control" style="width: 30%;">
                                <option value="1">Default</option>
                            </select>
                        </div>
                        <label style="font-weight: bold; color: #666; display: block; margin-bottom: 10px;">Upload File</label>
                        <div id="dropZone" style="border: 2px dashed #a5c3d1; border-radius: 5px; padding: 40px; text-align: center; background: #fdfdfd; cursor: pointer;">
                            <div class="upload-icon" style="font-size: 30px; color: #a5c3d1; margin-bottom: 10px;">
                                <i class="fa fa-upload"></i>
                            </div>
                            <p style="color: #888; margin: 0;">Choose an image file or drag it here.</p>
                            <input type="file" id="docFiles" multiple style="display: none;" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f9f9f9; border-top: 1px solid #eee;">
                    <button class="btn btn-primary" id="uploadDocsBtn" style="background-color: #428bca; border-color: #357ebd; padding: 6px 20px;">Upload</button>
                </div>
            </div>
        </div>
    </div>`;

    jQuery('body').append(modalHtml);

    // Logic to trigger file input when clicking the dashed box
    jQuery('#dropZone').on('click', function() {
        jQuery('#docFiles').click();
    });
    jQuery('#docFiles').on('click', function(e) {
        e.stopPropagation();
    });

    // Optional: Show file name when selected - sanitize filename display
    jQuery('#docFiles').on('change', function() {
        let files = this.files;
        if (files.length > 0) {
            // Validate file name length and sanitize for display
            let fileName = files[0].name;
            if (fileName.length > 50) {
                fileName = fileName.substring(0, 47) + '...';
            }
            jQuery('#dropZone p').text(files.length + " file(s) selected: " + fileName);
        }
    });

    jQuery('#docUploadModal').modal('show');
	// Fetch folders
		app.request.post({
			data: {
				module: 'Contacts',
				action: 'GetDocumentFolders'
			}
		}).then(function (err, response) {
			if (!err && response && response.folders) {
				var options = '';
				jQuery.each(response.folders, function (index, folder) {
					var selected = (folder.name === 'Default' || folder.id == 1) ? 'selected' : '';
					options += '<option value="' + folder.id + '" ' + selected + '>' + folder.name + '</option>';
				});
				var selectElem = jQuery('#docFolderSelect');
				selectElem.html(options);
				if (typeof vtUtils !== 'undefined' && vtUtils.showSelect2ElementView) {
					vtUtils.showSelect2ElementView(selectElem);
				} else if (typeof app.showSelect2ElementView !== 'undefined') {
					app.showSelect2ElementView(selectElem);
				} else if (selectElem.select2) {
					selectElem.select2();
				}
			}
		});
});

jQuery(document).on('click', '#uploadDocsBtn', function(e) {
    e.preventDefault();

    let files = jQuery('#docFiles')[0].files;
    if (!files.length) {
        app.helper.showErrorNotification({
            "message": "Please select files"
        });
        return;
    }

    let module = app.getModuleName();
    let view = app.getViewName();
    let recordIds = [];

    // Contacts Detail → single record
    if (module === 'Contacts' && view === 'Detail') {
        recordIds = [app.tRecordId()];
    }

    // Calendar List → multiple selected
    if ((module === 'Calendar' || module === 'Accounts') && view === 'List') {
        var listInstance = Vtiger_List_Js.getInstance();
        var selectedRecordCount = listInstance.getSelectedRecordCount();
        recordIds = listInstance.readSelectedIds();

		
        if (selectedRecordCount === 0) {
            app.helper.showErrorNotification({
                message: "Please select records"
            });
            return;
        }
  }
    let formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('documents[]', files[i]);
    }

    // Send array of recordIds
    formData.append('recordIds', JSON.stringify(recordIds));
    formData.append('module', 'Contacts');
    formData.append('action', 'UploadContactDocuments');
	let folderId = jQuery('#docFolderSelect').val();
			if (folderId) {
				formData.append('folderid', folderId);
			}
    let parentModule = module;

    // Calendar list actually contains Events records
    if (parentModule === 'Calendar') {
        parentModule = 'Events';
    }

    formData.append('parentModule', parentModule);

    app.helper.showProgress();

    app.request.post({
        url: 'index.php',
        data: formData,
        processData: false,
        contentType: false

    }).then(function(response) {
        app.helper.hideProgress();

        jQuery('#docUploadModal').modal('hide');

        app.helper.showSuccessNotification({
            "message": "Documents uploaded successfully"
        });
		
		if(parentModule == 'Contacts') {
		   $('.nav-tabs').find(".tab-item.active[data-module='Documents']").trigger('click');
		}
		
    });
});
// **********************    END MULTIPLE FILE UPLOADED   **********************
