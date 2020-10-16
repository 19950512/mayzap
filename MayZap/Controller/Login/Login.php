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
			header('location: /');
			exit;
		}

		$this->viewName = 'Login';

		$this->view->setTitle('LOGIN - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Login - '.SITE_NOME]
		]);

		$mustache = [
			'{{id}}' => $_GET['id'] ?? ''
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}

	/*public function novo(){

		LoginAcc::somenteLogin();

		$this->viewName = 'Novo';

		$mustache = array();

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}*/

	/*public function administrador(){

		LoginAcc::somenteLogin(1);

		$this->viewName = 'Administrador';

		$mustache = array();

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}*/

	function entrar(){

		if(isset($_POST['app_id'], $_POST['app_senha'])){

			$app_id = Core::strip_tags($_POST['app_id'] ?? '');
			$app_senha = Core::strip_tags($_POST['app_senha'] ?? '');

			if(empty($app_id) OR empty($app_senha)){
				echo json_encode(['r' => 'no', 'data' => 'Ops, seu ID e Senha precisam ser informados.']);
				exit;
			}

			// Limpa a sessão
			unset($_SESSION[SESSION_LOGIN]);

			$api = new Api();

			$app_senha = base64_encode(substr($app_senha, 3, 9));
			$app_senha = sha1($app_senha);
			$app_senha = md5($app_senha);

			$resposta = $api->login(['app_id' => $app_id, 'app_senha' => $app_senha]);
			
			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}

	/*function criar(){

		if(isset($_POST['app_id'], $_POST['app_senha'])){

			$app_id = Core::strip_tags($_POST['app_id'] ?? '');
			$app_senha = Core::strip_tags($_POST['app_senha'] ?? '');

			$checkEmail = Core::is_email($app_id);

			// Senha Curta
			if(strlen($app_senha) <= 6){
				echo json_encode(['r' => 'no', 'data' => 'Ops, sua senha está muito curta.']);
				exit;
			}

			// E-mail inválido
			if(!$checkEmail){
				echo json_encode(['r' => 'no', 'data' => 'Ops, o e-mail informado está inválido.']);
				exit;
			}

			$login = new LoginAcc();

			$data['app_id'] = $app_id;
			$data['app_senha'] = $app_senha;

			$resposta = $login->create($data);

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}*/

	public function logout(){
		if(isset($_SESSION[SESSION_LOGIN])){
			unset($_SESSION[SESSION_LOGIN]);
		}

		echo json_encode(['r' => 'ok', 'data' => 'Deslogado com sucesso.']);
		exit;
	}

	/*function ativar(){

		$this->viewName = 'Ativar';

		$resposta = 'Ops, seu e-mail e o token de ativação precisam ser informados.';
		if(isset($_GET['email'], $_GET['token'])){
			
			$app_id = Core::strip_tags($_GET['email'] ?? '');
			$app_token = Core::strip_tags($_GET['token'] ?? '');
			
			if(empty($app_id) OR empty($app_token)){
				echo json_encode(['r' => 'no', 'data' => $resposta]);
				exit;
			}

			$login = new LoginAcc();
			
			$resposta = $login->ativar($app_id, $app_token);
		}
		
		$mustache = [
			'{{resposta}}' => $resposta['data']
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}*/

	/*function uploadImagens(){

		if(isset($_FILES['file'], $_POST['upload_preset']) and $_FILES['file']['error'] == '0'){

			$administrador = new administrador();
			$resposta = $administrador->uploadImagens();

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, não foi possível subir suas imagens.']);
		exit;

	}*/
}