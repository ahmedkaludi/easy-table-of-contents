var strict;

jQuery(document).ready(function ($) {
    /**
     * DEACTIVATION FEEDBACK FORM
     */
    // show overlay when clicked on "deactivate"
    eztoc_deactivate_link = $('.wp-admin.plugins-php tr[data-slug="easy-table-of-contents"] .row-actions .deactivate a');
    eztoc_deactivate_link_url = eztoc_deactivate_link.attr('href');

    eztoc_deactivate_link.click(function (e) {
        e.preventDefault();

        // only show feedback form once per 30 days
        var c_value = eztoc_admin_get_cookie("eztoc_hide_deactivate_feedback");

        if (c_value === undefined) {
            $('#eztoc-reloaded-feedback-overlay').show();
        } else {
            // click on the link
            window.location.href = eztoc_deactivate_link_url;
        }
    });
    // show text fields
    $('#eztoc-reloaded-feedback-content input[type="radio"]').click(function () {
        // show text field if there is one
        $(this).parents('li').next('li').children('input[type="text"], textarea').show();
    });
    // send form or close it
    $('#eztoc-reloaded-feedback-content .button').click(function (e) {
        e.preventDefault();
        // set cookie for 30 days
        var exdate = new Date();
        exdate.setSeconds(exdate.getSeconds() + 2592000);
        document.cookie = "eztoc_hide_deactivate_feedback=1; expires=" + exdate.toUTCString() + "; path=/";

        $('#eztoc-reloaded-feedback-overlay').hide();
        if ('eztoc-reloaded-feedback-submit' === this.id) {
            // Send form data
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'eztoc_send_feedback',
                    data: $('#eztoc-reloaded-feedback-content form').serialize()
                },
                complete: function (MLHttpRequest, textStatus, errorThrown) {
                    // deactivate the plugin and close the popup
                    $('#eztoc-reloaded-feedback-overlay').remove();
                    window.location.href = eztoc_deactivate_link_url;

                }
            });
        } else {
            $('#eztoc-reloaded-feedback-overlay').remove();
            window.location.href = eztoc_deactivate_link_url;
        }
    });
    // close form without doing anything
    $('.eztoc-feedback-not-deactivate').click(function (e) {
        $('#eztoc-reloaded-feedback-overlay').hide();
    });
    
    function eztoc_admin_get_cookie (name) {
	var i, x, y, eztoc_cookies = document.cookie.split( ";" );
	for (i = 0; i < eztoc_cookies.length; i++)
	{
		x = eztoc_cookies[i].substr( 0, eztoc_cookies[i].indexOf( "=" ) );
		y = eztoc_cookies[i].substr( eztoc_cookies[i].indexOf( "=" ) + 1 );
		x = x.replace( /^\s+|\s+$/g, "" );
		if (x === name)
		{
			return unescape( y );
		}
	}
}

}); // document ready