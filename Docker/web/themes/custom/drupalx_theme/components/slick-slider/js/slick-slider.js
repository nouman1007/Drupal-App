!((document, Drupal, $) => {
  'use strict';

    /**
   * Behaviors for the Slick Slider.
   */
  Drupal.behaviors.Slider = {
    attach: function (context) {
      const $slider = $('.slider__items', context);

      // Slick initialization
      $slider.slick({
        arrows: true,
        dots: true,
        infinite: true,
        autoplay: false,
        slidesToShow: 4,
        adaptiveHeight: true
      });
    }
  };
})(document, Drupal, jQuery);