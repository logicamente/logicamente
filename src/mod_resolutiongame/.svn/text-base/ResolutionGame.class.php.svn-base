<?php

class ResolutionGame {
	
	// Auxiliary variable used in method autoSolveAux to ensure that once a derivation has been found, the search is stopped.
	private $derivationFound = false;
	// Auxiliary array used in method autoSolveAux to ensure that no eliminations will produce a clause already achieved.
	private $newClauseList = array();
	// Array that stores all clauses that can be used to derive bottom.
	private $clauseList = array();
	// Stores the clause list at the time the game was created.
	private $originalClauseList = array();
	// Stores the string corresponding to the automatic derivation.
	private $answer;
	
	/**
	 * Method used to add all clauses from a converted tree
	 *
	 * @param MegaConjunction $tree
	 */
	public function addConvertedTree($tree) {
		$clauses = $tree->children;
		// Loop used to look over the tree's elements
		for ($i = 0; $i < count($clauses); $i++) {
			// If the child isn'y a disjunction, the program creates a new clause
			if ($clauses[$i]->content->content != "|") {
				$newClauseRoot = new Node(new Connective("|", 2, 300));
				array_push($newClauseRoot->children, $clauses[$i]);
				// If the clause isn't a tautology (has no complementary literals inside it)
				if (count($this->findComplementaryLiterals($newClauseRoot, $newClauseRoot)) == 0) {
					$this->removeRepeatedElements($newClauseRoot->children);
					$this->addClause($newClauseRoot);
				}
			// If the child is a disnjunction
			} else {
				// If the clause isn't a tautology (has no complementary literals inside it)
				if (count($this->findComplementaryLiterals($clauses[$i], $clauses[$i])) == 0) {
					$this->removeRepeatedElements($clauses[$i]->children);
					$this->addClause($clauses[$i]);
				}
			}
		}
	}
	
	/**
	 * Auxiliary function to print a mega disjunction.
	 *
	 * @param Mega Disjunction $clause
	 * @return String corresponding to the Mega Disjunction passed as argument
	 */
	public function printMegaDisjunction($clause) {
		$result = "";
		if ($clause->children[0]->content instanceof Connective) {
			$result = "".$clause->children[0]->content->content.$clause->children[0]->children[0]->content->content;	
		} else {
			$result .= "".$clause->children[0]->content->content;
		}
		for ($i = 1; $i < count($clause->children); $i++) {
			if ($clause->children[$i]->content instanceof Connective) {
				$result .= " | ".$clause->children[$i]->content->content.$clause->children[$i]->children[0]->content->content;	
			} else {
				$result .= " | ".$clause->children[$i]->content->content;
			}
		}
		return "$result<br/>";
	}

	/**
	 * Method that finds the number of literals to be eliminated and evaluates it, printing
	 * an error message if there's no pair, executing an elimination, if there's only one pair
	 * or returning the array containing all pairs found.
	 *
	 * @param MegaDisjunction $firstClause
	 * @param MegaDisjunction $secondClause
	 * @return LiteralPairArray $literalPairArray If there's more than one pair, null otherwise.
	 */
	public function solve($firstClause, $secondClause) {
		$literalPairArray = $this->findComplementaryLiterals($firstClause, $secondClause);
		$nPairs = count($literalPairArray);
		// If there are no complementary literals
		if ($nPairs == 0) {
			return "No literals to be eliminated<br /><br />";
		} 
		// If there's only one pair of complementary literals
		elseif ($nPairs == 1) {
			$resp = array_search($firstClause, $this->clauseList)." - ".$this->printMegaDisjunction($firstClause);
			$resp .= array_search($secondClause, $this->clauseList)." - ".$this->printMegaDisjunction($secondClause);
			$newClause = $this->eliminateComplementaryLiterals($literalPairArray[0], $firstClause, $secondClause);
			// If after the elimination there are no more literals
			if (in_array($newClause, $this->newClauseList)) {
				return "You've already made this elimination<br /><br />";
			} elseif (count($newClause->children) == 0) {
				$resp .= "0<br />End of derivation";
				$this->derivationFound = true;
				return $resp;
			} else {
				$this->addClause($newClause);
				array_push($this->newClauseList, $newClause);
				$resp .= array_search($newClause, $this->clauseList)." - ".$this->printMegaDisjunction($newClause);
			}
			$resp .= "<br/>";
			return $resp;
		}
		// If there are more the one pair of complementary literals
		else {
			return $literalPairArray;
		}
	}
	
	/**
	 * Auxiliary method used to implement the automatic resolution.
	 *
	 * @param Mega Disjunction $secondClause 
	 * @param String $answer String modified by the algorithm to contain every step it has made to realize the derivation
	 * @return true if the
	 */
	public function autoSolveAux($secondClause) {
		// Look over the clauses in the clauseList
		for ($i = 0; $i < count($this->clauseList); $i++) {
			$firstClause = $this->clauseList[$i];
			// If firstClause is different from secondClause, look for complementary literals
			if ($firstClause != $secondClause) {
				$pair = $this->findComplementaryLiterals($firstClause, $secondClause);
				// If there's one pair of complementary literals
				if (count($pair) == 1) {
					// If there's only one literal in the firstClause and in the secondClause
					if ($this->checkEnd($firstClause, $secondClause)) {
						$this->answer .= array_search($firstClause, $this->clauseList)." - ".$this->printMegaDisjunction($firstClause);
						$this->answer .= array_search($secondClause, $this->clauseList)." - ".$this->printMegaDisjunction($secondClause);
						$this->answer .= "0<br />";
						$this->derivationFound = true;
						$this->originalClauseList = $this->clauseList;
						return;
					} else {
						// If no derivation was found
						if (!$this->derivationFound) {
							$newClause = $this->eliminateComplementaryLiterals($pair[0], $firstClause, $secondClause);
							// If the newClause isn't at the the newClauseList, remove the repeated elements and push it into the list
							if (!in_array($newClause, $this->newClauseList) && !in_array($newClause, $this->clauseList)) {
								$this->removeRepeatedElements($newClause->children);
								array_push($this->newClauseList, $newClause);
								$this->answer .= array_search($firstClause, $this->clauseList)." - ".$this->printMegaDisjunction($firstClause);
								$this->answer .= array_search($secondClause, $this->clauseList)." - ".$this->printMegaDisjunction($secondClause);
								$this->answer .= count($this->clauseList)." - ".$this->printMegaDisjunction($newClause)."<br/>";
								// If the newClause isn't null
								if ($newClause != null) {
									// If the newClause isn't at the clauseList, then push it into the list
									if (!in_array($newClause, $this->clauseList)) {		
										array_push($this->clauseList, $newClause);
									}
									$this->autoSolveAux($newClause);
								}
							// If the newClause is at the newClauseList, then it has already been obtained. Try again with another clause
							} else {
								continue;
							}
						}
					}
				// If there's more than one pair of complementary literals, try again with another clause
				} else {
					continue;
				}
			}
		}
		// If secondClause is in the newClauseList (this step prevents the program from removing a clause from the original clauseList)
		if (in_array($secondClause, $this->newClauseList)) {
			$x = 0;
			// If no derivation was found (this step prevents the program from removing the last 3 lines of the derivation)
			if (!$this->derivationFound) {
				// Remove the lines corresponding to the unsuccessful step in the string containing the deriavtion
				for ($i = strlen($this->answer)-1; $i >= 0; $i--) {
					if ($this->answer[$i] == ">") {
						$x++;
						if ($x == 5) {
							$this->answer = substr_replace($this->answer, "", $i+1, strlen($this->answer)-$i);
							break;
						}
					} elseif ($i == 0 && $x == 4) {
							$this->answer = substr_replace($this->answer, "", $i, strlen($this->answer)-$i);
							break;
					}
				}
			}
			// Remove the clause from the newClauseList and from the clauseList
			$i = array_search($secondClause, $this->newClauseList);
			$i = array_search($secondClause, $this->clauseList);
			unset($this->clauseList[$i]);
			$this->clauseList = array_merge($this->clauseList, array());
			unset($this->newClauseList[$i]);
			$this->newClauseList = array_merge($this->newClauseList, array());
		}
	}
	
	/**
	 * Method that, using a list of clauses, returns the derivation, if it's possible
	 * or returns "Impossible deriation" otherwise.
	 *
	 */
	public function autoSolve() {
		if (sizeof($this->newClauseList) == 0) {
			for ($i = 0; $i < count($this->clauseList); $i++) {
				$secondClause = $this->clauseList[$i];
				$bool = $this->autoSolveAux($secondClause, $this->answer);
				$this->newClauseList = array();
				if ($this->derivationFound) {
					echo $this->answer;
					echo "End of derivation";
					$this->clauseList = $this->originalClauseList;
					return;
				}
			}
			echo "Impossible derivation<br /><br />";
			$this->resetClauseList();
		} else {
			$size = sizeof($this->newClauseList);
			$secondClause = $this->newClauseList[$size-1];
			$bool = $this->autoSolveAux($secondClause, $this->answer);
			$this->newClauseList = array();
			if ($this->derivationFound) {
				echo $this->answer;
				echo "End of derivation";
				$this->clauseList = $this->originalClauseList;
				return;
			}
			echo "Impossible derivation<br /><br />";
			$this->resetClauseList();
		}
	}
	
	/**
	 * Method used to set the list of clauses that will be used on the game.
	 *
	 * @param Mega Disjunction array $newList Array containing the clauses to be used on the game
	 */
	public function setClauseList($newList) {
		$this->clauseList = $newList;
	}
	
	/**
	 * Add a clause to the list being used on the game
	 *
	 * @param Mega Disjunction $newClause Clause to be added
	 */
	public function addClause($newClause) {
		array_push($this->clauseList, $newClause);
		array_push($this->originalClauseList, $newClause);
	}
	
	/**
	 * Returns a clause stored in the list being used on the game
	 *
	 * @param int $i Index
	 * @return Mega Disjunction $newClause
	 */
	public function getClause($i) {
		if ($i < sizeof($this->clauseList)) {
			return $this->clauseList[$i];
		} else {
			return;
		}
	}
	
	/**
	 * Returns the clause list being used on the game
	 *
	 * @return array Clause List
	 */
	public function getClauseList() {
		return $this->clauseList;
	}
	
	/**
	 * Reset the list of clauses
	 *
	 */
	public function resetClauseList() {
		$this->clauseList = $this->originalClauseList;
	}
	
	/**
	 * Print the game's list of clauses
	 *
	 */
	public function printListOfClauses() {
		for ($i = 0; $i < count($this->clauseList); $i++) {
			echo "<label>Clause $i:</label><div id='clause$i' index='$i'>".$this->printMegaDisjunction($this->getClause($i))."</div>";
		}
	}
	
	/**
	 * Check if derivation has been finished
	 *
	 * @return True if the derivation is finished, false otherwise
	 */
	public function isDerivationFinished() {
		return $this->derivationFound;
	}
	
	/**
	 * Auxiliary method used to eliminate repeated elements from an array. Used when two clauses has one common literal.
	 *
	 * @param array $array
	 */
	private function removeRepeatedElements(&$array) {
		// Loops used to look over the array elements
		for ($i = 0; $i < count($array); $i++) {
			for ($j = $i+1; $j < count($array); $j++) {
				// If element at position i is equal to the one at position j, remove one of them
				if ($array[$i] == $array[$j]) {
					unset($array[$j]);
					$array = array_merge($array, array());
				}
			}
		}
	}
	
	/**
	 * Method used to find pairs of complementary literals in the two clauses.
	 *
	 * @param MegaDisjuntion $firstClause
	 * @param MegaDisjuntion $secondClause
	 * @return LiteralPairArray $literalPairArray Array that contains all pairs of complementary literals found.
	 */
	private function findComplementaryLiterals($firstClause, $secondClause) {
		$literalPairArray = array();
		// Loop used to search for a negation among the firstClause children
		foreach ($firstClause->children as $firstClauseLiteral) {
			// If a child is a connective, then it's a negation
			if ($firstClauseLiteral->content instanceof Connective) {
				$nodeToBeFound = $firstClauseLiteral->children[0];
				// Look over the secondClause searching for the complementary literal
				foreach ($secondClause->children as $secondClauseLiteral) {
					if ($secondClauseLiteral->content->content == $nodeToBeFound->content->content) {
						array_push($literalPairArray, array($firstClauseLiteral, $secondClauseLiteral));
					}
				}
			}
		}
		// If firstClause == secondClause there's no need for this next iteration.
		if ($firstClause != $secondClause) {
			// Loop used to search for a negation among the secondClause children
			foreach ($secondClause->children as $secondClauseLiteral) {
				// If a child is a connective, then it's a negation
				if ($secondClauseLiteral->content instanceof Connective) {
					$nodeToBeFound = $secondClauseLiteral->children[0];
					// Look over the secondClause searching for the complementary literal
					foreach ($firstClause->children as $firstClauseLiteral) {
						if ($firstClauseLiteral->content->content == $nodeToBeFound->content->content) {
							array_push($literalPairArray, array($secondClauseLiteral, $firstClauseLiteral));
						}
					}
				}
			}
		}
		return $literalPairArray;
	}
	
	/**
	 * Method used to eliminate a given pair of complementary literals in two clauses.
	 *
	 * @param LiteralPairArray $literalPair Array containing a pair of complementary literals.
	 * @param MegaDisjunction $firstClause 
	 * @param MegaDisjunction $secondClause
	 * @return MegaDisjunction $result Result of the elimination.
	 */
	private function eliminateComplementaryLiterals($literalPair, $firstClause, $secondClause) {
		// Look over the firstClause searching for an element of the literalPair
		for ($i = 0; $i < count($firstClause->children); $i++) {
			// If the child at position i is the first element of the literalPair
			if ($firstClause->children[$i] == $literalPair[0]) {
				// Look over the secondClause searching for the other element of the literalPair
				for ($j = 0; $j < count($secondClause->children); $j++) {
					// If the child at position j is the other element of the literalPair
					if ($secondClause->children[$j] == $literalPair[1]) {
						// Creating temporary clauses
						$tmpClause1 = new Node(null);
						$tmpClause1->content = $firstClause->content;
						$tmpClause1->children = $firstClause->children;
						$tmpClause2 = new Node(null);
						$tmpClause2->content = $secondClause->content;
						$tmpClause2->children = $secondClause->children;
						// Removing the complementary literals
						unset($tmpClause1->children[$i]);
						unset($tmpClause2->children[$j]);
						// Creating the new clausula containing the result of the elimination
						$result = new Node(new Connective("|", 2, 300));
						$result->children = array_merge($tmpClause1->children, $tmpClause2->children);
						$this->removeRepeatedElements($result->children);
						return $result;
					}
				}
			}
			// If the child at position i is the second element of the literalPair
			if ($firstClause->children[$i] == $literalPair[1]) {
				// Look over the secondClause searching for the other element of the literalPair
				for ($j = 0; $j < count($secondClause->children); $j++) {
					// If the child at position j is the other element of the literalPair
					if ($secondClause->children[$j] == $literalPair[0]) {
						// Creating temporary clauses
						$tmpClause1 = new Node(null);
						$tmpClause1->content = $firstClause->content;
						$tmpClause1->children = $firstClause->children;
						$tmpClause2 = new Node(null);
						$tmpClause2->content = $secondClause->content;
						$tmpClause2->children = $secondClause->children;
						// Removing the complementary literals
						unset($tmpClause1->children[$i]);
						unset($tmpClause2->children[$j]);
						// Creating the new clausula containing the result of the elimination
						$result = new Node(new Connective("|", 2, 300));
						$result->children = array_merge($tmpClause1->children, $tmpClause2->children);
						$this->removeRepeatedElements($result->children);
						return $result;
					}
				}
			}
		}
	}

	/**
	 * Auxiliary method used to check if method autoSolveAux reached the end of a derivation 
	 *
	 * @param Mega Disjunction $clause1
	 * @param Mega Disjunction $clause2
	 * @return boolean true if both clauses contains, each one, only a member of a pair of complementary literals; false otherwise.
	 */
	private function checkEnd($clause1, $clause2) {
		if (count($clause1->children) == 1 && count($clause2->children) == 1 && $clause1->children[0]->content instanceof Connective && $clause1->children[0]->children[0]->content->content == $clause2->children[0]->content->content) {
			return true;
		} elseif (count($clause2->children) == 1 && count($clause1->children) == 1 && $clause2->children[0]->content instanceof Connective && $clause2->children[0]->children[0]->content->content == $clause1->children[0]->content->content) {
			return true;
		} else {
			return false;
		}
	}
	
}
?>
