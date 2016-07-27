<?php

	function getRanLv($cvset, $userID){
		//Include stats.php for getSkill(), getDeckInfo()
		include_once('stats.php');
		
		/********************************************************************
		************* Get and Set Parameters for Algorithm ******************
		*********************************************************************/
		
		//Get users vocabulary style.
		//-- Explanation: Because this is Japanese, and Japanese has 3 alphabets, a single flashcard also has multiple ways of testing its single word, these are called 'vocabulary styles'
		//---->The deck a user is currently practicing is stored in the db. Delving further, that deck likely has 3 vocabulary styles for its set of vocabulary. Kanji+Kana->English; Kanji->English; Kanji->Kana
		$vStyle = getSkill('vStyle',$userID);
		
		//Get the maximum deck level for this current deck
		//-- Explanation: Each Vocabulary set (deck) has a maximum set of levels. The purpose of levels is because the current flash-card algorithm has a set of words introduced and attached to each level. When a user answers flashcards correctly they gain "xp" and reach new levels. A new level introduces new vocabulary. This proceeds until the max level is reached.
		$cvsetMxLv = getDeckInfo($cvset,'levels');
		
		//Get the user's current level for their progress on this vocabulary style for this deck. 
		$mxLv = getSkill($cvset . "_$vStyle"."_lv", $userID); //"max level" because words past this level cannot be selected
		
		//Set probability that a word will from this level will be selected.
		$prob = .5;
		
		//If user has reached the max level, grab words that are from any level
		if($mxLv >= $cvsetMxLv){
			$mxLv = $cvsetMxLv;
			$prob = 1 / $mxLv;
		}
	
	
		$tmin = 0;
		$tmax = $prob*100;
		$rn = mt_rand(0,25);
		//Get a really random number
		for($n=0; $n <3; $n++){
			$rn += mt_rand(0,25);
		}
		
		$returned = FALSE;
		$j=0;
		$i = $mxLv;
		
		//"Algorithm"
		//Idea: Generate a range: tmin - tmax; 
		//Generate a random number. 
		//--If random number falls between the range, stop & return current counter.
		//--If random number DOES NOT fall between the range, adjust range, decrease counter.
		//Returned counter is the level. 
		//--Level dictates the set of flashcards to randomly grab from. Current level is new, old levels are review.
		do{ 
			if($rn > $tmin && $rn<=$tmax){
				if($i<=0){return 1;
				}else{ return $i;}
			}else{
				$tmin = $tmax;
				$t = (100-$tmax)*($prob);
				$tmax += round($t);
			}
			
			$j++;
			$i--;
		}while($i > 1);
		return 1;//failsafe
	}
?>
