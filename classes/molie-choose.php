<?PHP

	class MOLIEchoose{
	
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
			add_submenu_page( "molie_mgmt", __("Choose course content"), __("Choose Course Content"), 'manage_options', "molie_choose", array($this,"choose"));
		}
		
		function choose(){
			if(wp_verify_nonce($_POST['molie-link-nonce'], "molie-link"))
			{
				$course_details = explode("|", $_POST['molie_course']);
				$posts = get_posts(array("post_type" => 'linkedcanvascourse'));
				$post_insert = true;
				foreach($posts as $post){
					if($post->post_title==$course_details[1]){
						$post_insert = false;
						$course = $post->ID;
					}
				}
				if($post_insert){
					$course = wp_insert_post(
												array(
													"post_type" => 'linkedcanvascourse',
													"post_status" => 'publish',
													"post_title" => $course_details[1],
													"post_author" => get_current_user_id()
												)
											);
					update_post_meta($course, "courseID", $course_details[0], true);
					update_post_meta($course, "courseToken", $_POST['token'], true);
					update_post_meta($course, "courseURL", $_POST['url'], true);
				}
				require_once(__DIR__ . "/../API/Psr4AutoloaderClass.php");
				$loader = new Psr4AutoloaderClass;
				$loader->register();
				$loader->addNamespace('InstructureCanvasAPI', __DIR__ . "/../API/InstructureCanvasAPI/src");
				
				$API = new InstructureCanvasAPI\InstructureCanvasAPI( 
																	array(
																		"site" => $_POST['url'],
																		"token" => $_POST['token'],
																		"webService" => "CURL"
																	)
																);
				
				$modules = new InstructureCanvasAPI\Courses\Modules\Modules();
				$modules->setAPI($API);
				$modules->setCourseID($course_details[0]);
				$data = $modules->getModules($course_details[0]);
				if($data){
					if(count($data->content)!=0){	
						echo "<div id='molie_choose'>";
						echo "<h2>" . __("Modules and Pages in this course") . "</h2>";
						echo "<p>" . __("Select the pages you'd like to import") . "</p>";
						echo '<form id="molie_choose_form" action="javascript:function connect(){return false;};">';
						echo "<input type='submit' id='molie_choose_submit' value='" . __("Import pages") . "' />";
						foreach($data->content as $module){
							echo "<p>" . __("Module") . " : " . $module->name . "</p>";
							echo "<ul>";
							$moduleItems = $modules->getModuleItems($module->id);
							foreach($moduleItems->content as $item){
								echo "<li><input checked course='" . $course . "' module_name='" . addslashes($module->name) . "' module='" . $module->id . "' type='checkbox' id='" . $item->id . "'/>" . $item->title . "</li>";
							}
							echo "</ul>";
						}
						echo "<input type='submit' id='molie_choose_submit' value='" . __("Import pages") . "' />";
						echo "</form>";
						echo "</div>";
						echo "<div id='molie_files' style='display:none'>";
						echo "<p>" . __("Page Linking complete") . "</p>";
						echo "<form action='" . admin_url("admin.php?page=molie_quiz") . "' method='post'>";
						echo "<input type='hidden' name='' value='" . $course_details[0] . "' />";
						echo "<p><input type='submit' value='" . __("Now let us get assignments and quizzes") . "' /></p>";
						echo "</div>";
					}
				}
				$loader->unregister();
			}
			else
			{
				print_r($_POST);
			}
		}	
	
	}
	
	$MOLIEchoose = new MOLIEchoose();
	