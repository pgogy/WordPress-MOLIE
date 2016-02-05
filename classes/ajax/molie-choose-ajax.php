<?PHP
	
	class MOLIEchooseAjax{
	
		function __construct(){;
			add_action("wp_ajax_molie_page_import", array($this, "page_import"));
		}
		
		private function create_categories($post){
		
			$categories = array();
		
			$course_category = get_post_meta($post->ID, "course_category_id", true);
			if($course_category==""){
				$course_category = wp_create_category( $post->post_title );
				add_post_meta($post->ID, "course_category_id", $course_category, true);
			}			
			array_push($categories, $course_category);
			
			if(isset($_POST['module'])){
				$module_category = get_post_meta($post->ID, "course_module_" . $_POST['module'], true);
				if($module_category==""){
					$module_category = wp_create_category( stripslashes($_POST['module_name']), $course_category );
					add_post_meta($post->ID, "course_module_" . $_POST['module'], $module_category, true);
				}
				array_push($categories, $module_category);
			}
			return $categories;
		}
		
		private function get_post_url($post){
			require_once(__DIR__ . "/../../API/Psr4AutoloaderClass.php");
			$loader = new Psr4AutoloaderClass;
			$loader->register();
			$loader->addNamespace('InstructureCanvasAPI', __DIR__ . "/../../API/InstructureCanvasAPI/src");
					
			$API = new InstructureCanvasAPI\InstructureCanvasAPI( 
															array(
																"site" => get_post_meta($post->ID, "courseURL", true),
																"token" => get_post_meta($post->ID, "courseToken", true),
																"webService" => "CURL"
															)
														);
				
			$modules = new InstructureCanvasAPI\Courses\Modules\Modules();
			$modules->setAPI($API);
			$modules->setCourseID(get_post_meta($post->ID, "courseID", true));
			$data = $modules->getModuleItem($_POST['module'], $_POST['item']);
			return $data->content->page_url;
		}
		
		private function get_post_content($post, $pageURL){
			require_once(__DIR__ . "/../../API/Psr4AutoloaderClass.php");
			$loader = new Psr4AutoloaderClass;
			$loader->register();
			$loader->addNamespace('InstructureCanvasAPI', __DIR__ . "/../../API/InstructureCanvasAPI/src");
					
			$API = new InstructureCanvasAPI\InstructureCanvasAPI( 
															array(
																"site" => get_post_meta($post->ID, "courseURL", true),
																"token" => get_post_meta($post->ID, "courseToken", true),
																"webService" => "CURL"
															)
														);
				
			$pages = new InstructureCanvasAPI\Courses\Pages\Pages();
			$pages->setAPI($API);
			$pages->setCourseID(get_post_meta($post->ID, "courseID", true));
			$data = $pages->getPage($pageURL);
			return $data;
		}
		
		private function get_home_page($post){
			require_once(__DIR__ . "/../../API/Psr4AutoloaderClass.php");
			$loader = new Psr4AutoloaderClass;
			$loader->register();
			$loader->addNamespace('InstructureCanvasAPI', __DIR__ . "/../../API/InstructureCanvasAPI/src");
					
			$API = new InstructureCanvasAPI\InstructureCanvasAPI( 
															array(
																"site" => get_post_meta($post->ID, "courseURL", true),
																"token" => get_post_meta($post->ID, "courseToken", true),
																"webService" => "CURL"
															)
														);
				
			$page = new InstructureCanvasAPI\Courses\FrontPage\FrontPage();
			$page->setAPI($API);
			$page->setCourseID(get_post_meta($post->ID, "courseID", true));
			$data = $page->getFrontPage();
			return $data;
		}
		
		private function get_course_syllabus($post){
			require_once(__DIR__ . "/../../API/Psr4AutoloaderClass.php");
			$loader = new Psr4AutoloaderClass;
			$loader->register();
			$loader->addNamespace('InstructureCanvasAPI', __DIR__ . "/../../API/InstructureCanvasAPI/src");
					
			$API = new InstructureCanvasAPI\InstructureCanvasAPI( 
															array(
																"site" => get_post_meta($post->ID, "courseURL", true),
																"token" => get_post_meta($post->ID, "courseToken", true),
																"webService" => "CURL"
															)
														);
				
			$course = new InstructureCanvasAPI\Courses\Courses();
			$course->setAPI($API);
			$data = $course->getCoursesForThisUserWithSyllabus();
			$courseID = get_post_meta($post->ID, "courseID", true);
			foreach($data->content as $course){
				if($course->id == $courseID){
					$syllabus = new StdClass();
					$syllabus->content = new StdClass();
					$syllabus->content->title = __("Syllabus");
					$syllabus->content->url = "course-syllabus";
					$syllabus->content->body = $course->syllabus_body;
					$syllabus->published = 1;
				}
			}
			return $syllabus;
		}
	
		function page_import(){
			if(wp_verify_nonce($_POST['nonce'], "molie_admin_choose"))
			{
			
				$course = $_POST['course'];
				$post = get_post($course);
				
				if($post){
				
					$categories = $this->create_categories($post);
					
					if($_POST['item']=="course-home-page"){
						$post_url = "course-home-page";
					}
					else if($_POST['item']=="course-syllabus"){
						$post_url = "course-syllabus";
					}
					else
					{
						$post_url = $this->get_post_url($post);
					}
					
					if(get_post_meta($post->ID, "course_" . $post->ID . "_" . $post_url, true)==""){
					
						if($_POST['item']=="course-home-page"){
							$post_content = $this->get_home_page($post);
						}else if ($_POST['item']=="course-syllabus"){
							$post_content = $this->get_course_syllabus($post);
						}else{
							$post_content = $this->get_post_content($post, $post_url);
						}
						
						if($post_content->content->published==1){
							$publish = "publish";
						}else{
							$publish = "draft";
						}
						
						$page = wp_insert_post(
													array(
														"post_type" => 'post',
														"post_status" => $publish,
														"post_title" => $post_content->content->title,
														"post_author" => get_current_user_id(),
														"post_content" => $post_content->content->body
													)
												);
																		
						update_post_meta($page, "CanvasLinked", true);
						update_post_meta($page, "CanvasCourse", get_post_meta($post->ID, "courseID", true));
						update_post_meta($page, "postCanvasID", $post_content->content->page_id);
						update_post_meta($page, "postURL", $post_content->content->url);
						update_post_meta($page, "postHTMLURL", $post_content->content->html_url);
						
						wp_set_post_categories($page, $categories);
						
						if($_POST['item']!="course-home-page"){
							add_post_meta($post->ID, "course_" . $post->ID . "_" . $post_content->content->url, $post_content->content->url, true);
						}
						else if($_POST['item']!="course-syllabus"){
							add_post_meta($post->ID, "course_" . $post->ID . "_" . $post_content->content->url, $post_content->content->url, true);
						}
						else
						{
							add_post_meta($post->ID, "course_" . $post->ID . "_" . $post_content->content->url, $post_content->content->url, true);
						}
						
						echo __("Page Linked");
						
					}else{
					
						echo __("Page Already Linked");
					
					}
					
				}
			}
			else
			{
				print_r($_POST);
				echo "Nonce failed";
			}
			wp_die();
		}	
	
	}
	
	$MOLIEchooseAjax = new MOLIEchooseAjax();
	