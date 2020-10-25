<?php

namespace Model\Whatsapp;

use Model\Core\De AS de;
use Model\Whatsapp\Consultas;

class Whatsapp extends Consultas{

	protected $_conexao;

	protected $_conexao_gestor;

	protected $_imo_codigo = 0;
	
	protected $_fil_codigo = 0;
	
	protected $_usu_codigo;

    public $_base_url = 'https://api.winzap.com.br';

	public $_cmd = 'chat';
	
	public $_tempo_em_minutos_reenviar_mensagens = 5;

    public $_id;
    
    public $_token = 'tFgn68COf9kX2CyauKcOs8bMtVdpuMoeyKto'; //'tFgn68COf9kX2CyauKcOs8bMtVdpuMoeyKto'; // Token do celular (Gestor)
	
	public $_userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Giapi';

	function __construct($conexao = false, $conexao_gestor = false, $imo_codigo = false, $webhook = true){

		$this->_conexao = $conexao;

		$this->_conexao_gestor = $conexao_gestor;

		$this->_imo_codigo = $imo_codigo;

		$this->_id = $this->_imo_codigo.';'.random_int(1, 999999);

		if($webhook === false){
			// Tenta enviar mensagens ainda não enviadas.
			$this->_trySendMessagensUnsent();
		}
	}

	// Libera da memória as instâncias dos Bancos de Dados
	function __destruct(){
		$this->_conexao = null;
		$this->_conexao_gestor = null;
	}

    function request($endpoint){

		// EXECUÇÃO INFINITA
		set_time_limit(0);

		// EVITA TRAVAMENTO DE NAVEGAÇÃO ENQUANTO BAIXA
		session_write_close();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($ch, CURLOPT_URL, $this->_base_url.$endpoint);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		
        $resultado = curl_exec($ch);
        
		return json_decode($resultado, true);
    }

	function base_request($para_quem = '', $mensagem = '', $url_media = '', $thumb = '', $title = '', $desc = '', $link = false){

		// Caso não tem numero whatsapp configurado ou não  exista
		if($para_quem == ''){
			return ['r' => 'no', 'data' => 'A pessoa que você deseja enviar mensagem não tem um número whatsapp configurado.'];
		}

        $mensagem = urlencode($mensagem); // Coloca a mensagem em URL encode para mandar via url
		$para_quem = self::removeMascara($para_quem); // Remove os caracteres especiais 

		// Caso tenha algum link
		if($url_media !== ''){
			$this->_cmd = 'media';
			$url_media = '&link='.urlencode($url_media);
		}

		// Caso tenha thumb
		if($link === true){
			$this->_cmd = 'link';
			$thumb = urlencode($thumb);
			$title = utf8_decode(urlencode($title));
			$desc = utf8_decode(urlencode($desc));
			$thumb = '&thumb='.$thumb.'&title='.$title.'&desc='.$desc;
		}

		
		// Monta a URL para o request
		$url_request = '/send/?token='.$this->_token.'&cmd='.$this->_cmd.'&id='.$this->_id.$url_media.$thumb.'&to='.$para_quem.'@c.us&msg='.$mensagem;

		// Tenta enviar a mensagem
		$resultado = $this->request($url_request);
		
		// Se o whatsapp encontra-se offline, tenta ligar ele.
		if(isset($resultado['status']) and $resultado['status'] == 'whatsapp offline'){
			$this->sessao('start');
			
			// Tenta enviar pela 2º a mensagem depois que tentou ligar.
			$resultado = $this->request($url_request);

			if(isset($resultado['status']) and $resultado['status'] == 'whatsapp offline'){
				$this->sessao('start');
				
				// Tenta enviar pela 3º a mensagem depois que tentou ligar.
				$resultado = $this->request($url_request);
			}
		}

		// Se existir um servidor, quer dizer que a mensagem foi enviada.
		if(isset($resultado['servidor']) and !empty($resultado['servidor'])){

			// Vamos salvar essa mensagem enviada no banco de dados.
			$resultado['wp_id'] = $this->_id;
			$resultado['wp_to'] = $para_quem;
			$resultado['wp_msg'] = $mensagem;

			$this->saveMensagem($resultado);
		}

		$status = 'no';
		$mensagem = 'Não conseguimos enviar sua mensagem, tente novamente mais tarde.';
		if(isset($resultado['servidor']) and !empty($resultado['servidor'])){
			$status = 'ok';
			$mensagem = 'Mensagem enviada com sucesso.';
		}

		return ['r' => $status, 'data' => $mensagem];
	}

	public static function removeMascara($string = ''){
		return str_replace(['.', '/', '-', ' ', ')', '(', '_'], '', $string);
    }
    
    function getmensagens(){
        return $this->_getmensagens();
	}
	
	function minutosToSegundos($minutos = 1){
		return (int) (60 * $minutos);
	}
}