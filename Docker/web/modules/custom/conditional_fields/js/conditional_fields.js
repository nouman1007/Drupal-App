(function ($, Drupal) {
  Drupal.behaviors.conditionalFields = {
      attach: function (context, settings) {
        const $editFieldCardModifier = $('#edit-settings-block-form-field-card-0-subform-field-card-modifier--yDzlQuH6oU8', context);
        const $editFieldCardMedia = $('#eedit-settings-block-form-field-card-0-subform-field-media-wrapper--ykyyMIZBtD8', context);
        $('$editFieldCardModifier', context).once('conditionalFields').change(function () {
            if ($(this).val() === 'info_card') {
                $('$editFieldCardMedia').hide();
            } else {
                $('$editFieldCardMedia').show();
            }
        }).trigger('change');
      }
  };
})(jQuery, Drupal);
