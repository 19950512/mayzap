<?php

namespace Model\Carrinho;

use Model\Core\Core;
use Model\Core\De AS de;

class Render {

    function _render(){

        $html = '';
        $carrinho = $this->getCarrinho();

        // macara de cada produto do carrinho
        $mascara = $this->View->getView('Buscar', 'Miniatura-Produto');

        foreach($carrinho as $key => $arr){

            $mustache = [
                '{{pro_nome}}' => $arr['pro_nome'],
            ];

            $html .= Core::mustache($mustache, $mascara);
        }

        return $html;
    }
}