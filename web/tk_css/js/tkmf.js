if(NodeList.prototype.forEach === undefined) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

// gestion des buttons en liste, affichage du contenu selon le clic
document.querySelectorAll('div.tkmf_btn-list > a').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        var div = this.parentNode.querySelector('div');
        var par = this.parentNode;
        if(!par.classList.contains('empty')) {
            var divs = par.querySelectorAll('div.tkmf_btn-list');
            // on replie tout les enfants ayant été ouvert lorsque le parent est fermé
            if(divs.length > 0) {
                divs.forEach(function (div) {
                    div.classList.remove('open');
                    div.classList.remove('active');
                });
                par.classList.toggle('active');
            } else {
                par.classList.toggle('open');
            }
        } else {
            if(par.classList.contains('open'))
                par.classList.remove('open');
        }
        e.preventDefault();
    });
});

// jQuery
(function($) {

    var close_search_box = function () {
        var $button = $('.search');
        var $search = $('.search-flyout');

        $button.toggleClass('active');
        $search.toggleClass('open');

        if($button.hasClass('active')) {
            $('.search-wrapper').css('display', 'block');
            $search.css('height', parseInt($('.search-wrapper').outerHeight()) + 'px');
            $('#theme-search-box').focus().select();
        } else {
            $search.css('height', '0px');
        }
    };

    // bouton de recherche
    $('.search, .search-wrapper .close').on('click', function (e) {
        e.preventDefault();
        close_search_box();
    });
    $('#searchQueryFormSubmit').on('click', function (e) {
        e.preventDefault()
        $(this).parent().submit();
    });

    // plugin pour faire suivre à un element le scroll de la page
    $.fn.followScrool = function () {
        var $el = $(this.selector);
        if($el.length < 1) return;

        var originalY = $el.offset().top;
        var topMargin = 10;

        $el.css('position', 'relative');
        $(window).on('scroll', function () {
            var scrollTop = $(window).scrollTop();
            $el.stop(false, false).animate({
                top: (scrollTop < originalY)
                    ? 0 : scrollTop - originalY + topMargin
            }, 300);
        });
    };
}( jQuery ));