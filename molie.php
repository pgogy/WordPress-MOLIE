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
		}
	
		
		function menu_create(){
			add_menu_page( __("M.O.L.I.E"), __("M.O.L.I.E"), "manage_options", "molie_mgmt", array($this,"mgmt"));
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
		
	}
	
	$MOLIE = new MOLIE();
	
	require_once("classes/ajax/molie-course-ajax.php");
	require_once("classes/ajax/molie-file-ajax.php");
	require_once("classes/ajax/molie-quiz-ajax.php");
	require_once("classes/ajax/molie-assignment-ajax.php");
	require_once("classes/ajax/molie-choose-ajax.php");
	require_once("classes/ajax/molie-discussion-ajax.php");
	require_once("classes/post_types/molie-course-post.php");
	require_once("classes/post_types/molie-quiz-post.php");
	require_once("classes/post_types/molie-discussion-post.php");
	require_once("classes/post_types/molie-quiz-answer-post.php");
	require_once("classes/molie-admin-roles.php");
	require_once("classes/molie-help.php");
	require_once("classes/molie-link.php");
	require_once("classes/molie-choose.php");
	require_once("classes/molie-files.php");
	require_once("classes/molie-quiz.php");
	require_once("classes/molie-discussion.php");
	require_once("classes/molie-assignments.php");
	require_once("classes/molie-guide.php");
	require_once("classes/molie-menu.php");