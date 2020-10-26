<?php

namespace Model\Produtos;

use Model\Core\De AS de;
use Model\Core\View;
use Model\Produtos\Consultas;

class Produtos extends Consultas{

    protected $View;

    public $quantidade = 0;

	public $porpagina = 3;

	public $paginaatual = 1;

	public $paginatotal = 1;

    public $maximo_caracteres_miniatura = 105;

	public function __construct(){
        parent::__construct();
        
        $this->View = new View();
    }

    function getProduto($pro_codigo){
        return $this->_getProdutos($pro_codigo);
    }

    function render(){
        return $this->_render();
    }

    function renderInterno($pro_codigo){
        return $this->_renderInterno($pro_codigo);
    }
}