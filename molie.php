<?PHP

	/*
		Plugin Name: M.O.L.I.E
		Description: Linking a course between Instructure Canvas and WordPress
		Author: pgogy
		Version: 0.1
	*/
	
	class MOLIE{
	
		function __construct(){
			add_action("admin_menu", array($this, "menu_create"));
			add_action("admin_enqueue_scripts", array($this, "admin_scripts_and_styles"));
		}
	
		function admin_scripts_and_styles(){
			wp_register_style( 'molie_admin_css', plugins_url() . '/molie/css/molie-admin-style.css', false, '1.0.0' );
			wp_enqueue_style( 'molie_admin_css' );
			wp_enqueue_script( 'molie-admin-script', plugins_url() . '/molie/js/molie-admin-script.js', array( 'jquery' ) );
			wp_localize_script( 'molie-admin-script', 'molie_admin_script', 
																			array( 
																					'ajaxURL' => network_site_url() . "/wp-admin/admin-ajax.php",
																					'nonce' => wp_create_nonce("molie_admin_script")
																				) 
			);
		}
		
		function menu_create(){
			add_menu_page( __("M.O.L.I.E"), __("M.O.L.I.E"), "manage_options", "molie_mgmt", array($this,"mgmt"));
			add_submenu_page( "molie_mgmt", __("Getting your Token"), __("Getting your Token"), 'manage_options', "molie_token", array($this,"token"));
			add_submenu_page( "molie_mgmt", __("Link your course"), __("Link your course"), 'manage_options', "molie_link", array($this,"link"));
		}
		
		function mgmt(){
			?>
				<h1>M.O.L.I.E</h1>
				<p>
					Use "Scan" to scan the site for new files or size changes
				</p>
				<p>
					Use "Re-Scan" to scan the site for new files or size changes after the site has changed
				</p>
				<p>
					Use "Scan for IP" to scan the site for files with the poorly IP Address	
				</p>
			<?PHP
		}	
		
		function token(){
			?>
				<h1><?PHP echo __("Link a Course to WordPress"); ?></h1>
				<p>
					<?PHP echo __("Log into Canvas and visit"); ?> <a href="https://canvas.instructure.com/profile/settings"> <?PHP echo __("the settings page"); ?> </a>
				</p>
				<p>
					<?PHP echo __("Scroll down till you see"); ?><br />
					<img src="<?PHP echo plugins_url(); ?>/molie/img/NewAccessToken.png" />
				</p>
				<p>
					<?PHP echo __("Click on 'New Access Token'"); ?>	
				</p>
				<p>
					<?PHP echo __("A pop up window will appear"); ?><br />
					<img src="<?PHP echo plugins_url(); ?>/molie/img/NewAccessTokenPopUp.png" /><br />
					<?PHP echo __("Enter a reason, it can be a simple sentence explaining what you are doing. You can leave date blank"); ?><br />
					<?PHP echo __("Once you've entered a reason, click on 'Generate Token'"); ?><br />
				</p>
				<p>
					<?PHP echo __("The screen will update to show the following"); ?><br />
					<img src="<?PHP echo plugins_url(); ?>/molie/img/NewAccessTokenDisplay.png" /><br />
					<?PHP echo __("Note down the token at the top, as you won't be able to get it again. The token is in the area highlighted with a red border."); ?><br />
				</p>
			<?PHP
		}

		function link(){
			?>
				<h1><?PHP echo __("Link a Course to WordPress"); ?></h1>
				<form id="molie_link_form" action="javascript:function connect(){return false;};">
					<label><?PHP echo __("Enter the Canvas URL"); ?></label>
					<input id="canvas_url" type="text" />
					<label><?PHP echo __("Enter the Canvas Token"); ?></label>
					<input id="canvas_token" type="text" />
					<input type="submit" id="molie_link_submit" value="<?PHP echo __("Connect"); ?>" />
				</form>
				<div id="molie_response">
				</div>
			<?PHP
		}	
	
	}
	
	$MOLIE = new MOLIE();
	
	require_once("classes/molie-admin-ajax.php");