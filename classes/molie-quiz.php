<?PHP

	class MOLIEquiz{
	
		function __construct(){
			add_action("admin_menu", array($this, "menu_create"));
			add_action("admin_enqueue_scripts", array($this, "admin_scripts_and_styles"));
		}
	
		function admin_scripts_and_styles(){
			wp_register_style( 'molie_admin_choose_css', plugins_url() . '/molie/css/molie-admin-choose.css', false, '1.0.0' );
			wp_enqueue_style( 'molie_admin_choose_css' );
			wp_enqueue_script( 'molie-admin-choose', plugins_url() . '/molie/js/molie-admin-choose.js', array( 'jquery' ) );
			wp_localize_script( 'molie-admin-choose', 'molie_admin_choose', 
																			array( 
																					'ajaxURL' => network_site_url() . "/wp-admin/admin-ajax.php",
																					'nonce' => wp_create_nonce("molie_admin_choose")
																				) 
			);
		}
		
		function menu_create(){
			add_submenu_page( "molie_mgmt", __("Choose course content"), __("Choose Course Content"), 'manage_options', "molie_quiz", array($this,"quiz"));
		}
		
		function quiz(){
			print_r($_POST);
		}
	
	}
	
	$MOLIEquiz = new MOLIEquiz();
	