<?php


#ReturnProposalsEndorsersTimelessArrayOriginalOnlyParetoFront
function ShowCommunityMap($question,$generation,$phase)
{
	
	#$proposalsEndorsers1=ReturnProposalsEndorsersTimelessArrayOriginal($question,$generation);
	#$proposalsEndorsers2=ReturnProposalsEndorsersTimelessArrayOriginalOnlyParetoFront($question,$generation); 

	list($ProposalsEndorserTimeless,$endorserProposalsTimeless)=ReturnProposalsEndorsersTimelessArrayOriginalOnlyParetoFront($question,$generation,$phase);

	#list($ProposalsEndorserTimeless,$endorserProposalsTimeless)=ReturnProposalsEndorsersTimelessArrayOriginal($question,$generation,$phase);
	#list($ProposalsEndorserTimeless,$endorserProposalsTimeless)=ReturnProposalsEndorsersTimelessArray($question,$generation,$phase);

	$ParetoFrontTimeless=CalculateParetoFrontFromProposals($ProposalsEndorserTimeless);
	$ParetoFrontEndorsersTimeless=	array_intersect_key($ProposalsEndorserTimeless, array_flip($ParetoFrontTimeless));

	#list($ProposalsEndorserTimeless2,$endorserProposalsTimeless2)=ReturnProposalsEndorsersTimelessArrayOriginal($question,$generation,$phase);
	#$ParetoFrontEndorsersTimeless=	array_intersect_key($ProposalsEndorserTimeless2, array_flip($ParetoFrontTimeless));

	#InsertMapFromArray($question,$generation,$ParetoFrontEndorsersTimeless,$ParetoFrontTimeless,$room,$userid,"M",0,$question_url,"NVotes","Layers");

	#echo "<table cellpadding=\"0\" cellspacing=\"0\" border=0>";
	#echo "<tr><td width=\"70%\">";
	#InsertMapFromArray($question,$generation,$ProposalsEndorserTimeless,$ParetoFrontTimeless,$room,$userid,"M",0,$question_url,"NVotes","Layers");
	#InsertMapFromArray($question,$generation,$ProposalsEndorserTimeless,$ParetoFrontTimeless,$room,$userid,"M",0,$question_url,"Layers","Layers");
	#echo "</td><td>";
	#InsertMapFromArray($question,$generation,$ParetoFrontEndorsersTimeless,$ParetoFrontTimeless,$room,$userid,"M",0,$question_url,"NVotes","Layers");

	InsertMapFromArray($question,$generation,$ParetoFrontEndorsersTimeless,$ParetoFrontTimeless,$room,$userid,"M",0,$question_url,"Layers","Layers");

	#echo "</td></tr>";
	#echo "</table>";

	echo "<br>";
	echo "<br>";
	
	return;
	
	
	/////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////
	
	$PFE=CalculateFullParetoFrontExcludingFromArray($ProposalsEndorserTimeless,$userid);

	$ParetoFrontPlus=array_diff($PFE,$ParetoFrontTimeless);
	$ParetoFrontMinus=array_diff($ParetoFrontTimeless,$PFE);

	if (FALSE) #We take this off for now
	#if (sizeof($ParetoFrontPlus) OR sizeof($ParetoFrontMinus))
	{
		echo "<div class=\"feedback\">By voting You have changed the results.<br>Without you ";
		if (sizeof($ParetoFrontPlus))
		{
			foreach ($ParetoFrontPlus as $p)	
				#{echo WriteProposalNumber($p,$room);}						
				{echo WriteProposalNumberInternalLink($p,$room);}						
			echo "would have been in the Pareto Front.";						
		}
		if (sizeof($ParetoFrontPlus) AND sizeof($ParetoFrontMinus))
		{
			echo "<br>While without you ";						
		}					
		if (sizeof($ParetoFrontMinus))
		{
			foreach ($ParetoFrontMinus as $p)	
				{echo WriteProposalNumberInternalLink($p,$room);}						
				#{echo WriteProposalNumber($p,$room);}						
			echo "would NOT have been in the Pareto Front.";
		}
		echo "</div>";						
	}

	if (sizeof($ParetoFrontMinus))
	{
		$HomeWork=CalculateKeyPlayersKnowingPFfromArrayInteractiveExcludingKnowingDiff($ProposalsEndorserTimeless,$ParetoFrontTimeless,$userid,$ParetoFrontMinus);
		#$HomeWork=CalculateKeyPlayersKnowingPFfromArrayInteractiveExcluding($proposalsEndorsers,$ParetoFront,$userid);
		if (count($HomeWork) > 0)
		{
			echo "<div class=\"feedback\">You are a Key Player. This means that with your vote you could simplify the Pareto Front. Please look at proposal(s) ";						
			foreach ($HomeWork as $PCD)
			{
				$proposalNumber = WriteProposalNumberInternalLink($PCD,$room);
				echo " ".$proposalNumber.", ";
			}
			echo "and consider if you could vote it.</div>";					
		}	
		else
		{
			echo "ATTENTION PARETO FRONT MINUS WITHOUT BEING A KEY PLAYER???";						
		}				
	}
	#$ParetoFront=CalculateParetoFront($question,$generation); #$ParetoFront=CalculateFullParetoFrontExcluding($proposals,0);
	#				$proposals=GetProposalsInGeneration($question,$generation);				
	#				$PFE=CalculateFullParetoFrontExcluding($proposals,$userid);


	$CouldDominate=CalculateKeyPlayersKnowingPFfromArrayInteractive($ProposalsEndorserTimeless,$ParetoFrontTimeless);
	$users=extractEndorsers($ProposalsEndorserTimeless);
	echo "<div class=\"feedback\">KEY PLAYERS: </br></br>";
	foreach ($users as $u)
	{
		if($u==$userid)
			{continue;}
		$HomeWork=$CouldDominate[$u];
		if (count($HomeWork) > 0)
		{
			$uString=WriteUserVsReader($u,$userid);
			echo "The result would be simpler if ".$uString." were to vote for ";					
			$PCD=$HomeWork[0];
			$proposalNumber = WriteProposalNumberInternalLink($PCD,$room);
			echo " ".$proposalNumber;
			foreach ($HomeWork as $PCD)
			{
				if ($PCD==$HomeWork[0]) continue;
				$proposalNumber = WriteProposalNumberInternalLink($PCD,$room);
				echo ", ".$proposalNumber;
			}
			echo ".</br>";
	#						echo "<u>Convince Them!</u>";
			echo "Convince Them!";
			echo "</br>";
			echo "</br>";
		}	
	}
	echo "</div>";					

	
	
	
}


function MergeProposalsEndorsersTimelessGeneration($endorsersProposals,$endorsersProposals2, $proposalsEndorsers2)
{
	
#	echo "Merging";
#	echo "<br>";
#	print_r($endorsersProposals);
#	echo "<br>";
#	echo "with";
#	echo "<br>";
#	print_r($endorsersProposals2);
#	echo "<br>";
#	echo "considering the proposals";
#	echo "<br>";
#	print_r($proposalsEndorsers2);
#	echo "<br>";
#	echo "<br>";
	
	$endorsers1=array_keys($endorsersProposals);  #All the people we know off
	
#	echo "<br>";
#	echo "Endorsers";
#	echo "<br>";
#	print_r($endorsers1);
#	echo "<br>";
#	echo "<br>";
	
			
	$endorsers2=array_keys($endorsersProposals2); #all the people active in this generation

#	echo "<br>";
#	echo "Endorsers NOW";
#	echo "<br>";
#	print_r($endorsers2);
#	echo "<br>";
#	echo "<br>";
#	
	
	$proposals2=array_keys($proposalsEndorsers2); #all the proposals active in this generation
	
#	echo "<br>";
#	echo "Proposals NOW";
#	echo "<br>";
#	print_r($proposals2);
#	echo "<br>";
#	echo "<br>";
	
	
	
	
	foreach($endorsers2 as $e)
	{
		if (!in_array ( $e , $endorsers1))
		{
#			echo "<br>";
#			echo "Endorser $s new";
#			echo "<br>";			
			
			$endorsersProposals[$e]=array(); #anybody new starts from zero
			
#			echo "New list:<br>";
#			print_r($endorsersProposals);
#			echo "<br>";
			
		}
		
			
#		echo "<br>";
#		echo "we now make the union between<br>";
#		print_r($endorsersProposals[$e]);
#		echo "<br>";
#		echo "<br>";
#		echo "and<br>";
#		print_r($endorsersProposals2[$e]);
#		echo "<br>";
#		echo "<br>";

		$endorsersProposals[$e] = array_unique(array_merge($endorsersProposals[$e], $endorsersProposals2[$e]));
		#$endorsersProposals[$e] = $endorsersProposals[$e] + $endorsersProposals2[$e]; #we make the union of what a person liked before and what he liked now
#		echo "<br>";
#		echo "and the result is:<br>";
#		print_r($endorsersProposals[$e]);
#		echo "<br>";
#		echo "<br>";

			
		$proposalsNOTliked      =  array_diff($proposals2, 	$endorsersProposals2[$e]); #but what is that he did NOT like, even though he could have?
		
#		echo "Those are the proposals that $e did NOT like on this generation :<br>";
#		print_r($proposalsNOTliked);
#		echo "<br>";
		
				
		
		$endorsersProposals[$e] =  array_diff($endorsersProposals[$e], 	$proposalsNOTliked);	#those things we exclude
		
#		echo "And once we take them away the result is:<br>";
#		print_r($endorsersProposals[$e]);
#		echo "<br>";
		
		
	}
	return $endorsersProposals;
}



function MergeProposalsEndorsersTimelessGenerationKeepLast($endorsersProposals,$endorsersProposals2)
{
	$endorsers2=array_keys($endorsersProposals2); #all the people active in this generation
	foreach($endorsers2 as $e)
	{
		$endorsersProposals[$e] = $endorsersProposals2[$e];
	}
	return $endorsersProposals;
}


function ReturnProposalsEndorsersTimelessArray($question,$generation,$phase)
{
	
#	$proposalsEndorsers=ReturnProposalsEndorsersArray($question,1); 
	$proposalsEndorsers=ReturnProposalsEndorsersArray($question,1); 
	
	

#	echo "generation=1 proposalsEndorsers ";
#	echo "<br>";
#	print_r($proposalsEndorsers);
#	echo "<br>";

	$endorsersProposalsTimeless=EndorsersProposalsFromProposalsEndorsers($proposalsEndorsers);
	
#	echo "generation=1 twisted and stored";
#	echo "<br>";
#	print_r($endorsersProposalsTimeless);
#	echo "<br>";
	
#	echo "pippo";
	for ($g = 2; $g < $generation; $g++) 
	{
#		$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$g); 
		$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$g); 
		
#		echo "generation=$g new proposalsEndorsers";
#		echo "<br>";
#		print_r($proposalsEndorsers);
#		echo "<br>";
#		echo "<br>";
		
		$endorsersProposals=EndorsersProposalsFromProposalsEndorsers($proposalsEndorsers);	
		
#		echo "twist now endorserProposals";
#		echo "<br>";		
#		print_r($endorsersProposals);
#		echo "<br>";
#		echo "<br>";
		
		
		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGeneration($endorsersProposalsTimeless,$endorsersProposals, $proposalsEndorsers);

#		echo "merging endorserProposals with endorserProposalsTimeless";
#		echo "<br>";
#		print_r($endorsersProposalsTimeless);
#		echo "<br>";
#		echo "<br>";

		
#		echo "generation=$g result endorserProposalsTimeless";
#		echo "<br>";
#		print_r($endorsersProposalsTimeless);
#		echo "<br>";
#		echo "<br>";
		
	}
	if ($phase==1) 
	{
		
		#$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$generation); 
		$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$generation); 
		$endorsersProposals=EndorsersProposalsFromProposalsEndorsers($proposalsEndorsers);	
		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGeneration($endorsersProposalsTimeless,$endorsersProposals, $proposalsEndorsers);
		
#		echo "last generation=$generation";
#		echo $generation;
#		print_r($endorsersProposalsTimeless);
#		echo "<br>";
		
		
	}

#	echo "final endorserProposalsTimeless";
#	echo $generation;
#	print_r($endorsersProposalsTimeless);
#	echo "<br>";
	
	$ProposalsEndorserTimeless=ProposalsEndorsersFromEndorsersProposals($endorsersProposalsTimeless) ;
	
#	echo "final ProposalsEndorserTimeless";
#	echo $generation;
#	print_r($ProposalsEndorserTimeless);
#	echo "<br>";
	
	
	return array($ProposalsEndorserTimeless,$endorsersProposalsTimeless); #catch it with list($ProposalsEndorserTimeless,$endorserProposalsTimeless)=ReturnProposalsEndorsersTimelessArray($question,$generation,$phase);
}



function ReturnProposalsEndorsersTimelessArrayOriginal($question,$generation,$phase)
{
	
#	$proposalsEndorsers=ReturnProposalsEndorsersArray($question,1); 
	$proposalsEndorsers=ReturnOriginalProposalsEndorsersArray($question,1); 
	
	
	
	

#	echo "generation=1 proposalsEndorsers ";
#	echo "<br>";
#	print_r($proposalsEndorsers);
#	echo "<br>";

	$endorsersProposalsTimeless=EndorsersProposalsFromProposalsEndorsers($proposalsEndorsers);
	
#	echo "generation=1 twisted and stored";
#	echo "<br>";
#	print_r($endorsersProposalsTimeless);
#	echo "<br>";
	
#	echo "pippo";
	for ($g = 2; $g < $generation; $g++) 
	{
#		$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$g); 
		$proposalsEndorsers=ReturnOriginalProposalsEndorsersArray($question,$g); 
		
#		echo "generation=$g new proposalsEndorsers";
#		echo "<br>";
#		print_r($proposalsEndorsers);
#		echo "<br>";
#		echo "<br>";
		
		$endorsersProposals=EndorsersProposalsFromProposalsEndorsers($proposalsEndorsers);	
		
#		echo "twist now endorserProposals";
#		echo "<br>";		
#		print_r($endorsersProposals);
#		echo "<br>";
#		echo "<br>";
		
		
		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGeneration($endorsersProposalsTimeless,$endorsersProposals, $proposalsEndorsers);

#		echo "merging endorserProposals with endorserProposalsTimeless";
#		echo "<br>";
#		print_r($endorsersProposalsTimeless);
#		echo "<br>";
#		echo "<br>";

		
#		echo "generation=$g result endorserProposalsTimeless";
#		echo "<br>";
#		print_r($endorsersProposalsTimeless);
#		echo "<br>";
#		echo "<br>";
		
	}
	if ($phase==1) 
	{
		
		#$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$generation); 
		$proposalsEndorsers=ReturnOriginalProposalsEndorsersArray($question,$generation); 
		$endorsersProposals=EndorsersProposalsFromProposalsEndorsers($proposalsEndorsers);	
		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGeneration($endorsersProposalsTimeless,$endorsersProposals, $proposalsEndorsers);
		
#		echo "last generation=$generation";
#		echo $generation;
#		print_r($endorsersProposalsTimeless);
#		echo "<br>";
		
		
	}

#	echo "final endorserProposalsTimeless";
#	echo $generation;
#	print_r($endorsersProposalsTimeless);
#	echo "<br>";
	
	$ProposalsEndorserTimeless=ProposalsEndorsersFromEndorsersProposals($endorsersProposalsTimeless) ;
	
#	echo "final ProposalsEndorserTimeless";
#	echo $generation;
#	print_r($ProposalsEndorserTimeless);
#	echo "<br>";
	
	
	return array($ProposalsEndorserTimeless,$endorsersProposalsTimeless); #catch it with list($ProposalsEndorserTimeless,$endorserProposalsTimeless)=ReturnProposalsEndorsersTimelessArray($question,$generation,$phase);
}

function ReturnProposalsEndorsersTimelessArrayOriginalOnlyParetoFront($question,$generation,$phase)
{
	
	$proposalsEndorsers=ReturnOriginalProposalsEndorsersArray($question,1); 
	
	$ParetoFront=CalculateParetoFrontFromProposals($proposalsEndorsers);
	$ParetoFrontEndorsers=	array_intersect_key($proposalsEndorsers, array_flip($ParetoFront));
	
	$endorsersProposalsTimeless=EndorsersProposalsFromProposalsEndorsers($ParetoFrontEndorsers);

	for ($g = 2; $g < $generation; $g++) 
	{
		$proposalsEndorsers=ReturnOriginalProposalsEndorsersArray($question,$g); 
		$ParetoFront=CalculateParetoFrontFromProposals($proposalsEndorsers);
		$ParetoFrontEndorsers=	array_intersect_key($proposalsEndorsers, array_flip($ParetoFront));
		$endorsersParetoFront=EndorsersProposalsFromProposalsEndorsers($ParetoFrontEndorsers);	
#		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGeneration($endorsersProposalsTimeless,$endorsersParetoFront, $ParetoFrontEndorsers);
		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGenerationKeepLast($endorsersProposalsTimeless,$endorsersParetoFront);
		
	}
	if ($phase==1) 
	{
		$proposalsEndorsers=ReturnOriginalProposalsEndorsersArray($question,$g); 
		$ParetoFront=CalculateParetoFrontFromProposals($proposalsEndorsers);
		$ParetoFrontEndorsers=	array_intersect_key($proposalsEndorsers, array_flip($ParetoFront));
		$endorsersParetoFront=EndorsersProposalsFromProposalsEndorsers($ParetoFrontEndorsers);	
#		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGeneration($endorsersProposalsTimeless,$endorsersParetoFront, $ParetoFrontEndorsers);		
		$endorsersProposalsTimeless=MergeProposalsEndorsersTimelessGenerationKeepLast($endorsersProposalsTimeless,$endorsersParetoFront);		
	}

	$ProposalsEndorserTimeless=ProposalsEndorsersFromEndorsersProposals($endorsersProposalsTimeless) ;	
	
	return array($ProposalsEndorserTimeless,$endorsersProposalsTimeless); #catch it with list($ProposalsEndorserTimeless,$endorserProposalsTimeless)=ReturnProposalsEndorsersTimelessArray($question,$generation,$phase);
}




function writeArrayOfArrays($AoA)
{
	$keys=array_keys($AoA); 	
	
	foreach($keys as $k)
	{
		echo "$k=[";
		$Pointed=array_keys($AoA[$k]); 	
		foreach($AoA[$k] as $v)
		{
			echo "$v, ";
		}
		echo "]<br>";		
	}
}
































?>