<?php

namespace Model\Login;

use Model\Db\Connection;
use Model\Model;
use PDO;
use PDOException;
use Model\Core\De as de;
use Model\Core\Core;

class Login extends Model{

	function __construct(){
		parent::__construct();
	}

	// Protejer controlador para caso o usuario não esteja logado
	// SOMENTE POR SENHA
	static function somenteLogin(){

		if(!self::_logado()){

			if(isset($_POST['push']) and $_POST['push'] === 'push'){
				header("HTTP/1.0 403 Not Found");
				exit;
			}
			
			header('location: /');
			exit;
		}
	}

	static function somentePermissao($app_ativo){
		if(self::_permissao($app_ativo)){
			header('location: /');
		}
	}

	static function _permissao($app_ativo = 3){
		return (!isset($_SESSION[SESSION_LOGIN]) OR $_SESSION[SESSION_LOGIN]['app_ativo'] > $app_ativo);
	}
	// Verifica se está logado
	static function _logado(){
		if(isset($_SESSION[SESSION_LOGIN]['pes_codigo']) and is_numeric($_SESSION[SESSION_LOGIN]['pes_codigo'])){
			return true;
		}

		return false;
	}

	// Retorna todos os dados da conta
	static function getAll($acc_email){

		$sql = Connection::getConnection()->prepare('
			SELECT
				*
			FROM cad_conta AS acc
			LEFT JOIN cad_pessoas AS pes ON pes.acc_codigo = acc.acc_codigo
			WHERE acc.acc_email = :acc_email
		');
		$sql->bindParam(':acc_email', $acc_email);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		return $temp;
	}

	function authentica($data = []){
		
		$acc_email = $data['acc_email'] ?? '';
		$acc_senha = $data['acc_senha'] ?? '';
		
		// Caso e-mail e senha não sejam informados
		if(empty($acc_email) OR empty($acc_senha)){
			return ['r' => 'no', 'data' => 'Ops, você precisa informar o E-mail e a Senha corretamente.'];
		}

		// Striptags.
		$acc_email = Core::strip_tags($acc_email);
		$acc_senha = Core::strip_tags(Core::senhaEncript($acc_senha));

	
		// acc_status 1 = ativo, 2 inativo
		$sql = $this->conexao->prepare('
			SELECT
				*
			FROM cad_conta AS acc
			WHERE acc.acc_email = :acc_email AND acc.acc_senha = :acc_senha
		');
		$sql->bindParam(':acc_email', $acc_email);
		$sql->bindParam(':acc_senha', $acc_senha);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Errou a senha
		if($temp === false){
			sleep(2);
			return ['r' => 'no', 'data' => 'Ops, seu e-mail ou a senha está incorreto, tente novamente.'];
		}

		// Conta inativa
		if(isset($temp['acc_ativo']) and $temp['acc_ativo'] == false){
			return ['r' => 'no', 'data' => 'Vish, sua conta está inativada, para ativa-la verifique a caixa de entrada do seu e-mail.'];
		}

		// Cria a sessão do login
		self::_createSession($temp['acc_email']);

		// Tudo certo
		return ['r' => 'ok', 'data' => $temp];
	}

	function ativar($acc_email, $acc_token){

		// Striptags.
		$acc_email = Core::strip_tags($acc_email);
		$acc_token = Core::strip_tags($acc_token);

		// Verificar se existe esse e-mail, se a conta está inativa e se o token é válido (igual).
		// acc_status 1 = ativo, 2 inativo
		$sql = $this->conexao->prepare('
			SELECT
				*
			FROM cad_conta AS acc
			WHERE acc.acc_email = :acc_email
		');
		$sql->bindParam(':acc_email', $acc_email);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Se não existe a conta com este email
		if(!$temp){
			return ['r' => 'no', 'data' => 'Ops, Não existe nenhuma conta com este e-mail.'];
		}

		// Se o status da conta está ativo
		if(isset($temp['acc_ativo']) and $temp['acc_ativo'] == true){
			return ['r' => 'no', 'data' => 'Ops, Sua conta já está ativa, para acessar sua conta, <a href="/login?email='.$temp['acc_email'].'">clique aqui</a>.'];
		}

		// Se o token informado está diferente do que foi gerado
		if(isset($temp['acc_token']) and $temp['acc_token'] !== $acc_token){
			return ['r' => 'no', 'data' => 'Ops, Seu token está inválido, verifique o link que recebeu no seu e-mail e tente novamente.'];
		}

		// Se chegou aqui, está tudo certo..
		$sql = $this->conexao->prepare("
			UPDATE cad_conta SET acc_status = 1, acc_token = 'ativado' WHERE acc_email = :acc_email AND acc_token = :acc_token AND acc_status = 2
		");
		$sql->bindParam(':acc_email', $acc_email);
		$sql->bindParam(':acc_token', $acc_token);
		$sql->execute();
		$ativaa = $sql->fetch(PDO::FETCH_ASSOC);

		// Caso o email e token não sejam válidos ou a conta já tenha sido ativada ( 2º verificação )
		if($ativaa === false){
			return ['r' => 'no', 'data' => 'Ops, Parece que sua conta já foi ativada ou o token está inválido'];
		}

		// Cria a sessão do login
		self::_createSession($temp['acc_email']);

		// Tudo OK
		return ['r' => 'ok', 'data' => 'Pronto, sua conta foi ativada, você já pode logar em sua conta. <a href="/login?email='.$temp['acc_email'].'">clicando aqui</a>.'];
	}

	function create($data){

		// Caso e-mail e senha não sejam informados
		if(!isset($data['acc_email'], $data['acc_senha']) OR empty($data['acc_email']) OR empty($data['acc_senha'])){
			return ['r' => 'no', 'data' => 'Ops, você precisa informar o e-mail e a senha corretamente.'];
		}

		// Striptags.
		$data['acc_email'] = Core::strip_tags($data['acc_email']);
		$data['acc_senha'] = Core::strip_tags(Core::senhaEncript($data['acc_senha']));
	
		// Check se exist uma conta com esse email
		$sql = $this->conexao->prepare('
			SELECT acc_email FROM cad_conta WHERE acc_email = :acc_email
		');
		$sql->bindParam(':acc_email', $data['acc_senha']);

		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		if($temp){     
			return ['r' => 'no', 'data' => 'Ops, já existe um cadastro com esse e-mail, tente outro.'];
		}

		$sql = null;

		$acc_ip = Core::ip();
		$erro = 'Ops, não foi possível criar uma conta agora, tente novamente mais tarde.';
		try {
			$sql = $this->conexao->prepare('
				INSERT INTO cad_conta (
					acc_email,
					acc_senha,
					acc_ip
				) VALUES (
					:acc_email,
					:acc_senha,
					:acc_ip
				)
			');
			$sql->bindParam(':acc_email', $data['acc_email']);
			$sql->bindParam(':acc_senha', $data['acc_senha']);
			$sql->bindParam(':acc_ip', $acc_ip);
			$sql->execute();
			$temp = $sql->fetch(PDO::FETCH_ASSOC);

		}catch (PDOException $e){
			if(($e->errorInfo[0] ?? '') == '23505'){
				$erro = 'Ops, já existe uma conta com este e-mail, tente recuperar ou tente outro e-mail.';
			}
		}	
		// Conta não cadastrada
		if($temp === false){     
			return ['r' => 'no', 'data' => $erro];
		}

		// Cria a sessão do login
		// NÃO PODE CRIAR A SESSÃO DO LOGIN, POIS PRECISA ATIVAR ELA, É O EMAIL QUE O USUARIO RECEBE PARA ATIVAR.
		self::_createSession($data['acc_email']);

		return ['r' => 'ok', 'data' => 'Pronto, sua conta foi criada.'];
	}

	// Função responsável por criar toda sessão de login conforme os dados do email cadastrado.
	static function _createSession($acc_email){

		// Coloca os dados da conta em uma sessão
		$contaFetch = self::getAll($acc_email);

		if(isset($contaFetch['acc_email']) and $contaFetch['acc_email'] == $acc_email){
			
			foreach($contaFetch as $coluna => $valor){
				$_SESSION[SESSION_LOGIN][$coluna] = $valor;
			}
		}
	}
}