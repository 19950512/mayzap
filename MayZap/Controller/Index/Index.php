<?php

namespace MayZap\Controller\Index;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as de;

class Index extends Controller {

	protected $controller = 'Index';

	public function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->viewName = 'Index';

		$this->view->setTitle('INÍCIO - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Início - '.SITE_NOME]
		]);

		$mustache = [];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
}