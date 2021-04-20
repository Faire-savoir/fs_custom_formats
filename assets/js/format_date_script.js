(function ($) {
  $(document).ready(function () {
    $('.nb_other_dates').click(function () {
      if (format_date_mode == 'listing') {
        // En mode 'listing' on deplie les autres dates
        $(this).closest('.format_date_in_template').toggleClass('active').find('.other_dates ul').toggle();
      } else {
        // En mode 'detail' on scroll sur la strate des dates
        location.hash = '#group-ancrage-ouverture';
      }
    });
  });
})(jQuery);
