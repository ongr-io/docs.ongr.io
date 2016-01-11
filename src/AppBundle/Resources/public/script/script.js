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

            //this.bindDropdown(g.sidebar.dropdown);
        },

        /**
         *
         * @param trigger
         */
        bindDropdown: function(trigger) {
            $(trigger).click(function() {
                //console.log($(this).children());
                $(this).next().toggleClass('hidden', 'fade');
            });
        }
    };

    var App = Object.create(ONGR); App.init();
})(jQuery);