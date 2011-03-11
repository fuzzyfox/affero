var $c = $common = (function(){
	var $ = {
		/**
		 * add an event to an element
		 * @param elem {object} the dom object to attach the event to
		 * @param type {string} the type of event to listen for
		 * @param fn {function} the function to run on event
		 */
		addevent : function(elem, type, fn, capture){
			if(typeof capture !== 'boolean')
			{ capture = false; }
			if(elem.addEventListener)
			{ elem.addEventListener(type, fn, capture); }
			else if(elem.attachEvent)
			{ elem.attachEvent('on'+type, fn); }
			else
			{ elem['on'+type]; }
			return this;
		},
		/**
		 * add a function (or more) to page load
		 * @param fn {function} the function to run on load
		 */
		onload : function(fn){
			if(typeof window.onload !== 'function')
			{
				window.onload = fn;
			}
			else
			{
				window.onload = function(){
					window.onload();
					fn();
				};
			}
			return this;
		},
		/**
		 * toggle an element in/out of view
		 */
		toggle : function(id)
		{
			elem = document.getElementById(id);
			if(elem.style.display != 'none')
			{ elem.style.display = 'none'; }
			else
			{ elem.style.display = ''; }
		},
		/**
		 * foreach emulation of sorts
		 * @param obj {object} the object to iterate through
		 * @param fn {function} what you want to do with the key and value passed to you
		 */
		each : function(obj, fn){
			for(var key in obj)
			{
				if(!isNaN(key))
				{
					fn(key, obj[key]);
				}
			}
		},
		/**
		 * simple pubsub system [setup]
		 */
		pubsub : function(){
			var signals = arguments;
			this.subscribers = {};
			for(var i = 0; i < signals.length; i++)
			{
				this.subscribers[signals[i]] = [];
			}
		},
		/**
		 * simple and useful little trim function to remove unwanted whitespace
		 */
		trim : function(str){
			return str.replace(/^\s*/, '').replace(/\s*$/, '');
		},
		/**
		 * a simple ajax control for quick easy ajax calls
		 * @param method {string} method of request in CAPS
		 * @param url {string} the url we want to make the request to
		 * @param callback {function} the function to run on success
		 */
		ajax : function(method, url, callback){
			var xhr;
			if(window.XMLHttpRequest)
			{
				xhr = new XMLHttpRequest();
			}
			else
			{
				xhr = new ActiveXObject('Microsoft.XMLHTTP');
			}
			xhr.onreadystatechange = function(){
				if((xhr.readyState == 4)&&(xhr.status == 200))
				{
					callback(xhr.responseText);
				}
			};
			xhr.open(method, url, true);
			xhr.send();
		}
	};
	
	/**
	 * pubsub system [completion]
	 */
	$.pubsub.prototype.pub = function(signal){
		var args = Array.prototype.slice.call(arguments, 1);
		for(var i = 0; i < this.subscribers[signal].length; i++)
		{
			var handler = this.subscribers[signal][i];
			handler.apply(this, args);
		}
	};
	$.pubsub.prototype.sub = function(signal, scope, handlerName){
		var curryArray = Array.prototype.slice.call(arguments, 3);
		this.subscribers[signal].push(function(){
			var normArgs = Array.prototype.slice.call(arguments, 0);
			scope[handlerName].apply((scope||window), curryArray.concat(normArgs));
		});
	}
	
	return $;
})();

// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};