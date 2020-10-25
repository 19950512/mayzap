<?php

namespace Model\Produtos;

use Model\Core\Core;
use Model\Core\De AS de;
use Model\Produtos\Render;

use Model\Model;

class Render extends Model {

    protected function _render($pro_codigo = false){

        // dataset produtos
        $produtos = $this->_getProdutos($pro_codigo);

        // macara miniatura produto
        $mascara = $this->View->getView('Buscar', 'Miniatura-Produto');
        
        if($pro_codigo !== false AND is_numeric($pro_codigo)){
            $mascara = $this->View->getView('Buscar', 'Interno-Produto');
        }

        $html = '';

        foreach($produtos as $key => $arr){

            $pro_valor = $arr['pro_valor'] > 0 ? 'R$ '.Core::number_format($arr['pro_valor']) : 'Consulte-nos';
            $pro_descricao_miniatura = Core::substr($arr['pro_descricao'], 0, $this->maximo_caracteres_miniatura);
            if(Core::strlen($pro_descricao_miniatura) == $this->maximo_caracteres_miniatura){
                $pro_descricao_miniatura = $pro_descricao_miniatura.'...';
            }

            $mustache = [
                '{{pro_nome}}' => $arr['pro_nome'],
                '{{pro_valor}}' => $pro_valor,
                '{{pro_descricao}}' => $arr['pro_descricao'],
                '{{pro_descricao_miniatura}}' => $pro_descricao_miniatura,
                '{{pro_descricao_url}}' => Core::trataURL(Core::substr($arr['pro_descricao'], 0, 20)),
                '{{pro_codigo}}' => $arr['pro_codigo'],
            ];

            $html .= Core::mustache($mustache, $mascara);
        }

        return $html;
    }

    protected function _renderInterno($pro_codigo){
        return $this->_render($pro_codigo);
    }
}