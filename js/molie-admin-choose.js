function molie_ajax_get(items){
	console.log(items.length);
	if(items.length!=0){
		item = items.shift();
		var data = {
			'action': 'molie_page_import',
			'module': jQuery(item).attr("module"),
			'module_name': jQuery(item).attr("module_name"),
			'item': jQuery(item).attr("id"),
			'course': jQuery(item).attr("course"),
			'nonce': molie_admin_choose.nonce
		};
		
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(molie_admin_choose.ajaxURL, data, function(response) {
			html = jQuery(item)
						.parent()
						.html();
			jQuery(item)
				.parent()
				.html(html + "<span>Page Linked</span>");
			molie_ajax_get(items);
			
		});
	}else{
		jQuery("div#molie_choose")
			.fadeOut(500);
		jQuery("div#molie_files")
			.fadeIn(500);
	}
}

jQuery(document).ready(
	function(){
		jQuery("form#molie_choose_form #molie_choose_submit")
			.on("click", 
					function(){
					
						items = Array();
					
						jQuery("form#molie_choose_form input:checked")
							.each(							
								function(index,value){									
									items.push(value);									
								}
							);
							
						molie_ajax_get(items);
					
					}
			);
	}
);