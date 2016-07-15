(function($) {
    $(function() {
        $('.card img').on('click', function() {
            // get img clicked img url
            var imgSrc = $(this).attr('src');
            var modalImg = $('.ui.modal').find('img');
            modalImg.attr('src', imgSrc);
            $('.ui.modal').modal('show');
        });
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#back-top').fadeIn();
            } else {
                $('#back-top').fadeOut();
            }
        });
        $('#back-top').click(function() {
            $('body, html').animate({
                scrollTop: 0
            }, 600);
            return false;
        });
        //detecting if user scroll to bottom
        window.addEventListener('scroll', function() {
            if (window.scrollY + screen.height == $(document).height()) {
                // console.log(window.scrollY);
                // console.log(screen.height);
                // console.log($(document).height());
                // console.log(window.scrollY + screen.height);
                // $.ajax({
                // });
                var html = "",
                    $container = $('.cards');
                $.ajax({
                    url: '',
                    type: 'GET',
                    success: function(data) {
                        for (var i = 0; i < data.length; i++) {
                            html += '<div class="card"><div class="image"><img src="' +
                                data[i].url +
                                '"></div><div class="content"><a class="header">' +
                                data[i].name +
                                '</a><div class="meta"><span class="date">' +
                                data[i].date +
                                '</span></div><div class="description">' +
                                data[i].tag +
                                '</div></div></div>';
                        }
                        $container.append(html);
                    },
                });
            }
        });
    });
})(jQuery);
