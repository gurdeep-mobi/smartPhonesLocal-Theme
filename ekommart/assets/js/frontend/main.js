(function ($) {
    'use strict';

    function login_dropdown() {
        $('.site-header-account').mouseenter(function () {
            $('.account-dropdown', this).append($('.account-wrap'));
        });
    }

    function megamenu_dropdown() {
        $('.icon-down-megamenu').click(function () {
            $(this).toggleClass('selected').siblings('.mega-menu').toggleClass('open');
        });
    }

    megamenu_dropdown();
    login_dropdown();
})(jQuery);

