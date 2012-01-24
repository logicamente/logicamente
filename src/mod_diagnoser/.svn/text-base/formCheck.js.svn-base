// JavaScript Document
Event.observe('btnCheck', 'click', checkFormula.bind(this, 'formula'));	

function checkFormula(formula){
	if( validFrm('formCheck') ){	
		var pars = 'action=checkFormula';
			pars += '&'+$(formula).serialize();		

		//up status
		updateWindow('status', 'src/controller.php', pars);
	}
}
