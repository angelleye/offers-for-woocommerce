(function ( $ ) {
	"use strict";

	$(function () {

		$(document).ready(function() {				
			// Admin Footer Scripts - Custom Pointers 
		   //<![CDATA[
			$('#angelleye-woocommerce-offers-pointer-output-div').pointer({
			content: '<?php echo $pointer_content; ?>',
			position: 'top',
			close: function() {
				// Once the close button is hit
			}
			}).pointer('open');
		   	//]]>
		});
	});

}(jQuery));