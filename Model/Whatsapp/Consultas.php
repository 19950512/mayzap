<?php

namespace Model\Whatsapp;

use Model\Core\De AS de;

use Model\Whatsapp\Endpoints;

class Consultas extends Endpoints {

    function update($data = []){

        $wp_id    = $data['muid'] ?? '';
        $wp_ack   = $data['ack'] ?? '';
        $wp_token = $data['token'] ?? '';

        $sql = $this->_conexao->prepare("
            UPDATE whatsapp_mensagens SET 
                wp_ack = :wp_ack,
                usu_codigo_change = :usu_codigo_change,
                wp_atualizacao = 'now()'
            WHERE wp_id = :wp_id AND wp_token = :wp_token
        ");
        $sql->bindParam(':wp_token', $wp_token);
        $sql->bindParam(':wp_id', $wp_id);
        $sql->bindParam(':wp_ack', $wp_ack);
        $sql->bindParam(':usu_codigo_change', $this->_usu_codigo);
        $sql->execute();
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        $sql = null;
    }

    function saveMensagem($data = []){

        $wp_id      = $data['wp_id'] ?? '';
        $wp_msg     = $data['wp_msg'] ?? '';
        $wp_link    = $data['wp_link'] ?? '';
        $wp_thumb   = $data['wp_thumb'] ?? '';
        $wp_title   = $data['wp_title'] ?? '';
        $wp_desc    = $data['wp_desc'] ?? '';
        $wp_ack     = $data['wp_ack'] ?? 0;
        $wp_to      = $data['wp_to'] ?? '';
        $pes_codigo = $data['pes_codigo'] ?? '';
        
        $wp_server  = $data['servidor'] ?? '';
        $wp_token   = $data['token'] ?? '';
        $wp_cmd     = $data['cmd'] ?? '';

        $sql = $this->_conexao->prepare('
            INSERT INTO whatsapp_mensagens (
                pes_codigo,
                wp_token,
                wp_cmd,
                wp_id,
                wp_to,
                wp_msg,
                wp_link,
                wp_thumb,
                wp_title,
                wp_desc,
                wp_ack,
                wp_server,
                usu_codigo,
                usu_codigo_change,
                imo_codigo,
                fil_codigo
            ) VALUES (
                :pes_codigo,
                :wp_token,
                :wp_cmd,
                :wp_id,
                :wp_to,
                :wp_msg,
                :wp_link,
                :wp_thumb,
                :wp_title,
                :wp_desc,
                :wp_ack,
                :wp_server,
                :usu_codigo,
                :usu_codigo_change,
                :imo_codigo,
                :fil_codigo
            )
        ');
        $sql->bindParam(':wp_token', $wp_token);
        $sql->bindParam(':wp_cmd', $wp_cmd);
        $sql->bindParam(':wp_id', $wp_id);
        $sql->bindParam(':wp_to', $wp_to);
        $sql->bindParam(':wp_msg', $wp_msg);
        $sql->bindParam(':wp_link', $wp_link);
        $sql->bindParam(':wp_thumb', $wp_thumb);
        $sql->bindParam(':wp_title', $wp_title);
        $sql->bindParam(':wp_desc', $wp_desc);
        $sql->bindParam(':wp_ack', $wp_ack);
        $sql->bindParam(':wp_server', $wp_server);
        $sql->bindParam(':pes_codigo', $pes_codigo);
        $sql->bindParam(':imo_codigo', $this->_imo_codigo);
        $sql->bindParam(':fil_codigo', $this->_fil_codigo);
        $sql->bindParam(':usu_codigo', $this->_usu_codigo);
        $sql->bindParam(':usu_codigo_change', $this->_usu_codigo);
        $sql->execute();
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);
        $sql = null;

        return $fetch;
    }

	function getWhatsappNumber($pes_codigo){

        $sql = $this->_conexao->prepare("SELECT pes_whatsapp FROM cad_pessoa WHERE pes_codigo = :pes_codigo AND pes_whatsapp <> ''");
		$sql->bindParam(':pes_codigo', $pes_codigo);
		$sql->execute();
        $fetch = $sql->fetch(PDO::FETCH_ASSOC);

		// Caso a pessoa exista e tenha número Whatsapp configurado.
		if(isset($fetch['pes_whatsapp']) and !empty($fetch['pes_whatsapp'])){
			return '55'.$fetch['pes_whatsapp'];
		}

        return false;
	}

    function _getmensagens(){

        $sql = $this->_conexao->prepare('
            SELECT wp.*,
            pes.pes_nome
            FROM whatsapp_mensagens AS wp
            LEFT JOIN cad_pessoa AS pes ON pes.pes_codigo = wp.pes_codigo
            ORDER BY wp_codigo DESC
        ');
        $sql->execute();
        $temp = $sql->fetchAll(PDO::FETCH_ASSOC);

        $fetch = [];
        foreach($temp as $key => $arr){
            
            $arr['wp_msg'] = urldecode($arr['wp_msg']);

            switch($arr['wp_cmd']){

                case 'link';
                    $arr['wp_cmd_str'] = '<i class="icl ic-external-link" title="Mensagem tipo Link"></i>';
                break;
                case 'media';
                    $arr['wp_cmd_str'] = '<i class="icl ic-images" title="Mensagem tipo media, arquivos, .jpg, .png, .pdf.."></i>';
                break;
                case 'chat';
                    $arr['wp_cmd_str'] = '<i class="icl ic-comment-alt-lines" title="Mensagem tipo chat, apenas textos"></i>';
                break;
				default:
                    $arr['wp_cmd_str'] = '-';
            }

            switch($arr['wp_ack']){

                case '0';
                    $arr['wp_status'] = 'Mensagem ainda não enviada para os servidores do WhatsApp';
                    $arr['wp_status_str'] = '<i class="strong icl ic-clock gray3"></i>';
                break;
				case '1':
                    $arr['wp_status'] = 'Mensagem enviada para servidores WhatsApp';
                    $arr['wp_status_str'] = '<i class="strong icl ic-check gray3"></i>';
				break;
				case '2':
                    $arr['wp_status'] = 'Mensagem entregue ao destinatário';
                    $arr['wp_status_str'] = '<i class="strong icl ic-check gray3"></i><i style="margin-left: -6px" class="strong icl ic-check gray3"></i>';
				break;
				case '3':
                    $arr['wp_status'] = 'Mensagem lida pelo destinatário';
                    $arr['wp_status_str'] = '<i class="strong icl ic-check" style="color: #4fc3f7"></i><i style="margin-left: -6px; color: #4fc3f7" class="strong icl ic-check"></i>';
				break;
				default:
                    $arr['wp_status'] = '-';
            }

            $fetch[$key] = $arr;
        }

        return $fetch;
    }

    function _trySendMessagensUnsent(){

        // Vamos retornar mensagens ainda não enviadas.
        $sql = $this->_conexao->prepare('
            SELECT wp.*,
            pes.pes_nome
            FROM whatsapp_mensagens AS wp
            LEFT JOIN cad_pessoa AS pes ON pes.pes_codigo = wp.pes_codigo
            WHERE wp.wp_ack = 0
            ORDER BY wp_codigo DESC
        ');
        $sql->execute();
        $temp = $sql->fetchAll(PDO::FETCH_ASSOC);

        $listaMensagensRemover = [];
        foreach($temp as $key => $mensagem){

            $hora_mensagem_enviada = date('d-m-Y H:i:s', strtotime($mensagem['wp_autodata']));
            $agora = date('d-m-Y H:i:s');

            $data_inicio = new DateTime($hora_mensagem_enviada);
            $data_fim = new DateTime($agora);

            // Resgata diferença entre as datas
            $dateInterval = $data_inicio->diff($data_fim);

            // tempo_em_minutos, se o tempo da mensagem for maior que o $tempo_em_minutos, então reenvia a mensagem e exclui essa primeira mensgem.
            $tempo_em_minutos = $this->_tempo_em_minutos_reenviar_mensagens;
            if($dateInterval->i > $tempo_em_minutos){
                
                /*
                    TENTA REENVIAR AS MENSAGENS PARADAS
                */
                $url_media = urldecode($mensagem['wp_msg']);
                $url_media = explode('?', $url_media);
                $url_media = $url_media[0] ?? '';
                $extensao = substr($url_media, -4);
                
                switch ($extensao){
                    case '.jpg':
                        $link = false;
                    break;
                    case '.png':
                        $link = false;
                    break;
                    case '.pdf':
                        $link = false;
                    break;
                    default:
                        $link = true;
                }

                if(!preg_match('/http/', $url_media)){
                    $resposta = $this->texto($mensagem['pes_codigo'], $url_media);
                }else if ($link){
                    $resposta = $this->thumb($mensagem['pes_codigo'], $url_media, $url_media, $link);
                }else {
                    $resposta = $this->media($mensagem['pes_codigo'], $url_media, $url_media);
                }

                // Coloca a mensagem em uma lista para remove-la depois.
                $listaMensagensRemover[$mensagem['wp_codigo']] = 'OR wp_codigo = '.$mensagem['wp_codigo'];
            }
        }

        // Deleta as mensagems Reenviadas.
        $where_delete = implode(' ', $listaMensagensRemover);
        $where_delete = trim($where_delete, 'OR ');

        if($where_delete !== ''){
            $sql = $this->_conexao->prepare('DELETE FROM whatsapp_mensagens WHERE '.$where_delete);
            $sql->execute();
        }
    }

    function getContas(){
        $sql = $this->_conexao->prepare('
            SELECT * FROM whatsapp_token
            ORDER BY wtk_codigo DESC
        ');
        $sql->execute();
        $temp = $sql->fetchAll(PDO::FETCH_ASSOC);
        $fetch = false;

		if($temp !== false){

            foreach($temp as $key => $arr){
                $this->_token = $arr['wtk_token']; // Seta qual Token vamos trabalhar.
                $data = $this->sessao('status');
                $arr['wtk_status'] = ($data['status'] == 'OFFLINE') ? '<span class="danger">'.$data['status'].'</span>' : '<span class="green">'.$data['status'].'</span>';
                $arr['wtk_btn'] = ($data['status'] == 'OFFLINE') ? '<button onclick="whatsapp_conta_toggle(this, \''.$data['status'].'\', \''.$arr['wtk_token'].'\')" class="b b-green width-100">LIGAR</button>' : '<button class="b b-danger width-100" onclick="whatsapp_conta_toggle(this, \''.$data['status'].'\', \''.$arr['wtk_token'].'\')">DESLIGAR</button>';
                $arr['picture'] = $data['picture'] ?? '/jnh/icones/whatsapp.jpg';
                $arr['nome'] = $data['pushname'] ?? 'desconhecido';
                $arr['numero'] = $data['phone'] ?? 'desconhecido';

                $fetch[$key] = $arr;
            }
        }

        return $fetch;
    }

    function toggleConta($status, $wtk_token = ''){
        $this->_token = $wtk_token; // Seta qual Token vamos trabalhar.
        
        $data = [];
        if($status == 'OFFLINE'){
            $data = $this->sessao('start'); 
        }else{
            $data = $this->sessao('stop'); 
        }

        return $data;
    }

	function _imobiliaria(){

		$sql = $this->_conexao_gestor->prepare('
			SELECT *
			FROM ger_imobiliaria AS imo
			LEFT JOIN ger_imobiliaria_filiais AS fil ON fil.imo_codigo = imo.imo_codigo
			WHERE imo.imo_codigo = :imo_codigo
		');
		$sql->bindParam(':imo_codigo', $this->_imo_codigo);
        $sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		$this->_fil_codigo = $temp['fil_codigo'] ?? 0;

		return $this;
	}
}