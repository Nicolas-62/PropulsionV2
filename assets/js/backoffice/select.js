// js/backoffice/select.js


function formatState (option) {
    console.log(option);
    let url = $(option.element).attr('data-thumbnail-path');
    if (!option.id) {
        return option.text;
    }
    var $option = $(
        '<span><img src="' + url + '" class="image-icon" /> ' + option.text + '</span>'
    );
    return $option;
}

$('.custom-select').select2({
    allowClear: true,
    theme: 'bootstrap-5',
    templateResult: formatState,
    // templateSelection: formatState
});