// JavaScript Document
Event.observe('btnFormulas', 'click', generateFormulas);
Event.observe('formGenerator', 'submit', generateFormulas);

//gerar formulas 
function generateFormulas(){
	//valid form
	var cont = $('cont').innerHTML;
	if( validFrm('formGenerator') ){

	var pars  = 'action=generateFormulas';
		pars += '&'+$('nConnective').serialize();
	    pars += '&'+$('nAtom').serialize();
		pars += '&cont=' + cont;

		//up content of area's transfer
	var request = new Ajax.Request('src/controller.php',{
					method: 'post', parameters: pars, onFailure: reportError,
					 onSuccess: function(transport) {
						var formula =(transport.responseText);
  						new Insertion.Bottom('transfer', '<div id = "f'+cont+'">' + formula + '</div>');
						new Insertion.Bottom('transfer', '<button id="b'+cont +'">Delete</button>');
						Event.observe('b'+cont, 'click', del.bind(this, cont));
						Event.observe('f'+cont, 'click', sel.bind(this, cont));
						cont++;
						$('cont').innerHTML = cont;
					}
					 });
	}

}


function del(id){
	$('f'+ id).remove();
	$('b'+ id).remove();
	var pars = "&action=delFormula&form=f"+id;
	new Ajax.Request('src/controller.php',{
					method: 'post', parameters: pars, onFailure: reportError			 
					 });
}

function sel(id){	
	var listMenu = $('transfer').getElementsByTagName('div');
	var node = $A(listMenu);
	
	//adiciona class
	$('f'+ id).addClassName('selected');
	//remove class das oturas
	var elmBotao = $('f'+ id);
	node.without(elmBotao).each(function(node){
		node.removeClassName('selected');
	});
	var pars = "&action=selFormula&id="+id;
	new Ajax.Request('src/controller.php',{
					method: 'post', parameters: pars, onFailure: reportError			 
					 });
}
