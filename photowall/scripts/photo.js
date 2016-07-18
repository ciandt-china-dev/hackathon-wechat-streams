(function($) {
    $(function() {
        $('.cards').on('click', 'img', function() {
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
        // console.log(window.scrollY);
        // console.log(screen.height);
        // console.log($(document).height());
        // console.log(window.scrollY + screen.height);
        // $.ajax({
        // });
        var html = "",
            $container = $('.cards');

        $('.cards').delegate('a[data-tag]', 'click', function(evt){
            evt.preventDefault();
            var tag = $(this).data('tag');
            render(tag);
        });

        var render = function(tag) {
            $.ajax({
                url: 'http://hackathon-wx3.ciandt.cn/api/photos/' + tag,
                type: 'GET',
                success: function(data) {
                    for (var i = 0; i < data.length; i++) {
                        var tagHTML = "";
                        if (data[i].tags) {
                            for (var j = 0; j < data[i].tags.length; j++) {
                                tagHTML += '<a href="#" data-tag="' + data[i].tags[j].label + '">' + '<div class="description">' + data[i].tags[j].label + '</div>' + '</a>';
                            }
                        }
                        html += '<div class="card"><div class="image"><img src="' +
                            data[i].picUrl +
                            '"></div><div class="content"><a class="header">' +
                            data[i].wxUser +
                            '</a><div class="meta"><span class="date">' +
                            data[i].updated_at +
                            '</span></div>' +
                            tagHTML +
                            '</div></div>';
                    }
                    $container.append(html);
                },
            });
        }

        render('all');
    });
})(jQuery);
