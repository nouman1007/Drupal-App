(($, Drupal) => {
  Drupal.behaviors.jumpLinks = {
    attach: function (context, settings) {
      $(context).find('.jump-link').on('click', function (e) {
        e.preventDefault();
        const targetId = $(this).attr('href').substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
          const offset = targetElement.getBoundingClientRect().top + window.scrollY - 60;
          window.scrollTo({
            top: offset,
            behavior: 'smooth'
          });
        }
      });
      $('.material-icons:last').remove();
    }
  };
})(jQuery, Drupal);