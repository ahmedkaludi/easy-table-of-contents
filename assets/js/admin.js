jQuery(document).ready(function ($) {

    var ez_toc_color_picker = $('.ez-toc-color-picker');

    if (ez_toc_color_picker.length) {
        ez_toc_color_picker.wpColorPicker();
    }

    var ezTocSettingsWidth = document.getElementById('ez-toc-settings[width]');
    var ezTocSettingsCustomWidth = document.getElementById('ez-toc-settings[width_custom]');

    if(ezTocSettingsCustomWidth) {
        if(ezTocSettingsWidth.value != 'custom')
            ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "none";

        ezTocSettingsWidth.addEventListener('change', function () {
            if (document.getElementById('ez-toc-settings[width]').value == 'custom') {
                ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "revert";
            } else {
                ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "none";
            }
        });
    }

    $("#subscribe-newsletter-form").on('submit', function (e) {
        e.preventDefault();
        var $form = $("#subscribe-newsletter-form");
        var name = $form.find('input[name="name"]').val();
        var email = $form.find('input[name="email"]').val();
        var website = $form.find('input[name="company"]').val();
        $.post(ajaxurl, {action: 'eztoc_subscribe_newsletter', name: name, email: email, website: website, eztoc_security_nonce: cn_toc_admin_data.eztoc_security_nonce},
            function (data) {
            }
        );
    });
});

/**
 * DisableScrolling Function
 * @since 2.0.33
 */
function disableScrolling() {
    var x=window.scrollX;
    var y=window.scrollY;
    window.onscroll=function(){window.scrollTo(x, y);};
}
/**
 * EnableScrolling Function
 * @since 2.0.33
 */
function enableScrolling(){
    window.onscroll=function(){};
}

/**
 * unsecuredCopyToClipboard Function
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
/**
 * ez_toc_clipboard Function
 * Clipboard JS
 * @since 2.0.33
 */
function ez_toc_clipboard(id, tooltipId, $this, event) {
    event.preventDefault();
    disableScrolling();
    var copyText = $this.parentNode.parentNode.querySelectorAll("#" + id)[0];
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    // if (window.isSecureContext && navigator.clipboard) {
    // 	navigator.clipboard.writeText(content);
    // } else {
    unsecuredCopyToClipboard(copyText.value);
    // }

    var tooltip = $this.querySelectorAll('span.' + tooltipId)[0];
    tooltip.innerHTML = "Copied: " + copyText.value;
}
/**
 * ez_toc_outFunc Function
 * Clipboard JS
 * @since 2.0.33
 */
function ez_toc_outFunc(tooltipId, $this, event) {
    event.preventDefault();
    var tooltip = $this.querySelectorAll('span.' + tooltipId)[0];
    tooltip.innerHTML = "Copy to clipboard";
    enableScrolling();
}
