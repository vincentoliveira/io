/* 
 * Main.js
 */
var app = function() {

    var init = function() {

        $(window).resize(function() {

            function viewport() {
                var e = window,
                    a = 'inner';
                if (!('innerWidth' in window)) {
                    a = 'client';
                    e = document.documentElement || document.body;
                }
                return {
                    width: e[a + 'Width'],
                    height: e[a + 'Height']
                };
            }

            var value = viewport().width;

            if (value < 767) {
                $('.sidebar').addClass('sidebar-toggle');
                $('.main-content-wrapper').addClass('main-content-toggle-left');
            }

        });


        resize();
        tooltips();
        toggleMenuLeft();
        toggleMenuRight();
        menu();
        togglePanel();
        closePanel();
    };

    //global functions
    var resize = function() {
        $(window).resize(function() {
            $('#main-content, .nano').height($(window).height() - 80).css('padding-bottom', '100px');
            $('#inbox-wrapper').height($(window).height() - 80).css('padding-bottom', '100px');
        });

        $(window).trigger('resize');
    };

    var tooltips = function() {
        $('#toggle-left').tooltip();
    };

    var togglePanel = function() {
        $('.actions > .fa-chevron-down').click(function() {
            $(this).parent().parent().next().slideToggle('fast');
            $(this).toggleClass('fa-chevron-down fa-chevron-up');
        });

    };

    var toggleMenuLeft = function() {
        $('#toggle-left').bind('click', function(e) {
            if (!$('.sidebarRight').hasClass('.sidebar-toggle-right')) {
                $('.sidebarRight').removeClass('sidebar-toggle-right');
                $('.main-content-wrapper').removeClass('main-content-toggle-right');
            }
            $('.sidebar').toggleClass('sidebar-toggle');
            $('.main-content-wrapper').toggleClass('main-content-toggle-left');
            e.stopPropagation();
        });
    };

    var toggleMenuRight = function() {
        $('#toggle-right').bind('click', function(e) {
            if (!$('.sidebar').hasClass('.sidebar-toggle')) {
                $('.sidebar').addClass('sidebar-toggle');
                $('.main-content-wrapper').addClass('main-content-toggle-left');
            }
            $('.sidebarRight').toggleClass('sidebar-toggle-right');
            $('.main-content-wrapper').toggleClass('main-content-toggle-right');
            e.stopPropagation();
        });
    };

    var closePanel = function() {
        $('.actions > .fa-times').click(function() {
            $(this).parent().parent().parent().fadeOut();
        });

    }

    var menu = function() {
        $(".nano").nanoScroller();
        $("#leftside-navigation .sub-menu a").click(function() {
            $("#leftside-navigation ul ul").slideUp();
            if (!$(this).next().is(":visible")) {
                $(this).next().slideDown();
            }

        });
    };
    return {
        init: init
    }
    //End functions
}();
        
$(document).ready(function() {
    app.init();
    
    $('.confirm-delete').click(function(e) {
        return confirm('Etes-vous sûr de vouloir supprimer cet élément ?');
    })
});
