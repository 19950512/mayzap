<?php

namespace Model\Produtos;

use Model\Core\De AS de;
use Model\Produtos\Render;

use Model\Db\Connection;
use PDO;

class Consultas extends Render {

    protected function _getProdutos($pro_codigo = false){

        $where = '';
        if($pro_codigo !== false and is_numeric($pro_codigo)){
            $where = 'AND pro_codigo = '.$pro_codigo;
        }

        $sql = $this->conexao->prepare('
            SELECT * 
            FROM cad_produtos 
            WHERE pro_ativo = true AND pro_quantidade > 0 '.$where.'
            ORDER BY pro_valor ASC
        ');
        $sql->execute();
        $fetch = $sql->fetchAll(PDO::FETCH_ASSOC);
        $sql = null;

        if($fetch !== false){
            
            // Seta a quantidade de produtos encontrados.
            $this->quantidade = count($fetch);
            if(count($fetch) < 10){
                $this->quantidade = '0'.count($fetch);
            }
        }

        /* INÍCIO DA PAGINAÇÃO */
        $this->paginaatual = $_GET['pag'] ?? $this->paginaatual;

		$this->paginatotal = floor(count($fetch) / $this->porpagina);
		if(count($fetch) % $this->porpagina !== 0){
			$this->paginatotal += 1;
		}

		$offsetimoveis = ($this->porpagina * $this->paginaatual) - $this->porpagina + 1;

		if($offsetimoveis > 0){
			$offsetimoveis -= 1;
		}
		if($offsetimoveis < 0){
			$offsetimoveis = 0;
		}

        $fetch = array_slice($fetch, $offsetimoveis, $this->porpagina);
        /* FIM DA PAGINAÇÃO */

        return $fetch;
    }
}