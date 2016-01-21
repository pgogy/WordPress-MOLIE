jQuery(document).ready(
	function(){
		jQuery("form#molie_link_form #molie_link_submit")
			.on("click", 
					function(){
					
						jQuery("#molie_response")
								.fadeOut(200);
					
						var data = {
							'action': 'molie_course_list',
							'url': jQuery("#canvas_url").val(),
							'token': jQuery("#canvas_token").val(),
							'nonce': molie_admin_link.nonce
						};

						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						jQuery.post(molie_admin_link.ajaxURL, data, function(response) {
							jQuery("#molie_response")
								.html(response);
							jQuery("#molie_response")
								.fadeIn(200);
						});
					}
			);
	}
);