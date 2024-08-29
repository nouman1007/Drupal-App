(function ($, Drupal, once) {
  Drupal.behaviors.facets_block = {
    attach: function (context, settings) {
      const formFacetsInputs = once('allFormFacets', 'form#facets input[type="checkbox"]', context);
      formFacetsInputs.forEach(formFacetsInput => {
        formFacetsInput.addEventListener('change', function(event) {
          formFacetsInput.closest('form').submit();
        });
      });

      const formFacetsMoreLinks = once('allFormFacetsMoreLinks', 'form#facets a', context);
      formFacetsMoreLinks.forEach(formFacetsMoreLink => {
        formFacetsMoreLink.addEventListener('click', function(event) {
          event.preventDefault();
          const hiddenDivs = formFacetsMoreLink.parentElement.querySelectorAll('div.hidden');
          hiddenDivs.forEach(function(div) {
            div.classList.remove('hidden');
          });
          this.remove();
        });
      });
    }
  };
})(jQuery, Drupal, once);
