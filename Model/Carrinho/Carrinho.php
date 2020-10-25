<?php

namespace Model\Carrinho;

use Model\Core\View;
use Model\Core\De as de;
use Model\Carrinho\Consultas;
use Model\Produtos\Produtos;

class Carrinho extends Consultas{

    protected $View;

    protected $Produtos;

	public function __construct(){
        $this->View = new View();
        $this->Produtos = new Produtos();
    }

    function getCarrinho(){
        return $_SESSION[SESSION_CARRINHO] ?? [];
    }

    function remove($pro_codigo){

        if(!is_numeric($pro_codigo)){
            return ['res' => 'no', 'data' => 'Ops, o produto não tem um código válido.'];
        }

        if(!isset($_SESSION[SESSION_CARRINHO][$pro_codigo])){
            return ['res' => 'no', 'data' => 'Ops, o produto que deseja remover do carrinho não está no carrinho.'];
        }

        // Pega informaçẽs do produto.
        $produto = $this->Produtos->getProduto($pro_codigo);
        unset($_SESSION[SESSION_CARRINHO][$pro_codigo]);
        return ['res' => 'ok', 'data' => 'Pronto, o produto '.$produto[0]['pro_nome'].' foi removido do seu carrinho'];
    }

    function add($pro_codigo){

        if(!is_numeric($pro_codigo)){
            return ['res' => 'no', 'data' => 'Ops, o produto não tem um código válido.'];
        }

        // Pega informaçẽs do produto.
        $produto = $this->Produtos->getProduto($pro_codigo);

        if(isset($produto[0]['pro_nome']) and !empty($produto[0]['pro_nome'])){

            $_SESSION[SESSION_CARRINHO][$pro_codigo] = $produto[0];

            return ['res' => 'ok', 'data' => 'Pronto, o produto '.$produto[0]['pro_nome'].' foi adicionado ao seu carrinho'];
        }

        return ['res' => 'no', 'data' => 'Ops, não encontramos o seu produto.'];
    }

    function render(){
        return $this->_render();
    }
}