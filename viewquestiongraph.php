<?php
$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='';
include('header.php');
?>

<script type="text/javascript" src="js/vilfredo.php"></script>

<script type="text/javascript">
//Assumes id is passed in the URL
var recaptcha_public_key = '<?php echo $recaptcha_public_key;?>';
</script>
<?php

	$question = fetchValidQuestionFromQuery();
	$room = fetchValidRoomFromQuery();
	$generation = fetchValidIntValFromQueryWithKey(QUERY_KEY_GENERATION);
	
	
	// Return false if bad query parameters passed
	if ($question === false || $room === false)
	{
		header("Location: error_page.php");
		exit;
	}
		
	if (!HasQuestionAccess())
	{
		header("Location: viewquestions.php");
		exit;
	}
		
	$QuestionInfo = GetQuestion($question);
	
	if (!$QuestionInfo)
	{
		header("Location: error_page.php");
		exit;
	}
	
	$phase=$QuestionInfo['phase'];
	$ActualGeneration=$QuestionInfo['roundid'];
	
	if ($phase==0)
	{
		if($ActualGeneration==0)
		{
			header("Location: error_page.php");
			exit;			
		}
		if ($ActualGeneration <= $generation)
		{
			header("Location: error_page.php");
			exit;
		}		
	}
	else
	{
		if ($ActualGeneration < $generation)
		{
			header("Location: error_page.php");
			exit;
		}
	}


	$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$generation); 
	$ParetoFront=CalculateParetoFrontFromProposals($proposalsEndorsers);

	echo "<table width=\"1200\" cellpadding=\"0\" cellspacing=\"0\" border=1>";

	echo "<tr><td width=\"33%\">";
	InsertMapFromArray($question,$generation,$proposalsEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"NVotes","Flat");
	echo "</td><td width=\"34%\">";
	InsertMapFromArray($question,$generation,$proposalsEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"NVotes","Layers");
	echo "</td><td width=\"33%\">";
	InsertMapFromArray($question,$generation,$proposalsEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"NVotes","NVotes");		
	echo "</td></tr>";

	echo "<tr><td>";		
	InsertMapFromArray($question,$generation,$proposalsEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"Layers","Flat");
	echo "</td><td>";
	InsertMapFromArray($question,$generation,$proposalsEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"Layers","Layers");
	echo "</td><td>";
	InsertMapFromArray($question,$generation,$proposalsEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"Layers","NVotes");		
	echo "</td></tr>";

	$ParetoFrontEndorsers=	array_intersect_key($proposalsEndorsers, array_flip($ParetoFront));

	echo "<tr><td>";		
	InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"NVotes","Flat");
	echo "</td><td>";
	InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"NVotes","Layers");
	echo "</td><td>";
	InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"NVotes","NVotes");
	echo "</td></tr>";

	echo "<tr><td>";		
	InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"Layers","Flat");
	echo "</td><td>";
	InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"Layers","Layers");
	echo "</td><td>";
	InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"XS",0,true,"Layers","NVotes");
	echo "</td></tr>";

	echo "</table>";


if (!$userid)
{
	//set_log('Not logged in - storing request');
	SetRequest();
}

include('footer.php');

?>
