(function ($, Drupal) {
  Drupal.behaviors.customSearch = {
    attach: function (context, settings) {
      $('#search-form').submit(function (event) {
        event.preventDefault();
        var query = $('#search-input').val();
        $.get('/custom-search', { q: query }, function (data) {
          // Update the view with search results
          $('#search-results').empty();
          data.forEach(function (result) {
            $('#search-results').append(
              `<div>
                <h3>${result.title}</h3>
                <p>${result.summary}</p>
              </div>`
            );
          });
        });
      });
    }
  };
})(jQuery, Drupal);