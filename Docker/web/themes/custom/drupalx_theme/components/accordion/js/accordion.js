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

      // Move the logic inside the attach function
      const validPaths = [
        '/grantees-sponsors/evaluation-resources/scaler-resources',
        '/node/442710/layout',
        '/node/442670/layout'
      ];

      if (validPaths.includes(window.location.pathname)) {
        document.body.classList.add('scaler-resources');
      }
    }
  };
})(Drupal);