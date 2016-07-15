(function($) {
    $(function() {
        $('.card').on('click', function() {
            // get img clicked img url
            var imgSrc = $(this).find('img').attr('src');
            var modalImg = $('.ui.modal').find('img');
            modalImg.attr('src', imgSrc);
            $('.ui.modal').modal('show');
        });
        //detecting if user scroll to bottom
        window.addEventListener('scroll', function() {
          if(window.scrollY + screen.height == $(document).height()) {
            // console.log(window.scrollY);
            // console.log(screen.height);
            // console.log($(document).height());
            // console.log(window.scrollY + screen.height);
            // $.ajax({
            // });
          }
        });
    });
})(jQuery);
