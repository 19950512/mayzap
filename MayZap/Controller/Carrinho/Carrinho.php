<?php

namespace MayZap\Controller\Carrinho;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\Core;
use Model\Core\De as de;
use Model\Carrinho\Carrinho as CarrinhoCompras;

class Carrinho extends Controller {

    protected $controller = 'Carrinho';
    
    protected $Carrinho;

	public function __construct(){
        parent::__construct();
        
        $this->Carrinho = new CarrinhoCompras();
	}

	public function index(){

		$this->viewName = 'Carrinho';
		$this->view->setTitle('Carrinho - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Carrinho - '.SITE_NOME]
		]);

		$mustache = [
			'{{items_carrinho_compras}}' => $this->Carrinho->render(),
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}
	
	function add(){

		if(isset($_POST['pro_codigo']) and is_numeric($_POST['pro_codigo'])){

			$data = $this->Carrinho->add($_POST['pro_codigo']);

			echo json_encode($data);
			exit;
		}

		echo json_encode(['res' => 'no', 'data' => 'Ops, algo de errado não deu certo.']);
		exit;
	}

	function remove(){

		if(isset($_POST['pro_codigo']) and is_numeric($_POST['pro_codigo'])){

			$data = $this->Carrinho->remove($_POST['pro_codigo']);

			echo json_encode($data);
			exit;
		}

		echo json_encode(['res' => 'no', 'data' => 'Ops, algo de errado não deu certo.']);
		exit;
	}
}