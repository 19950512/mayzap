<?php

namespace MayZap\Controller\Folha;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as De;

class Folha extends Controller {

	protected $controller = 'Folha';

	public function __construct(){
		parent::__construct();
		header("HTTP/1.0 404 Not Found");
	}

	public function index(){

		$this->viewName = 'Folha';

		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow'],
			['name' => 'author', 'content' => 'DevNux'],
		]);


		$titulo = 'Página não encontrada';
		$pagina = '';
		$mustache = array(
		);

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
}