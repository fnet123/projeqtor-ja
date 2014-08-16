//>>built
define("dojo/debounce",[],function(){return function(cb,_1){var _2;return function(){if(_2){clearTimeout(_2);}var a=arguments;_2=setTimeout(function(){cb.apply(this,a);},_1);};};});