<?php

namespace MayZap\Controller;

use Model\Core\View as View;
use Model\Core\De as de;
use Model\Core\Core;
use Model\Router\Router;

class Controller {

	/* Object VIEW / Layout */
	public $view;

	/* Name any action */
	public $viewName;

	public $pushHistory = false;

	public $Router;

	public function __construct(){

		if(isset($_POST['push']) and $_POST['push'] === 'push'){
			$this->pushHistory = true;
		}

		$this->view = new View();
		$this->Router = new Router();
	}

	public function render($mustache = [], $controller = '', $viewName = '', $metas = [], $layout = 'Layout'){

		/* Se for por F5 */
		if($this->pushHistory === false){

			echo $this->view->mustache($mustache, $this->view->getView($controller, $viewName), $layout);
			exit;

		}else{

			/* Se for por pushHistory */
			$result['html'] = $this->view->pushHistory($mustache, $this->view->getView($controller, $viewName), $layout);
			$result['metas'] = [
				'title' => $this->view->title,
			];

			echo json_encode($result);
			exit;
		}
	}
}