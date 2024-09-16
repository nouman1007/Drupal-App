(function (Drupal, once) {
  Drupal.behaviors.facets_block = {
    attach: function (context, settings) {

      // Support hiding extra options.
      const softLimit = 4;
      const formFieldsets = once('allFormFacetsMoreLinks', 'form#facets-form fieldset', context);
      formFieldsets.forEach(formFieldset => {
        let i = 1;
        let hiddenCount = 0;
        formFieldset.querySelectorAll('.form-type-checkbox').forEach(formCheckboxWrapper => {
          if (i > softLimit) {
            if (!formCheckboxWrapper.querySelector("input[type='checkbox']").checked) {
              formCheckboxWrapper.classList.add('hidden');
              hiddenCount++;
            }
          }
          i++;
        });
        if (hiddenCount == 1) {
          formFieldset.querySelectorAll('.form-type-checkbox')
            .forEach(formCheckboxWrapper => {
              formCheckboxWrapper.classList.remove('hidden');
            });
          hiddenCount = 0;
        }
        if (hiddenCount > 0) {
          let showMoreLink = document.createElement("a");
          showMoreLink.href = "#";
          showMoreLink.textContent = "Show (" + hiddenCount + ") More";
          showMoreLink.addEventListener("click", function(event) {
            event.preventDefault();
            formFieldset.querySelectorAll('.form-type-checkbox')
              .forEach(formCheckboxWrapper => {
                formCheckboxWrapper.classList.remove('hidden');
              });
          });
          formFieldset.appendChild(showMoreLink);
        }
      });

      // Support unhiding extra options.
      const formFacetsMoreLinks = once('allFormFacetsMoreLinks', 'form#facets-form a', context);
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

      // Support form submission on facet field checkbox click.
      const formFacetsInputs = once('allFormFacets', 'form#facets-form input[type="checkbox"]', context);
      formFacetsInputs.forEach(formFacetsInput => {
        formFacetsInput.addEventListener('change', function(event) {
          formFacetsInput.closest('form').submit();
        });
      });

      // Support form submission via "Sort by" field in Search Sort Form Block.
      const formSortInputs = once('formSortInput', 'form#search-sort-form select', context);
      if (formSortInputs.length == 1) {
        formSortInputs[0].addEventListener('change', function(event) {
          const facetsForm = document.querySelector('form#facets-form');
          facetsForm.querySelector('input[data-drupal-selector="edit-sort-by"]')
            .value = formSortInputs[0].value;
          facetsForm.submit();
        });
      }
    }
  };
})(Drupal, once);
