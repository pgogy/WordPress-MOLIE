jQuery(document).ready(
	function(){
		jQuery("form#molie_link_form #molie_link_submit")
			.on("click", 
					function(){
						var data = {
							'action': 'molie_course_list',
							'whatever': 1234,
							'nonce': molie_admin_script.nonce
						};

						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						jQuery.post(molie_admin_script.ajaxURL, data, function(response) {
							jQuery("#molie_response")
								.html(response);
						});
					}
			);
	}
);