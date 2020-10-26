window.scrollInfinity = {
	paginationNumeric: 1,
	buscarequest: false,
	feedbackElement: false,
    renderProdutosElemento: false,
    htmlAnimation: '<p class="text-center down arrow "><i class="icl ic-chevron-double-down font30"></i></p>',
	imoveisVisiveisMerged: {},
	urlToArray: url => {
		var request = {};
		var pairs = url.substring(url.indexOf('?') + 1).split('&');
		for (var i = 0; i < pairs.length; i++) {
			if(!pairs[i])
				continue;
			var pair = pairs[i].split('=');
			request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
		}
		return request;
	},
	reset: f => {
		if(typeof(scrollInfinity.feedbackElement) !== 'undefined'){
            scrollInfinity.feedbackElement.innerHTML = scrollInfinity.htmlAnimation;
        }

        scrollInfinity.paginationNumeric = 1;
		scrollInfinity.buscarequest = false;
	},
	request: () => {
		if(scrollInfinity.feedbackElement){
			scrollInfinity.feedbackElement.innerHTML = '<p class="text-center"><i class="icl ic-spinner-third rotating"></i></p>';
		}
		scrollInfinity.paginationNumeric += 1;

		var tempFields = window.location.href;
		
		tempFields = scrollInfinity.urlToArray(tempFields);
		tempFields['pag'] = scrollInfinity.paginationNumeric;

        var scroll = dev.ajax('/buscar/scroll', tempFields);
        
        scroll.then(rtn => {

            /* if(typeof(setimoveisVisiveis) !== 'undefined' && typeof(getImovelImagens) !== 'undefined'){
                loadImages(rtn.imagensVisiveis).then(images => activeSetaImagens()).catch(e => console.error('Ops, parece que algo não deu certo', e));
                setimoveisVisiveis(Object.assign(scrollInfinity.imoveisVisiveisMerged, imoveisVisiveis, rtn.imoveisVisiveis));
                getImovelImagens();	
            } */
            
            if(scrollInfinity.renderProdutosElemento){
                scrollInfinity.renderProdutosElemento.innerHTML = scrollInfinity.renderProdutosElemento.innerHTML + rtn.data;
            }

            if(scrollInfinity.feedbackElement){
                scrollInfinity.feedbackElement.innerHTML = scrollInfinity.htmlAnimation;
            }

            /* Isso é para pausar a busca por scroll */
            if(rtn['data'].length <= 50){
                scrollInfinity.buscarequest = true;
                if(scrollInfinity.feedbackElement){
                    scrollInfinity.feedbackElement.innerHTML = '';
                }
            }
		});
    },
	delay: ((fn, ms, label) => {
		if(typeof(delay) === 'undefined'){
			window.delay = {};
		}
		return function(fn, ms, label){
			clearTimeout(delay[label]);
			delay[label] = setTimeout(fn, ms);
		};
	})(),
	init: (footerElement, renderProdutosElemento, feedbackElement) => {

		if(typeof(footerElement) == 'undefined'){ 
			console.warn('Ops, você precisa informar qual é o footer para o ScrollInfinity funcionar.');
			return false;
		}
		if(typeof(renderProdutosElemento) == 'undefined'){
			console.warn('Ops, você precisa informar qual é o elemento que vai receber os imóveis para o ScrollInfinity funcionar.');
			return false;
		}

		scrollInfinity.renderProdutosElemento = renderProdutosElemento;
		scrollInfinity.feedbackElement = feedbackElement;

		dev.add('scroll', document, f => {

            if(window.location.pathname == '/buscar'){
                
                var tamanhoScreen = window.scrollY + footerElement.offsetHeight;
            
                if(scrollInfinity.buscarequest){
                    return false;
                }
                
                scrollInfinity.delay(f => {
                    
                    if(tamanhoScreen >= (document.body.offsetHeight - (footerElement.offsetHeight / 3))){
                        return false;
                    }
                    
                    scrollInfinity.request();
                    
                }, 500, 'true');
            }
        });
	}
};