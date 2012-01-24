<?php
	/**
	* SCMUtil.class.php
	*
	* @author Max Rosan
	* @author Thales Galdino
	* @author Giuliano Vilela
	* @author Lucas Araújo
	*/

	error_reporting(E_ALL);

	/**
	* Retorna um array capaz de representar
	* uma relação ou uma função n-ária.
	* Inicializa os campos com o valor $default.
	*/
	function n_arity_array($ar,$univ_size,$default) {
		// Caso o array requisitado seja de dimensão 0
		if ($ar == 0) {
			// Então o array se reduz à um único elemento
			return $default;
		}
		// Caso a dimensão do array seja maior ou igual à 1
		else {
			// $ret será o array que será retornado
			// Ele terá um tamanho $univ_size
			$ret = array();
			for ($i = 0; $i < $univ_size; ++$i) {
				// E cada posição dele será um array de aridade $ar-1
				$ret[] = n_arity_array($ar-1,$univ_size,$default);
			}
			return $ret;
		}
	}

	/**
	* Transforma um array de relação multi-dimensional em
	* um array uni-dimensional.
	*/
	function rel_to_flat($rel) {
		$str = array();
		rel_to_flat_rec($rel,$str);
		return $str;
	}

	function rel_to_flat_rec($rel,&$str) {
		if (is_bool($rel)) {
			$str[] = $rel;
		}
		else {
			foreach ($rel as $sub_rel)
				rel_to_flat_rec($sub_rel,$str);
		}
	}

	/**
	* Transforma um array de relação uni-dimensional em
	* um array multi-dimensional.
	*/
	function flat_to_rel($str,$ar,$univ_size) {
		$pos = 0;
		$rel = n_arity_array($ar,$univ_size,false);
		flat_to_rel_rec($rel,$str,$pos);
		return $rel;
	}

	function flat_to_rel_rec(&$rel,&$str,&$pos) {
		if (is_bool($rel)) {
			$rel = $str[$pos++];
		}
		else {
			foreach ($rel as &$sub_rel) {
				flat_to_rel_rec($sub_rel,$str,$pos);
			}
		}
	}

	/**
	* Transforma um array de função multi-dimensional em
	* um array uni-dimensional.
	*/
	function func_to_flat($func) {
		$str = array();
		func_to_flat_rec($func,$str);
		return $str;
	}

	function func_to_flat_rec($func,&$str) {
		if (is_int($func)) {
			$str[] = $func;
		}
		else {
			foreach ($func as $sub_func)
				func_to_flat_rec($sub_func,$str);
		}
	}

	/**
	* Transforma um array de função uni-dimensional em
	* um array multi-dimensional.
	*/
	function flat_to_func($str,$ar,$univ_size) {
		$pos = 0;
		$func = n_arity_array($ar,$univ_size,0);
		flat_to_func_rec($func,$str,$pos);
		return $func;
	}

	function flat_to_func_rec(&$func,&$str,&$pos) {
		if (is_int($func)) {
			$func = $str[$pos++];
		}
		else {
			foreach ($func as &$sub_func) {
				flat_to_func_rec($sub_func,$str,$pos);
			}
		}
	}

	/**
	* À partir de um array de relação uni-dimensional,
	* performa uma soma com o valor lógico 1 e 
	* gera a próxima relação na sequência.
	*/
	function gen_next_rel(&$str_rel) {
		foreach ($str_rel as &$val) {
			if ($val) {
				$val = false;
			}
			else {
				$val = true;
				return true;
			}
		}

		return false;
	}

	/**
	* À partir de um array de função uni-dimensional,
	* performa uma soma com o valor lógico 1 e 
	* gera a próxima função na sequência.
	*/
	function gen_next_func(&$str_func,$univ_size) {
		foreach ($str_func as &$val) {
			if ($val < $univ_size-1) {
				++$val;
				return true;
			}
			else {
				$val = 0;
			}
		}

		return false;
	}

	function gen_next_const(&$str_const,$univ_size) {
		return gen_next_func($str_const,$univ_size);
	}

	function relation_to_tuples($rel,$acc,&$arr) {
		if (!is_array($rel)) {
			if ($rel)
				$arr[] = $acc;
		}
		else {
			foreach ($rel as $key => $x) {
				$tmp = $acc;
				$tmp[] = $key;
				relation_to_tuples($x,$tmp,$arr);
			}
		}	
	}

	function function_to_tuples($fun,$acc,&$arr) {
		if (!is_array($fun)) {
			$acc[] = $fun;
			$arr[] = $acc;
		}
		else {
			foreach ($fun as $key => $x) {
				$tmp = $acc;
				$tmp[] = $key;
				function_to_tuples($x,$tmp,&$arr);
			}
		}
	}

	function setMappedElement(&$arr,$args,$pos,$val) {
		if ($pos == sizeof($args))
			$arr = $val;
		else
			setMappedElement($arr[$args[$pos]],$args,$pos+1,$val);
	}

?>
