<?php
/**
*  Software License Agreement (Affero GPL)
*  
*  Vilfredo
*  You can use this system to make decisions amongst groups of people.
*  Vilfredo is used to really explore the possible alternative answers to an open question, 
*  and find an answer endorsed by as many people as possible (theorethically everybody). 
*  As such the question should be an open question. 
*  The Vilfredo project is at http://www.vilfredo.org
*  Copyright (C) 2009  Vilfredo.org
*  
*  This program is free software: you can redistribute it and/or modify
*  it under the terms of the GNU Affero General Public License as
*  published by the Free Software Foundation, either version 3 of the
*  License, or (at your option) any later version.
*  
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU Affero General Public License for more details.
*  
*  You should have received a copy of the GNU Affero General Public License
*  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*  
*/
ob_start();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<link rel="stylesheet" type="text/css" href="style.css" media="screen, print" />
		<?php 	echo $headcommands; ?>
		<title>Vilfredo goes to Athens</title>


	</head>
	<?php
#	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">


//******************************************
// TEMP WIN/PHP FIX
// Use a dummy function to return true if no checkdnsrr()
// --  This function not available on Windows platforms
//      before PHP version 5.3.0. For live windows platforms without
//	checkdnsrr() another function could be substituted.
//
//	Eg. From PHP Manual:  http://php.net/manual/en/function.checkdnsrr.php
//	For compatibility with Windows before this was implemented, 
//	then try the » PEAR class » Net_DNS. 
//	
//******************************************
function check_dnsrr($host, $type)
{
	if (function_exists('checkdnsrr'))
		return checkdnsrr($host, $type);
	else
		return true;
}
//******************************************
// ADMIN
//	Users with id listed in admin table can delete 
//	questions and proposals.
//******************************************
function isAdmin($userid)
{
	$admin = false;
	$sql = "SELECT userid FROM admin WHERE userid = '$userid'";
	$response = mysql_query($sql);
	if (mysql_num_rows($response) > 0) {
		$admin = true;
	}
	
	return $admin;
}
//******************************************
// ERRORS
//
//******************************************
// define("VILFREDO_ERROR", E_USER_NOTICE);
define("VILFREDO_ERROR", E_USER_ERROR);

function error($message, $level=VILFREDO_ERROR) 
{
	$caller = next(debug_backtrace());
	trigger_error($message.' in <strong>'.$caller['function'].'</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."\n<br />error handler", $level);
}
//******************************************
// VILFREDO ROOMS
//
//******************************************
define("QUERY_KEY_USER", "u");
define("QUERY_KEY_QUESTION", "q");
define("QUERY_KEY_ROOM", "room");
define("RANDOM_ROOM_CODE_LENGTH", 8);
define("USE_PRIVACY_FILTER", TRUE);

function GetParamFromQuery($key)
{
	$param = isset($_GET[$key]) ? $_GET[$key] : "";
	return $param;
}

function CheckQuery($key)
{
	$isSet = (isset($_GET[$key]) && !empty($_GET[$key])) ? true : false;
	return $isSet;
}

function CreateNewQuestionURL()
{
	$question_url = '';
	$room = GetParamFromQuery(QUERY_KEY_ROOM);
	// Add room id if not empty
	if (!empty($room)) {
        	$question_url .= "?" . QUERY_KEY_ROOM . "=".$room;
        }
        
        return  $question_url;
}
function CreateNewRoomURL()
{
	$question_url = '';
	$room = GetParamFromQuery(QUERY_KEY_ROOM);
	// Add room id if not empty
	if (!empty($room)) 
		{
        	$question_url .= "?" . QUERY_KEY_ROOM . "=".$room;
        }        
    return  $question_url;
}

function CreateQuestionURL($question, $room="")
{
	if (!isset($question) or empty($question))
             error("Question parameter not set!!!");

	$question_url = "?" . QUERY_KEY_QUESTION . "=".$question;

	// Add room id if not empty
	if (!empty($room))
            $question_url .= "&" . QUERY_KEY_ROOM . "=".$room;

	return $question_url;
}

function GetViewAllRoomAccessFilter($userid)
{	
	// Get room if set
	$room = GetParamFromQuery(QUERY_KEY_ROOM);
	// Get user if set
	$uid = GetParamFromQuery(QUERY_KEY_USER);
	// Check if user IDs match
	$sameuser = ($uid == $userid);
	
	if (USE_PRIVACY_FILTER === false) return '';


	// View All Questions
	//
	// USER
	//
	// A different user
	if (CheckQuery(QUERY_KEY_USER) && !$sameuser && !CheckQuery(QUERY_KEY_ROOM)) 
	{
		$filter=" AND (questions.usercreatorid = '$uid' AND questions.room = '') ";
	}
	//
	// Current user - 'View My Questions' Menu Option
	elseif (CheckQuery(QUERY_KEY_USER) && $sameuser && !CheckQuery(QUERY_KEY_ROOM)) 
	{
		$filter=" AND (questions.usercreatorid = '$userid') ";
	}
	//
	//USER & ROOM
	//
	// Makes no difference whether same or different user. Room is the key.
	elseif (CheckQuery(QUERY_KEY_USER) && CheckQuery(QUERY_KEY_ROOM)) 
	{
		$filter=" AND (questions.usercreatorid = '$uid' AND questions.room = '$room') ";
	}
	//
	// ROOM
	// 
	// Room is the key.
	elseif  (!CheckQuery(QUERY_KEY_USER) && CheckQuery(QUERY_KEY_ROOM)) 
	{
		$filter=" AND (questions.room = '$room') ";
	}
	// 
	// View COMMON ROOM
	// 
	else
	{
		$filter=" AND questions.room = '' ";
	}
	
	return $filter;
}

function GetUserAccessFilter($uid)
{	
	// Get logged in ID
	$userid = isloggedin();
	// Check if user IDs match
	$is_current_user = ($userid == $uid);
	
	$filter = "";
	
	if (USE_PRIVACY_FILTER)
	{	
		if (!$is_current_user)
		{
			$filter=" AND (questions.usercreatorid = '$uid' AND questions.room = '') ";
		}
	}
	
	return $filter;
}

function GetRoomAccessFilter($userid, $room='')
{	
	// Get logged in ID
	$current_user = isloggedin();
	// Check if user IDs match
	$is_current_user = ($current_user == $userid);
	
	if (USE_PRIVACY_FILTER)
	{	
		if ((!$is_current_user) && empty($room))
		{
			$filter=" AND (questions.usercreatorid = '$userid' AND questions.room = '') ";
		}
		if (empty($room)) 
		{
			$filter=" AND (questions.usercreatorid = '$userid' OR questions.room = '') ";
		}
		else 
		{
			$filter=" AND (questions.room = '$room') ";
		}
	}
	else
	{
		$filter = "";
	}
	
	return $filter;
}

function FormatRoomId($room)
{
	// Alpha-numeric characters and underscores only.
	if (!empty($room))
	{
		$room = trim($room);
		$room = ereg_replace("[^A-Za-z0-9_[:space:]]", "", $room );
		$room = str_replace(" ", "_", $room);
	}
	return $room;
}

function getUniqueRoomCode()
{
	$code = md5(uniqid(rand(), true));
	return substr($code, 0, RANDOM_ROOM_CODE_LENGTH);
}

function CreateVFURL($url, $question="", $room="")
{
	if (!isset($url) or empty($url))
             error("URL parameter not set!!!");

	 $vf_url = $url;
	
	if (!empty($question))
		$vf_url .= "?" . QUERY_KEY_QUESTION . "=" . $question;

	// Add room id if not empty
	if (!empty($room))
            $vf_url .= "&" . QUERY_KEY_ROOM . "=". $room;

	return $vf_url;
}

function HasRoomAccess($room="")
{
	if (empty($room))
		return true;

	$sql = "SELECT * FROM questions WHERE room = '$room'";
	$response = mysql_query($sql);
	        
        if (mysql_num_rows($response) > 0)
	
	if (isset($_GET[QUERY_KEY_QUESTION]))
             $question = $_GET[QUERY_KEY_QUESTION];
	else
            return false;

	$roomdetails = GetRoomDetails($question);

	if ($userid ==  $roomdetails['creator']) return true;

	if (isset($_GET[QUERY_KEY_ROOM]))
		$room = $_GET[QUERY_KEY_ROOM];
	else
		$room = "";

	if (empty($roomdetails['room']) or ($room == $roomdetails['room']))
            return true;
	else
            return false;
}

function HasQuestionAccess()
{
	$question = "";
	$room = "";

	if (isset($_GET[QUERY_KEY_QUESTION]))
             $question = $_GET[QUERY_KEY_QUESTION];
	else
            return false;

	$roomdetails = GetRoomDetails($question);

	#// Return true if owner
	#if ($userid ==  $roomdetails['creator']) return true;

	if (isset($_GET[QUERY_KEY_ROOM]))
		$room = $_GET[QUERY_KEY_ROOM];
	else
		$room = "";

	if (empty($roomdetails['room']) or ($room == $roomdetails['room']))
            return true;
	else
            return false;
}

function GetRoomDetails($question)
{
	 $roomdetails = array();

	 $sql="SELECT usercreatorid, room
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_row($result);

	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);

	$roomdetails['creator'] = $row[0];
	$roomdetails['room'] = $row[1];

	return $roomdetails;
}

// Returns empty string if no room set,
// or room id as a string
function GetRoom($question)
{
	 $sql="SELECT room
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_row($result);

	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	$room=$row[0];

	return $room;
}

// Returns empty string if no room set,
// or room id as a string
function GetQuestionCreator($question)
{
	 $sql="SELECT usercreatorid
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_row($result);

	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	$creator=$row[0];

	return $creator;
}
//******************************************
//******************************************
###############################################################
// Connects to the Database
include('priv/dbdata.php');
mysql_connect($dbaddress, $dbusername, $dbpassword) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());


// returns true if the user is logged in
function isloggedin()
{

	if(isset($_COOKIE['ID_my_site']))
	{
		$username = $_COOKIE['ID_my_site'];
		$pass = $_COOKIE['Key_my_site'];
		$check = mysql_query("SELECT * FROM users WHERE username = '$username'")or die(mysql_error());
		while($info = mysql_fetch_array( $check ))
		{

		//if the cookie has the wrong password, they are taken to the login page
			if ($pass != $info['password'])
			{
				return 0;
			}

			//otherwise they are shown the admin area
			else
			{
				return $info['id'];
			}
		}
	}
	else	//if the cookie does not exist, they are taken to the login screen
	{
		return false;
	}
}


function CountProposals($question,$generation)
{
	$sql = "SELECT * FROM proposals WHERE experimentid = ".$question." and roundid = ".$generation."";
	$n=0;
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$n+=1;
	}
	return $n;
}




function CountEndorsersToAProposal($proposal)
{
	$sql = "SELECT DISTINCT userid FROM endorse WHERE proposalid = ".$proposal." ";
	return mysql_num_rows(mysql_query($sql));
}

function EndorsersToAProposal($proposal)
{
	$endorsers=array();
	$sql = "SELECT DISTINCT userid FROM endorse WHERE proposalid = ".$proposal." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		array_push($endorsers,$row[0]);
	}
	return array_unique($endorsers);

}



function HasThisUserEndorsedSomething($question,$generation,$user)
{
	$proposals=ProposalsInGeneration($question,$generation);
	foreach ($proposals as $proposal)
	{
		if(in_array($user,EndorsersToAProposal($proposal)))
		{
			return $proposal;
		}
	}
	return 0;
}
function HasThisUserProposedSomething($question,$generation,$user)
{
	if(in_array($user,AuthorsOfNewProposals($question,$generation)))
	{
		return 1;
	}
	return 0;
}



function CountEndorsers($question,$generation)
{
	$sql = "SELECT DISTINCT endorse.userid FROM proposals,endorse WHERE proposals.experimentid = ".$question." and proposals.roundid = ".$generation." and proposals.id = endorse.proposalid";
	$n=0;
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$n+=1;
	}
	return $n;
}

function ProposalsInGeneration($question,$generation)
{
	$proposals=array();
	$sql = "SELECT id FROM proposals WHERE experimentid = ".$question." and roundid= ".$generation." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		array_push($proposals,$row[0]);
	}
	return $proposals;
}



function AuthorsOfNewProposals($question,$generation)
{
	$authors=array();
	$sql = "SELECT usercreatorid FROM proposals WHERE experimentid = ".$question." and roundid = ".$generation." and source = 0";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		array_push($authors,$row[0]);
	}
	return array_unique($authors);
}

function Endorsers($question,$generation)
{
	$authors=array();
	$sql = "SELECT endorse.userid  FROM endorse, proposals WHERE proposals.experimentid = ".$question." AND proposals.roundid = ".$generation." AND endorse.proposalid = proposals.id ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		array_push($authors,$row[0]);
	}
	return array_unique($authors);
}


function WriteQuestion($question,$userid)
{
	$answer="";

	$sql = "SELECT questions.id, questions.title, questions.roundid, questions.phase, users.username, users.id, questions.question, questions.room  FROM questions, users WHERE questions.id = ".$question." AND users.id = questions.usercreatorid ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$answer=$answer.'<p>';
		$title=$row[1];
		$generation=$row[2];
		$phase=$row[3];
		$thatusername=$row[4];
		$thatuserid=$row[5];
		$questionid=$question;
		$questiontext=$row[6];
		$pastgeneration=$generation -1;

		$room=$row[7];
		$urlquery = CreateQuestionURL($questionid, $room);

		$nrecentproposals=CountProposals($questionid,$pastgeneration);
		$nactualproposals=CountProposals($questionid,$generation);
		$nrecentendorsers=CountEndorsers($questionid,$pastgeneration);
		$nAuthorsNewProposals=count(AuthorsOfNewProposals($questionid,$generation));
		$nrecentparetofront=count(ParetoFront($questionid,$pastgeneration));

		if(	!$phase)
		{
			if(	$generation>1)
			{
				if(	!$nAuthorsNewProposals)#ProposalsWritten
				{
					$sql2 = "SELECT id, source FROM proposals WHERE experimentid = ".$questionid." and roundid = ".$pastgeneration." and dominatedby = 0 ";
					$response2 = mysql_query($sql2);
					$row2= mysql_fetch_row($response2);

					if($row2)
					{
						if ($nrecentendorsers==CountEndorsersToAProposal($row2[0]))#AGREEMENT FOUND
						{
							$answer=$answer.'<fieldset class="foottip">';
							$sql3 = "SELECT id, blurb  FROM proposals WHERE experimentid = ".$questionid." and roundid = ".$generation." ";
							$response3 = mysql_query($sql3);

							if (mysql_num_rows($response3)>1)
							{
								$answer=$answer.'<table border=0><tr><td><a href="http://www.flickr.com/photos/lencioni/2223801603/"><img src="images/fruits.jpg" title="Everybody Agreed on More than One Answer" height=42 ></a></td><td><a href="viewquestion.php' . $urlquery . '" tooltip="#footnote' . $row[0] . '">' . $title . '</a><br />';
								$UserString=WriteUserVsReader($thatuserid,$userid);
								$answer=$answer.'by '.$UserString;
								$answer=$answer.'</td></tr></table>';
							}
							else
							{
								$answer=$answer.'<table border=0><tr><td><a href="http://www.flickr.com/photos/don-piefcone/395175227/"><img src="images/apple.jpg" title="Generated Answer" height=42 ></a></td><td><a href="viewquestion.php' . $urlquery . '" tooltip="#footnote' . $questionid . '">' . $title . '</a> <br/>';
								$UserString=WriteUserVsReader($thatuserid,$userid);
								$answer=$answer.'by '.$UserString;
								$answer=$answer.'</td></tr></table>';
							}
							$endorsers=Endorsers($questionid,$pastgeneration);
							$answer=$answer."<b>Agreement found between</b>: ";
							foreach ($endorsers as $e)
							{
								$UserString=WriteUserVsReader($e,$userid);
								$answer=$answer.' '.$UserString;
							}

							$answer=$answer.'<div class="invisible" id="footnote' . $questionid . '">';
							$answer=$answer.'<ol>';

							while ($row3 = mysql_fetch_row($response3))
							{
								$answerid=$row3[0];
								$answertext=$row3[1];
								$answer=$answer.'<li>'.$answertext.'</li>';
							}
							$answer=$answer.'</ol>';
							$answer=$answer.'</div>';
							$answer=$answer.'</fieldset>';
						}
						else #AGREEMENT FOUND / NOT FOUND
						{

							$newproposalswritten=$nactualproposals-$nrecentparetofront;

							$answer=$answer.'<fieldset class="foottip">';

							$answer=$answer.'<table border=0><tr><td>';
							if (HasThisUserProposedSomething($questionid,$generation,$userid))
							{
								$answer=$answer.'<img src="images/tick.jpg" height=20 title="you have already proposed something this generation, but you can propose more"> ';
							}
							else
							{
								$answer=$answer.'<img src="images/tick_empty.png" height=20 title="you have not yes proposed anything new this generation"> ';
							}

							$answer=$answer.'</td><td><a href="http://www.flickr.com/photos/jphilipson/2100627902/"><img src="images/tree.jpg" title="Generation '.$generation.'" height=42 ></a></td><td><a href="viewquestion.php'.$urlquery.'"  tooltip="#footnote'.$questionid.'" >' . $title . '</a><br /> ';

							$UserString=WriteUserVsReader($thatuserid,$userid);
							$answer=$answer.'by '.$UserString;

							$answer=$answer.'</td></tr></table>';

							$answer=$answer.'<div class="invisible" id="footnote' . $questionid . '">' .$questiontext. '.<br/><strong>Generation</strong>='.$generation.';<br/>Recently '.$nAuthorsNewProposals.' proposers, proposed '.$newproposalswritten.' solutions.<br/>There are also '.$nrecentparetofront.' inherited from the previous generation.</div>';
							$answer=$answer.'</fieldset>';

						}#AGREEMENT FOUND
					}
				}
				else#ProposalsWritten
				{

					$newproposalswritten=$nactualproposals-$nrecentparetofront;
					$answer=$answer.'<fieldset class="foottip">';

					$answer=$answer.'<table border=0><tr><td>';
					if (HasThisUserProposedSomething($questionid,$generation,$userid))
					{
						$answer=$answer.'<img src="images/tick.jpg" height=20 title="you have already proposed something this generation, but you can propose more"> ';
					}
					else
					{
						$answer=$answer.'<img src="images/tick_empty.png" height=20 title="you have not yes proposed anything new this generation"> ';
					}

					$answer=$answer.'</td><td><a href="http://www.flickr.com/photos/jphilipson/2100627902/"><img src="images/tree.jpg" title="Generation '.$generation.'" height=42 ></a></td><td><a href="viewquestion.php'.$urlquery .'"  tooltip="#footnote'.$questionid.'" >' . $title . '</a><br /> ';

					$UserString=WriteUserVsReader($thatuserid,$userid);
					$answer=$answer.'by '.$UserString;

					$answer=$answer.'</td></tr></table>';

					$answer=$answer.'<div class="invisible" id="footnote' . $questionid . '">' .$questiontext. '.<br/><strong>Generation</strong>='.$generation.';<br/>Recently '.$nAuthorsNewProposals.' proposers, proposed '.$newproposalswritten.' solutions.<br/>There are also '.$nrecentparetofront.' inherited from the previous generation.</div>';
					$answer=$answer.'</fieldset>';
				}
			}
			else #generation==1
			{
				if(	!$nAuthorsNewProposals)
				{
					$answer=$answer.'<fieldset class="foottip">';

					$answer=$answer.'<table><tr><td>';

					if (HasThisUserProposedSomething($questionid,$generation,$userid))
					{
						$answer=$answer.'<img src="images/tick.jpg" height=20 title="you have already proposed something this generation, but you can propose more"> ';
					}
					else
					{
						$answer=$answer.'<img src="images/tick_empty.png" height=20 title="you have not yes proposed anything new this generation"> ';
					}

					$answer=$answer.'</td><td><a href="http://www.flickr.com/photos/found_drama/1023671528/"><img src="images/germinating.jpg" title="New Question" height=42 ></a></td><td><a title="This is a new question. Be the first to suggest an answer!" href="viewquestion.php'.$urlquery.'" tooltip="#footnote'.$questionid.'" >'.$title.'</a> <br/>';

					$UserString=WriteUserVsReader($thatuserid,$userid);

					$answer=$answer.'by '.$UserString;
					$answer=$answer.'</td></tr></table>';

					$answer=$answer.'<div class="invisible" id="footnote'.$questionid.'">This is a new question. Be the first to suggest an answer!<br/>QUESTION: '.$row[6].'.</div>';
					$answer=$answer.'</fieldset>';
				}
				else
				{
					$answer=$answer.'<fieldset class="foottip">';


					$answer=$answer.'<table><tr><td>';

					if (HasThisUserProposedSomething($questionid,$generation,$userid))
					{
						$answer=$answer.'<img src="images/tick.jpg" height=20 title="you have already proposed something this generation, but you can propose more"> ';
					}
					else
					{
						$answer=$answer.'<img src="images/tick_empty.png" height=20 title="you have not yes proposed anything new this generation"> ';
					}

					$answer=$answer.'</td><td><a href="http://www.flickr.com/photos/found_drama/1023671528/"><img src="images/germinating.jpg" title="New Question" height=42 ></a></td><td><a title="This is a new question. Be the first to suggest an answer!" href="viewquestion.php'.$urlquery.'" tooltip="#footnote'.$questionid.'" >'.$title.'</a> <br/>';

					$UserString=WriteUserVsReader($thatuserid,$userid);

					$answer=$answer.'by '.$UserString;
					$answer=$answer.'</td></tr></table>';

					$answer=$answer.'<div class="invisible" id="footnote'.$questionid.'">This is a new question. Be the first to suggest an answer!<br/>QUESTION: '.$questiontext.'.</div>';
					$answer=$answer.'</fieldset>';

				}
			}
		}#phase
		else#phase
		{

			$answer=$answer.'<fieldset class="foottip">';

			$answer=$answer.'<table border=0><tr><td>';
			if (HasThisUserEndorsedSomething($questionid,$generation,$userid))
			{
				$answer=$answer.'<img src="images/tick.jpg" height=20 title="you have already expressed your endorsements over here"> ';
			}
			else
			{
				$answer=$answer.'<img src="images/tick_empty.png" height=20 title="you have not expressed any endorsements over here"> ';
			}


			$answer=$answer.'</td><td><a href="http://www.flickr.com/photos/johnfahertyphotography/2675723448/"><img src="images/flowers.jpg" title="Chose the ones you like" height=42 ></a></td><td> <a href="viewquestion.php' . $urlquery . '" tooltip="#footnote' . $row[0] . '"   >' . $row[1] . '</a> <br />';

			$UserString=WriteUserVsReader($thatuserid,$userid);

			$answer=$answer.'by '.$UserString;
			$answer=$answer.'</td></tr></table>';

			$answer=$answer.'<div class="invisible" id="footnote' . $row[0] . '">';
			$answer=$answer.'<ol>';
			$sql3 = "SELECT id, blurb  FROM proposals WHERE experimentid = ".$row[0]." and roundid = ".$generation." ";
			$response3 = mysql_query($sql3);

			while ($row3 = mysql_fetch_row($response3))
			{
				$answerid=$row3[0];
				$answertext=$row3[1];
				$answer=$answer.'<li>'.$answertext.'</li>';
			}
			$answer=$answer.'</ol>';
			$answer=$answer.'<strong>Generation</strong>='.$generation.';<br/>Recently '.$nrecentendorsers.' human beings voted on '.$nrecentproposals.' possible solutions,<br/>'.$newproposalswritten.' produced by '.$nAuthorsNewProposals.' human being(s)<br/>
		and '.$nrecentparetofront.' inherited from the previous generation.';
			$answer=$answer.'</div>';

			$answer=$answer.'</fieldset>';
		}#phase
	}
	if ($userid==2)	{$answer=$answer.'<a href="deletequestion.php?q='.$questionid.'"><img src="images/delete.gif"></a>';}
	$answer=$answer.'</p>';

	return $answer;
}


function WriteTime($timetotranslate)
{
	$answer="";
	$dayselapsed=(int)($timetotranslate/(60*60*24));
	$timetotranslate=$timetotranslate-$dayselapsed*60*60*24;
	$hourseselapsed=(int)($timetotranslate/(60*60));
	$timetotranslate=$timetotranslate-$hourseselapsed*60*60;
	$minuteselapsed=(int)($timetotranslate/60);
	if ($dayselapsed>0)	{ $answer=$answer." ".$dayselapsed." days";}
	if ($hourseselapsed>0)	{ $answer=$answer." ".$hourseselapsed." hours";}
	if ($minuteselapsed>0)	{ $answer=$answer." ".$minuteselapsed." minutes";}
	return $answer;
}


function WriteUser($user)
{
	$sql = "SELECT  users.username, users.email FROM users WHERE  users.id = " .$user. " ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$answer= '<a href="user.php?u='.$user.'">' . $row[0] . '</a> ';
		if ($row[3])
		{
		$answer= $answer.'<img src="images/email.png" height=12 title="the user receives emails updates">';
		}
	}

	return $answer;
}
function WriteUserVsReader($user,$reader)
{
	$sql = "SELECT  users.username, users.email FROM users WHERE  users.id = " .$user. " ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$answer='<a href="user.php?u='.$user.'">' . $row[0] . '</a> ';
		if ($row[1]=="")
		{
#		$answer= $answer.'<sup><img src="images/noemail.jpg" height=12 title="the user does not receives emails updates"></sup>';
		}
		else
		{
		$answer= $answer.'<sup><img src="images/email.png" height=12 title="the user receives emails updates"></sup>';
		}


		if($reader==$user)
		{
			$answer='<b>'.$answer.'</b>';
		}
	}
	return $answer;
}


function TimeLastProposalOrEndorsement($question, $phase, $generation)
{
	if ($phase)
	{
		$sql = "SELECT endorse.endorsementdate FROM proposals, endorse WHERE proposals.experimentid = ".$question." and proposals.roundid = ".$generation." and proposals.id = endorse.proposalid  ORDER BY endorse.endorsementdate  LIMIT 100 ;";
		$response = mysql_query($sql);
		while ($row = mysql_fetch_row($response))
		{
			if ($row[0]!="0000-00-00 00:00:00")
			{
				return strtotime( $row[0] );
			}
		}
	}
	else
	{
		$sql = "SELECT proposals.creationtime FROM proposals WHERE proposals.experimentid = ".$question." and proposals.roundid = ".$generation." and proposals.creationtime is not NULL ORDER BY proposals.creationtime  LIMIT 1;";
		$response = mysql_query($sql);
		while ($row = mysql_fetch_row($response))
		{
			if ($row[0]!="0000-00-00 00:00:00")
			{
				return strtotime( $row[0] );
			}
		}
	}
	return 0;
}

function HasConsensusBeenFound($question,$phase,$generation)
{
	if ($phase==1)
		{return 0;}
	if ($generation==1)
		{return 0;}
	$nAuthorsNewProposals=count(AuthorsOfNewProposals($question,$generation));
	if ($nAuthorsNewProposals)
		{return 0;}
	$pastgeneration=$generation -1;
	$nrecentendorsers=CountEndorsers($question,$pastgeneration);
	$nrecentparetofront=count(ParetoFront($question,$pastgeneration));

	$sql2 = "SELECT id,source  FROM proposals WHERE experimentid = ".$question." and roundid = ".$pastgeneration." and dominatedby = 0 ";
	$row2= mysql_fetch_row(mysql_query($sql2));
	if(!$row2)
		{return 0;}
	if ($nrecentendorsers==CountEndorsersToAProposal($row2[0]))
		{return 1;}
	return 0;
}

function IsQuestionReadyToBeMovedOn($question,$phase,$generation)
{
	if($phase)
	{
		$endorsers=CountEndorsers($question,$generation);
		if ($endorsers<2)
			{return 0;}
	}
	else
	{
		$proposals=count(ProposalsInGeneration($question,$generation));
		if($proposals<2)
				{return 0;}
		if($generation>1)
		{
			$inheritedproposals=count(ParetoFront($question,$generation-1));
			if($proposals==$inheritedproposals)
				{return 0;}
		}
	}

	$timefrom=TimeLastProposalOrEndorsement($question, $phase, $generation);
	if (!$timefrom) 	#{return 0;}
	{
		$sql = "SELECT questions.lastmoveon FROM questions WHERE questions.id = ".$question." ;";
		$row = mysql_fetch_row(mysql_query($sql));
		$timefrom=strtotime( $row[0] );
	}

	$sql = "SELECT questions.minimumtime FROM questions WHERE questions.id = ".$question." ;";
	$row = mysql_fetch_row(mysql_query($sql));
	$timepassed=time()-$timefrom;
	if ($timepassed>$row[0])
	{	return 1; }
	return 0;
}
function IsQuestionReadyToAutoMoveOn($question,$phase,$generation)
{
	if($phase)
	{
		$endorsers=CountEndorsers($question,$generation);
		if ($endorsers<2)
			{return 0;}
	}
	else
	{
		$proposals=count(ProposalsInGeneration($question,$generation));
		if($proposals<2)
				{return 0;}
		if($generation>1)
		{
			$inheritedproposals=count(ParetoFront($question,$generation-1));
			if($proposals==$inheritedproposals)
				{return 0;}
		}
	}

	$timefrom=TimeLastProposalOrEndorsement($question, $phase, $generation);
	if (!$timefrom)
	{
		$sql = "SELECT questions.lastmoveon FROM questions WHERE questions.id = ".$question." ;";
		$row = mysql_fetch_row(mysql_query($sql));
		$timefrom=strtotime( $row[0] );
	}
	$sql = "SELECT questions.maximumtime FROM questions WHERE questions.id = ".$question." ;";
	$row = mysql_fetch_row(mysql_query($sql));
	$timepassed=time()-$timefrom;
	if ($timepassed>$row[0])
	{	return 1; }
	return 0;
}



function SendMails($question)
{
	$EmailsSent=array();

	$sql2 = "SELECT * FROM questions WHERE id = ".$question." ";
	$response2 = mysql_query($sql2);
	$row2 = mysql_fetch_row($response2);
	$content=wordwrap($row2[1], 70,"\n",true);
	$round=$row2[2];
	$phase=$row2[3];
	$title=wordwrap($row2[5], 70,"\n",true);
	$room = $row2[9];
	$urlquery = CreateQuestionURL($question, $room);
	if (!$phase)
	{
		$consensus=HasConsensusBeenFound($question,$phase,$round);
		if ($consensus)
		{
			$subject="VgtA, Agreement Found on: ".$title."";
			$message="Hello, \n The question: ".$title."\n\n has just been updated, and we found an agreement!\n Please read the agreed answer here:\nhttp://vilfredo.org/viewquestion.php".$urlquery." \n If you are not satisfied and want to reopen the question you can do so by proposing something better at:\nhttp://vilfredo.org/viewquestion.php".$urlquery." ";
		}
		else
		{
			$subject="VgtA: ".$title."";
			$message="Hello, \n The question: ".$title."\n\n has just been updated.\nWe are now in Generation=".$round.".\n\nYou can now see the minimum set of proposal on which everybody agrees.\nIf you think you can propose something better,\nthat would satisfy more people,\nplease do so. Here:\nhttp://vilfredo.org/viewquestion.php".$urlquery."";
		}
	}else{
		$subject="VgtA: ".$title."";
		$message="Hello, \n The question: ".$title."\n\n has just been updated.\nWe are now in Generation=".$round.".\n\n You can now see proposals that has been suggested. Please vote on ALL the ones you agree on here:\nhttp://vilfredo.org/viewquestion.php".$urlquery."";
	}

	$sql = "SELECT user FROM updates WHERE question = ".$question." and how = 'asap' ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$user=$row[0];
		sendmail($user,$question,$subject,$message);
		array_push($EmailsSent,$user);
	}
	if (!$phase)
	{
		$proposers=AuthorsOfNewProposals($question,$round-1);
		foreach ($proposers as $user)
		{
			if (in_array($user,$EmailsSent))
			{
				continue;
			}
			sendmail($user,$question,$subject,$message);
			array_push($EmailsSent,$user);
		}
		$endorsers=Endorsers($question,$round-1);
		foreach ($endorsers as $user)
		{
			if (in_array($user,$EmailsSent))
			{
				continue;
			}
			sendmail($user,$question,$subject,$message);
			array_push($EmailsSent,$user);
		}
	}else{
		$proposers=AuthorsOfNewProposals($question,$round);
		foreach ($proposers as $user)
		{
			if (in_array($user,$EmailsSent))
			{
				continue;
			}
			sendmail($user,$question,$subject,$message);
			array_push($EmailsSent,$user);
		}
		$endorsers=Endorsers($question,$round-1);
		foreach ($endorsers as $user)
		{
			if (in_array($user,$EmailsSent))
			{
				continue;
			}
			sendmail($user,$question,$subject,$message);
			array_push($EmailsSent,$user);
		}
	}

}


function AwareAuthorOfNewProposal($question)
{
	$sql = "SELECT users.username, users.email, questions.title, questions.roundid, questions.phase, questions.room FROM questions, users WHERE questions.id = ".$question." AND questions.usercreatorid = users.id ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	if ($row)
	{
		$username=$row[0];
		$to=$row[1];
		if (!$to) return;
		$title=$row[2];
		$generation=$row[3];
		$phase=$row[4];
		$room=$row[5];
		$urlquery = CreateQuestionURL($question, $room);
		$QuestionReady= IsQuestionReadyToBeMovedOn($question,$phase,$generation);
		if (!$QuestionReady)
		{ return;}
		$NProposals=CountProposals($question,$generation);
		$nrecentparetofront=count(ParetoFront($question,$generation-1));
		$nAuthorsNewProposals=count(AuthorsOfNewProposals($question,$generation));


		$subject="VgtA: New proposal at: ".$title."";
		$message='Dear '.$username.'
		another user has added a possible solution to your question:
		'.$title.'
		Now the question has '.$NProposals.' answers written from '.$nAuthorsNewProposals.' users,
		plus '.$nrecentparetofront.' answers that have been inherited from the previous generation.

		If you think it is enough, or you think you are not going to receive many more,
		you can move on the question to the next phase.
		By moving it on you are giving the possibility to everybody to read, and evaluate each other response.
		Throught this process the best answers (the pareto front of the chosen answers) will be selected.

		to move on you should go to the page of the question:
		http://vilfredo.org/viewquestion.php'.$urlquery.'
		and click on the moveon button.

		Please consider that until you do so,
		no one is allowed to vote on this question,
		and the question will just wait there.
		It is thus very important that after the question has waited enough you move it on.';
		$message=wordwrap  ( $message, 70,"\n",true);
		$result=mail($to,$subject, $message );
	}
}

function AwareAuthorOfNewEndorsement($question)
{
	$sql = "SELECT users.username, users.email, questions.title, questions.roundid, questions.phase, questions.room FROM questions, users WHERE questions.id = ".$question." AND questions.usercreatorid = users.id ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	if ($row)
	{
		$username=$row[0];
		$to=$row[1];
		if (!$to) return;
		$title=$row[2];
		$generation=$row[3];
		$phase=$row[4];
		$room=$row[5];
		$urlquery = CreateQuestionURL($question, $room);
		$QuestionReady= IsQuestionReadyToBeMovedOn($question,$phase,$generation);
		if (!$QuestionReady)
		{ return;}

		$nEndorsers=CountEndorsers($question,$generation);


		$subject="VgtA: New endorsement at: ".$title."";
		$message='	Dear '.$username.'
another user has submitted its endorsement to the possible solution to your question:

'.$title.'

Now the question has endorsement(s) from '.$nEndorsers.' user(s).

	If you think it is enough, or you think you are not going to receive many more, you can move on the question to the next phase. In the next phase the system will evaluate all the answers and see what is the minimum set of answers where all the users agree on (the pareto front of proposals).

	If there is an answer (or a set of answers) where all the users agree, this answer is extracted as the consensus of the community on this topic.

	If instead (as it is often the case) no consensus can be found at this point in time, the pareto front is returned as the minimum set that hold the community solution, and the users are invited to write new answers, being inspired by the previous ones.

	The previous answers are also added as possible answers and will run for evaluation again.

	To move on you should go to the page of the question:
http://vilfredo.org/viewquestion.php'.$urlquery.'
and click on the "moveon" button.

	Please consider that until you do so, no one is allowed to post new solutions to this question, and the question will just wait there. It is thus very important that after the question has waited enough you move it on.';
		$message=wordwrap  ( $message, 70,"\n",true);
		$result=mail($to,$subject, $message );
	}
}

function InviteUserToQuestion($user,$question,$room,$userid)
{
        $question_url = CreateQuestionURL($question,$room);

        $sql = "SELECT users.username FROM users WHERE id = ".$userid." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	$authorusername=$row[0];

	$sql = "SELECT questions.title FROM questions WHERE questions.id = ".$question." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	$title=$row[0];

	$sql = "SELECT users.username, users.email FROM users WHERE id = ".$user." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	$to=$row[1];
	$username=$row[0];

	$subject="VgtA: Invitation for: ".$title."";
	$message='	Dear '.$username.'
user '.$authorusername.' would like to invite you to participate in the question:

'.$title.'

You can do this by going to the page
http://vilfredo.org/viewquestion.php'.$question_url.'

If you would like not to receive any more invitations from '.$authorusername.' you can tell him directly.';

		$message=wordwrap  ( $message, 70,"\n",true);
		mail($to,$subject, $message );
}

function SendMail($user,$question,$subject,$message)
{
	$sql = "SELECT email FROM users WHERE id = ".$user." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$to=$row[0];
		if (!$to) continue;
		$message=wordwrap  ( $message, 70,"\n",true);
		mail($to,$subject, $message );
	}
}


function MoveOnToWriting($question)
{
	$sql2 = "SELECT phase FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_row($response2))
	{
		$phase =	$row2[0];
	}
	if($phase==1)
	{
		$sql = "UPDATE questions SET phase = '0'  WHERE id = ".$question." ";
		mysql_query($sql);
		$sql = "UPDATE questions SET roundid = roundid+1 WHERE id = ".$question." ";
		mysql_query($sql);
		$sql = "UPDATE questions SET lastmoveon = NOW() WHERE id = ".$question." ";
		mysql_query($sql);
		SelectParetoFront($question);
		SendMails($question);
	}
}

function MoveOnToEndorse($question)
{
	$sql2 = "SELECT phase FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_row($response2))
	{
		$phase =	$row2[0];
	}

	if($phase==0)
	{
		$sql = "UPDATE questions SET phase = '1' WHERE id = ".$question." ";
		mysql_query($sql);
		$sql = "UPDATE questions SET lastmoveon = NOW() WHERE id = ".$question." ";
		mysql_query($sql);
		SendMails($question);
	}
}



function ParetoFront($question,$generation)
{
	$paretofront=array();
	$sql = "SELECT id FROM proposals WHERE experimentid = ".$question." and roundid= ".$generation." and dominatedby = 0";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		array_push($paretofront,$row[0]);
	}
	return $paretofront;
}

function SelectParetoFront($question)
{
	$sql = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$generation=$row[0];
		$pastgeneration=$generation-1;
	}
	$proposals=array();
	$dominated=array();
	$sql = "SELECT id FROM proposals WHERE experimentid = ".$question." and roundid= ".$pastgeneration."";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		array_push($proposals,$row[0]);
	}
	foreach ($proposals as $p1)
	{
		foreach ($proposals as $p2)
		{
			if (in_array($p2,$dominated))
			{
				continue;
			}
			$dominating=WhoDominatesWho($p1,$p2);
			if ($dominating==$p1)
			{
				array_push($dominated,$p2);
				$sql = "UPDATE proposals SET dominatedby = ".$p1." WHERE id = ".$p2." ";
				mysql_query($sql);
				continue;
			}
			elseif ($dominating==$p2)
			{
				array_push($dominated,$p1);
				$sql = "UPDATE proposals SET dominatedby = ".$p2." WHERE id = ".$p1." ";
				mysql_query($sql);
				break;
			}
		}
	}
	$paretofront=array_diff($proposals,$dominated);
	foreach ($paretofront as $p)
	{
		$sql = "SELECT * FROM proposals WHERE id = ".$p." LIMIT 1";
		$response = mysql_query($sql);

		while ($row = mysql_fetch_row($response))
		{

			if (!get_magic_quotes_gpc())
			{
				$blurb = addslashes($row[1]);
			}
			else
			{
				$blurb = $row[1];
			}

			$sql = 'INSERT INTO `proposals` (`blurb`, `usercreatorid`, `roundid`, `experimentid`,`source`,`dominatedby`,`creationtime` ) VALUES (\'' . $blurb . '\', \'' . $row[2] . '\', \'' . $generation . '\', \'' . $question . '\', \''.$p.'\',\'0\', NOW());';
			mysql_query($sql);
		}
	}
}

/////////////////////////////////////////////////////////////////////////////////////
/////////////////This function takes two proposals, and returns 0 if neither dominates the other because they have different users
/////////////////returns -1 if neither dominates the other because they have the same users
/////////////////returns the id of the dominating proposal if one dominates the other
function WhoDominatesWho($proposal1,$proposal2)
{
	$sql1 = "SELECT userid FROM endorse WHERE endorse.proposalid = ".$proposal1." ";
	$response1 = mysql_query($sql1);
	$users1=array();
	while ($row1 = mysql_fetch_row($response1))
	{
		array_push($users1,$row1[0]);
	}
	$sql2 = "SELECT userid FROM endorse WHERE endorse.proposalid = ".$proposal2." ";
	$response2 = mysql_query($sql2);
	$users2=array();
	while ($row2 = mysql_fetch_row($response2))
	{
		array_push($users2,$row2[0]);
	}
	if (count($users1))
	{
		if (count($users2))
		{
			if (count(array_diff($users1,$users2)))
			{//there are elements in 1 that are not in 2, so 2 does not dominate 1
				if (count(array_diff($users2,$users1)))
				{//there are elements in 2 that are not in 1, so 1 does not dominate 2
					return 0;//neither dominates the other, they each have extra endorsers
				}
				else
				{//there are NO elements in 2 that are not in 1, so 1 DOES dominate 2
					return $proposal1; //1 dominates 2
				}
			}
			else
			{//there are NO elements in 1 that are not in 2, so 2 DOES dominate 1
				if (count(array_diff($users2,$users1)))
				{//there are elements in 2 that are not in 1, so 1 does not dominate 2
					return $proposal2; //2 dominates 1
				}
				else
				{//there are NO elements in 2 that are not in 1, so 1 DOES dominate 2
					return -1; //neither dominates the other, they have exactly the same endorsers
				}
			}
		}
		else
		{//the second proposal has no endorsers
			return $proposal1;
		}
	}
	else
	{//the first proposal has no endorsers
		if (count($users2))
		{
			return $proposal2;
		}
		else
		{//neither endorsers has any proposal
			return -1;
		}
	}
}




$userid=isloggedin();
if ($userid)
{

	$room_param = GetParamFromQuery(QUERY_KEY_ROOM);
	
	?>

		<body>
			<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9953942-1");
pageTracker._trackPageview();
} catch(err) {}</script>

			<div id="header">

			<a href="http://en.wikipedia.org/wiki/Vilfredo_Pareto"><img src="images/pareto.png" id="paretoimg" /></a>
			<img src="images/athens.png" id="athens" />

				<h1><img src="images/titleimg.png" alt="Vilfredo goes to Athens" /></h1>

				<ul id="nav">
					<li><a href="viewquestions.php">View Common Room</a></li>
					<?php if  ($room_param)
						{ 
						QUERY_KEY_ROOM
						?>
						<li><a href="viewquestions.php<?php echo CreateNewRoomURL(); ?>">View Room <?php echo $room_param; ?></a></li>
						<?php
						} 
					?>
					<li><a href="viewquestions.php?u= <?php echo $userid; ?>">View My Questions</a></li>
					<li><a href="FAQ.php">F.A.Q.</a></li>
					<li>Hello <?php echo $_COOKIE['ID_my_site']; ?></li>
					<li><a href="editdetails.php">Update Email</a></li>
					<li><a href="map.php">Website Map</a></li>
					<li><a href="logout.php">Logout</a></li>
				</ul>

			</div>
	<?php
	$sql = "SELECT email FROM users WHERE id = ".$userid." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	if ($row[0]=="")
	{
		?>
		<form action="editdetails.php" method="post">
			<table border="0">
				<tr>
					<td>Please insert your email address (<a href="FAQ.php#email">why</a>?):</td>
					<td>
						<input type="text" name="email" maxlength="60">
					</td>
				</tr>
				<tr>
					<th colspan=2>
						<input type="submit" name="submit" value="Update Email">
					</th>
				</tr>
			</table>
		</form>
		<?php
	}
	?>

			<div id="content_page">
	<?php
}
else
{
	?>
	<body>
		<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9953942-1");
pageTracker._trackPageview();
} catch(err) {}</script>


			<div id="header">

			<img src="images/pareto.png" id="paretoimg" />
			<img src="images/athens.png" id="athens" />

				<h1><img src="images/titleimg.png" alt="Vilfredo goes to Athens" /></h1>

				<ul id="nav">
					<li><a href="index.php">Home</a></li>
					<li><a href="viewquestions.php">View Questions</a></li>
					<li><a href="login.php">Login</a></li>
					<li><a href="register.php">Register</a></li>
				</ul>

			</div>

			<div id="content_page">

	<?php
}


