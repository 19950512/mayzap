<?php

namespace MayZap\Controller\Erro404;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as De;

class Erro404 extends Controller {

	protected $controller = 'Erro404';

	public function __construct(){
		parent::__construct();
		header("HTTP/1.0 404 Not Found");
	}

	public function index(){

		$this->viewName = 'Erro404';

		$this->view->setTitle('PÁGINA NÃO ENCONTRADA - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow']
		]);

		$mustache = [];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
}