//get url params start
$.urlParam = function(name, url) {
	if (!url) {
		url = window.location.href;
	}
	var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
	if (!results) {
		return undefined;
	}
	return results[1] || undefined;

	//to call if url is xyz.com/index.html?lang=en
	//then to call just go:
	// var langval = $.urlParam('lang');
}

//get url params end

//change url params functions start
function a() {
	//window.history.replaceState('{}', null, ''+window.location.search+'?' + urlNew); 
					
	$(document).trigger('function_a_complete')
}
function b(param, paramValue, url_New) {
	url_New.set(''+param+'', paramValue)				
	window.history.replaceState('{}', null, 'market?' + url_New)
}

function c() {
	sortParam = $.urlParam('sort');
	filterParam = $.urlParam('filter');
	//console.log('sort='+sortParam +'filter='+filterParam)
	//add the ajax to filter/sort stores here
	$('.timelineshops').html('<div class="loader-div"><img src="img_loading/loading.gif" alt="loading..."/></div>')
	setTimeout(function(){
				//loaderSkeleton.className += " hidden";
				const loader_div= document.querySelector(".loader-div");
				loader_div.className += " hidden";
				loadStores(sortParam, filterParam)
			},1500);
	
	
}
//change url params functions end

/******************Sort Start******************/


$(document).on('click','.c-sort', function() {
	sortParam = $.urlParam('sort');
	filterParam = $.urlParam('filter');
	$('.shop-filter-li').removeClass('active');
	$(this).addClass('active');
	cSort = $(this).attr('data-csort');
	
	if (sortParam != undefined) {
		if (filterParam == undefined) {
			if (sortParam != cSort) {
				const urlNew = new URLSearchParams(window.location.search);
				urlNew.set('sort', cSort)
				//window.location.search = urlNew;
				
				window.history.pushState('{}', null, '?sort='+cSort+'')
				c()
			} else {
				window.location.reload
			}
		} else {
			if (sortParam != cSort) {
				//const urlNew = new URLSearchParams(window.location.search);
				//urlNew.set('sort', cSort)
				//window.location.search = urlNew;
				
				const urlNew = new URLSearchParams(window.location.search);
				
				urlNew.delete('sort');
				
				/*function a() {
					//window.history.replaceState('{}', null, ''+window.location.search+'?' + urlNew); 
					console.log('we in here 1')
					$(document).trigger('function_a_complete')
				}
				function b(param, paramValue) {
					urlNew.set(''+param+'', paramValue)
					console.log('we in here 2')
					window.history.replaceState('{}', null, 'market?' + urlNew)
				}*/
				
				$(document).bind('function_a_complete', function() {
					b('sort', cSort, urlNew)
					
				});
				//a()
				$.when(a()).then(c())
			} else {
				window.location.reload
			}
		}

	} else {
		if (filterParam == undefined) { 

			const urlNew = new URLSearchParams(window.location.search);
			urlNew.set('sort', cSort)
			//window.location.search = urlNew;
				
			window.history.pushState('{}', null, ''+window.location+'?sort='+cSort+'')
			c()
		} else {
			const urlNew = new URLSearchParams(window.location.search);
			urlNew.set('sort', cSort)
			//window.location.search = urlNew;
				
			window.history.pushState('{}', null, ''+window.location+'&sort='+cSort+'')
			c()
		}
		
	}
	
})

/******************Sort End ******************/
/******************Filter Start******************/
$(document).on('click','.c-filter', function() {
	cFilter = $(this).attr('data-cfilter');
	filterParam = $.urlParam('filter');
	sortParam = $.urlParam('sort');

	//cSort = $(this).attr('data-cfilter');

	if (filterParam != undefined) {
		
		if (sortParam == undefined) {		

			if (filterParam != cFilter) {
				const urlNew = new URLSearchParams(window.location.search);
				urlNew.set('filter', cFilter)
				//window.location.search = urlNew;

				window.history.pushState('{}', null, '?filter='+cFilter+'')
				c()
			} else {
				window.location.reload
			}
			
		} else {
			if (filterParam != cFilter) {
				//const urlNew = new URLSearchParams(window.location.search);
				//urlNew.set('sort', cSort)
				//window.location.search = urlNew;
				
				const urlNew = new URLSearchParams(window.location.search);
				
				urlNew.delete('filter');
				
				/*function a() {
					//window.history.replaceState('{}', null, ''+window.location.search+'?' + urlNew); 
					
					$(document).trigger('function_a_complete')
				}
				function b(param, paramValue) {
					urlNew.set(''+param+'', paramValue)
					
					window.history.replaceState('{}', null, 'market?' + urlNew)
				}*/
				
				$(document).bind('function_a_complete', function() {
					b('filter', cFilter, urlNew)
					
				});
				//a()
				$.when(a()).then(c())
			} else {
				window.location.reload
			}
		}
	} else {
		if (sortParam == undefined) { 
			const urlNew = new URLSearchParams(window.location.search);
			urlNew.set('filter', cFilter)
			//window.location.search = urlNew;
				
			window.history.pushState('{}', null, ''+window.location+'?filter='+cFilter+'')
			c()
		} else {
			const urlNew = new URLSearchParams(window.location.search);
			urlNew.set('filter', cFilter)
			//window.location.search = urlNew;
				
			window.history.pushState('{}', null, ''+window.location+'&filter='+cFilter+'')
			c()
		}
		
	}
	
})

/******************Filter End******************/