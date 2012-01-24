// JavaScript Document
Event.observe('back', 'click', addItem.bind(this, 'selected', 'unselected'));	
Event.observe('go', 'click', addItem.bind(this, 'unselected', 'selected'));	
Event.observe('btnAddConnective', 'click', showAddCon.bind(this, 'formNewConnective') );	
Event.observe('btnNewCon', 'click', addNewCon.bind(this, 'symbol', 'arity','order', 'unselected') );
Event.observe('btnSet', 'click', setConnectives.bind(this, 'selected') );

Event.observe('infix',      'click', upSelect.bind(this, 'infix', 'unselected') );		
Event.observe('polish',     'click', upSelect.bind(this, 'polish', 'unselected') );		
Event.observe('functional', 'click', upSelect.bind(this, 'functional', 'unselected') );	

function setConnectives(arrayCon){
	var pars  = 'action=setConnectives';
	
	//symbols
	for(var i = 0; i < $('selected').length; i++ ){
		pars += '&symbol[]='+ encodeURIComponent( $('selected').options[i].text );
                //pars += '&symbol[]='+ html_entity_encode( $('selected').options[i].text);		
		pars += '&arity[]='+ $('selected').options[i].value;
}

	//up content of area's config
	updateWindow('config', 'src/controller.php', pars);

	alert("New Settings are Defined!");
}

//class con to simulate connective
function con(symbol, arity){
	this.symbol = symbol;
	this.arity  = arity;
}

/**
 * Up DOMSELECT
 *
 * @param string tipo
 * @param DOMSELECT local
 */
function upSelect(tipo, local){		
	var infix = new Array( new con("~", 1), 
				      	   new con("&", 2), 
						   new con("|", 2), 
						   new con("+", 2), 
						   new con("-->", 2),
						   new con("<->", 2) );
	
	var polish = new Array( new con("N", 1), 
							new con("K", 2), 
							new con("A", 2), 
							new con("X", 2), 
							new con("C", 2), 
							new con("E", 2) );

	var functional = new Array( new con("neg", 1), 
								new con("and", 2), 
								new con("or", 2), 
								new con("xor", 2), 
								new con("imp", 2), 
								new con("eq", 2));

	switch (tipo){
		case "infix"  :	_upSlct(infix, local); break;
		case "polish" :	_upSlct(polish, local); break;
		case "functional":	_upSlct(functional, local); break;
	}
}

/**
 * Up DOMSELECT
 *
 * @param array array
 * @param DOMSELECT local
 */
function _upSlct(array, local){
	var list = $(local);
	var len  = list.length;
	var j = len;

	//clear select
	list.options.length = null;

	//insert options
	for(i = 0; i < array.length; i++){
		list.options[i] = new Option(array[i].symbol, array[i].arity);		
	}
}

function addNewCon(text, value,order, local){
	$('newConnective').style.display = "none";
	var list = $(local);
	var len  = list.length;
	t = $(text).value;
	v = $(value).value;
	v += "&order[]=" + $(order).value;
	list.options[len] =  new Option(t, v);
}

function showAddCon(frm){
	$('newConnective').style.display = "block";
	$(frm).reset();
}

function addItem(campoOrig,campoDest) 
{
	x = campoOrig.value;
	
	if (x == "")
	{
		alert('Select a item!');
	}
	
	ListaDisponiveis = $(campoOrig); 
	ListaAcordo = $(campoDest);
	
	var len = ListaAcordo.length;
	
	for(var i = 0; i < ListaDisponiveis.length; i++) 
	{
		if ((ListaDisponiveis.options[i] != null) && 
			  (ListaDisponiveis.options[i].selected)) 
		{
			
			ListaAcordo.options[len] = new Option(ListaDisponiveis.options[i].text, ListaDisponiveis.options[i].value); 
			len++;
			ListaDisponiveis.options[i] = null;  
			i--;
		}
	}
}
