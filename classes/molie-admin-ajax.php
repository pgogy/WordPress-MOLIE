<?PHP
	
	class MOLIEAjax{
	
		function __construct(){;
			add_action("wp_ajax_molie_course_list", array($this, "course_list"));
		}
	
		function course_list(){
			require_once(__DIR__ . "/../API/Psr4AutoloaderClass.php");
			$loader = new Psr4AutoloaderClass;
			$loader->register();
			$loader->addNamespace('InstructureCanvasAPI', __DIR__ . "/../API/InstructureCanvasAPI/src");
			
			$API = new InstructureCanvasAPI\InstructureCanvasAPI( 
																array(
																	"site" => "https://umw.instructure.com",
																	"token" => "8~tuivUvM3GFS3UuGWSSledw0YcR1btI97KlPCgnoZ7g47tynNY0KPNv8rwBNTlypl",
																	"webService" => "CURL"
																)
															);
			
			$courses = new InstructureCanvasAPI\Courses\Courses();
			$courses->setAPI($API);
			$data = $courses->getCoursesForThisUser();
			if($data){
				foreach($data->content as $course){
					print_r($course->id . " " . $course->name);
				}
			}
			$loader->unregister();
			wp_die();
		}	
	
	}
	
	$MOLIEAjax = new MOLIEAjax();
	