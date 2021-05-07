function scrollToTop(offset) {
    let toTop = document.createElement('div');
    let effIn = 'animation-rotatein';
    let effOut = 'animation-rotateout';

    toTop.className = 'hide-print pos-fixed pos10-r pos10-b bg-gray200 hover-bg-primary500 t-primary500 hover-t-white cursor-pointer t25px im-angle-double-up icon-circle trans05-all b-hide-imp b-shadow-var';

    document.body.append(toTop);

    if (window.pageYOffset > offset) {
        toTop.classList.remove(effOut);
        toTop.classList.remove('b-hide-imp');
        toTop.classList.add(effIn);
    }

    window.addEventListener('scroll', function () {
        if (window.pageYOffset > offset) {
            toTop.classList.remove(effOut);
            toTop.classList.remove('b-hide-imp');
            toTop.classList.add(effIn);
        } else {
            toTop.classList.remove(effIn);
            toTop.classList.add(effOut);
        }
    });

    toTop.onclick = function (event) {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    };
}

scrollToTop(100);

/* form invalid */
if (document.addEventListener) {
    document.addEventListener('invalid', function (e) {
        e.target.classList.add("js-form-invalid");
    }, true);
}

/* jQuery */
document.addEventListener("DOMContentLoaded", () => {
    // меню
    var is_touch = (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0));
    
    var menu = $('ul.menu');

    if (is_touch) {
        menu.removeClass('menu-hover');
        menu.addClass('menu-click');
    } else {
        if ($('ul.menu-tablet > li').css('float') != 'left') {
            menu.removeClass('menu-hover');
            menu.addClass('menu-click');
        }

        $(window).resize(function () {
            if ($('ul.menu-tablet > li').css('float') != 'left') {
                menu.removeClass('menu-hover');
                menu.addClass('menu-click');
            }
            else {
                menu.addClass('menu-hover');
                menu.removeClass('menu-click');
                $('ul.menu li ul').css('display', 'none');
                $('ul.menu li').removeClass('group-open');
            }
        });
    }

    $('nav').on('click', 'ul.menu-click li > a', function (e) {
        var href = $(this).attr("href");
        var ul = $(this).next();
        var li = $(this).parent('li');

        if (href === "#") {
            e.preventDefault();

            $('ul.menu li.group ul:visible').slideUp(200);
            $('ul.menu li.group').removeClass('group-open');

            if (ul.is(':visible')) {
                ul.slideUp(200);
                li.removeClass('group-open');
            } else {
                ul.stop().slideDown(200);
                li.addClass('group-open');
            }
        }
    });

    $('nav').on('mouseenter', 'ul.menu-hover li.group', function (e) {
        /* $(this).children('ul').slideDown(200); */
        $(this).children('ul').fadeIn(200);
    });

    $('nav').on('mouseleave', 'ul.menu-hover li.group', function (e) {
        /* $(this).children('ul').hide().slideUp(200); */
        $(this).children('ul').hide().fadeOut(200);
    });

    menu.removeClass('menu-no-load');

    if (is_touch) {
        menu.removeClass('menu-hover');
        menu.addClass('menu-click');
    }

});
