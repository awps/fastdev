/**
 * Scripts
 *
 */
;(function ($) {

    "use strict";

    $(document).ready(function () {

        // General tooltips
        $('.fdtip').zTip();

        // Tooltips for tabs
        $('.fastdev-tab').zTip({
            source: function (elem) {
                return elem.next('div.fastdev-tab-tip').html();
            },
        });

        function fastdev_url_param(param, value) {
            var reg_exp = new RegExp(param + "(.+?)(&|$)", "g");
            var new_url = window.location.href.replace(reg_exp, param + "=" + value + "$2");
            window.history.pushState("", "", new_url);
        }

        function fastdev_filter(elem) {
            if ($(elem).length < 1) {
                return;
            }
            var value = $(elem).val().toLowerCase();

            // Hide or show keywords
            $(".fd-key-val-table .fd-kv-row").not('.fd-kv-head').each(function () {
                $(this).children('div.filter-this').text().toLowerCase().search(value) > -1 ? $(this).show() : $(this).hide();
            });
        }

        // Filter on document ready
        fastdev_filter('.fd-filter-field');

        // Filter when field value changes(real-time)
        $('.fd-filter-field').on('keyup', function () {
            fastdev_filter(this);
        });

        function fastdev_phpinfo_filter(elem) {
            var value = $(elem).val().toLowerCase();

            // Hide or show keywords
            $("#phpinfo table").each(function () {
                if ($(this).text().toLowerCase().search(value) > -1) {
                    $(this).show();
                    $(this).prev('h2').show();
                }
                else {
                    $(this).hide();
                    $(this).prev('h2').hide();
                }
            });

            $("#phpinfo table tr").not(".h").each(function () {
                $(this).text().toLowerCase().search(value) > -1 ? $(this).show() : $(this).hide();
            });
        }

        $('.fd-filter-field').on('keyup', function () {
            fastdev_phpinfo_filter(this);
        });

        //Events
        $('#fd-refresh-option, #fd-delete-option').on('click', function () {
            $(this).trigger('fastdev:option');
        });

        $(window).on('focus', function () {
            if ($('#fd-auto-refresh').is(':checked')) {
                $('#fd-refresh-option').trigger('fastdev:option');
            }
        });

        /* Delete and Refresh an option
        ------------------------------------*/
        $('#fd-refresh-option, #fd-delete-option').on('fastdev:option', function () {
            var _t = $(this),
                _action = (_t.hasClass('fd-button-delete')) ? 'fastdev_delete_option' : 'fastdev_refresh_option';

            if (_action == 'fastdev_delete_option') {
                if (!confirm('Warning: Deleting an option may result in a broken website! Are you sure you want to delete this option?')) {
                    return;
                }
            }

            _t.prepend('<span class="fastdev-loader"></span>').addClass('active');

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    "action": _action,
                    "option_id": _t.data('option'),
                    "nonce": _t.data('nonce'),
                },

                success: function (response) {
                    // console.log(response);
                    if (response) {
                        $('#fd-wpo-code-block').html(response);
                        if (_action == 'fastdev_refresh_option' && $('#fd-wpo-code-block pre').length > 0) {
                            var pre_block = $('#fd-wpo-code-block pre:not(.disable-highlight)');
                            if (pre_block.length > 0) {
                                Prism.highlightElement(pre_block[0]);
                            }
                        }
                    }
                },
                complete: function (jqXHR, textStatus) {
                    _t.removeClass('active').children('.fastdev-loader').remove();
                },

                timeOut: 1000 * 60 //1 minute

            });

        });

        $('#wp-option-edit-key').on('change', function () {
            var _t = $(this),
                _origin = _t.data('original-option-key'),
                _to = _t.val();
            if (!window.confirm('Warning: Are you sure that you want to change the key of this option? ')) {
                return;
            }

            _t.attr('disabled', 'disabled').css('opacity', 0.4);

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    "action": 'fastdev_edit_option_key',
                    "option_from": _origin,
                    "option_to": _to,
                },

                success: function (response) {
                    console.log(response);

                    _t.removeAttr('disabled').css('opacity', '');

                    if (response && response === 'success') {
                        fastdev_url_param('fd-get-option', _to);

                        $('#fd-refresh-option').data('option', _to);
                        $('#fd-delete-option').data('option', _to);
                        _t.data('original-option-key', _to);
                    }
                },
                complete: function (jqXHR, textStatus) {
                    // _t.removeClass('active').children( '.fastdev-loader' ).remove();
                },

                timeOut: 1000 * 60 //1 minute

            });

        });

        $(window).on('focus', function () {
            if ($('#testing-autorefresh').is(':checked')) {
                $('.js-fastdev-testing-form').trigger('submit');
            }
        });

        // Fill the testing field when the document is ready.
        var testing_form = $('.js-fastdev-testing-form');
        var function_name = sessionStorage.getItem('fastdev_last_testing_function');
        if (function_name && testing_form.length > 0) {
            testing_form.find('[name="function_name"]').val(function_name).trigger('change');
        }

        $(document).on('submit', '.js-fastdev-testing-form', function (event) {
            event.preventDefault();

            var button = $(this).find('.button');
            var function_name = $(this).find('[name="function_name"]').val();
            var nonce = $(this).find('[name="nonce"]').val();

            // Save this function name in Session storage.
            // So, we'll not have to type it again when coming back tho this page.
            sessionStorage.setItem('fastdev_last_testing_function', function_name);

            button.attr('disabled', true);

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    "action": 'fastdev_testing',
                    "function_name": function_name,
                    "nonce": nonce,
                },

                success: function (response) {
                    // console.log( response );
                    if (response) {
                        $('#js-fastdev-testing-result').html(response);
                        var pre_block = $('#js-fastdev-testing-result pre:not(.disable-highlight)');
                        if (pre_block.length > 0) {
                            Prism.highlightElement(pre_block[0]);
                        }
                    }
                },
                complete: function (jqXHR, textStatus) {
                    button.attr('disabled', false);

                    if ('error' === textStatus) {
                        $('#js-fastdev-testing-result').html('<div class="notice inline notice-error notice-alt">' +
                            '<h3>Oops! Looks like it\'s a server error there...</h3>' +
                            '</div>');
                    }
                },

                timeOut: 1000 * 60 //1 minute

            });

        });

        $('.toggle-string span').on('click', function () {
            var _this = $(this),
                _main_container = _this.parents('.fastdev-trimmed-string');

            if (_this.hasClass('open')) {
                _this.text(_this.data('expand'));
                _this.removeClass('open');
                _main_container.children('.original-string').slideUp(150);
                _main_container.children('.trimmed-string').slideDown(150);
            }
            else {
                _this.text(_this.data('collapse'));
                _this.addClass('open');
                _main_container.children('.original-string').slideDown(150);
                _main_container.children('.trimmed-string').slideUp(150);
            }
        });

        /*
        -------------------------------------------------------------------------------
        JSON parser
        -------------------------------------------------------------------------------
        */
        $(document).on('submit', '.js-fastdev-json-parser-form', function (event) {
            event.preventDefault();

            var wrapper = document.getElementById("js-fastdev-json-parser-result");
            var json_field =  $('#js-json-string');
            var json_string = $(this).find('.js-json-string').val();

            // button.attr('disabled', true);

            json_field.removeClass('json-string-needed');
            if(! have_json_string()){
                json_field.addClass('json-string-needed');
                $(wrapper).html('<div class="notice inline notice-error notice-alt">' +
                    '<h3>Please insert the JSON string in textarea the field.</h3></div>');
                return;
            }

            try {
                json_string = json_string.replace(/\\\\/g, '\\');
                json_string = JSON.parse(json_string);
            } catch (e) {
                $(wrapper).html('<div class="notice inline notice-error notice-alt">' +
                    '<h3>Oops! Looks like it\'s an error:</h3><p>' + e + '</p></div>');
                return;
            }

            // Clear the wrapper
            $(wrapper).html('');

            // Create json-tree
            var tree = jsonTree.create(json_string, wrapper);

            // Expand all (or selected) child nodes of root (optional)
            tree.expand(function (node) {
                return node.childNodes.length < 2;
            });

        });

        function reveal_cursor_postion() {
            var reveal = $('#js-cursor-position-reveal');
            $('#js-json-string').on('change input keypress keyup click', function (event) {
                var position = event.target.selectionStart;

                reveal.text(position);
            });
        }

        reveal_cursor_postion();

        function have_json_string() {
            return ($('#js-json-string').val()).replace(/^\s+|\s+$/g, '') !== '';
        }

    });

})(jQuery);
