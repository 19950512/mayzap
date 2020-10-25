<?php

namespace MayZap\Controller\Conta;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\Core;
use Model\Core\De as de;
use Model\Login\Login as LoginAcc;

class Conta extends Controller {

	protected $controller = 'Conta';

	public function __construct(){
		parent::__construct();

		LoginAcc::somenteLogin();
	}

	public function index(){

		$this->viewName = 'Conta';
		$this->view->setTitle('CONTA - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'CONTA - '.SITE_NOME]
		]);

		$mustache = [
			'{{pes_nome}}' => $_SESSION[SESSION_LOGIN]['pes_nome'] ?? '-',
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
    }
}