/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function($) {
    var ONGR = {
        /**
         * Global DOM selectors
         * used for JavaScript bindings
         */
        globals: {
            sidebar: {
                dropdown: '.sidebar-dropdown'
            },
            search: {
                autocomplete: '#search-input'
            }
        },

        /**
         * Main app initialization
         */
        init: function() {
            /**
             * Reference to globals` object
             * @type {ONGR.globals}
             */
            var g = this.globals;

            $(g.search.autocomplete).autocomplete(
                {
                    minLength: 3,
                    source: function (request, response) {
                        $.ajax({
                            url: this.element.closest('form').prop('action'),
                            dataType: 'json',
                            data: {
                                q: request.term
                            },
                            success: function (data) {
                                response($.map(data, function(item) {
                                    return {
                                        value: item.title,
                                        url: item.url,
                                        description: item.description
                                    };
                                }));
                            }
                        });
                    },
                    select: function (event, ui) {
                        window.location = ui.item.url;
                    }
                }
            );

            $(g.search.autocomplete).data('ui-autocomplete')._renderItem = function (ul, item) {
                return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append($('<span>')
                        .append($('<strong>').text(item.label))
                        .append($('<br><small>' + item.description + '</small>')))
                    .appendTo(ul);
            };

            $(g.search.autocomplete).data('ui-autocomplete').menu.element.css(
                'max-width',
                $(g.search.autocomplete).css('width')
            );
            //this.bindDropdown(g.sidebar.dropdown);
        },

        /**
         *
         * @param trigger
         */
        bindDropdown: function(trigger) {
            $(trigger).click(function() {
                $(this).next().toggleClass('hidden', 'fade');
            });
        }
    };

    var App = Object.create(ONGR);
    App.init();

})(jQuery);
