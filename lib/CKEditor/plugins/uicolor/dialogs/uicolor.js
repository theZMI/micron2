﻿/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.dialog.add('uicolor', function (a) {
    var b, c, d, e = a.getUiColor(), f = 'cke_uicolor_picker' + CKEDITOR.tools.getNextNumber();

    function g(j) {
        if (/^#/.test(j)) j = window.YAHOO.util.Color.hex2rgb(j.substr(1));
        c.setValue(j, true);
        c.refresh(f);
    };

    function h(j, k) {
        if (k || b._.contents.tab1.livePeview.getValue()) a.setUiColor(j);
        b._.contents.tab1.configBox.setValue('config.uiColor = "#' + c.get('hex') + '"');
    };d = {
        id: 'yuiColorPicker', type: 'html', html: "<div id='" + f + "' class='cke_uicolor_picker' style='width: 360px; height: 200px; position: relative;'></div>", onLoad: function (j) {
            var k = CKEDITOR.getUrl('plugins/uicolor/yui/');
            c = new window.YAHOO.widget.ColorPicker(f, {showhsvcontrols: true, showhexcontrols: true, images: {PICKER_THUMB: k + 'assets/picker_thumb.png', HUE_THUMB: k + 'assets/hue_thumb.png'}});
            if (e) g(e);
            c.on('rgbChange', function () {
                b._.contents.tab1.predefined.setValue('');
                h('#' + c.get('hex'));
            });
            var l = new CKEDITOR.dom.nodeList(c.getElementsByTagName('input'));
            for (var m = 0; m < l.count(); m++) l.getItem(m).addClass('cke_dialog_ui_input_text');
        }
    };
    var i = true;
    return {
        title: a.lang.uicolor.title, minWidth: 360, minHeight: 320, onLoad: function () {
            b = this;
            this.setupContent();
            if (CKEDITOR.env.ie7Compat) b.parts.contents.setStyle('overflow', 'hidden');
        }, contents: [{
            id: 'tab1', label: '', title: '', expand: true, padding: 0, elements: [d, {
                id: 'tab1', type: 'vbox', children: [{
                    id: 'livePeview', type: 'checkbox', label: a.lang.uicolor.preview, 'default': 1, onLoad: function () {
                        i = true;
                    }, onChange: function () {
                        if (i) return;
                        var j = this.getValue(), k = j ? '#' + c.get('hex') : e;
                        h(k, true);
                    }
                }, {
                    type: 'hbox', children: [{
                        id: 'predefined', type: 'select', 'default': '', label: a.lang.uicolor.predefined, items: [[''], ['Light blue', '#9AB8F3'], ['Sand', '#D2B48C'], ['Metallic', '#949AAA'], ['Purple', '#C2A3C7'], ['Olive', '#A2C980'], ['Happy green', '#9BD446'], ['Jezebel Blue', '#14B8C4'], ['Burn', '#FF893A'], ['Easy red', '#FF6969'], ['Pisces 3', '#48B4F2'], ['Aquarius 5', '#487ED4'], ['Absinthe', '#A8CF76'], ['Scrambled Egg', '#C7A622'], ['Hello monday', '#8E8D80'], ['Lovely sunshine', '#F1E8B1'], ['Recycled air', '#B3C593'], ['Down', '#BCBCA4'], ['Mark Twain', '#CFE91D'], ['Specks of dust', '#D1B596'], ['Lollipop', '#F6CE23']], onChange: function () {
                            var j = this.getValue();
                            if (j) {
                                g(j);
                                h(j);
                                CKEDITOR.document.getById('predefinedPreview').setStyle('background', j);
                            } else CKEDITOR.document.getById('predefinedPreview').setStyle('background', '');
                        }, onShow: function () {
                            var j = a.getUiColor();
                            if (j) this.setValue(j);
                        }
                    }, {id: 'predefinedPreview', type: 'html', html: '<div id="cke_uicolor_preview" style="border: 1px solid black; padding: 3px; width: 30px;"><div id="predefinedPreview" style="width: 30px; height: 30px;">&nbsp;</div></div>'}]
                }, {
                    id: 'configBox', type: 'text', label: a.lang.uicolor.config, onShow: function () {
                        var j = a.getUiColor();
                        if (j) this.setValue('config.uiColor = "' + j + '"');
                    }
                }]
            }]
        }], buttons: [CKEDITOR.dialog.okButton]
    };
});
