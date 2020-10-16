<?php

namespace Model\Api;

use Model\Core\De AS de;

class Client {

	private $_token = 'Heitor_Maydana';

	private $_baseEndPoint = 'http://sis.laboratoriais.local';

	function request($data = [], $url = ''){

		$navegador = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

		$headers = [];
		$headers[] = 'token:'.$this->_token;
		$headers[] = 'pes_codigo:'.$this->pes_codigo;
		$headers[] = 'app_id:'.$this->app_id;

		$this->_baseEndPoint = !DEV ? 'https://sis.laboratoriais.com.br' : $this->_baseEndPoint;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $navegador);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $this->_baseEndPoint.$url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		$resultado = curl_exec($ch);

		$json = json_decode($resultado, true);

		return $json;
	}

}