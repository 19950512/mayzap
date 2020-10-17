<?php

use Model\Router\Router AS Router;
use Model\Core\De AS de;
use Model\Sessions; 
use Model\Login\Login; 

class Aplication {

	protected $router;

	function __construct(){

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);


		$this->router = new Router();

		// Inicia a session
		$sessions = new Sessions($_SERVER['SERVER_NAME']);

		$sessao_login = $sessions->get(SESSION_LOGIN);

		if(isset($sessao_login['acc_email']) and !empty($sessao_login['acc_email'])){
			Login::_createSession($sessao_login['acc_email']);
		}

		// Inicia o controlador
		$controller = new $this->router->namespace();

		if(!method_exists($controller, $this->router->action)){
			$this->router->set404();
			$controller = new $this->router->namespace();
		}

		$controller->{$this->router->action}();
	}
}