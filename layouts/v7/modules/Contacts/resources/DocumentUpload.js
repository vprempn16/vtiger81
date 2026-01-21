jQuery.Class("Contacts_DocumentUpload_Js", {

}, {
    registerUploadButton: function () {
        let btn = `
        <button class="btn btn-primary" id="customUploadBtn">
            <i class="fa fa-upload"></i> Upload Documents
        </button>`;
        
        // Insert before More button
        jQuery('.detailview-header .btn-toolbar').prepend(btn);
    },

    registerClickEvent: function () {
	    var thisInstance = this;
        jQuery(document).on('click', '#customUploadBtn', function () {
           thisInstance.showUploadModal();
        });
    },

	showUploadModal: function () {
    // Check if modal already exists to avoid duplicates
    if (jQuery('#docUploadModal').length > 0) {
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

    // Optional: Show file name when selected
    jQuery('#docFiles').on('change', function() {
        let files = this.files;
        if(files.length > 0) {
            jQuery('#dropZone p').text(files.length + " file(s) selected: " + files[0].name);
        }
    });

    jQuery('#docUploadModal').modal('show');
},
	/*   showUploadModal: function () {
        let modalHtml = `
        <div class="modal fade" id="docUploadModal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header"><h4>Upload Documents</h4></div>
              <div class="modal-body">
                <input type="file" id="docFiles" multiple />
              </div>
              <div class="modal-footer">
                <button class="btn btn-success" id="uploadDocsBtn">Upload</button>
              </div>
            </div>
          </div>
        </div>`;
        
        jQuery('body').append(modalHtml);
        jQuery('#docUploadModal').modal('show');
    },
*/

	registerUploadEvent: function () {
		var thisInstance = this;

		jQuery(document).on('click', '#uploadDocsBtn', function (e) {
			e.preventDefault();
			   var container = jQuery('.relatedContainer');
			let files = jQuery('#docFiles')[0].files;
			if (!files.length) {
				app.showNotify({text: 'Please select files', type: 'error'});
				return;
			}

			let formData = new FormData();
			for (let i = 0; i < files.length; i++) {
				formData.append('documents[]', files[i]);
			}

			formData.append('record', app.getRecordId());
			formData.append('module', 'Contacts');
			formData.append('action', 'UploadContactDocuments');

			app.helper.showProgress();

			app.request.post({
				url: 'index.php',
				data: formData,
				processData: false,
				contentType: false
			}).then(function (err, response) {

				app.helper.hideProgress();

				if (err) {
					app.helper.showErrorNotification({"message":'Upload failed'});
					return;
				}

				// Close modal
				jQuery('#docUploadModal').modal('hide');
				jQuery('#docUploadModal').remove();

				// Success message
				app.helper.showSuccessNotification({"message":'Documents uploaded successfully'});

				// Refresh Documents related tab only
				//app.event.trigger('post.relatedListLoad', {module:'Documents'});
				console.log(container.find(".searchRow"));
				app.event.trigger("post.relatedListLoad",container.find(".searchRow"));
				//

			});
		});
	},

    registerEvents: function () {
        this.registerUploadButton();
        this.registerClickEvent();
        this.registerUploadEvent();
    }
});

jQuery(document).ready(function(){
    let instance = new Contacts_DocumentUpload_Js();
    instance.registerEvents();
});

