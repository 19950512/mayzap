<?php

namespace Model\Conta;

use Model\Core\De AS de;
use Model\Core\Core;

use Model\Db\Connection;
use Model\Model;
use PDO;

use Imagick;

class Conta extends Model{

	public $imagens_permitidas = [
		'image/png' => 'png',
		'image/jpeg' => 'jpg',
	];

	public $pathVisitante = '../www/img/upload';
	public $pathVisitante_src;

	public function __construct(){
		parent::__construct();
    }

    function uploadImagens(){

        // VERIFICA/CRIA PASTA
        if(!is_dir($this->pathVisitante)){

            $mkdir = explode('/', $this->pathVisitante);
            $atual = '';
            foreach ($mkdir as $nivel => $pasta){
                if($pasta === '..'){
                    $atual .= $pasta.'/';
                }else{
                    $atual .= $pasta.'/';
                    if(!is_dir($atual)){
                        mkdir($atual);
                    }
                }
            }
        }

        $mime = mime_content_type($_FILES['file']['tmp_name']);

        // Arquivo INVÁLIDO
        if(!isset($this->imagens_permitidas[$mime])){
            return ['r' => 'no', 'data' => 'Ops, o arquivo que você tentou enviar não é uma imagem.'];
        }

        $nome = Core::base64_encode($_FILES['file']['name']).'.jpg';
        $nome_big = Core::base64_encode($_FILES['file']['name']).'_big.jpg';

        move_uploaded_file($_FILES['file']['tmp_name'], $this->pathVisitante.'/ORIGINAL_'.$nome);

        ///////////////////////
        // CRIA A VERSÃO GRANDE
        $img = new Imagick();
        $img->readImageBlob(file_get_contents($this->pathVisitante.'/ORIGINAL_'.$nome));

        // REDIMENSIONA PARA TAMANHO LIMITE MANTENDO PROPORÇÕES
        $img->scaleImage(1920, 1080, true);

        $img->setImageCompressionQuality(90);
        $img->setImageFormat('jpg');
        $img->setInterlaceScheme(Imagick::INTERLACE_JPEG);
        file_put_contents($this->pathVisitante.'/'.$nome_big, $img->getImageBlob());

        $img = null;

        ///////////////////
        // CRIA A MINIATURA
        $img = new Imagick();
        $img->readImageBlob(file_get_contents($this->pathVisitante.'/ORIGINAL_'.$nome));
        $img->cropThumbnailImage(640, 640);
        $img->setImageCompressionQuality(100);
        $img->setImageFormat('jpg');
        $img->setInterlaceScheme(Imagick::INTERLACE_JPEG);
        file_put_contents($this->pathVisitante.'/thumb/'.$nome, $img->getImageBlob());

        $img = null;

        return ['r' => 'ok', 'data' => 'Pronto, upload realizado com sucesso.'];
    }

    function get($post_codigo){

        $sql = $this->conexao->prepare('
            SELECT *
            FROM postagem
            WHERE post_codigo = :post_codigo
        ');
        $sql->bindParam(':post_codigo', $post_codigo);
        $sql->execute();
        $temp = $sql->fetch(PDO::FETCH_ASSOC);
        $sql = null;

        return $temp;

    }
    function savePublicacao(){

		$post_ip = Core::ip();

		$sql = $this->conexao->prepare('
			INSERT INTO postagem (
				post_nome,
				pes_codigo,
                post_ip
			) VALUES (
				:post_nome,
				:pes_codigo,
                :post_ip
            )
		');
		$sql->bindParam(':post_nome', $data['post_nome']);
		$sql->bindParam(':pes_codigo', $_SESSION[SESSION_LOGIN]['pes_codigo']);
		$sql->bindParam(':post_ip', $post_ip);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Conta não cadastrada
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível criar uma publicação agora, tente novamente mais tarde.'];
		}

		return ['r' => 'ok', 'data' => 'Pronto, sua publicação foi criada.'];
    }
}
