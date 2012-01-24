Event.observe('addClause', 'click', addClause);
Event.observe('removeClause', 'click', removeClause);
Event.observe('startGame', 'click', startGame);
Event.observe('solveAutomatically', 'click', solveAutomatically);
Event.observe('solve', 'click', solve);
Event.observe('resetGame', 'click', resetGame);

function addClause() {
	var inputs = $('formResolutionGame').getElementsByTagName('input');
	var newSpan = document.createElement('div');
	newSpan.setAttribute('id', 'clauseSpan'+inputs.length);
	newSpan.setAttribute('class', 'form-row');
	var newInput = document.createElement('input');
	newInput.setAttribute('id', 'clauseInput'+inputs.length);
	newInput.setAttribute('name', 'clauseInput'+inputs.length);
	newInput.setAttribute('type', 'text');
	newInput.setAttribute('class', 'formGame');
	newInput.setAttribute('title', 'Digite aqui uma cláusula');
	var newBreak = document.createElement('br');
	var newBreak2 = document.createElement('br');
	newSpan.appendChild(newInput);
	$('formResolutionGame').appendChild(newSpan);
	if (inputs.length > 1) {
		$('removeClause').style.display = "";	
	}
}

function removeClause() {
	var inputs = $('formResolutionGame').getElementsByTagName('input');
	$('formResolutionGame').removeChild($('clauseSpan'+(inputs.length-1)));
	if (inputs.length == 1) {
		$('removeClause').style.display = "none";	
	}
}

function startGame() {
	validFrm('formResolutionGame');
	var pars  = 'action=startGame';
	var clauseNumber = $('formResolutionGame').getElementsByTagName('input').length;
	pars += '&clauseNumber='+clauseNumber;
	for (var i = 0; i < clauseNumber; i++) {
		pars += '&'+$("clauseInput"+i).serialize();
	}
	new Ajax.Request('src/controller.php',{
		method: 'post',
		parameters: pars,
		onSuccess: function(transport) {
			$('derivation').innerHTML = "";
			$('clausesList').innerHTML = transport.responseText; 
			var listDir = $('clausesList').getElementsByTagName('div');
			var nodes   = $A(listDir);
			nodes.each( function(nodes){
				nodes.onclick = selectClause.bind(this, nodes);
			});
		}
	});
}

function solve() {
	var pars  = 'action=solve';
	var selectedClauses = $("clausesList").getElementsByClassName("selected");
	for (var i = 0; i < 2; i++) {
		pars += '&clause'+i+'='+selectedClauses[i].getAttribute("index");
	}
	new Ajax.Request('src/controller.php',{
		method: 'post',
		parameters: pars,
		onSuccess: function(transport) {
			new Insertion.Bottom('derivation', transport.responseText);
			new Ajax.Request('src/controller.php',{
				method: 'post',
				parameters: 'action=getClauseList',
				onSuccess: function(transport) {
					$('clausesList').innerHTML = transport.responseText;
					var listDir = $('clausesList').getElementsByTagName('div');
					var nodes   = $A(listDir);
					nodes.each( function(nodes){
						nodes.onclick = selectClause.bind(this, nodes);
					});	
				}
			});		
		}
	});
}

function solveAutomatically() {
	var pars  = 'action=solveAutomatically';
	var clauseNumber = $('clausesList').getElementsByTagName('div').length;
	pars += '&clauseNumber='+clauseNumber;
	for (var i = 0; i < clauseNumber; i++) {
		pars += '&clause'+i+'='+$('clause'+i).innerHTML;
	}
	new Ajax.Request('src/controller.php',{
		method: 'post',
		parameters: pars,
		onSuccess: function(transport) {
			new Insertion.Bottom('derivation', transport.responseText);
			new Ajax.Request('src/controller.php',{
				method: 'post',
				parameters: 'action=getClauseList',
				onSuccess: function(transport) {
					$('clausesList').innerHTML = transport.responseText;
					var listDir = $('clausesList').getElementsByTagName('div');
					var nodes   = $A(listDir);
					nodes.each( function(nodes){
						nodes.onclick = selectClause.bind(this, nodes);
					});	
				}
			});		
		}
	});
}

function resetGame() {
	$('derivation').innerHTML = "";
	$('clausesList').innerHTML = "";
	new Ajax.Request('src/controller.php',{
		method: 'post',
		parameters: "action=resetGame",
		onFailure: reportError
	});
}

function selectClause(element) {
	var listMenu = $('clausesList').getElementsByTagName('div');
	var node = $A(listMenu);
	var selectedClausesNumber = parseInt($('clausesList').getAttribute("selectedClausesNumber"));
	if (selectedClausesNumber >= 2) {
		node.each(function(node){
				node.removeClassName('selected');
		});
		selectedClausesNumber = 0;
	}
	element.addClassName('selected');
	$('clausesList').setAttribute("selectedClausesNumber", ++selectedClausesNumber);
}
