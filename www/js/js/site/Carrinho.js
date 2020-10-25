class Carrinho {
 
    constructor(){

    }

    add(btn, pro_codigo){

		btn.innerHTML = '<i class="icl ic-spinner-third rotating"></i>';
		btn.disabled = true;

		var carrinho = dev.ajax('/carrinho/add', {pro_codigo});

		carrinho.then(res => {

			btn.innerHTML = 'Adicionar';
			dev.mensagem(res.data);
            btn.disabled = false;

            if(res.res == 'ok'){
                btn.innerHTML = 'Remover';
                btn.setAttribute('onclick', `carrinho.remove(this,${pro_codigo})`);
            }

		}).catch(res => {
			console.error("Ops, algo de errado não deu certo.", res);
		});
    }

    remove(btn, pro_codigo){

		btn.innerHTML = '<i class="icl ic-spinner-third rotating"></i>';
		btn.disabled = true;

		var carrinho = dev.ajax('/carrinho/remove', {pro_codigo});

		carrinho.then(res => {

			btn.innerHTML = 'Adicionar';
			dev.mensagem(res.data);
            btn.disabled = false;
            
            if(res.res == 'ok'){
                btn.innerHTML = 'Adicionar';
                btn.setAttribute('onclick', `carrinho.add(this,${pro_codigo})`);
            }

		}).catch(res => {
			console.error("Ops, algo de errado não deu certo.", res);
		});
    }
}

window.carrinho = new Carrinho();