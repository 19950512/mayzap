<?php

namespace MayZap\Controller\Buscar;

use MayZap\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as de;
use Model\Produtos\Produtos;

class Buscar extends Controller {

    protected $controller = 'Buscar';
    
    protected $Produtos;

	public function __construct(){
        parent::__construct();
        
        $this->Produtos = new Produtos();
	}

	public function index(){

		$this->viewName = 'Buscar';

		$this->view->setTitle('Buscar - '.SITE_NOME);
		$this->view->setHeader([
			['name' => 'description', 'content' => 'Buscar - '.SITE_NOME]
		]);

        $produtos = $this->Produtos->render();
        $quantidade = $this->Produtos->quantidade;

		$mustache = [
            '{{miniatura_produtos}}' => $produtos,
            '{{quantidade_produtos}}' => $quantidade,
        ];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
    }
    
	public function ver(){

        $this->viewName = 'Ver';
        
        $pro_codigo = $this->Router->param;
        $produto = $this->Produtos->renderInterno($pro_codigo);
        
        $this->view->setTitle('Ver - '.SITE_NOME);
		$this->view->setHeader([[
            'name' => 'description', 'content' => 'Ver - '.SITE_NOME]
        ]);
            
        $mustache = [
            '{{interno_produto}}' => $produto,
        ];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
    }


	function scroll(){

		foreach($_POST as $indice => $valor){
			$_GET[$indice] = $valor;
		}

        $produtos = $this->Produtos->render();

		echo json_encode([
			'r' => 'ok',
			'data' => $produtos,
		]);
		exit;
	}
}