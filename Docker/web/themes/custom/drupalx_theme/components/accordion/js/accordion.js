(function (Drupal) {
  Drupal.behaviors.toggleAccordionText = {
    attach: function (context, settings) {
      context.querySelectorAll('a.accordion-toggler').forEach(function(toggler) {
        toggler.addEventListener('click', function() {
          var text = toggler.textContent;
          toggler.textContent = text === 'Collapse all' ? 'Expand all' : 'Collapse all';
          toggler.blur(); // Clear link focus after click
        });
      });
    }
  };
})(Drupal);