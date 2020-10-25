<?php

namespace Model\Whatsapp;

use Model\Core\De AS de;

class Endpoints {

    // Função para trabalhar com a sessão / aparelho conectado.
    // $status = 'start'
    // $status = 'stop'
    // $status = 'status'
    function sessao($status = 'start'){

        $resultado = $this->request('/'.$status.'/'.$this->_token);
		return $resultado;
    }


    // Mensagem de texto
	function texto($pes_codigo = '', $mensagem = ''){
		return $this->base_request($pes_codigo, $mensagem);
	}

    // Mensagem com mídia (Imagem/Video/Áudio/PDF/...)
	function media($pes_codigo = '', $mensagem = '', $url_media = ''){
		return $this->base_request($pes_codigo, $mensagem, $url_media);
    }
    
    // Mensagem com URL e Miniatura
	function thumb($pes_codigo = '', $mensagem = '', $url_media = '', $link = false){

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
		curl_setopt($ch, CURLOPT_URL, $url_media);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $html = curl_exec($ch);
        
        //parsing begins here:
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $nodes = $doc->getElementsByTagName('title');

        $thumb  = '';
        $desc   = '';
        $title = $nodes->item(0)->nodeValue;

        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++)
        {
            $meta = $metas->item($i);
            if($meta->getAttribute('name') == 'description')
                $desc = $meta->getAttribute('content');
            if($meta->getAttribute('property') == 'og:image')
                $thumb = $meta->getAttribute('content');
        }

        return $this->base_request($pes_codigo, $mensagem, $url_media, $thumb, $title, $desc, $link);
	}

}