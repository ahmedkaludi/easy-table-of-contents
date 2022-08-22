jQuery(document).ready(function ($) {

    var ez_toc_color_picker = $('.ez-toc-color-picker');

    if (ez_toc_color_picker.length) {
        ez_toc_color_picker.wpColorPicker();
    }
    var tableBody = document.getElementById('eztoc-appearance');
    var tableRows = tableBody.getElementsByTagName('tr');
    var targetElement = tableRows[1];
    targetElement.style.display = "none";
    document.getElementById('ez-toc-settings[width]').addEventListener('change', function () {
        if (document.getElementById('ez-toc-settings[width]').value == 'custom') {
            targetElement.style.display = "revert";
        } else {
            targetElement.style.display = "none";
        }
    });
    $("#subscribe-newsletter-form").on('submit', function (e) {
        e.preventDefault();
        var $form = $("#subscribe-newsletter-form");
        var name = $form.find('input[name="name"]').val();
        var email = $form.find('input[name="email"]').val();
        var website = $form.find('input[name="company"]').val();
        $.post(ajaxurl, {action: 'eztoc_subscribe_newsletter', name: name, email: email, website: website},
            function (data) {
            }
        );
    });
});

/**
 * Clipboard JS
 * @since 2.0.33
 */
const unsecuredCopyToClipboard = (text) => {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy')
    } catch (err) {
        console.error('Unable to copy to clipboard', err)
    }
    document.body.removeChild(textArea)
};

function ez_toc_clipboard(id, tooltipId) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    // if (window.isSecureContext && navigator.clipboard) {
    // 	navigator.clipboard.writeText(content);
    // } else {
    unsecuredCopyToClipboard(copyText.value);
    // }

    var tooltip = document.getElementById(tooltipId);
    tooltip.innerHTML = "Copied: " + copyText.value;
}

function ez_toc_outFunc(tooltipId) {
    var tooltip = document.getElementById(tooltipId);
    tooltip.innerHTML = "Copy to clipboard";
}
