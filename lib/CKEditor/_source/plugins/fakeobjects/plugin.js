﻿/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function () {
    var cssStyle = CKEDITOR.htmlParser.cssStyle,
        cssLength = CKEDITOR.tools.cssLength;

    var cssLengthRegex = /^((?:\d*(?:\.\d+))|(?:\d+))(.*)?$/i;

    /*
     * Replacing the former CSS length value with the later one, with
     * adjustment to the length  unit.
     */
    function replaceCssLength(length1, length2) {
        var parts1 = cssLengthRegex.exec(length1),
            parts2 = cssLengthRegex.exec(length2);

        // Omit pixel length unit when necessary,
        // e.g. replaceCssLength( 10, '20px' ) -> 20
        if (parts1) {
            if (!parts1[2] && parts2[2] == 'px')
                return parts2[1];
            if (parts1[2] == 'px' && !parts2[2])
                return parts2[1] + 'px';
        }

        return length2;
    }

    var htmlFilterRules =
        {
            elements:
                {
                    $: function (element) {
                        var attributes = element.attributes,
                            realHtml = attributes && attributes['data-cke-realelement'],
                            realFragment = realHtml && new CKEDITOR.htmlParser.fragment.fromHtml(decodeURIComponent(realHtml)),
                            realElement = realFragment && realFragment.children[0];

                        // Width/height in the fake object are subjected to clone into the real element.
                        if (realElement && element.attributes['data-cke-resizable']) {
                            var styles = new cssStyle(element).rules,
                                realAttrs = realElement.attributes,
                                width = styles.width,
                                height = styles.height;

                            width && (realAttrs.width = replaceCssLength(realAttrs.width, width));
                            height && (realAttrs.height = replaceCssLength(realAttrs.height, height));
                        }

                        return realElement;
                    }
                }
        };

    CKEDITOR.plugins.add('fakeobjects',
        {
            requires: ['htmlwriter'],

            afterInit: function (editor) {
                var dataProcessor = editor.dataProcessor,
                    htmlFilter = dataProcessor && dataProcessor.htmlFilter;

                if (htmlFilter)
                    htmlFilter.addRules(htmlFilterRules);
            }
        });

    CKEDITOR.editor.prototype.createFakeElement = function (realElement, className, realElementType, isResizable) {
        var lang = this.lang.fakeobjects,
            label = lang[realElementType] || lang.unknown;

        var attributes =
            {
                'class': className,
                src: CKEDITOR.getUrl('images/spacer.gif'),
                'data-cke-realelement': encodeURIComponent(realElement.getOuterHtml()),
                'data-cke-real-node-type': realElement.type,
                alt: label,
                title: label,
                align: realElement.getAttribute('align') || ''
            };

        if (realElementType)
            attributes['data-cke-real-element-type'] = realElementType;

        if (isResizable) {
            attributes['data-cke-resizable'] = isResizable;

            var fakeStyle = new cssStyle();

            var width = realElement.getAttribute('width'),
                height = realElement.getAttribute('height');

            width && (fakeStyle.rules.width = cssLength(width));
            height && (fakeStyle.rules.height = cssLength(height));
            fakeStyle.populate(attributes);
        }

        return this.document.createElement('img', {attributes: attributes});
    };

    CKEDITOR.editor.prototype.createFakeParserElement = function (realElement, className, realElementType, isResizable) {
        var lang = this.lang.fakeobjects,
            label = lang[realElementType] || lang.unknown,
            html;

        var writer = new CKEDITOR.htmlParser.basicWriter();
        realElement.writeHtml(writer);
        html = writer.getHtml();

        var attributes =
            {
                'class': className,
                src: CKEDITOR.getUrl('images/spacer.gif'),
                'data-cke-realelement': encodeURIComponent(html),
                'data-cke-real-node-type': realElement.type,
                alt: label,
                title: label,
                align: realElement.attributes.align || ''
            };

        if (realElementType)
            attributes['data-cke-real-element-type'] = realElementType;

        if (isResizable) {
            attributes['data-cke-resizable'] = isResizable;
            var realAttrs = realElement.attributes,
                fakeStyle = new cssStyle();

            var width = realAttrs.width,
                height = realAttrs.height;

            width != undefined && (fakeStyle.rules.width = cssLength(width));
            height != undefined && (fakeStyle.rules.height = cssLength(height));
            fakeStyle.populate(attributes);
        }

        return new CKEDITOR.htmlParser.element('img', attributes);
    };

    CKEDITOR.editor.prototype.restoreRealElement = function (fakeElement) {
        if (fakeElement.data('cke-real-node-type') != CKEDITOR.NODE_ELEMENT)
            return null;

        var element = CKEDITOR.dom.element.createFromHtml(
            decodeURIComponent(fakeElement.data('cke-realelement')),
            this.document);

        if (fakeElement.data('cke-resizable')) {
            var width = fakeElement.getStyle('width'),
                height = fakeElement.getStyle('height');

            width && element.setAttribute('width', replaceCssLength(element.getAttribute('width'), width));
            height && element.setAttribute('height', replaceCssLength(element.getAttribute('height'), height));
        }

        return element;
    };

})();
