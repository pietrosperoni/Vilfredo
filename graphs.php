<?php
function StudyQuestion($question)
{
	$sql = "SELECT roundid, phase FROM questions WHERE id = ".$question." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$generations=$row[0];
		$phasenow=$row[1];
	}

	$gen=1;
	$AllAuthors=array();
	$AuthorsArray=array();
	$AllParticipants=array();
	$ParticipantsSoFar=array();
	$AllVoters=array();
	do {
		$authors=AuthorsOfNewProposals($question,$gen);
		$voters=Endorsers($question,$gen);
		$participants=array_unique(array_merge($authors,$voters));
		$ParticipantsSoFar=array_unique(array_merge($ParticipantsSoFar,$participants));
		$AllAuthors= array_merge ( $AllAuthors,	$authors );
		$AllVoters= array_merge ( $AllVoters,	$voters );

		$NVoters=count($voters); #B
		$NAuthors=count($authors); #A
		$NParticipants=count($participants); #p
		$NParticipantsSoFar=count($ParticipantsSoFar);

		$NonParticipants=$NParticipantsSoFar-$NParticipants;

		$SizeIntersection=$NVoters+$NAuthors-$NParticipants; #	a+i+b=p;p-(a+b)=i;a=A-i;b=B-i;A-i+i+B-i=p;A+B-p=i;

		$AuthorsArray[]=$NAuthors;
		$VotersArray[]=$NVoters;
		$ParticipantsArray[]=$NParticipants;
		$ParticipantsSoFarArray[]=$NParticipantsSoFar;

		$IntersectionArray[]=$SizeIntersection;
		$OnlyAuthorsArray[]=$NAuthors-$SizeIntersection;
		$OnlyVotersArray[]=$NVoters-$SizeIntersection;
		$NonParticipantsArray[]=$NonParticipants;

		$gen++;
	} while ($gen < $generations);
	$AllAuthors=array_unique($AllAuthors);
	$AllVoters=array_unique($AllVoters);
	$AllParticipants=array_merge ( $AllAuthors,	$AllVoters );
	$AllParticipants=array_unique($AllParticipants);

# line graph
#	$graph="http://chart.apis.google.com/chart?cht=lc&chd=t:".implode(",",$IntersectionArray)."|".implode(",",$AuthorsArray)."|".implode(",",$VotersArray)."|".implode(",",$ParticipantsArray)."|".implode(",",$ParticipantsSoFarArray)."&chs=700x300&chds=0,".$NParticipantsSoFar."&chco=000000,FF0000,0000FF,FF00FF,00FF00&chdl=Authors ∩ Voters|Authors|Voters|Authors ∪ Voters|Total N. Participants";

#line graph

	$agreements=GetAgreements($question,$generations);
	$VisibleAgreements=PreviousAgreementsStillVisible($question,$generations);

	$graph="http://chart.apis.google.com/chart?cht=lxy&chd=t:"."1"."|".implode(",",$ParticipantsSoFarArray)."|"."1"."|".implode(",",$ParticipantsArray)."|"."1"."|".implode(",",$AuthorsArray)."|"."1"."|".implode(",",$VotersArray)."|"."1"."|".implode(",",$IntersectionArray)."&chs=692x433&chds=0,".($NParticipantsSoFar+0)."&chco=00FF00,FF00FF,FF0000,0000FF,222222&chdl=All Particip.|Auth. ∪ Vot.|Authors|Voters|Auth. ∩ Vot.&chm=o,00DD00,0,-1,5|o,DD00DD,1,-1,5|o,DD0000,2,-1,5|o,0000DD,3,-1,5|o,000000,4,-1,5";
	
	foreach($agreements as $a)
	{
		$b=$a-1;
		$graph.="|o,FF9900,3,".$b.",12";		
	}
	
	foreach($VisibleAgreements as $a)
	{
		$b=$a-1;
		$graph.="|o,FFFF00,3,".$b.",9";		
	}
	
	
	$graph.="&chxt=x,y,x,y&chxr=0,1,".($generations-1).",1|1,0,".$NParticipantsSoFar.",1&chxl=2:||Generations||3:|e|l|p|o|e|P||f|o||r|e|b|m|u|N&chtt=Participation+at+the+Question";


#	$graph="http://chart.apis.google.com/chart?cht=lxy&chd=t:"."1"."|".implode(",",$ParticipantsSoFarArray)."|"."1"."|".implode(",",$ParticipantsArray)."|"."1"."|".implode(",",$AuthorsArray)."|"."1"."|".implode(",",$VotersArray)."|"."1"."|".implode(",",$IntersectionArray)."&chs=692x433&chds=0,".($NParticipantsSoFar+0)."&chco=00FF00,FF00FF,FF0000,0000FF,222222&chdl=All Particip.|Auth. ∪ Vot.|Authors|Voters|Auth. ∩ Vot.&chm=o,00DD00,0,-1,5|o,DD00DD,1,-1,5|o,DD0000,2,-1,5|o,0000DD,3,-1,5|o,000000,4,-1,5&chxt=x,y,x,y&chxr=0,1,".($generations-1).",1|1,0,".$NParticipantsSoFar.",1&chxl=2:||Generations||3:|e|l|p|o|e|P||f|o||r|e|b|m|u|N&chtt=Participation+at+the+Question";




# Bar Graph with two colors voters and authors, side by side
#	$graph="http://chart.apis.google.com/chart?cht=bvg&chd=t:".implode(",",$AuthorsArray)."|".implode(",",$VotersArray)."&chs=700x300&chds=0,".$NParticipantsSoFar."&chco=FF0000,0000FF&chdl=Authors|Voters";

# Bar Graph, with only authors, intersection, only voters, and non participants. Precise but not pretty
#	$graph="http://chart.apis.google.com/chart?cht=bvs&chd=t:".implode(",",$OnlyAuthorsArray)."|".implode(",",$IntersectionArray)."|".implode(",",$OnlyVotersArray)."|".implode(",",$NonParticipantsArray)."&chs=700x300&chds=0,".$NParticipantsSoFar."&chco=FF0000,FF00FF,0000FF,00FF00&chdl=Authors|Authors that Voted|Voters|Non Participants";
	return $graph;
}
?>
