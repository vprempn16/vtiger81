Settings_Vtiger_List_Js("Settings_Whatsapp_List_Js", {}, {

    // Trigger delete record
    triggerDelete: function (event, url) {
        event.stopPropagation();
        var self = this;
        Vtiger_Helper_Js.showConfirmationBox({ 'message': app.vtranslate('LBL_DELETE_CONFIRMATION') }).then(
            function (e) {
                AppConnector.request(url).then(
                    function (data) {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            Vtiger_Helper_Js.showPnotify(data.error.message);
                        }
                    }
                );
            },
            function (error, err) { }
        );
    },

    // Trigger sync templates from Meta
    syncTemplates: function (url) {
        var self = this;
        app.helper.showProgress();
        AppConnector.request(url).then(
            function (data) {
                app.helper.hideProgress();
                if (data.success) {
                    app.helper.showSuccessNotification({ 'message': app.vtranslate('JS_TEMPLATES_SYNCED_SUCCESSFULLY', 'Settings:Whatsapp') });
                    window.location.reload();
                } else {
                    app.helper.showErrorNotification({ 'message': data.error.message });
                }
            },
            function (error) {
                app.helper.hideProgress();
            }
        );
    },

    // Register filter change event
    registerChannelFilterEvent: function () {
        var self = this;
        jQuery('#channelFilter').on('change', function (e) {
            var channelId = jQuery(e.currentTarget).val();
            window.location.href = 'index.php?module=Whatsapp&parent=Settings&view=Templates&channel_id=' + channelId;
        });
    },

    // Register sync button on templates page
    registerSyncTemplatesEvent: function () {
        var self = this;
        jQuery('#syncTemplates, #syncTemplatesCenter').on('click', function (e) {
            var channelId = jQuery(e.currentTarget).data('channel-id');
            var url = 'index.php?module=Whatsapp&parent=Settings&action=ActionAjax&mode=syncTemplates&record=' + channelId;
            self.syncTemplates(url);
        });
    },

    registerEvents: function () {
        this._super();
        this.registerChannelFilterEvent();
        this.registerSyncTemplatesEvent();
    }
});
