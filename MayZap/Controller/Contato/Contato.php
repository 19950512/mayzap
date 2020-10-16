<?php

namespace MayZap\Controller\Contato;

use MayZap\Controller\Controller;
use Model\Core\De as de;
use Model\Core\Core;
use Model\Email\Email;

class Contato extends Controller {

	protected $controller = 'Contato';

	public $visitante;

	public function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->viewName = 'Contato';

		$this->view->setTitle('CONTATO - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Contato - '.SITE_NOME]
		]);

		$mustache = [];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header);
	}
}