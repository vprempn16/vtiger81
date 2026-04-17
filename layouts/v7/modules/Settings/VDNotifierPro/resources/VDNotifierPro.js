/* * *******************************************************************************
 * The content of this file is subject to the Notificator Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */
var translation;
var running = false;
var notifyCalcMessages = 0;

jQuery.Class('VDNotifierPro_Basic_Js', {
    //stores the module that need to be searched
    container: false,
    sound: true,
    message: true,
    ajax: 1,
    time: 15000,
    demo: '',
    title: document.title,
    alert: '!!!New Notifier for you!!!',
    timer: false,

    /**
     * Function to get the search module
     */
    vdtranslate: function (sbkey) {
        if (!sbkey) {
            return sbkey;
        }
        if (translation.hasOwnProperty(sbkey)) {
            return translation[sbkey];
        } else {
            return sbkey;
        }
    },

    start: function () {
        //var self = this;
        if (this.container === false) {
            var self = this;
            var metka = $('#navbar .nav.navbar-nav');
            metka.prepend(this.getHeader(self));
            this.container = $('#VDNotifier');
            var sound = '';
            var message = '';
            if (this.sound == true) sound = 'checked="checked"';
            if (this.message == true) message = 'checked="checked"';
            var content = '<div id="VDNotifierSetting">';
            content += '<div class="panel-heading" style="margin-left: -5px;">' + this.vdtranslate('LBL_SETTING') + '</div>';
            content += '<div class="col-xs-4"><div><b>' + this.vdtranslate('LBL_SOUND') + ':</b></div><br /><div><b>' + this.vdtranslate('LBL_MESSAGE') + ':</b></div></div>';
            content += '<div class="col-xs-2"><div><input type="checkbox" name="sound" ' + sound + ' onchange="saveSetting(this)" /></div>';
            content += '<br /><div><input type="checkbox" name="message" ' + message + ' onchange="saveSetting(this)" /></div></div><a class="pull-right" onclick="VDNotifierSetting(event)" href="#">' + this.vdtranslate('LBL_CLOSE') + '</a></div>';

            $('#VDNotifierProSettingContainer').prepend(content);
/*            $('ul.dropdown-menu.VDNotifierPro-ul').live('click', function (event) {
                $(this).parent().toggleClass('open');
            });*/
            $(document).on('click', '.VDNotifierPro-ul', function (e) {
                e.stopPropagation();
            });
        }
        //console.log(this);


    },
    set: function (name, value) {
        this[name] = value;
    },
    setSetting: function (data) {
        this.sound = data.sound;
        this.message = data.message;
        this.ajax = data.ajax;
        this.time = data.time;
        if (data.demo) {
            this.demo = data.demo;
        }
    },
    getHeader: function (self) {
        return '<li id="VDNotifier" class="dropdown span settingIcons"><div>' +
            '<a id="VDNotifierPro" class="dropdown-toggle fa fa-bell" data-toggle="dropdown" href="#">' +
            '<span id="rowVDNotifier"></span></a>' +
            '<ul class="dropdown-menu VDNotifierPro-ul">' +
            '<div id="VDNotifierProHeader"><div class="row"><div class="col-xs-12"><div class="pull-left"><h5>' + this.vdtranslate('LBL_HEADER') + '</h5></div>' +
            '<div class="pull-right"><a id="notifieProClean" onclick="VDNotifierClean(event)" ><i class="fa fa-trash alignMiddle" aria-hidden="true"></i></a><a id="notifieProSetting" onclick="VDNotifierSetting(event)"><i class="fa fa-wrench" aria-hidden="true"></i></a>' +
            '</div></div></div><hr /><div id="VDNotifierProContainer"></div><div id="VDNotifierProSettingContainer"></div></div></div><div class="footerVDNotifier">' + self.demo + '</div></ul><audio><source src="layouts/v7/modules/Settings/VDNotifierPro/resources/beep.mp3"></source><source src="layouts/v7/modules/Settings/VDNotifierPro/resources/beep.ogg"></source><audio></audio></div></li>';

    },

    run: function () {
        var self = this;
        var postData = {
            'action': 'ActivityVDNotifier',
            'module': 'VDNotifierPro',
            'mode': 'getVDNotifier'
        };

        AppConnector.request(postData).then(function (data) {

            if (data.success) {
                var row = data.result;
                self.getContent(row);
            }
        });
    },
    changeValue: function (elem) {
        var self = this;
        var name = elem.attr('name');
        var moduleId = elem.data('module');
        if (name == 'status') {

            var value = this.changeAllBox(elem);
        } else if (elem.prop("checked")) {
            $('input[data-module="' + moduleId + '"][name="status"]').attr('checked', 'checked');
            var value = 1;
        } else {
            var value = 0;
        }
        if (running === true) {
            running = false;
            return false;
        }
        running = true;
        var postData = {
            'action': 'ActivityVDNotifier',
            'module': 'VDNotifierPro',
            'mode': 'setSeting',
            'field': name,
            'value': value,
            'moduleId': moduleId,

        };
        AppConnector.request(postData).then(function (data) {
            var params = {
                text: app.vtranslate('JS_RECORD_UPDATED'),
                title: app.vtranslate('JS_RECORD_UPDATED')
            };
            Vtiger_Helper_Js.showPnotify(params);
            running = false;
        });

    },
    getContent: function (row) {
        var self = this;
        var container = $('#VDNotifierProContainer');
        if (row.length > 0) {
            var i = row.length;
            var alarm = 0;
            $('#rowVDNotifier').html(i);
            var limitDivide = i - 2;
            row.forEach(function(elem) {
                if (elem.type == 'Reminder') {
                    if (self.checkVDNotifierPopander(elem.id)) {
                        if (elem.status != 5) {
                            if (self.sound) {
                                alarm++;
                            }
                        }
                        var content = '<div class="row" id="VDNotifierPopunder-' + elem.id + '">';
                        content += '<div class="col-xs-3"><img src="' + elem.modiImg + '" class="summaryImg" style="width: 70px !important; height: 70px !important;"></div>';
                        content += '<div class="col-xs-9" style="margin-left: -10px;"><div class="header"><a href="' + elem.link + '"><b>' + elem.title + '</b></a></div>';
                        content += '<div class="VDNotifierTime">' + self.vdtranslate('LBL_WHEN') + elem.modifiedtime + '</div>';
                        content += '<div class="header">' + self.vdtranslate('LBL_ASSIGNED_TO') + elem.modiName + '</div>';
                        content += '<b>' + self.vdtranslate('LBL_CALENDAR_EVENT') + '</b> <a class="pull-right" onclick="VDPostpone(event,' + elem.id + ')"><u>' + elem.Postponed + '</u></a></div></div>';
                        content += '</div><hr id="VDNotifierPopunder-' + elem.id + '-hr"/>';

                        container.prepend(content);

                        if (elem.status != 5) {
                            if (alarm < 4) {
                                if (this.message) {
                                    var params = {
                                        title: '<span>' + elem.action + '</span> <span>' + elem.module + '</span>',
                                        text: '<a onclick="changeVDPopunder(event,' + elem.id + ');" class="closePopup"></a><a href="index.php?' + elem.link + '" onclick="jQuery(this).closest(\'div.ui-pnotify-container\').find(\'span.icon-remove\').trigger(\'click\')">' + elem.title + '</a><a class="btn pull-right" onclick="VDPostpone(event,' + elem.id + ');jQuery(this).closest(\'div.ui-pnotify-container\').find(\'span.icon-remove\').trigger(\'click\')">' + elem.Postponed + '</a>',
                                        animation: 'show',
                                        type: 'info',
                                        hide: false,
                                    };
                                    var params2 = {
                                        icon: "fa fa-check-circle",
                                        message: params.title,
                                        title: '<span>' + self.vdtranslate(elem.action) + '</span> <span>' + elem.module + '</span>',
                                        text: '<a onclick="changeVDPopunder(event,' + elem.id + ');" class="closePopup"></a><a href="index.php?' + elem.link + '" onclick="jQuery(this).closest(\'div.ui-pnotify-container\').find(\'span.icon-remove\').trigger(\'click\')">' + elem.title + '</a><a class="btn pull-right" onclick="VDPostpone(event,' + elem.id + ');jQuery(this).closest(\'div.ui-pnotify-container\').find(\'span.icon-remove\').trigger(\'click\')">' + self.vdtranslate(elem.Postponed) + '</a>',
                                        type: 'success',
                                        // hide: false,
                                        allow_dismiss: true,
                                        closer: true,
                                        closer_hover: true
                                    };

                                    if (self.ajax > 0) {
                                        setTimeout(function () {
                                            jQuery.pnotify(params2);
                                        }, self.time);
                                    }

                                }
                            }
                        }
                    }


                } else if (self.checkVDNotifierId(elem.id)) {
                    if (elem.status != 5) {
                        if (self.sound) {
                            alarm++;
                        }
                    }
                    var content = '<div class="row" id="VDNotifierMessage-' + elem.id + '">';
                    content += '<div class="col-xs-3"><img src="' + elem.modiImg + '" class="summaryImg" style="width: 70px !important; height: 70px !important;"></div>';
                    content += '<div class="col-xs-8" style="margin-left: -10px;">';
                    if (elem.action == 'MENTIONED') {
                        content += '<div class="header"><a href="index.php?' + elem.link + '" ><b>' + self.vdtranslate('JS_MENTIONED_CHECK_COMMENT') + '</b></a></div>';
                    } else {
                        content += '<div class="header"><a href="index.php?' + elem.link + '" ><b>' + elem.title + '</b></a></div>';
                    }                    
                    content += '<div class="header">' + self.vdtranslate(elem.action) + self.vdtranslate('LBL_IN_MODULE') + elem.module + '</div>';
                    content += '<div class="title">' + self.vdtranslate('LBL_WHEN') + elem.modifiedtime + '</div>';
                    content += '<div class="VDNotifierTime">' + self.vdtranslate('LBL_WHO') + elem.modiName + '</div></div>';
                    content += '<div class="col-xs-1"><a class="changeVDNotifier" onclick="removeNotifier(event,' + elem.id + ')" >x</a></div>';
                    content += '</div><hr id="VDNotifierMessage-' + elem.id + '-hr"/>';
                    container.prepend(content);
                    if (elem.status != 5) {
                        if (alarm < 4) {
                            if (self.message) {
                                var action = '';
                                var action2 = '';
                                var icon = '';
                                switch (elem.action) {
                                    case 'CREATED':
                                        action = self.vdtranslate('JS_NOTIFIERPRO_CREATED_ACTION');
                                        action2 = self.vdtranslate('JS_NOTIFIERPRO_CREATED_ACTION_EXT');
                                        icon = 'fa-plus';
                                        break;
                                    case 'UPDATED':
                                        action = self.vdtranslate('JS_NOTIFIERPRO_UPDATED_ACTION');
                                        action2 = self.vdtranslate('JS_NOTIFIERPRO_UPDATED_ACTION_EXT');
                                        icon = 'fa-pencil';
                                        break;
                                    case 'DELETED':
                                        action = self.vdtranslate('JS_NOTIFIERPRO_DELETED_ACTION');
                                        action2 = self.vdtranslate('JS_NOTIFIERPRO_DELETED_ACTION_EXT');
                                        icon = 'fa-trash';
                                        break;
                                    case 'RESTORED':
                                        action = self.vdtranslate('JS_NOTIFIERPRO_RESTORED_ACTION');
                                        action2 = self.vdtranslate('JS_NOTIFIERPRO_RESTORED_ACTION_EXT');
                                        icon = 'fa-recycle';
                                        break;
                                    case 'MENTIONED':
                                        action = self.vdtranslate('JS_NOTIFIERPRO_MENTIONED_ACTION');
                                        action2 = self.vdtranslate('JS_NOTIFIERPRO_MENTIONED_ACTION_EXT');
                                        icon = 'fa-comment';
                                        break;
                                }
                                var params = {
                                    title: '<span>' + self.vdtranslate('LBL_ROW') + elem.title + self.vdtranslate('LBL_WAS') + action2 + self.vdtranslate('LBL_USER') + elem.modiName + '</span>',
                                    //content += '<div class="col-xs-1"><a class="changeVDNotifier" onclick="removeNotifier(event,'+row[key].id+')" >x</a></div>';
                                    text: '<a onclick="changeVDNotifier(event,' + elem.id + ');" class="closePopup"></a><a href="index.php?' + elem.link + '" onclick="jQuery(this).closest(\'div.ui-pnotify-container\').find(\'span.icon-remove\').trigger(\'click\')">' + elem.title + '</a>',
                                    animation: 'show',
                                    type: 'info',
                                    hide: false,
                                };
                                var params2 = {
                                    icon: "vdmt5 fa " + icon,
                                    message: params.title,
                                    title: '<span>' + elem.module + ' - </span><span>' + action + '</span>',
                                    text: params.title,
                                    type: 'success',
                                    // addclass: 'brighttheme',
                                    hide: false,
                                    styling: 'bootstrap',
                                    allow_dismiss: true,
                                    overlayClose: true,
                                    // modal: true,
                                    sticker: false,
                                    closer: true,
                                    closer_hover: false,
                                    nonblock: false,
                                    curid: elem.id
                                };
                                if (elem.action == 'MENTIONED') {
                                    var menmessage = '<span>' + self.vdtranslate('LBL_ROW_MENTIONED') + elem.module + self.vdtranslate('LBL_MENTION_USER') +  elem.modiName + '</span>';
                                    params2.message = menmessage;
                                    params2.text = menmessage;
                                }
                                if (self.ajax > 0) {
                                    setTimeout(function () {
                                        //jQuery.pnotify(params2);
                                        notifyCalcMessages++;
                                        if (notifyCalcMessages > 2) {
                                            self.displayRemoveAllPopups();
                                        }
                                    }, self.time);
                                }
                                // Vtiger_Helper_Js.showPnotify(params);
                            }
                        }
                    }
                }
            });


            if (alarm > 0) {
                var audio = document.getElementsByTagName("audio")[0];
                audio.play();
                titleAlert(this);
            }
        } else {
            $('#rowVDNotifier').html('');
        }
        return;
    },
    displayRemoveAllPopups: function() {
        if (document.getElementById('clearNotifierPopups')) {
            return;
        }
        //jQuery('nav.navbar').prepend("<div id='clearNotifierPopups' class='alert alert-block alert-success' style='cursor: pointer; position: absolute; z-index: 100; margin: 5px;'><span><strong>" + this.vdtranslate('LBL_CLEAR_ALL_ALERTS') + "</strong></span></div>");
        //this.clearNotifications();
    },
    clearNotifications: function() {
        jQuery('#clearNotifierPopups').on('click', function() {
            jQuery.pnotify_remove_all();
            jQuery('#clearNotifierPopups').remove();
        });
    },
    titleAlert: function () {
        var r = this.alert;
        var t = document.title;

        if (t != r) {
            document.title = r;
        } else {
            document.title = this.title;
        }
        return;
    },
    checkVDNotifierId: function (id) {
        if ($('div').is('#VDNotifierMessage-' + id)) {
            return false;
        } else {
            return true;
        }
    },
    checkVDNotifierPopander: function (id) {
        if ($('div').is('#VDNotifierPopunder-' + id)) {
            return false;
        } else {
            return true;
        }
    },
    changeAllBox: function (elem) {
        var moduleId = elem.data('module');
        if (elem.prop("checked")) {
            $('input[data-module="' + moduleId + '"]').attr('checked', 'checked');
            return 1;
        } else {
            $('input[data-module="' + moduleId + '"]').removeAttr('checked');
            return 0;
        }

    }

});


var $VDNotifierPro;

jQuery(document).ready(function () {
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'VDTranslate',
        // 'str' : val
    };
    AppConnector.request(postData).then(function (data) {
        if (data.success) {
            translation = data.result;
            jQuery('body').on('click', '.ui-pnotify-closer .icon-remove', function () {
                jQuery(this).closest('div.ui-pnotify-container').find('a.closePopup').trigger('click');
            });
            var postData = {
                'action': 'ActivityVDNotifier',
                'module': 'VDNotifierPro',
                'mode': 'setting'
            };
            AppConnector.request(postData).then(function (data) {
                if (data.success && !$VDNotifierPro) {
                    $VDNotifierPro = new VDNotifierPro_Basic_Js();
                    $VDNotifierPro.setSetting(data.result);
                    $VDNotifierPro.start();
                    runVDNotifier($VDNotifierPro);
                }
            });
        }
    });


    $('.VDNotifierProInput').bind('click', function () {
        $VDNotifierPro.changeValue($(this));
    });
    $('[data-toggle="tooltips"]').tooltip();
    $('.saveSetting').on('click', function () {
        var a = 0;
        if ($('#ajaxVDNotifier').prop("checked")) {
            a = 1;
        }
        var k = $('#keyVDNotifier').val();
        var t = $('#ajaxTime').val();
        var postData = {
            'action': 'ActivityVDNotifier',
            'module': 'VDNotifierPro',
            'mode': 'setSetingGlob',
            'a': a,
            't': t,
            'k': k,


        };
        if (running === true) {
            running = false;
            return false;
        }
        running = true;
        AppConnector.request(postData).then(function (data) {
            var params = {
                text: app.vtranslate('JS_RECORD_UPDATED'),
                title: app.vtranslate('JS_RECORD_UPDATED')
            };
            Vtiger_Helper_Js.showPnotify(params);
            running = false;
        });
        return false;
    });
    $('.installDomen').on('click', function () {

        var k = $('#keyVDNotifier');
        if (k.val().length != 12) {
            alert('Key incorect');
            return false;
        }
        var postData = {
            'action': 'ActivityVDNotifier',
            'module': 'VDNotifierPro',
            'mode': 'installDomen',
            'k': k.val(),


        };
        AppConnector.request(postData).then(function (data) {
            var params = {
                title: data.result.title,
                text: data.result.text,
                animation: 'show',
                type: data.result.type,
            };
            Vtiger_Helper_Js.showPnotify(params);
            if (data.result.type == 'info') {
                window.location.reload();
            }

        });
        return false;
    });
    $('.deleteDomen').on('click', function () {

        var k = $('#keyVDNotifier');
        var postData = {
            'action': 'ActivityVDNotifier',
            'module': 'VDNotifierPro',
            'mode': 'deleteDomen',
            'k': k.val(),


        };
        AppConnector.request(postData).then(function (data) {
            var params = {
                title: data.result.title,
                text: data.result.text,
                animation: 'show',
                type: data.result.type,
            };
            Vtiger_Helper_Js.showPnotify(params);
            if (data.result.type == 'info') {
                window.location.reload();
            }

        });
        return false;
    });


});

function removeNotifier(e, id) {
    if (e) {
        e.stopPropagation();
    }

    var i = parseInt($('#rowVDNotifier').text()) - 1;
    if (i == 0) i = '';
    if (i == 'NaN') i = '';
    $('#rowVDNotifier').html(i);
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'removeVDNotifier',
        'id': id
    };
    AppConnector.request(postData);
    $('#VDNotifierMessage-' + id).remove();
    $('#VDNotifierMessage-' + id + '-hr').remove();
    jQuery.pnotify_remove_all();
    return true;
}

function removeTempNotifier(e, id) {
    if (e) {
        e.stopPropagation();
    }

    var i = parseInt($('#rowVDNotifier').text()) - 1;
    if (i == 0) i = '';
    if (i == 'NaN') i = '';
    $('#rowVDNotifier').html(i);
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'changeVDNotifier',
        'id': id
    };
    AppConnector.request(postData);
    $('#VDNotifierMessage-' + id).remove();
    $('#VDNotifierMessage-' + id + '-hr').remove();
    // jQuery.pnotify_remove_all();
    return true;
}

function deleteVDNotifier(e, id) {
    e.stopPropagation();

    var i = parseInt($('#rowVDNotifier').text()) - 1;
    if (i == 0) i = '';
    if (i == 'NaN') i = '';
    $('#rowVDNotifier').html(i);
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'deleteVDNotifier',
        'id': id
    };
    AppConnector.request(postData);
    $('#VDNotifierMessage-' + id).remove();
    $('#VDNotifierMessage-' + id + '-hr').remove();
    jQuery.pnotify_remove_all();
    return true;

}

function changeVDNotifier(e, id) {
    e.stopPropagation();


    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'changeVDNotifier',
        'id': id
    };
    AppConnector.request(postData);

    return true;

}

function deleteVDPopunder(e, id) {
    e.stopPropagation();

    var i = parseInt($('#rowVDNotifier').text()) - 1;
    if (i == 0) i = '';
    if (i == 'NaN') i = '';
    $('#rowVDNotifier').html(i);
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'deleteVDPopunder',
        'id': id
    };
    AppConnector.request(postData);
    $('#VDNotifierPopunder-' + id).remove();
    $('#VDNotifierPopunder-' + id + '-hr').remove();
    return true;

}

function changeVDPopunder(e, id) {
    e.stopPropagation();


    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'changeVDPopunder',
        'id': id
    };
    AppConnector.request(postData);

    return true;

}

function VDPostpone(e, id) {
    e.stopPropagation();

    var i = parseInt($('#rowVDNotifier').text()) - 1;
    if (i == 0) i = '';
    if (i == 'NaN') i = '';
    $('#rowVDNotifier').html(i);
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'postpone',
        'id': id
    };
    AppConnector.request(postData);
    $('#VDNotifierPopunder-' + id).remove();
    $('#VDNotifierPopunder-' + id + '-hr').remove();
    return true;

}

function VDNotifierSetting(e) {
    e.stopPropagation();
    $('#VDNotifierProContainer').toggle('fast')
    $('#VDNotifierProSettingContainer').toggle('fast')
}

function VDNotifierClean(e) {
    e.stopPropagation();
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'cleanMessage',
    };
    AppConnector.request(postData).then(function (data) {
        if (data.success) {
            $('#VDNotifierProContainer').html('');
            $('#rowVDNotifier').html('');
        }
    });
}

function runVDNotifier(obj) {
    obj.run();
    if ($VDNotifierPro.ajax == 1) {
        setTimeout(function () {
            runVDNotifier(obj)
        }, $VDNotifierPro.time);
    }
}


function saveSetting(cont) {

    var elem = $(cont);
    var name = elem.attr('name');
    var value = 0;
    if (elem.prop("checked")) {
        value = 1;
    }
    $VDNotifierPro.set(name, value);
    var postData = {
        'action': 'ActivityVDNotifier',
        'module': 'VDNotifierPro',
        'mode': 'setSetingUser',
        'field': name,
        'value': value,


    };
    AppConnector.request(postData).then(function (data) {
        $('.VDNotifierPro-ul').parent().addClass('open');
    });

    return true;
}

function titleAlert(obj) {
    if (focus === true) {
        clearTimeout(obj.timer);
        document.title = obj.title;
    }
    ;

    if (focus === false) {
        obj.titleAlert();
        obj.timer = setTimeout(function () {
            titleAlert(obj)
        }, 1000);
    }
    ;

}

var focus = true;
$(function () {

    $(window).bind('focus', function () {
        focus = true;

    });

    $(window).bind('blur', function () {
        focus = false;

    });
});
