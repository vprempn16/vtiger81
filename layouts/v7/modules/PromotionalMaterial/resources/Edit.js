Vtiger_Edit_Js("PromotionalMaterial_Edit_Js", {} ,{

    getForm : function() {
        if(this.formElement === false){
                this.formElement = jQuery('#EditView');
        }
        return this.formElement;
    },

        registerFileElementChangeEvent : function(container) {
                var thisInstance = this;
                container.on('change', 'input[name="promotional_document"]', function(e){
            vtUtils.hideValidationMessage(container.find('input[name="promotional_document"]'));
            if(e.target.type == "text") return false;
            PromotionalMaterial_Edit_Js.file = e.target.files[0];
            var element = container.find('[name="promotional_document"]');
                        //ignore all other types than file
                        if(element.attr('type') != 'file'){
                                return ;
                        }
                        var uploadFileSizeHolder = element.closest('.fileUploadContainer').find('.uploadedFileSize');
                        var fileSize = e.target.files[0].size;
            var fileName = e.target.files[0].name;
                        var maxFileSize = thisInstance.getMaxiumFileUploadingSize(container);
                        if(fileSize > maxFileSize) {
                                alert(app.vtranslate('JS_EXCEEDS_MAX_UPLOAD_SIZE'));
                                element.val('');
                                uploadFileSizeHolder.text('');
                        }else{
                if(container.length > 1){
                    jQuery('div.fieldsContainer').find('form#I_form').find('input[name="promotional_document"]').css('width','80px');
                    jQuery('div.fieldsContainer').find('form#W_form').find('input[name="promotional_document"]').css('width','80px');
                } else {
                    container.find('input[name="promotional_document"]').css('width','80px');
                }
                                uploadFileSizeHolder.text(fileName+' '+thisInstance.convertFileSizeInToDisplayFormat(fileSize));
                        }

                });
        },

    registerBasicEvents : function(container) {
        this._super(container);
        //this.registerFileLocationTypeChangeEvent(container);
                this.registerFileChangeEvent(container);
    },

        registerEvents : function() {
        this.registerBasicEvents(this.getForm());
        }

})
