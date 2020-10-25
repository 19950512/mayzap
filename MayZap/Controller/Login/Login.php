<?php

namespace MayZap\Controller\Login;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\Core;
use Model\Core\De as de;
use Model\Api\Api as Api;
use Model\Login\Login as LoginAcc;
use Model\Administrador\Administrador;

class Login extends Controller {

	protected $controller = 'Login';

	public function __construct(){
		parent::__construct();
	}

	public function index(){

		if(isset($_SESSION[SESSION_LOGIN])){
			
			header('location: /conta');
			exit;
		}

		$this->viewName = 'Login';

		$this->view->setTitle('LOGIN - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Login - '.SITE_NOME]
		]);

		$mustache = [
			'{{email}}' => $_GET['email'] ?? ''
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}

	public function novo(){

		//LoginAcc::somenteLogin();

		$this->viewName = 'Novo';

		$mustache = [
			'{{email}}' => $_GET['email'] ?? '',
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}

	/*public function administrador(){

		LoginAcc::somenteLogin(1);

		$this->viewName = 'Administrador';

		$mustache = array();

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}*/

	function entrar(){

		if(isset($_POST['acc_email'], $_POST['acc_senha'])){

			$acc_email = Core::strip_tags($_POST['acc_email'] ?? '');
			$acc_senha = Core::strip_tags($_POST['acc_senha'] ?? '');

			if(empty($acc_email) OR empty($acc_senha)){
				echo json_encode(['r' => 'no', 'data' => 'Ops, seu E-mail e Senha precisam ser informados.']);
				exit;
			}

			$checkEmail = Core::is_email($acc_email);

			// E-mail inválido
			if(!$checkEmail){
				echo json_encode(['r' => 'no', 'data' => 'Ops, o e-mail informado está inválido.']);
				exit;
			}

			$login = new LoginAcc();

			$data['acc_email'] = $acc_email;
			$data['acc_senha'] = $acc_senha;

			$resposta = $login->authentica($data);

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}

	function criar(){

		if(isset($_POST['acc_email'], $_POST['acc_senha'])){

			$acc_email = Core::strip_tags($_POST['acc_email'] ?? '');
			$acc_senha = Core::strip_tags($_POST['acc_senha'] ?? '');

			$checkEmail = Core::is_email($acc_email);

			// Senha Curta
			if(strlen($acc_senha) <= 6){
				echo json_encode(['r' => 'no', 'data' => 'Ops, sua senha está muito curta.']);
				exit;
			}

			// E-mail inválido
			if(!$checkEmail){
				echo json_encode(['r' => 'no', 'data' => 'Ops, o e-mail informado está inválido.']);
				exit;
			}

			$login = new LoginAcc();

			$data['acc_email'] = $acc_email;
			$data['acc_senha'] = $acc_senha;

			$resposta = $login->create($data);

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}

	public function logout(){
		if(isset($_SESSION[SESSION_LOGIN])){
			unset($_SESSION[SESSION_LOGIN]);
		}

		echo json_encode(['r' => 'ok', 'data' => 'Deslogado com sucesso.']);
		exit;
	}

	function ativar(){

		$this->viewName = 'Ativar';

		$resposta = 'Ops, seu e-mail e o token de ativação precisam ser informados.';
		if(isset($_GET['email'], $_GET['token'])){
			
			$acc_email = Core::strip_tags($_GET['email'] ?? '');
			$app_token = Core::strip_tags($_GET['token'] ?? '');
			
			if(empty($acc_email) OR empty($app_token)){
				echo json_encode(['r' => 'no', 'data' => $resposta]);
				exit;
			}

			$login = new LoginAcc();
			
			$resposta = $login->ativar($acc_email, $app_token);
		}
		
		$mustache = [
			'{{resposta}}' => $resposta['data']
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}

	function uploadImagens(){

		if(isset($_FILES['file'], $_POST['upload_preset']) and $_FILES['file']['error'] == '0'){

			$administrador = new administrador();
			$resposta = $administrador->uploadImagens();

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, não foi possível subir suas imagens.']);
		exit;

	}
}