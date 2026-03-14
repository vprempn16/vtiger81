if (typeof(VTSendWhatsappTask_Js) === 'undefined') {
    Vtiger_Helper_Js.extend("VTSendWhatsappTask_Js", {}, {
        container: null,

        registerEvents: function(container) {
            var self = this;
            this.container = container;
            
            if (this.container.data('wa-initialized')) return;
            this.container.data('wa-initialized', true);

            // 1. Channel Change -> Load Templates
            this.container.on('change', 'select.wa-wf-channel', function(e) {
                var channelId = jQuery(e.currentTarget).val();
                var templateSelect = self.container.find('select.wa-wf-template');
                
                if (!self.container.data('initializing')) {
                    templateSelect.html('<option value="">' + app.vtranslate('LBL_SELECT_OPTION') + '</option>');
                    self.container.find('.wa-wf-mapping-container').html('');
                    templateSelect.trigger('change.select2');
                }

                if (!channelId) return;
                self.loadTemplates(channelId);
            });

            // 2. Template Change -> Load Mapping UI
            this.container.on('change', 'select.wa-wf-template', function(e) {
                var templateId = jQuery(e.currentTarget).val();
                var mappingContainer = self.container.find('.wa-wf-mapping-container');
                var sourceModule = jQuery('[name="module_name"]').val();

                if (!templateId || templateId === 'Loading...') {
                    return;
                }

                var params = {
                    module: 'Whatsapp',
                    action: 'MassActionAjax',
                    mode: 'getMappingUIForWorkflow',
                    template_id: templateId,
                    source_module: sourceModule
                };

                app.helper.showProgress();
                app.request.post({data: params}).then(function(err, data) {
                    app.helper.hideProgress();
                    if (data) {
                        mappingContainer.html(data);
                        vtUtils.showSelect2ElementView(mappingContainer.find('select.select2'));
                        self.preFillMapping();
                    }
                });
            });

            // 3. Mapping Update -> Save to Hidden Field
            this.container.on('change', 'select.wa-wf-map', function() {
                self.updateMappingData();
            });

            this.initialLoad();
        },

        loadTemplates: function(channelId) {
            var self = this;
            var templateSelect = this.container.find('select.wa-wf-template');
            var savedTemplateId = templateSelect.val();

            var params = {
                module: 'Whatsapp',
                action: 'MassActionAjax',
                mode: 'getTemplatesByChannel',
                channel_id: channelId
            };

            app.request.post({data: params}).then(function(err, data) {
                if (data) {
                    var html = '<option value="">' + app.vtranslate('LBL_SELECT_OPTION') + '</option>';
                    jQuery.each(data, function(index, template) {
                        var selected = (template.id == savedTemplateId) ? 'selected' : '';
                        html += '<option value="' + template.id + '" ' + selected + '>' + template.name + '</option>';
                    });
                    templateSelect.html(html).trigger('change.select2');
                    
                    if (self.container.data('initializing') && savedTemplateId) {
                        templateSelect.trigger('change');
                    }
                }
            });
        },

        initialLoad: function() {
            var self = this;
            this.container.data('initializing', true);
            
            var channelId = this.container.find('select.wa-wf-channel').val();
            var templateId = this.container.find('select.wa-wf-template').val();

            if (channelId) {
                this.container.find('select.wa-wf-channel').trigger('change');
            }
            
            setTimeout(function() {
                self.container.data('initializing', false);
            }, 5000);
        },

        updateMappingData: function() {
            var mapping = {};
            this.container.find('select.wa-wf-map').each(function() {
                var el = jQuery(this);
                var comp = el.data('comp');
                var variable = el.data('var');
                var val = el.val();
                
                if (val) {
                    if (!mapping[comp]) mapping[comp] = {};
                    mapping[comp][variable] = val;
                }
            });
            this.container.find('input.wa-wf-mapping-data').val(JSON.stringify(mapping));
        },

        preFillMapping: function() {
            var self = this;
            var rawData = this.container.find('input.wa-wf-mapping-data').val();
            if (!rawData || rawData === 'Array' || rawData === '[]' || rawData === '{}') return;

            try {
                var mapping = JSON.parse(rawData);
                jQuery.each(mapping, function(comp, vars) {
                    jQuery.each(vars, function(variable, field) {
                        var select = self.container.find('select.wa-wf-map[data-comp="' + comp + '"][data-var="' + variable + '"]');
                        if (select.length) {
                            select.val(field).trigger('change.select2');
                        }
                    });
                });
            } catch (e) {
                console.error("Failed to parse WhatsApp mapping. Data:", rawData, e);
            }
        }
    });

    // Global initializer for all VTSendWhatsappTask containers
    VTSendWhatsappTask_Js.init = function() {
        jQuery('.whatsapp-task-container').each(function() {
            var container = jQuery(this);
            if (!container.data('wa-initialized')) {
                var instance = new VTSendWhatsappTask_Js();
                instance.registerEvents(container);
            }
        });
    };

    // Listen for workflow task form loading
    jQuery(document).ajaxComplete(function(event, xhr, options) {
        if (options.url && options.url.indexOf('view=EditTask') !== -1) {
            setTimeout(function() {
                VTSendWhatsappTask_Js.init();
            }, 800);
        }
    });
}

// Immediate init for the first load
jQuery(document).ready(function() {
    VTSendWhatsappTask_Js.init();
});
