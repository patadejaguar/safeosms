/*
 * Tabs 3 extensions
 *
 * Copyright (c) 2007 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
 
(function($) {
    
    /*
     * Rotate
     */
    $.extend($.ui.tabs.prototype, {
        rotation: null,
        rotate: function(ms) {
            var self = this;
            function stop(e) {
                if (e.clientX) { // only in case of a true click
                    clearInterval(self.rotation);
                }
            }
            // start interval
            if (ms) {
                var t = 0;
                this.rotation = setInterval(function() {
                    t = ++t <= self.$tabs.length ? t : 1;
                    self.click(t);
                }, ms);
                this.$tabs.bind(this.options.event, stop);
            }
            // stop interval
            else {
                clearInterval(this.rotation);
                this.$tabs.unbind(this.options.event, stop);
            }
        }
    });

    $.fn.tabsRotate = function(ms) {
        return this.each(function() {
            $.ui.tabs.getInstance(this).rotate(ms);
        });
    };

})(jQuery);