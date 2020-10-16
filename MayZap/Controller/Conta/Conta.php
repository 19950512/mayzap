<?php

namespace MayZap\Controller\Conta;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\Core;
use Model\Core\De as de;
use Model\Login\Login as LoginAcc;
use Model\Api\Api;

class Conta extends Controller {

	protected $controller = 'Conta';

	protected $Api;

	public function __construct(){
		parent::__construct();

		LoginAcc::somenteLogin();

		$this->Api = new Api($_SESSION[SESSION_LOGIN] ?? []);
	}

	public function index(){

		$this->viewName = 'Conta';
		$this->view->setTitle('CONTA - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'CONTA - '.SITE_NOME]
		]);

		$saldo = Core::number_format(($this->Api->getSaldo()['saldo'] ?? '-'), 2, ',');

		$mustache = [
			'{{pes_nome}}' => $_SESSION[SESSION_LOGIN]['pes_nome'] ?? '-',
			'{{saldo}}' => $saldo,
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
    }
}