<?PHP
	
	class MOLIEquizAjax{
	
		function __construct(){;
			add_action("wp_ajax_molie_quiz_import", array($this, "quiz_import"));
			add_action("wp_ajax_no_priv_molie_quiz_import", array($this, "quiz_import"));
		}
		
		private function get_quiz_questions($post){
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
				
			$quiz = new InstructureCanvasAPI\Courses\Quizzes\Quizzes();
			$quiz->setAPI($API);
			$quiz->setCourseID(get_post_meta($post->ID, "courseID", true));
			$quiz_data = $quiz->getQuizQuestions($_POST['item']); 
			
			print_r($quiz_data);
			
			return $quiz_data->content;
		}
		
		function get_quiz($post){
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
			
			$quizzes = new InstructureCanvasAPI\Courses\Quizzes\Quizzes();
			$quizzes->setAPI($API);
			$quizzes->setCourseID(get_post_meta($post->ID, "courseID", true));
			$data = $quizzes->getQuiz($_POST['item']);
			$quizzes = $data->content;
			$loader->unregister();
			return $quizzes;
		}
	
		function quiz_import(){
			if(wp_verify_nonce($_POST['nonce'], "molie_admin_quiz"))
			{
			
				$post = get_post($_POST['course_post']);
				
				$quiz = $this->get_quiz($post);
				if(get_post_meta($post->ID, "quiz_" . $quiz->id, true)==""){
				
					$quiz_post = wp_insert_post(
												array(
													"post_type" => 'linkedcanvasquiz',
													"post_status" => 'publish',
													"post_title" => $quiz->title,
													"post_content" => $quiz->description,
													"post_author" => get_current_user_id()
												)
											);
					update_post_meta($quiz_post, "quizURL", $quiz->html_url, true);
					update_post_meta($post->ID, "quiz_" . $quiz->id, $quiz_post, true);
					
				}
				else
				{
					$quiz_post = get_post_meta($post->ID, "quiz_" . $quiz->id, true);
				}
					
				echo "****";	
				print_r($post);	
				echo "****";		
	
				$questions = $this->get_quiz_questions($post);
				
				foreach($questions->content as $question){
					
					if(get_post_meta($quiz_post, "quiz_question_" . $question->id, true)==""){
				
						$question_post = wp_insert_post(
												array(
													"post_type" => 'linkedcanvasqa',
													"post_status" => 'publish',
													"post_title" => $quiz->title . " " . $question->question_name,
													"post_content" => $question->question_text,
													"post_author" => get_current_user_id()
												),
												true
											);
											
						update_post_meta($question_post, "quiz", $question->quiz_id, true);
						update_post_meta($question_post, "question_id", $question->id, true);
						update_post_meta($question_post, "question_position", $question->position, true);
						update_post_meta($quiz_post, "quiz_question_" . $question->id, $question_post ,true);
						
						if($question->question_type=="multiple_choice_question"){
							$counter = 1;
							foreach($question->answers as $answer){
								update_post_meta($question, "id_" . $counter, $answer->id, true);
								update_post_meta($question, "answer_" . $counter, $answer->text, true);
								update_post_meta($question, "feedback_" . $counter, $answer->comments, true);
								$counter++;
							}
						}
					
					}
					echo __("Quiz linked");
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
	
	$MOLIEquizAjax = new MOLIEquizAjax();
	