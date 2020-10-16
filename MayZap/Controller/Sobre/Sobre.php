<?php

namespace MayZap\Controller\Sobre;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as De;

class Sobre extends Controller {

	protected $controller = 'Sobre';

	public function __construct(){
		parent::__construct();
		header("HTTP/1.0 404 Not Found");
	}

	public function index(){

		$this->viewName = 'Sobre';

		$this->view->setTitle('SOBRE - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Sobre - '.SITE_NOME]
		]);

		$mustache = [];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
}