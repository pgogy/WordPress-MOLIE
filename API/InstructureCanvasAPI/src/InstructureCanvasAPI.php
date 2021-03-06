<?PHP

namespace InstructureCanvasAPI;

class InstructureCanvasAPI{
	
	public $site;
	public $token;
	public $webService;
	
	public function __construct($data){
		if(is_array($data)){
			foreach($data as $key => $value){
				switch($key){
					case "site": $this->setSite($value); break;
					case "token": $this->setToken($value); break;
					case "webService": $this->setWebService($value); break;
				}
			}
		}
	}
	
	public function setSite($site){
		$this->site = $site;
	}

	public function getSite(){
		return $this->site;
	}
		
	public function setToken($token){
		$this->token = $token;
	}
		
	public function getToken(){
		return $this->token;
	}
		
	public function setWebService($service){
		$this->webService = $service;
	}
		
	public function getWebService(){
		return $this->webService;
	}
	
	public function get($url){
		$service = $this->getWebService();
		$webService = "\InstructureCanvasAPI\WebService\\" . $service . "\\" . $service;
		$request = new $webService($this);
		return $request->get($url);
	}
	
	public function send($url, $type, $parameters = array()){
		$service = $this->getWebService();
		$webService = "\InstructureCanvasAPI\WebService\\" . $service . "\\" . $service;
		$request = new $webService($this);
		return $request->send($url, $type, $parameters);
	}
	
	public function sendFile($url, $type, $parameters = array(), $files = array()){
		$service = $this->getWebService();
		$webService = "\InstructureCanvasAPI\WebService\\" . $service . "\\" . $service;
		$request = new $webService($this);
		return $request->sendFile($url, $type, $parameters, $files);
	}
	
	public function confirmFile($url){
		$service = $this->getWebService();
		$webService = "\InstructureCanvasAPI\WebService\\" . $service . "\\" . $service;
		$request = new $webService($this);
		return $request->confirmFile($url);
	}
	
}