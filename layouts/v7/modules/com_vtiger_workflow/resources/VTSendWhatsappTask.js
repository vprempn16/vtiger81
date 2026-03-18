VTSendWhatsappTask_Js("VTSendWhatsappTask_Js", {}, {
    registerEvents: function() {
        var self = this;
        var container = jQuery('#VtTaskContainer');

        // 1. Channel Change -> Load Templates
        container.on('change', '#whatsapp_wf_channel', function(e) {
            var channelId = jQuery(e.currentTarget).val();
            var templateSelect = container.find('#whatsapp_wf_template');
            
            templateSelect.html('<option value="">' + app.vtranslate('LBL_SELECT_OPTION') + '</option>');
            container.find('#whatsapp_wf_mapping_container').html('');

            if (!channelId) return;

            var params = {
                module: 'Whatsapp',
                action: 'MassActionAjax',
                mode: 'getTemplatesByChannel',
                channel_id: channelId
            };

            app.helper.showProgress();
            app.request.post({data: params}).then(function(err, data) {
                app.helper.hideProgress();
                if (data) {
                    var html = '';
                    jQuery.each(data, function(id, name) {
                        html += '<option value="' + id + '">' + name + '</option>';
                    });
                    templateSelect.append(html).trigger('change');
                }
            });
        });

        // 2. Template Change -> Load Mapping UI
        container.on('change', '#whatsapp_wf_template', function(e) {
            var templateId = jQuery(e.currentTarget).val();
            var mappingContainer = container.find('#whatsapp_wf_mapping_container');
            var sourceModule = jQuery('[name="module_name"]').val(); // Workflow target module

            mappingContainer.html('');

            if (!templateId) return;

            var params = {
                module: 'Whatsapp',
                action: 'MassActionAjax',
                mode: 'getMappingUIForWorkflow',
                template_id: templateId,
                source_module: sourceModule
            };

            app.helper.showProgress();
            app.request.post({data: params}).then(function(err, html) {
                app.helper.hideProgress();
                if (html) {
                    mappingContainer.html(html);
                    vtUtils.showSelect2(mappingContainer.find('select.select2'));
                    self.preFillMapping(); // Fill if we have saved data
                }
            });
        });

        // 3. Mapping Update -> Save to Hidden Field
        container.on('change', '.wa-wf-map', function() {
            self.updateMappingData();
        });

        this.initialLoad();
    },

    initialLoad: function() {
        var container = jQuery('#VtTaskContainer');
        var savedTemplateId = jQuery('[name="templateid"]').val();
        if (savedTemplateId) {
            container.find('#whatsapp_wf_channel').trigger('change');
            // We need to wait for templates to load before triggering template change
            // But for now, we'll let the user re-select if it doesn't auto-trigger correctly
        }
    },

    updateMappingData: function() {
        var container = jQuery('#VtTaskContainer');
        var mapping = {};
        
        container.find('.wa-wf-map').each(function() {
            var el = jQuery(this);
            var comp = el.data('comp');
            var variable = el.data('var');
            var val = el.val();
            
            if (val) {
                if (!mapping[comp]) mapping[comp] = {};
                mapping[comp][variable] = val;
            }
        });

        container.find('#whatsapp_wf_mapping_data').val(JSON.stringify(mapping));
    },

    preFillMapping: function() {
        var container = jQuery('#VtTaskContainer');
        var rawData = container.find('#whatsapp_wf_mapping_data').val();
        if (!rawData) return;

        try {
            var mapping = JSON.parse(rawData);
            jQuery.each(mapping, function(comp, vars) {
                jQuery.each(vars, function(variable, field) {
                    var select = container.find('.wa-wf-map[data-comp="' + comp + '"][data-var="' + variable + '"]');
                    if (select.length) {
                        select.val(field).trigger('change.select2');
                    }
                });
            });
        } catch (e) {
            console.error("Failed to parse WhatsApp mapping", e);
        }
    }
});

// Initialize on document ready or when task modal is loaded
jQuery(document).ready(function() {
    var instance = new VTSendWhatsappTask_Js();
    instance.registerEvents();
});
