(function (Drupal, once) {
  Drupal.behaviors.search_keywords_block = {
    attach: function (context, settings) {
      var inputElement = document.querySelector('[data-drupal-selector="edit-keywords"]');
      if (inputElement) {
        var rawValue = inputElement.getAttribute('data-raw-value');
        if (rawValue) {
          inputElement.value = rawValue.replace(/&quot;/g, '"');
        }
      }
    }
  };
})(Drupal, once);
