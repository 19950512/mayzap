<?php

namespace MayZap\Controller\Webhook;

use Model\Core\De as De;

class Webhook {

	protected $controller = 'Webhook';

	public function __construct(){
	}

    /**
     * Quando Recebe mensagem no whats
    {
        "event":"chat",
        "token":"tFgn68COf9kX2CyauKcOs8bMtVdpuMoeyKto",
        "user":"555484192072",
        "contact":{
            "number":"555491628540",
            "name":"Rita Rosa",
            "server":"c.us"
        },
        "chat":{
            "dtm":"1603000391",
            "uid":"885E859941CBA42163B48A054BC02A91",
            "dir":"i",
            "type":"chat",
            "body":"Oi"
        },
        "ack":"-1"
    }
     */
	public function index(){

        $json = json_encode($_POST);

        file_put_contents('/home/projetos/mayzap/webhook/whatsapp.json', $json);
        exit;
	}
}