jQuery(document).ready(function($) {

	var ez_toc_color_picker = $( '.ez-toc-color-picker' );

	if ( ez_toc_color_picker.length ) {
		ez_toc_color_picker.wpColorPicker();
	}
	var tableBody = document.getElementById('eztoc-appearance');
	var tableRows = tableBody.getElementsByTagName('tr');
	var targetElement = tableRows[1];
	targetElement.style.display = "none";
	document.getElementById('ez-toc-settings[width]').addEventListener('change', function () {
		if(document.getElementById('ez-toc-settings[width]').value == 'custom'){
			targetElement.style.display = "revert";
		}else{
			targetElement.style.display = "none";
		}
	});
});
