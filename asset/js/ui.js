var $ui = $userinterface = (function($c){
	var $ = {
		/**
		 * autoSlug
		 *
		 * generates slugs based on an area/skill name as it is typed and ouputs
		 * this into a designated field
		 *
		 * @param input {element} the input element to get the name from.
		 * @param output {element} this input element to put the slug into.
		 */
		autoSlug : function(input, output){
			$c.addevent(input, 'keyup', function(){
				value = this.value.toLowerCase().replace(/\s/g, '_').replace(/[^a-z0-9_\-]/g, '');
				output.value = value;
			});
		},
		
		/**
		 * tabs
		 *
		 * this creates and powers a tabbed user interface element, 'tabs' can be
		 * any collection of elements (usually ul>li), and 'panels' the items the
		 * tabs bring to view
		 *
		 * 'tabs' should have a rel that matches the id of the 'panel'
		 *
		 * @param tabs {element} the collection of tabs
		 */
		tabs : function(tabs){
			//tab switching based on clicking tabs
			$c.each(tabs, function(key, value){
				//add events to select tabs/panels on click
				$c.addevent(value, 'click', function(){
					//clear all tabs
					$c.each(tabs, function(k, v){
						v.setAttribute('class', '');
						document.getElementById(v.getAttribute('rel')).style.display = 'none';
					});
					
					//set correct tab as active
					this.setAttribute('class', 'active');
					
					//switch to correct section
					document.getElementById(this.getAttribute('rel')).style.display = 'block';
				});
			});
			//initial tab selection by url
			//var tab = window.location.href.split('#');
			//if(tab.length > 1)
			//{
			//	$c.each(tabs, function(k, v){
			//		if(v.getAttribute('rel') == tab[1])
			//		{
			//			v.setAttribute('class', 'active');
			//			document.getElementById(v.getAttribute('rel')).setAttribute('style', 'display:block');
			//		}
			//		else
			//		{
			//			v.setAttribute('class', '');
			//			document.getElementById(v.getAttribute('rel')).setAttribute('style', 'display:none');
			//		}
			//	});
			//}
		}
	};
	return $;
})($c);