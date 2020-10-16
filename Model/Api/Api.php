<?php

namespace Model\Api;

use Model\Core\De AS de;
use Model\Api\Client;

class Api extends Client {

	protected $pes_codigo;
	protected $app_id;

	function __construct($data = []){

		$this->pes_codigo = $data['pes_codigo'] ?? false;
		$this->app_id = $data['app_id'] ?? false;
	}

	function getSaldo(){
		$url = '/api/financeiro';
		$data = [];
		$resposta = $this->request($data, $url);

		$saldo = 0;

		if(isset($resposta['data']) and is_array($resposta['data'])){
			foreach($resposta['data'] as $arr){
				$saldo += $arr['fin_valor'] ?? 0;
			}
		}

		$resposta['saldo'] = $saldo;
		return $resposta;
	}

	function login($data = []){
		$url = '/api/login';
		$resposta = $this->request($data, $url);

		// Se o Login deu certo, cria a sessão
		if(($resposta['r'] ?? 'no') == 'ok'){
			unset($resposta['data']['app_senha']);
			$_SESSION[SESSION_LOGIN] = $resposta['data'];
		}

		return $resposta;
	}

	function getallos(){

		$url = '/api/os';
		$data = [];

		$resposta = $this->request($data, $url);
		if(isset($resposta['data']) and is_array($resposta['data'])){

			foreach($resposta['data'] as $key => $os){

				// Não exibir as OS canceladas
				// os_status => 1 = Pendente
				// os_status => 2 = Concluída
				// os_status => 3 = Cancelada
				if(isset($os['os_status']) and $os['os_status'] == 3){
					unset($resposta['data'][$key]);
				}
			}
		}

		return $resposta;
	}
}