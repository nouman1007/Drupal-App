  // Checks for modal popup.
  // Check for URL before opening URL in new window.

//Modal popup step 1
jQuery('.continue-1.button').on('click', function() {

  var url = jQuery('#scaler-link-1').val();
  event.preventDefault();
  if ( url ) {
    if (jQuery(".link-input-error")[0]){
      jQuery('.link-input-error').remove();
    }
    window.open(url);
  }
  else {
    jQuery('#scaler-link-1').attr("aria-invalid", "true").attr("aria-describedby", "input-error");
    if (!jQuery(".link-input-error")[0]){
      jQuery('#scaler-link-1').before("<div class=\"link-input-error\" id=\"input-error\" tabindex=\"0\">Please enter your unique web address</div>");
      jQuery(".link-input-error")[0].focus();
    }

  }

});

//Remove input error message on modal popup close.
jQuery('.close').on('click', function() {
  if (jQuery(".link-input-error")[0]){
    jQuery('.link-input-error').remove();
  }
});


//Modal popup step 2
jQuery('.continue-2.button').on('click', function(e) {

  var url = jQuery('#scaler-link-2').val();
  e.preventDefault();
  if ( url ) {
    if (jQuery(".link-input-error-2")[0]){
      jQuery('.link-input-error-2').remove();
    }
    window.open(url);
  }
  else {
    jQuery('#scaler-link-2').attr("aria-invalid", "true").attr("aria-describedby", "input-error");
    if (!jQuery(".link-input-error-2")[0]){
      jQuery('#scaler-link-2').before("<div class=\"link-input-error-2\" id=\"input-error\" tabindex=\"0\">Please enter your unique web address</div>");
      jQuery(".link-input-error-2")[0].focus();
    }
  }
});

//Remove input error message on modal popup close.
jQuery('.close').on('click', function() {
  if (jQuery(".link-input-error-2")[0]){
    jQuery('.link-input-error-2').remove();
  }
});

//Modal popup step 3
jQuery('.continue-3.button').on('click', function(e) {

  var url = jQuery('#scaler-link-3').val();
  e.preventDefault();
  if ( url ) {
    if (jQuery(".link-input-error-3")[0]){
      jQuery('.link-input-error-3').remove();
    }
    window.open(url);
  }
  else {
    jQuery('#scaler-link-3').attr("aria-invalid", "true").attr("aria-describedby", "input-error");
    if (!jQuery(".link-input-error-3")[0]){
      jQuery('#scaler-link-3').before("<div class=\"link-input-error-3\" id=\"input-error\" tabindex=\"0\">Please enter your unique web address</div>");
      jQuery(".link-input-error-3")[0].focus();
    }
  }
});

//Remove input error message on modal popup close.
jQuery('.close').on('click', function() {
  if (jQuery(".link-input-error-3")[0]){
    jQuery('.link-input-error-3').remove();
  }
});
