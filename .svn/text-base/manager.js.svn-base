jQuery.noConflict();
jQuery(document).ready(function () {
	jQuery("#helpBorder").corner("rounded 15px");
	shortcut.add("Ctrl+Shift+h", toggleHelp);
	jQuery("#blackground").bind("click", toggleHelp);
});
/*
Validation.add('formGame', 'Please use this zip code pattern Ex. 99999 or 99999-9999',
function (v) {
	return Validation.get('IsEmpty').test(v) || /(^.([^(\x2D\x2D\x3E)]).$)|(^.[^(\x26)].$)/.test(v)
});
*/
Event.observe(window, 'load', function(){
	Event.observe('menuGenerator', 'click', updateContent.bind(this, "menuGenerator"));
	Event.observe('menuDiagnoser', 'click', updateContent.bind(this, "menuDiagnoser"));
	Event.observe('menuTranslator', 'click', updateContent.bind(this, "menuTranslator"));
	Event.observe('menuChecker', 'click', updateContent.bind(this, "menuChecker"));
	Event.observe('menuSettings', 'click', updateContent.bind(this, "menuSettings"));
	Event.observe('menuReadFormula', 'click', updateContent.bind(this, "menuReadFormula"));
	Event.observe('menuResolutionGame', 'click', updateContent.bind(this, "menuResolutionGame"));
	Event.observe('menuTruthTable', 'click', updateContent.bind(this, "menuTruthTable"));
	Event.observe('menuTreeInteraction', 'click', updateContent.bind(this, "menuTreeInteraction"));
	Event.observe('menuTester', 'click', updateContent.bind(this, "menuTester"));
	Event.observe('menuSubstitutionMaster', 'click', updateContent.bind(this, "menuSubstitutionMaster"));
	Event.observe('menuSkolemizer', 'click', updateContent.bind(this, "menuSkolemizer"));
	Event.observe('menuPrenex', 'click', updateContent.bind(this, "menuPrenex"));

	//disparar a acao de resetar a session sempre que a pagina for carregada
	new Ajax.Request('src/controller.php',{
					method: 'post', parameters: "action=load", onFailure: reportError
					 });
	//caso o link seja do tipo URL#menuGenerator
	var url = window.location.href.split("#", 2);
	if(url.length == 2){
		if (url[1] != "") {
			updateContent(url[1]);
		}
	} else {
		updateWindow('helpContent', 'src/controller.php', 'helpModule=logicamente');
	}

});

function updateContent(element){
	//content
	updateWindow('content'    , 'src/controller.php', 'module='+$(element).id);

	//help
	updateWindow('helpContent', 'src/controller.php', 'helpModule='+$(element).id);

	//javascript
	upJs('js', 'src/controller.php', 'js='+$(element).id);

	//css
	upCss("css", "src/controller.php", "css="+$(element).id);

	//menu
	selectMenu( $(element) );
}

function updateWindow(window, url, parameters){
	var myAjax = new Ajax.Updater( {success: window}, url, {method: 'post', parameters: parameters, onFailure: reportError});
}

function upJs(element, url, pars){
	//var url = '/proxy?url=' + encodeURIComponent('http://www.google.com/search?q=Prototype');
	// notice the use of a proxy to circumvent the Same Origin Policy.
	new Ajax.Request(url, {
	  method: 'post',
	  parameters: pars,
	  onSuccess: function(transport) {
		//up javascript
		//see http://www.prototypejs.org/api/string/evalScripts, http://www.prototypejs.org/api/insertion

		var html = "<script>"+transport.responseText+"</script>";
		new Insertion.Bottom( $(element), html);

		//$(element).innerHTML = transport.responseText;
	  }
	});
}

function upCss(element, url, pars){
	//$(element).href = url;
	new Ajax.Request(url, {
	  method: 'post',
	  parameters: pars,
	  onSuccess: function(transport) {
		//up css
		$(element).href = transport.responseText;
	  }
	});
}

function reportError(request)
{
	alert('Error!');
}

function selectMenu(elmBotao){
	var listMenu = $('menu').getElementsByTagName('li');
	var node     = $A(listMenu);

	//adiciona class
	elmBotao.addClassName('selected');

	//remove class das oturas
	node.without(elmBotao).each(function(node){
		//alert(node.nodeName + ': ' + node.innerHTML)
		node.removeClassName('selected');
	});
}

function validFrm(frm){
   var valid = new Validation(frm, {
	onSubmit:false,
	useTitles : true
	});

	var result = valid.validate();
	return result;
}

function toggleHelp(){
	if (jQuery("#helpBorder").css("opacity") == 0) {
		jQuery("#blackground").css("display", "block")
		jQuery("#helpBorder").fadeTo("normal", 0.8);
	} else {
		jQuery("#blackground").css("display", "none")
		jQuery("#helpBorder").fadeTo("normal", 0, function() {
			jQuery("#helpBorder").css("display", "none");
		});
	}
}
