// bo/js/select.js
console.info("welcome to select.js");


(function() {'use strict';
function formatState (option) {
    console.log(option)
    let url = $(option.element).attr('data-thumbnail-path');
    if (!option.id) {
        return option.text;
    }
    var $option = $(
        '<span><img src="' + url + '" class="image-icon" /> ' + option.text + '</span>'
    );
    return $option;
}

    // When DOM is ready.
    $(document).ready(function() {
        $('.custom-select').select2({
            allowClear: true,
            theme: 'bootstrap-5',
            templateResult: formatState,
            placeholder: 'Liste de médias existants'
            // templateSelection: formatState
        });
    });
})();