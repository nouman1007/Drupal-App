(function (Drupal) {
  Drupal.behaviors.scalerSteps = {
    attach: function (context, settings) {
      function validateInput() {
        const input = context.querySelector('[id^="scaler-link"]');
        if (!input.value) {
          alert('Please enter a unique web address.');
          input.focus();
          return false;
        }
        return true;
      }

      // Attach event listener or call validateInput as needed
      const inputElement = context.querySelector('[id^="scaler-link"]');
      if (inputElement) {
        inputElement.addEventListener('blur', validateInput);
      }
    }
  };
})(Drupal);