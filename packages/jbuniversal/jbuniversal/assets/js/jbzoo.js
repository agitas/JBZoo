/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo', {}, {

        /**
         * Link to global helper
         */
        jbzoo: window.JBZoo,

        /**
         * Ajax process flag
         */
        isAjax: false,

        /**
         * Custom ajax handler
         * @param options = {
         *      'url'     : 'index.php?format=raw&tmpl=component',
         *      'data'    : {},
         *      'dataType': 'json',
         *      'success' : false,
         *      'error'   : false,
         *      'onFatal' : function () {}
         *  }
         */
        ajax: function (options) {

            var $this = this;

            this.isAjax = true;

            JBZoo.logger('w', 'ajax::request', options);

            var options = $.extend({}, {
                'url'     : 'index.php?format=raw&tmpl=component',
                'data'    : {},
                'dataType': 'json',
                'success' : false,
                'error'   : false,
                'onFatal' : function (responce) {
                    if (JBZoo.DEBUG) {
                        JBZoo.logger('e', 'ajax::request - ' + options.url, options.data);
                        JBZoo.dump(responce.responseText, 'Ajax error responce:');
                    }

                    $.error("Ajax response no parse");
                }
            }, options);

            if (JBZoo.empty(options.url)) {
                $this.error("AJAX url is no set!");
            }

            // set default request data
            options.data = $.extend({}, {
                'nocache': Math.random(),
                'option' : 'com_zoo',
                'tmpl'   : 'component',
                'format' : 'raw'
            }, options.data);

            $.ajax({
                'url'     : options.url,
                'data'    : options.data,
                'dataType': options.dataType,
                'type'    : 'POST',
                'cache'   : false,
                'headers' : {
                    "cache-control": "no-cache"
                },
                'success' : function (data) {

                    $this.isAjax = false;

                    if (typeof data == 'string') {
                        data = $.trim(data);
                    }

                    if (options.dataType == 'json') {
                        //JBZoo.logger('i', 'ajax::responce', {'result': data.result, 'message': data.message});

                        if (data.result && $.isFunction(options.success)) {
                            options.success.apply(this, arguments);
                        } else if (!data.result && $.isFunction(options.error)) {
                            options.error.apply(this, arguments);
                        }

                    } else if ($.isFunction(options.success)) {
                        options.success.apply(this, arguments);
                    }

                },
                'error'   : function () {
                    $this.isAjax = false;
                    options.onFatal(arguments);
                }
            });
        },

        /**
         * Get data from parent or nested element
         * @param key
         * @param selector
         * @returns {*}
         */
        data: function (key, selector) {
            if (selector) {
                return this.$(selector).data(key);
            }
            return this.el.data(key);
        },

        /**
         * Get attr from parent or nested element
         * @param attr
         * @param selector
         * @returns {*}
         */
        attr: function (attr, selector) {
            if (selector) {
                return this.$(selector).attr(attr);
            }
            return this.el.attr(attr);
        },

        /**
         * Plugin fatal error
         * @param message
         */
        error: function (message) {
            return JBZoo.error('Plugin "' + this._name + '": ' + message);
        },

        /**
         * @param key
         * @returns String
         */
        _: function (key) {
            return key;
        }

    });

})(jQuery, window, document);