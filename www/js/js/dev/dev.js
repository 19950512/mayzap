class Dev {

   async add(evento, elemento, funcao) {
        if(elemento !== null){
            if(window.addEventListener){
                elemento.addEventListener(evento, function(evento){
                    funcao(evento);
                }, true);
            }else{
                elemento.attachEvent("on"+evento, function(){
                    funcao(evento);
                });
            }
        }
    }

    debounce(func, wait, immediate){

        let timeout;
        return function(...args){
            
            const context = this;

            const later = () => {

                timeout = null;
                if(!immediate){
                    func.apply(context, args);
                }
            };

            const callNow = immediate && !timeout;

            clearTimeout(timeout);

            timeout = setTimeout(later, wait);

            if (callNow){
                func.apply(context, args);
            }
        };
    }

    targt(evts) {
        return evts.target;
    }

    trigger (evts, elemento){
        if(document.createEvent){
            var Evento = document.createEvent('HTMLEvents');
            Evento.initEvent(evts, true, true);
            elemento.dispatchEvent(Evento);
        }else{
            var Evento = document.createEventObject();
            elemento.fireEvent('on'+evts, Evento);
        }
    }

    id(element){
        return document.getElementById(element);
    }

    delay(funcao, time){
        setTimeout( funcao, time );
    }

    testJSON(text){
        if (typeof text !== "string"){
            return false;
        }
        try{
            return true;
        }
        catch (error){
            return false;
        }
    }

    render(data, opts){

        if(!opts.renderTo){
            console.warn('Hey, set up: renderTo');
            return false;
        }

        if(!opts.mask){
            console.warn('Hey, set up: mask');
            return false;
        }

        if(!opts.filters){
            opts.filters = {};
        }

        var sizeData = Object.keys(data).length;

        if(this.id(opts.renderTo) && sizeData > 0){

            var renderTo = this.id(opts.renderTo);
            var li = '';

            var exp = new RegExp(opts.search, 'i');

            var ct = 1;

            for (var cols in data){

                var temp = opts.mask;
                var f = true;

                for (var ind in data[cols]){

                    if(typeof(opts.filters[ind]) !== 'undefined' && opts.filters[ind] !== '' && data[cols][ind] !== ''  && opts.filters[ind] != data[cols][ind]){
                        f = false;
                    }else{
                        temp = temp.split('{{'+ind+'}}').join(data[cols][ind]);
                    }
                }

                temp = temp.split('{{ct}}').join(ct);
                temp = temp.split('{{key}}').join((ct - 1));

                if(opts.search != '' && exp.test(temp) === false){
                    temp = '';
                }

                if(f === true){
                    li += temp;
                    ct = ct + 1;
                }
            }

            if(opts.empty && li == ''){
                li = opts.empty;
            
            }else if(opts.maskLast && li != '' && ct > 1){
                li = li + opts.maskLast;
            }

            renderTo.innerHTML = li;

        }else{

            var renderTo = this.id(opts.renderTo);
            renderTo.innerHTML = opts.empty;

        }
    }

    openDialog(obj){

        var c = document.createElement('button');
        c.classList.add('boss-dialog-close');
        c.setAttribute('id', 'boss-dialog-close');
        c.innerHTML = '<i class="ic ic-times"></i>';

        obj.html = obj.html.split('{{dialogs}}').join('');

        if(!this.id('boss-dialog')){
            var dialog = document.createElement('div');
            dialog.setAttribute('id', 'boss-dialog');

            var area = document.createElement('div');
            area.classList.add('boss-dialog-area');
            area.innerHTML = obj.html;

            if(obj.close){
                area.appendChild(c);
            }

            dialog.appendChild(area);
            document.body.appendChild(dialog);

        }else{

            var dialog = this.id('boss-dialog');
            dialog.classList.remove('hidden');
            dialog.innerHTML = '';

            var area = document.createElement('div');
            area.classList.add('boss-dialog-area');
            area.innerHTML = obj.html;

            if(obj.close){
                area.appendChild(c);
            }

            dialog.appendChild(area);

        }

        if(obj.invisible){
            area.classList.add('boss-dialog-invisible');
        }

        this.delay(function(){
            var scripts = dialog.getElementsByTagName('script');
            for(var x in scripts){
                eval(scripts[x].innerHTML);
            }
        }, 10);

        this.add('click', this.id('boss-dialog-close'), function(evts){

            dev.closeDialog();

            if(obj.callBack && typeof(obj.callBack) === 'function'){
                obj.callBack();
            }
        });
    }
    
    closeDialog(){

        if(this.id('boss-dialog')){

            var dialog = this.id('boss-dialog');
            dialog.classList.add('hidden');
            dialog.innerHTML = '';

        }
        /*document.body.removeAttribute('style');*/
    }

   async ajax(url, data){

		var result = [];
		for(var i in data){
			result.push([i]+'='+data [i]);
		}

		try {

			return fetch(url, {
				method: 'POST',
				mode: 'cors',
				headers: {
             	   'Content-Type':'application/x-www-form-urlencoded'
				},
				body: result.join('&')
			}).then(resposta => {

				let res = resposta.clone().json();
			
				return res;

			}).catch (error => {
					console.error(error)
				}
			);

		} catch (erro) {
			console.error('ERROW: ' + erro);
		}
    }

    mensagem(mensagem, tipo = 'blue', time = 3000){
        
        let classeBG = (tipo == 'blue') ? 'feed_blue' : 'feed_red';

        feed_back.classList.add(classeBG);
        feed_back.style.opacity = '1';
        feed_back.style.top = '-1px';
        feed_back.innerHTML = '<p>'+mensagem+'</p>';
        
        this.delay( f => {
        
            feed_back.style.opacity = null;
            feed_back.style.top = null;
            feed_back.classList.remove(classeBG);
            feed_back.innerHTML = '';

        }, time);
    }
}

var dev = new Dev();