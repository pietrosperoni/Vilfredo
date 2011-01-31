<?php
// Vilfredo Fuctions - previously in header.php
//**************************************
// ******************************************/
//
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
function check_dnsrr($host, $type)
{
	if (function_exists('checkdnsrr'))
		return checkdnsrr($host, $type);
	else
		return true;
}
//  ******************************************/
// ADMIN
//	Users with id listed in admin table can delete 
//	questions and proposals.
//
// ******************************************/
function formatSeconds($seconds)
{
	if ($seconds > 0)
	{
		$mins = floor ($seconds / 60);
		$secs = $seconds % 60;
		return sprintf("%d:%d (m:s)", $mins, $secs);
	}
	else
	{
		return sprintf("%d:%d (m:s)", 0, 0);
	}
}

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
//  ******************************************/
// ANONYMOUS USERS
//	Reusable user records used by external anonymous users
//
// ******************************************/
function isAnonymous($userid)
{
	if (is_int($userid))
	{
		$sql = "SELECT `anon` FROM `users` WHERE id = $userid";
		if ($result = mysql_query($sql))
		{
			if (mysql_num_rows($result) == 0)
			{
				return false;
			}
			else
			{
				$row = mysql_fetch_assoc($result);
				return (bool)$row['anon'];
			}
		}
		else
		{
			db_error($sql);
			return false;
		}
	}
	else
	{
		log_error(__FUNCTION__ . "called with userid \'$userid\'");
		return false;
	}
}

function createAnonymousUser()
{	
	// Create new anonymous user
	$sql = "INSERT INTO users 
			(username, password, email, anon) 
			VALUES ('anon', '', '', 1)";

	if (!$result = mysql_query($sql))
	{
		db_error($sql);
		return false;
	}			
	$userid = mysql_insert_id();
	$sql = "UPDATE users 
		SET username = '$userid' 
		WHERE id = $userid";
	if (!$result = mysql_query($sql))
	{
		set_log("Incomplete creation of anonymous user $userid");
		db_error($sql);
		return false;
	}
	return $userid;
}

// Fetch all endorsers of proposals
function GetAnonymousEndorsers($proposals)
{
	if (!is_array($proposals) or empty($proposals))
	{
		return false;
	}
	
	$endorsers = array();
	$pids = implode(",", $proposals);
	
	$sql = "SELECT DISTINCT endorse.userid AS userid FROM `endorse`, `users` 
	WHERE endorse.userid = users.id
	AND endorse.proposalid IN ( $pids )
	AND users.anon = 1";
	
	if(!$result = mysql_query($sql))
	{
		db_error(__FUNCTION__ . " SQL: $sql");
		return false;
	}
	elseif (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			array_push($endorsers, $row['userid']);
		}
	}
	return $endorsers;
}

function GetAnonymousProposers($proposals)
{
	if (!is_array($proposals) or empty($proposals))
	{
		return false;
	}
	$proposers = array();
	$pids = implode(",", $proposals);
	
	$sql = " SELECT DISTINCT proposals.usercreatorid AS userid
	FROM `proposals` , `users`
	WHERE proposals.usercreatorid = users.id
	AND proposals.id IN ( $pids )
	AND users.anon =1";
	
	if(!$result = mysql_query($sql))
	{
		db_error(__FUNCTION__ . ": $sql");
		return false;
	}
	elseif (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			array_push($proposers, $row['userid']);
		}
	}
	return $proposers;
}

function GetAllQuestionProposals($question)
{
	$proposals = array();
	$sql = "SELECT `id` as pid FROM `proposals` 
	WHERE `experimentid` = $question AND `source` = 0";

	if(!$result = mysql_query($sql))
	{
		db_error(__FUNCTION__ . ": $sql");
		return false;
	}
	elseif (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			array_push($proposals, $row['pid']);
		}
	}
	return $proposals;
}

function getAllAnonymousUsersForQuestion($question)
{
	$proposals = array();
	$proposers = array();
	$endorsers = array();
	$users = array();
	
	$proposals = GetAllQuestionProposals($question);
	if ($proposals === false)
	{
		return false;
	}
	else
	{
		if (count($proposals) > 0)
		{
			//$pros = implode(",", $proposals);//DEBUG
			//set_log("Proposals: " . $pros);//DEBUG
			// Fetch authors of all proposals
			$proposers = GetAnonymousProposers($proposals);
			if ($proposers === false)
			{
				log_error("GetAnonymousProposers failed");
				return false;
			}
			// Fetch endorsers of all proposals
			$endorsers = GetAnonymousEndorsers($proposals);
			if ($endorsers === false)
			{
				log_error("GetAnonymousEndorsers failed");
				return false;		
			}
			//$props = implode(",", $proposers);//DEBUG
			//set_log("Proposers: " . $props);//DEBUG
			//$ends = implode(",", $endorsers);//DEBUG
			//set_log("Endorsers: " . $ends);//DEBUG
			$users = array_unique(array_merge($proposers, $endorsers));
		}
		return $users;
	}
}

function getAnonymousUser($question)
{
	if (!isset($question))
	{
		return false;
	}
	
	$current_anon_users = getAllAnonymousUsersForQuestion($question);
	
	if ($current_anon_users === false)
	{
		return false;
	}
	
	$sql;
	
	if (count($current_anon_users) > 0)
	{
		$uids = implode(",", $current_anon_users);
		$sql = "SELECT id FROM users WHERE anon = 1
		AND id NOT IN ($uids)
		ORDER BY id ASC LIMIT 1";
	}
	else
	{
		$sql = "SELECT id FROM users WHERE anon = 1 
		ORDER BY id ASC LIMIT 1";
	}

	if ($result = mysql_query($sql))
	{
		if (mysql_num_rows($result) == 0)
		{
			// create new anonymous user
			return createAnonymousUser();
		}
		else
		{
			// assign first available anonymous user
			$row = mysql_fetch_assoc($result);
			return $row['id'];
		}
	}
	else
	{
		db_error($sql);
		return false;
	}
}
//****************************************
function GetEndorsers($proposals)
{
	$endorsers = array();
	$pids = implode(",", $proposals);
	$sql = "SELECT DISTINCT userid FROM `endorse 
	WHERE id IN ($pids)";
	if(!$result = mysql_query($sql))
	{
		db_error(__FUNCTION__ . ": $sql");
		return $endorsers;
	}
	elseif (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			array_push($endorsers, $row['userid']);
		}
		return $endorsers;
	}
	else
	{
		// No endorsers found - return empty array
		return endorsers;
	}
}

function getAnonymousUserForNewProposal($authors)
{
	if (count($authors) > 0)
	{
		$uids = implode(",", $authors);
		$sql = "SELECT id FROM users WHERE anon = 1
		AND id NOT IN ($uids)";
	}
	else
	{
		$sql = "SELECT id FROM users WHERE anon = 1";
	}

	if ($result = mysql_query($sql))
	{
		if (mysql_num_rows($result) == 0)
		{
			// create new anonymous user
			return createAnonymousUser();
		}
		else
		{
			// assign first available anonymous user
			$row = mysql_fetch_assoc($result);
			return $row['id'];
		}
	}
	else
	{
		db_error($sql);
		return false;
	}
}

function getAnonymousUserForVoting($proposals)
{
	if (count($proposals) == 0)
	{
		log_error(__FUNCTION__ . " called with zero proposal list");
		return false;
	}
	
	$pids = implode(",", $proposals);

	$sql = "SELECT  DISTINCT `userid` FROM `endorse` 
	WHERE `proposalid` IN ($pids)";
	
	$voters = array();
	if ($result = mysql_query($sql))
	{
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_row($result))
			{
				array_push($voters,$row[0]);
			}
			
			$uids = implode(",", $voters);
			$sql = "SELECT id FROM users WHERE anon = 1
			AND id NOT IN ($uids)";

			if ($result = mysql_query($sql))
			{
				if (mysql_num_rows($result) == 0)
				{
					// create new anonymous user
					return createAnonymousUser();
				}
				else
				{
					// assign first available anonymous user
					$row = mysql_fetch_assoc($result);
					return $row['id'];
				}
			}
			else
			{
				db_error($sql);
				return false;
			}
		}
		else
		{
			// create new anonymous user
			return createAnonymousUser();
		}
	}
	else
	{
		db_error($sql);
		return false;
	}
}

// ******************************************
// ERRORS
//
// ******************************************
function error($message, $level=VILFREDO_ERROR) 
{
	$caller = next(debug_backtrace());
	trigger_error($message.' in <strong>'.$caller['function'].'</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."\n<br />error handler", $level);
}

function set_trace($msg, $log = false)
{
	if ($log) {
		set_log($msg);
	}
}

function log_error($msg)
{
	$timestamp = date("D M j G:i:s T Y");
	error_log("log_error: $msg $timestamp \n", 3, ERROR_FILE);
}

function HaveSameElements($a,$b) #considering two arrays as sets, do they have the same content?
{
	if (count(array_diff(array_merge($a, $b), array_intersect($a, $b))) === 0)
	{ return true;}
	return false;
}

function set_log($msg)
{
	$timestamp = date("D M j G:i:s T Y");
	error_log("set_log: $msg $timestamp \n", 3, LOG_FILE);
}

function printdberror($sql="")
{
	$msg = "MySQL Error=[" .  mysql_errno() . " : " . mysql_error() . ']';
	if (!empty($sql))
	{
		$msg .= '   SQL=[' . $sql . ']  ';
	}
	printbr($msg);
}

function db_error($sql="")
{
	$msg = "MySQL Error=[" .  mysql_errno() . " : " . mysql_error() . ']';
	if (!empty($sql))
	{
		$msg .= '   SQL=[' . $sql . ']  ';
	}
	
	log_error($msg);
}

function handle_db_error($result=false, $sql="")
{
	if (!$result)
	{
		$msg = "";
		if (!empty($sql))
		{
			$msg .= 'SQL=[' . $sql . ']  ';
		}
		$msg .= "MySQL Error=[" .  mysql_errno() . " : " . mysql_error() . ']';
		log_error($msg);
	}
}

function display_viewall_link() 
{
	return '<h3>Abstract <a href="#" class="viewall" title="Click here to display the full proposal text"><img src="images/fulltext32.png" width="18" height="18" alt="" /><span class="viewall-label">view full text</span></a></h3>';
}

function display_view_all_link() 
{
	return '<h3>Abstract</h3>';
	//return '<h3>Abstract <a href="#" class="view-all" title="Click here to display the full proposal text"><img src="images/fulltext32.png" width="18" height="18" alt="" /><span class="view-all-label">view full text</span></a></h3>';
}

function display_show_full_text_link()
{	
	//return '';
	return '<a class="expandbtn" href="#" title="Click here to display the full proposal text"><img src="images/fulltext32.png" width="30" height="30" alt="" /><span class="show-full-label">View Full Text</span></a>';
}

function display_fulltext_link()
{	
	return '<span class="expandabstract" title="Click here to display the full proposal text"><img src="images/fulltext32.png" width="30" height="30" alt="" /><span class="show-full-label">View Full Text</span></span>';
}

function display_editproposal_link2()
{	
	return '<span class="editproposal" title="Click here to edit or delete your proposal"><span class="label">Edit or Delete</span></span>';
}

function display_editproposal_link()
{	
	return '<span class="editproposal" title="Click here to edit or delete your proposal"><form method="POST" action="deleteproposal.php">
	<input type="hidden" name="p" id="p" value="<?php echo $row[0]; ?>" />
	<input type="submit" name="submit" id="submit" value="Edit or Delete" />
</form></span>';
}

function display_fulltext_link2()
{	
	return '<span class="paretoabstractfulltextlink"><a class="expandabstractbtn" href="#" title="Click here to display the full proposal text"><img src="images/fulltext32.png" width="30" height="30" alt="" /><span class="show-full-label">View Full Text</span></a></span>';
}

function display_show_full_text_link_2($str='Show Full Text...')
{
	$intro = 100;
	
	$str = strip_tags($str);
	
	if (empty($str)) 
		$str = 'Show Full Text...';
		
	$str = substr($str, 0, $intro) . '...';
		
	return '<a class="expandbtn" href="#">' . $str . '</a>';
}

function messages_set($message_type=false)
{
	if (!$message_type)
	{
		return isset($_SESSION['messages']);
	}
	else
	{
		return isset($_SESSION['messages'][$message_type]);
	}
}

function set_message($message_type, $message)
{
    $_SESSION['messages'][$message_type][] = $message;
}

function get_messages($message_type='error')
{
    $messages_array = $_SESSION['messages'][$message_type];
    return $messages_array;
}

function get_messages_clr($message_type='error')
{
    $messages_array = $_SESSION['messages'][$message_type];
    unset($_SESSION['messages']);
    return $messages_array;
}

function countMessages($message_type='error')
{
	return count($_SESSION['messages'][$message_type]);
}

function clear_messages()
{
	unset($_SESSION['messages']);
}

function get_message_string($message_type='error')
{
	$msg = get_messages($message_type);
	return implode("<br/>", $msg);
}

function get_message_string_clr($message_type='error')
{
	$msg = get_messages($message_type);
	unset($_SESSION['messages']);
	return implode("<br/>", $msg);
}
// ******************************************
// VILFREDO ROOMS
//
// ******************************************
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

function CreateProposalURL($proposal, $room="")
{
	if (!isset($proposal) or empty($proposal))
             error("Question proposal not set!!!");

	$proposal_url = "?" . QUERY_KEY_PROPOSAL . "=".$proposal;

	// Add room id if not empty
	if (!empty($room))
            $proposal_url .= "&" . QUERY_KEY_ROOM . "=".$room;

	return $proposal_url;
}

function CreateGenerationURL($question,$generation,$room)
{
	#$room=GetQuestionRoom($question);
	if (!isset($question) or empty($question))
             error("Question parameter not set!!!");
	if (!isset($question) or empty($question))
	         error("Generation parameter not set!!!");
	$generation_url= "?" . QUERY_KEY_QUESTION . "=".$question;
	$generation_url.= "&" . QUERY_KEY_GENERATION . "=".$generation;
	// Add room id if not empty
	if (!empty($room))
            $generation_url .= "&" . QUERY_KEY_ROOM . "=".$room;
	return $generation_url;
}


function RenderQIconInfo($username, $room)
{	
	if (!empty($room) && SHOW_QICON_ROOMS)
	{
		return "by $username in <a href=\"" . SITE_DOMAIN . "/viewquestions.php?room=$room\">$room</a>";
	}
	else
	{
		return "by $username";
	}
}

//
// To-Do Lists
//
// Return id's as sql list, eg '34', '45', '78'
function db_make_id_list($id_array)
{
	$id_sql = "";

	for ($i=0; $i < count($id_array); $i++)
	{
		if ($i == count($id_array)-1)
			$id_sql .= "'" . $id_array[$i] . "'";
		else
			$id_sql .= "'" . $id_array[$i] . "', ";
	}
	return $id_sql;
}

function GetQuestioner($question)
{
	$sql = "SELECT `usercreatorid` FROM questions WHERE `id` = '$question'";
	
	$result = mysql_query($sql);
			
	if (!$result)
	{
		handle_db_error($result, $sql);
		return false;
	}
	else {
		$row = mysql_fetch_assoc($result);
		return $row["usercreatorid"];
	}
}

function GetAgreements($question,$generation=0)
{
	if ($generation==0)	{	$generation=GetQuestionGeneration($question);	}
	$generation-=1;
	$Agreements=array();
	while($generation)
	{
		$participants=Endorsers($question,$generation);		
		$sp=Count($participants);
		$pf=ParetoFront($question,$generation);		
		if(Count(EndorsersToAProposal($pf[0]))==$sp)
		{	array_push($Agreements,$generation);	}
		$generation-=1;
	}
	return $Agreements;
}



function PreviousAgreementsStillVisible($question,$generation=0)
{
	if ($generation==0)
	{
		$generation=GetQuestionGeneration($question);		
	}
	$BiggestAgreement=0;
	$generation-=1;
	$AgreementsVisisble=array();
	while($generation)
	{
		$participants=Endorsers($question,$generation);		
		$sp=Count($participants);
		
		if ($sp<=$BiggestAgreement)
		{
			$generation-=1;
			continue;
		}
		$pf=ParetoFront($question,$generation);		
		if(Count(EndorsersToAProposal($pf[0]))==$sp)
		{
			$BiggestAgreement=$sp;
			array_push($AgreementsVisisble,$generation);			
		}
		$generation-=1;
	}
	return $AgreementsVisisble;
}

function GetProposalsInGeneration($question,$generation)
{
	$proposals=array();
	$sql = "SELECT id FROM proposals WHERE experimentid = $question AND roundid = $generation";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($proposals,$row[0]);
	}
	return $proposals;
}

function GetRelatedQuestions($userid)
{
	$related=array();
	
	$sql =  "SELECT questions.id as id FROM questions WHERE `usercreatorid` = '$userid' 
	UNION
	SELECT DISTINCT 
	proposals.experimentid as id FROM proposals WHERE `usercreatorid` = '$userid'
	UNION
	SELECT question as id FROM  updates WHERE user = '$userid'
	UNION
	SELECT question as id FROM invites WHERE receiver = '$userid'
	UNION
	SELECT questions.id as id 
	FROM questions, endorse, proposals
	WHERE 
	endorse.userid = '$userid'
	AND
	endorse.proposalid = proposals.id
	AND
	proposals.experimentid = questions.id ORDER BY id";
	
	#printbr($sql);
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($related,$row[0]);
	}
	#print_array($related);
	return ($related);
}





function HasProposalBeenSuggested($question,$blurb,$abstract,$generation=0)
{
	if ($generation==0)	{	$generation=GetQuestionGeneration($question);}
	$proposals=GetProposalsInGeneration($question,$generation);
	foreach($proposals as $p)
	{
		$sql3 = "SELECT blurb, abstract FROM proposals WHERE id = ".$p." LIMIT 1 ";
		$response3 = mysql_query($sql3);
		while ($row3 = mysql_fetch_array($response3))
		{
			if($row3['blurb']===$blurb AND $row3['abstract']===$abstract)	{return $p;}
		}
	}
	return 0;
}






function SendInvite($userid, $receiver, $question)
{  	     
  	$sql = "INSERT INTO `invites` (sender, receiver, question, creationtime) 
  	VALUES ('$userid', '$receiver', '$question', NOW())";
  
  	mysql_query($sql) or die(mysql_error());
}
// **************************************
function GetQuestionFilterOpen($userid)
{	
	if ($userid and isset($_GET[QUERY_KEY_TODO]))
	{
		$related_ids = db_make_id_list(GetRelatedQuestions($userid));

		if (empty($related_ids))
			$filter = " AND questions.id IN (0) ";
		else
			$filter = " AND questions.id IN ($related_ids) ";
		
		return $filter;
	}
	
	// Get room if set
	$room = GetParamFromQuery(QUERY_KEY_ROOM);
	// Get user if set
	$uid = GetParamFromQuery(QUERY_KEY_USER);
	
	$sameuser = false;
	if ($userid) {
		// Check if user IDs match
		$sameuser = ($uid == $userid);
	}

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

function GetQuestionFilter($userid)
{	
	if (isset($_GET[QUERY_KEY_TODO]))
	{
		$related_ids = db_make_id_list(GetRelatedQuestions($userid));

		if (empty($related_ids))
			$filter = " AND questions.id IN (0) ";
		else
			$filter = " AND questions.id IN ($related_ids) ";
		
		return $filter;
	}
	
	// Get room if set
	$room = GetParamFromQuery(QUERY_KEY_ROOM);
	// Get user if set
	$uid = GetParamFromQuery(QUERY_KEY_USER);
	// Check if user IDs match
	$sameuser = ($uid == $userid);

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

function LoadLoginRegisterLinks($userid, $target, $debug=false) 
{
$str = <<<_HTML_
<div id="register_request" class="ui-widget register-alert">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;"> 
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Note:</strong> You must register to activate the submit button: <a href="#" id="ajax_register" btn=$target>Register</a></p>
	</div>
</div>
_HTML_;

$loggedin = (bool)$userid;

if ($debug) {
	set_log('LoadLoginRegisterLinks: User logged in? : ' . boolString($loggedin));
}

return (!$loggedin && false) ? $str : ''; 
}

function LoadGoogleAnalytics($display=true) 
{
	$str = <<<_HTML_
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9953942-1");
pageTracker._trackPageview();
} catch(err) {}</script>
_HTML_;

	return (USE_GOOGLE_ANALYTICS && $display) ? $str : ''; 
}

function GetUserAccessFilter($uid)
{	
	// Get logged in ID
	$userid = getCurrentUser();
	// Check if user IDs match
	$is_current_user = ($userid == $uid);
	
	$filter = "";
	
	#if (USE_PRIVACY_FILTER)
	#{	
		if (!$is_current_user)
		{
			$filter=" AND (questions.usercreatorid = '$uid' AND questions.room = '') ";
		}
	#}
	
	return $filter;
}

function GetRoomAccessFilter($userid, $room='')
{	
	// Get logged in ID
	$current_user = getCurrentUser();
	// Check if user IDs match
	$is_current_user = ($current_user == $userid);
	
	#if (USE_PRIVACY_FILTER)
#	{	
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
	#}
#	else
#	{
#		$filter = "";
#	}
	
	return $filter;
}

function inputIsSafe($input)
{
	if ( preg_match("/[<>;]/", $input) )
	{
		return false;
	}
	else
	{
		return true;
	}
}

function stripBad($input)
{
	if (!empty($input))
	{
		$input = trim($input);
		$input = strip_tags($input);
		$input = ereg_replace("<|;.*", "", $input );
	}
	return $input;
}

function FormatInputString($input)
{
	// Alpha-numeric characters and underscores only.
	if (!empty($input))
	{
		$input = trim($input);
		$input = strip_tags($input);
		$input = ereg_replace("[^A-Za-z0-9_[:space:]]", "", $input );
	}
	return $input;
}

function FormatRoomId($room)
{
	// Alpha-numeric characters and underscores only.
	if (!empty($room))
	{
		$room = trim($room);
		$room = strip_tags($room);
		$room = ereg_replace("[^A-Za-z0-9_[:space:]]", "", $room );
		$room = str_replace(" ", "_", $room);
	}
	return $room;
}

function GetRoomList()#this room is a threat to security. It just shouldn't be there at all
{
	$sql = "SELECT DISTINCT room FROM `questions` 
	WHERE room != '' AND room NOT LIKE '\_%'";
	$result = mysql_query($sql) or die(mysql_error());
	$rooms = mysql_fetch_array($result);
	
	return $rooms;
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

function HasProposalAccess()
{
	$proposal = "";
	$room = "";

	if (isset($_GET[QUERY_KEY_PROPOSAL]))
             $proposal = $_GET[QUERY_KEY_PROPOSAL];
	else
            return false;

	$room_id = GetProposalRoom($proposal);

	if (isset($_GET[QUERY_KEY_ROOM]))
		$room = $_GET[QUERY_KEY_ROOM];
	else
		$room = "";


	if (empty($room_id) or ($room == $room_id))
            return true;
	else
            return false;
}

function add_querystring_var($url, $key, $value) 
{
	$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	if (strpos($url, '?') === false) 
	{
		return ($url . '?' . $key . '=' . $value);
	} 
	else 
	{
		return ($url . '&' . $key . '=' . $value);
	}
}

function remove_querystring_var($url, $key) 
{
	$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	return ($url);
}

function HasQuestionAccess()
{
	$question = "";
	$room = "";

	if (isset($_GET[QUERY_KEY_QUESTION]))
             $question = $_GET[QUERY_KEY_QUESTION];
	else
            return false;

	$room_id = GetQuestionRoom($question);

	if (isset($_GET[QUERY_KEY_ROOM]))
		$room_param = $_GET[QUERY_KEY_ROOM];
	else
		$room_param = "";
	
	// rooms are case sensitive
	if ($room_param == $room_id)
	// make roome id comparison case insensitive
	//if(strcasecmp($room_param, $room_id) == 0)
		return true;
	else
        	return false;
}


function GetOriginalProposal($proposal)
{
	$sql="SELECT source, roundid FROM proposals WHERE proposals.id = '$proposal'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if ($row['source']==0)
	{
		$result=array();
		$result['proposalid']=$proposal;
		$result['generation']=$row['roundid'];
		return $result;
	}
	else
	{
		return GetOriginalProposal($row['source']);
	}
}


function GetProposalGeneration($proposal)
{
	$sql="SELECT roundid FROM proposals WHERE proposals.id = '$proposal'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row['roundid'];
}

function GetProposalAuthor($proposal)
{
	$sql="SELECT usercreatorid FROM proposals WHERE proposals.id = '$proposal'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row['usercreatorid'];
}







function GetProposalDaughter($proposal)
{
	$daughter=0;
	$sql="SELECT id FROM proposals WHERE proposals.source = '$proposal'";
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_array($result))
	{
		$daughter=$row[0];
	}

	return $daughter;
}


function GetProposalQuestion($proposal)
{
	 $sql="SELECT experimentid FROM proposals WHERE proposals.id = '$proposal'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row['experimentid'];
}





function GetProposalRoom($proposal)
{
	 $sql="SELECT room FROM proposals, questions 
	 WHERE proposals.experimentid = questions.id 
	 AND proposals.id = '$proposal'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	return $row['room'];
}

function GetQuestionRoom($question)
{
	 $sql="SELECT room
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	return $row['room'];
}

function GetQuestionGeneration($question)
{
	 $sql="SELECT roundid
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	return $row['roundid'];
}


function GetQuestionTitle($question)
{
	 $sql="SELECT title
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	return $row['title'];
}

function IsQuestionWriting($question)
{
	 $sql="SELECT phase
	     FROM questions
	     WHERE id=$question";
		
	$result = mysql_query($sql);
				
	if (!$result)
	{
		db_error($sql);
		return false;
	}
	else 
	{
		$row = mysql_fetch_assoc($result);
		if ($row["phase"] == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

function GetQuestion($question)
{
	 $sql="SELECT *
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$info = mysql_fetch_assoc($result);

	return $info;
}

// Returns empty string if no room set,
// or room id as a string
function GetRoom($question)
{
	 $sql="SELECT room
	     FROM questions
	     WHERE id='$question'";

	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($result);

	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
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
	$row = mysql_fetch_array($result);

	$creator=$row[0];

	return $creator;
}

function display_logout_link()
{
	/*
	if ($_SESSION[USER_LOGIN_MODE] == 'FB') 
	{
		return facebook_logout_link('fb_logout.php', 'Logout of Facebook'); 
	}
	else
	{
		return '<a href="logout.php">Logout</a>';
	}*/
	return '<a href="logout.php">Logout</a>';
}

function getpostloginredirectlink()
{
	if (isset($_SESSION['request'] )) 
	{
		// Now send the user to his desired page
		$request = $_SESSION['request'];
		unset($_SESSION['request']);
		return $request;
	}
	else 
	{
		return "viewquestions.php";
	}
}

function make_URI_absolute($loc)
{
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	return "http://$host$uri/$loc";
}

function postloginredirect()
{
	if (isset($_SESSION['request'] )) 
	{
		// Now send the user to his desired page
		$request = $_SESSION['request'];
		unset($_SESSION['request']);
		#return $request;
		header("Location: " . $request);
	}
	else 
	{
		#return "viewquestions.php";
		header("Location: viewquestions.php");
	}
}

function GetRequestString()
{
	return array_pop(explode('/', $_SERVER[REQUEST_URI]));
}

function GetRequest($location="viewquestions.php")
{
	//then redirect them to the members area
	if (isset($_SESSION['request'] )) 
	{
		// Now send the user to his desired page
		$request = $_SESSION['request'];
		unset($_SESSION['request']);
		header("Location: " . $request);
	}
	else {
		header("Location: ".$location);
	}
}

function SetRequest($location="viewquestions.php")
{
	if (DEBUG) {
		unset($_SESSION['request']);
		header("Location: ".$location);
	}
	else {
		// Store user's request for after login
		$_SESSION['request'] = array_pop(explode('/', $_SERVER[REQUEST_URI]));
		header("Location: ".$location);
	}
}

function DoLogin()
{
	if (DEBUG) {
		unset($_SESSION['request']);
		header("Location: login.php");
	}
	else {
		// Store user's request for after login
		$_SESSION['request'] = array_pop(explode('/', $_SERVER[REQUEST_URI]));
		header("Location: login.php");
	}
}

function fb_user_logout()
{
	if (IsAuthenticated() && $_SESSION[USER_LOGIN_MODE] == 'FB')
	{
		unset($_SESSION[USER_LOGIN_ID]);
		unset($_SESSION[USER_LOGIN_MODE]);
	}
}

// Workaround to detect invalid Facebook session
function get_current_facebook_userid($fb)
{
	try 
	{
		// Test the current Facebook session with API call
		$fb->api_client->users_isAppUser();
		// Session valid, so get Facebook ID
		$fb_uid = $fb->get_loggedin_user();
		return $fb_uid;
	} 
	catch (FacebookRestClientException $e) 
	{
		// Exception thrown, session invalid
		return null;
	}
}

function getCurrentUser()
{            
	return IsAuthenticated();
}

// Salt Generator
function generate_salt()
{ 
     // Declare $salt
     $salt = '';

     // And create it with random chars
     for ($i = 0; $i < 3; $i++)
     { 
          $salt .= chr(rand(35, 126)); 
     } 
          return $salt;
}

function unsetcookies()
{
	// Unset old-style cookies
	$past = time() - TWO_DAYS;
	setcookie(COOKIE_USER, 'DELETED', $past);
	setcookie(COOKIE_PASSWORD, 'DELETED', $past);
}

function user_logout()
{
	if (IsAuthenticated())
	{	
		unset($_SESSION[USER_LOGIN_ID]);
		unset($_SESSION[USER_LOGIN_MODE]);
		
		// Unset any cookies
		vga_cookie_logout();
	}
}

function isadminonly($userid)
{
	if (ADMIN_ACCESS_ONLY)
	{
		if (isAdmin($userid))
			return $userid;
		else
			return false;
	}
	else
	{
		return $userid;
	}
}

function setlogintime($user)
{
	$sql = "UPDATE users 
	SET lastlogin = NOW()
	WHERE id = $user";
	
	if (!$result = mysql_query($sql))
	{
		db_error($sql);
		return false;
	}
	else
	{
		return true;
	}
}

function IsAuthenticated()
{            
        return (isset($_SESSION[USER_LOGIN_ID])) ? $_SESSION[USER_LOGIN_ID] : false;
}

function isloggedin()
{
	global $FACEBOOK_ID;

	// First check if user has a current login session
        $userid = IsAuthenticated();
	if ($userid)
	{
		// verify facebook session
		if ($_SESSION[USER_LOGIN_MODE] == 'FB')
		{
		 	if (is_null($FACEBOOK_ID))
		 	{
		 		//fb_user_logout();
		 		user_logout();
		 		return false;
		 	}
		}
		//$userid = isadminonly($userid);
		return $userid;
	}
	// Check if logging in
	elseif (isset($_POST['user_login_action']))
	{
		return false;
	}
	// Check if logging out
	elseif (isset($_SESSION['logout']))
	{
			unset($_SESSION['logout']);
			return false;
	}
	// Check of user has opted for permenant login
	elseif ($userid = vga_cookie_login())
	{
		//$userid = isadminonly($userid);
		if ($userid)
		{
			$_SESSION[USER_LOGIN_ID] = $userid;
			$_SESSION[USER_LOGIN_MODE] = 'VGAP';
			// log time
			setlogintime($userid);
		}
		return $userid;
	}
	// Finally check if a current Facebook session is available for a connected account
	/*
	elseif ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
	{
		//$userid = isadminonly($userid);
		if ($userid)
		{
			$_SESSION[USER_LOGIN_ID] = $userid;
			$_SESSION[USER_LOGIN_MODE] = 'FB';
			// log time
			setlogintime($userid);
		}
		return $userid;
	}*/
	// Else return false so the user can be redirected to the login page
	else
	{
		return false;
	}
}


function StringSafe($stringOriginal)
{
	$newstring=strip_tags($stringOriginal);
	$newstring=str_replace ( '"' , "'" , $newstring );
	$newstring=str_replace ( "&nbsp;" , " " , $newstring );
	$newstring=str_replace ( "\r\n" , " " , $newstring );
	$newstring=str_replace ( "\n" , " " , $newstring );
	$newstring=str_replace ( "\r" , " " , $newstring );
	$newstring=str_replace ( "  " , " " , $newstring );
	$newstring=str_replace ( "  " , " " , $newstring );
	$newstring=str_replace ( "  " , " " , $newstring );
	$newstring=str_replace ( "  " , " " , $newstring );
	$newstring = preg_replace('/[^(\x20-\x7F)]*/','', $newstring);#Thanks http://www.stemkoski.com/php-remove-non-ascii-characters-from-a-string/
	return $newstring;
}

function SafeStringProposal($proposal)
{
	 $sql="SELECT blurb, abstract FROM proposals, questions WHERE proposals.experimentid = questions.id AND proposals.id = '$proposal'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$abstract=$row['abstract'];
	$blurb=$row['blurb'];
	if ($abstract)
	{
		return StringSafe($abstract);	
	}
	else
	{
		return StringSafe($blurb);
	}
}


//******************************************************************/
//			START: Persistant Cookies
//
//******************************************************************/
function delete_vga_cookie_entry($userid, $token)
{
	set_log("delete_vga_cookie_entry: Deleting entry $userid $token");
	
	$sql = "DELETE FROM user_persist_tokens	
	            WHERE  userid = $userid AND token = '$token'";
		
	$result = mysql_query($sql);

	if (!$result)
	{
		handle_db_error($result, $sql);
	}
}

// returns true if the user is logged in
function vga_cookie_login()
{	
	// unset old-style cookies
	//unsetcookies();
	if (isset($_COOKIE[VGA_PL]))
	{		
		$clean = array();
    		$mysql = array();
    		$now = time();
    		$past = time() - TWO_DAYS;
		
		list($identifier, $token) = explode(':', $_COOKIE[VGA_PL]);
		if (ctype_alnum($identifier) && ctype_alnum($token))
		{
			$clean['identifier'] = $identifier;
			$clean['token'] = $token;
		}
		else
		{
			return false;
		}
				
		$mysql['identifier'] = mysql_real_escape_string($clean['identifier']);
		$mysql['token'] = mysql_real_escape_string($clean['token']);
		
		 $sql = "SELECT userid, token, timeout		
		            FROM   user_persist_tokens
            		    WHERE  userid = '{$mysql['identifier']}' AND token = '{$mysql['token']}'";
	
		$result = mysql_query($sql);
		
		if (!$result)
		{
			handle_db_error($result, $sql);
			return false;
		}
		
		if ($row = mysql_fetch_assoc($result))
		{
			if ($now > $row['timeout'])
			{
				// cookie expired - delete it
				//set_log('Invalid cookie: expired');
				setcookie(VGA_PL, 'DELETED', $past);
				delete_vga_cookie_entry($mysql['identifier'] , $mysql['token']);
				return false;
			}
			else
			{
				// *** valid cookie found ***
				// update PL cookie with new token and max expiry date
				//set_log('resetting cookie');
				resetpersistantcookie($clean['identifier'], $clean['token']);
				// return userid
				return $clean['identifier'];
			}
		}
		
		// invalid token - ignore it and return false
		return false;
	}
	else	//if the cookie does not exist, they are taken to the login screen
	{
		//set_log('No cookie found');
		return false;
	}
}

//*** Temp function to deal with old-style persistant cookies */
function temp_check_for_oldstyle_cookies()
{
	if(isset($_COOKIE[COOKIE_USER]))
	{
		$username = $_COOKIE[COOKIE_USER];
		$pass = $_COOKIE[COOKIE_PASSWORD];
		
		$clean = array();
    		$mysql = array();
		
		if (ctype_alnum($pass) && inputIsSafe($username))// scan username for <, >, and ;
		{
			$clean['username'] = $username;
			$clean['pass'] = $pass;
		}
		else
		{
			return false;
		}
		
		$mysql['username'] = mysql_real_escape_string($clean['username']);		
		
		$sql = "SELECT * FROM users WHERE username = '{$mysql['username']}'";
		
		$result = mysql_query($sql);
		
		if (!$result)
		{
			handle_db_error($result, $sql);
			return false;
		}
		
		while($info = mysql_fetch_array( $result ))
		{
			//if the cookie has the wrong password, they are taken to the login page
			if ($pass != $info['password'])
			{
				return false;
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

function vga_cookie_logout()
{
	if(isset($_COOKIE[VGA_PL]))
	{
		$clean = array();
    		$mysql = array();
    		$now = time();
    		$past = time() - TWO_DAYS;
		
		list($identifier, $token) = explode(':', $_COOKIE[VGA_PL]);
		if (ctype_digit($identifier) && ctype_alnum($token))
		{
			$clean['identifier'] = $identifier;
			$clean['token'] = $token;
		}
		else
		{
			return false;
		}
		
		//set_log('log out: deleting cookie: user ' . $clean['identifier']);
		
		// delete cookie
		setcookie(VGA_PL, 'DELETED', $past);
		
		$mysql['identifier'] = mysql_real_escape_string($clean['identifier']);
		$mysql['token'] = mysql_real_escape_string($clean['token']);
		
		delete_vga_cookie_entry($mysql['identifier'] , $mysql['token']);
		
		//set_log('deleted PL token: user ' . $clean['identifier']);
		
		return true;
	}
	
	else 
	{
		// no cookie found
		//set_log("vga_cookie_logout(): no cookie found");
		return true;
	}
}

function setpersistantcookie($userid)
{	
	$token = generateTOKEN();
	$expire = time() + COOKIE_LIFETIME;
	
	//set_log("setpersistantcookie(): $userid:$token");

	$sql = "INSERT INTO user_persist_tokens (userid, token, timeout)
		VALUES ($userid, '$token', $expire)";

	$add_ptoken = mysql_query($sql);

	if ($add_ptoken)
	{
		//set_log("setting cookie:  $userid:$token");
		setcookie(VGA_PL, "$userid:$token", $expire);
	}
	else
	{
		handle_db_error($add_ptoken, $sql);
	}
}

function resetpersistantcookie($userid, $old_token)
{	
	$new_token = generateTOKEN();
	$expire = time() + COOKIE_LIFETIME;

	//set_log("resetpersistantcookie(): $userid:$old_token => $new_token");

	$sql = "UPDATE user_persist_tokens SET 
		token = '$new_token',
		timeout = $expire 
		WHERE userid = $userid AND token = '$old_token'";

	$update_ptoken = mysql_query($sql);

	if ($update_ptoken)
	{
		//set_log("updating cookie:  $userid:$new_token");
		setcookie(VGA_PL, "$userid:$new_token", $expire);
	}
	else
	{
		handle_db_error($update_ptoken, $sql);
	}
}
//******************************************************************/
//
//			END: Persistant Cookies
//******************************************************************/

// returns true if the user is logged in
function fb_user_login($fb_uid)
{
	// Return the local user ID conected to the Facebook account
	return fb_isconnected($fb_uid);
}

// Return user ID of connected account
function fb_isconnected($fb_uid)
{
	if ($fb_uid)
	{
		$sql = "SELECT id FROM users WHERE fb_userid = '$fb_uid'";
		$response = mysql_query($sql) or die(mysql_error());

		if (mysql_num_rows($response) > 0)
		{
			$user = mysql_fetch_assoc($response);
			return $user['id'];
		}
		else 
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

// Return user details of connected account
function fb_getuserdetails($fb_uid)
{
	$sql = "SELECT * FROM users WHERE fb_userid = '$fb_uid'";
	$response = mysql_query($sql) or die(mysql_error());
	
	if (mysql_num_rows($response) > 0)
		return mysql_fetch_assoc($response);
	else 
		return false;
}

// Return user details of connected account
function getuserdetails($userid)
{
	$sql = "SELECT * FROM users WHERE id = '$userid'";
	$response = mysql_query($sql) or die(mysql_error());
	
	if (mysql_num_rows($response) > 0)
		return mysql_fetch_assoc($response);
	else 
		return false;
}

// Return user details of connected account
function get_session_username()
{
	if (!IsAuthenticated())
	{
		error('invalid user id / session');
	}	
	$userid = $_SESSION[USER_LOGIN_ID];
	$sql = "SELECT username FROM users WHERE id = '$userid'";
	$response = mysql_query($sql) or die(mysql_error());
	if ($info = mysql_fetch_assoc($response))
	{
		return $info['username'];
	}
	else
	{
		log_error('Unable to retrieve username for user $userid');
		return false;
	}
}
// ***********************************************/
function longestproposal()
{
	$sql = "SELECT username as user, proposals.id, blurb, LENGTH(blurb) as length  
		FROM proposals, users
		WHERE proposals.usercreatorid = users.id
		ORDER BY LENGTH(blurb) DESC LIMIT 1 ";
		
	$result = mysql_query($sql) or die(mysql_error());
	$proposal = mysql_fetch_assoc($result);
	$proposal['blurb'] = strip_tags($proposal['blurb'] );
	$proposal['text'] = strlen($proposal['blurb']);
	$proposal['html'] =  $proposal['length'] - $proposal['text'];
	
	$proposal['blurb'] = substr($proposal['blurb'], 0, 50 ) . '...';
	
	return $proposal;
}

/* *
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(check_dnsrr($domain,"MX") || check_dnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

function GetAncestorEndorsements($source, $userid, $question, $generation)
{
	$ancestors = GetAncestors($source);	
	$results = GetEndorsements($userid, $ancestors, $question, $generation);
	$ancestorendorsements = array_reverse($results);
	return $ancestorendorsements;
}

function GetEndorsements($userid, $proposals, $question, $generation)
{
	$endorsements = array();
	
	foreach ($proposals as $proposal)
	{
		$endorsement['proposal'] = $proposal;
		$generation--;
		$endorsement['generation'] = $generation;
		
		if (HasThisUserEndorsedSomething($question, $generation, $userid) == 0)
		{
			$endorsement['endorsed'] = -1;
		}
		else
		{
			$sql = "SELECT  id FROM endorse WHERE 
			userid = $userid AND proposalid = $proposal LIMIT 1";

			$response = mysql_query($sql);

			if (!$response)
			{
				handle_db_error($response);
				log_error("GetEndorsements(): MySQL error: " . mysql_error());
				return false;
			}

			if(mysql_fetch_assoc($response))
			{
				$endorsement['endorsed'] = 1;
			}
			else
			{
				$endorsement['endorsed'] = 0;
			}
		}
		
		$endorsements[] = $endorsement;
	}
	
	return $endorsements;
}

function GetAncestors($source)
{
	$ancestors = array();
	
	if ($source == 0)
	{
		return $ancestors;
	}

	while ($source != 0)
	{		
		$sql = "SELECT id as proposal, source from proposals
		WHERE id = $source";
		
		$response = mysql_query($sql);
		
		if (!$response)
		{
			handle_db_error($response);
			log_error("GetAncestors(): MySQL error: " . mysql_error());
			return false;
		}
		
		$info = mysql_fetch_assoc($response);
		$ancestors[] = $info['proposal'];
		$source = $info['source'];
	}

	return $ancestors;
}

function CountAllProposals($question)
{
	$sql = "SELECT COUNT(id) as proposals FROM proposals WHERE experimentid = $question";
	$response = mysql_query($sql);
	$row = mysql_fetch_assoc($response);
	return $row['proposals'];
}

function CountProposals($question,$generation)
{
	$sql = "SELECT * FROM proposals WHERE experimentid = ".$question." and roundid = ".$generation."";
	$n=0;
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$n+=1;
	}
	return $n;
}


function CountEndorsersToAProposal($proposal)
{
	$sql = "SELECT DISTINCT userid FROM endorse WHERE proposalid = ".$proposal." ";
	$m=mysql_query($sql); #echo "P=".$proposal."M=".$m;	
	return mysql_num_rows($m);
}

function EndorsersToAProposal($proposal)
{
	$endorsers=array();
	$sql = "SELECT DISTINCT userid FROM endorse WHERE proposalid = ".$proposal." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($endorsers,$row[0]);
	}
	return array_unique($endorsers);

}
function ProposalsToAnEndorser($user,$question,$generation)
{
	$proposals=array();	
	$sql = "SELECT DISTINCT endorse.proposalid 
	FROM proposals, endorse 
	WHERE proposals.experimentid = ".$question." and proposals.roundid = ".$generation." and proposals.id = endorse.proposalid and endorse.userid= ".$user." ";
	
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($proposals,$row[0]);
	}
	return $proposals;

}


function HasThisUserEndorsedSomething($question,$generation,$user)
{
	$proposals=ProposalsInGeneration($question,$generation);
	foreach ($proposals as $proposal)
	{
		if(in_array($user,EndorsersToAProposal($proposal)))
		{
			return 1;
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
	while ($row = mysql_fetch_array($response))
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
	while ($row = mysql_fetch_array($response))
	{
		array_push($proposals,$row[0]);
	}
	return $proposals;
}

function AuthorsOfInheritedProposals($question,$generation)
{
	$authors=array();
	$sql = "SELECT usercreatorid FROM proposals WHERE experimentid = ".$question." and roundid = ".$generation." and source > 0";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($authors,$row[0]);
	}
	return array_unique($authors);
}

function AuthorOfProposal($proposal)
{
	$authors=array();
	$sql = "SELECT usercreatorid FROM proposals WHERE id = ".$proposal." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$author=$row[0];
	}
	return $author;
}



function AuthorsOfNewProposals($question,$generation)
{
	$authors=array();
	$sql = "SELECT usercreatorid FROM proposals 
	WHERE experimentid = $question AND roundid = $generation AND source = 0";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($authors,$row[0]);
	}
	return array_unique($authors);
}

function RoomsUsed($userid)
{
	$rooms=array();
	$sql = "SELECT questions.room  FROM questions WHERE questions.usercreatorid = ".$userid." ";
	#	echo $sql;
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($rooms,$row[0]);
	}
	
	$sql = "SELECT questions.room  FROM questions, proposals WHERE proposals.usercreatorid = ".$userid." AND proposals.experimentid = questions.id  ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($rooms,$row[0]);
	}

	$sql = "SELECT questions.room  FROM questions, proposals, endorse WHERE endorse.userid = ".$userid." AND endorse.proposalid=proposals.id AND proposals.experimentid = questions.id ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($rooms,$row[0]);
	}

	$sql = "SELECT questions.room  FROM questions, updates WHERE updates.user = ".$userid." AND updates.question = questions.id  ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($rooms,$row[0]);
	}
	
	return array_unique($rooms);
}


function WhereHaveWeMet($RoomsUser1,$RoomsUser2)
{
	$Intersect=array();
	if (empty($RoomsUser1) OR empty($RoomsUser2)) 
	{	
		return $Intersect;
	}

	$Intersect=array_intersect ( $RoomsUser1, $RoomsUser2);
	return $Intersect;
}

//*****************************/
//  People who have endorsed something 
// in that generation of that question
//*****************************/

function Endorsers($question,$generation)
{
	$authors=array();
	$sql = "SELECT endorse.userid  FROM endorse, proposals WHERE proposals.experimentid = ".$question." AND proposals.roundid = ".$generation." AND endorse.proposalid = proposals.id ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
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
	while ($row = mysql_fetch_array($response))
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
					$row2= mysql_fetch_array($response2);

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

							while ($row3 = mysql_fetch_array($response3))
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

			while ($row3 = mysql_fetch_array($response3))
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

function WriteUserName($user)
{
	$sql = "SELECT  users.username FROM users WHERE  users.id = " .$user. " ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$answer= $row[0];
	}

	return $answer;
}


function WriteUser($user)
{
	$sql = "SELECT  users.username, users.email FROM users WHERE  users.id = " .$user. " ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
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
	while ($row = mysql_fetch_array($response))
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

function WriteProposalOnlyContent($p,$question)#,$generation,$room,$userid)
{
#	$OriginalProposal=GetOriginalProposal($p);
#	$OPropID=$OriginalProposal["proposalid"];
#	$OPropGen=$OriginalProposal["generation"];
	
	$sql3 = "SELECT blurb, abstract FROM proposals WHERE id = ".$p." LIMIT 1 ";
	$response3 = mysql_query($sql3);
	while ($row3 = mysql_fetch_array($response3))
	{
		if (!empty($row3['abstract'])) {
			echo '<div class="paretoabstract">';
			echo display_fulltext_link();
			echo '<h3>Proposal Abstract</h3>';
			echo $row3['abstract'] ;
			echo '</div>';
			echo '<div class="paretotext">';
			echo $row3['blurb'];
			echo '</div>';
		}
		else {
			echo '<div class="paretofulltext">';
			echo $row3['blurb'] ;
			echo '</div>';
		}
	}
}

function WriteProposalOnlyText($p,$question,$generation,$room,$userid)
{
	$OriginalProposal=GetOriginalProposal($p);
	$OPropID=$OriginalProposal["proposalid"];
	$OPropGen=$OriginalProposal["generation"];
	
	$sql3 = "SELECT blurb, abstract FROM proposals WHERE id = ".$p." LIMIT 1 ";
	$response3 = mysql_query($sql3);
	while ($row3 = mysql_fetch_array($response3))
	{
		if (!empty($row3['abstract'])) {
			echo '<div class="paretoabstract">';
			echo display_fulltext_link();
			echo '<h3>Proposal Abstract</h3>';
			echo $row3['abstract'] ;
			echo '</div>';
			echo '<div class="paretotext">';
			echo '<h3>'.WriteProposalPage($p,$room).'</h3>';
			echo $row3['blurb'];
			echo '</div>';
		}
		else {
			echo '<div class="paretofulltext">';
			echo '<h3>'.WriteProposalPage($p,$room).'</h3>';
			echo $row3['blurb'] ;
			echo '</div>';
		}
	}
}

function WriteEndorsersToAProposal($p,$userid)
{
	$endorsers=EndorsersToAProposal($p);
	echo '<br />Endorsed by: ';
	foreach($endorsers as $e)
	{
		echo WriteUserVsReader($e,$userid);
	}
}

function WriteAuthorOfAProposal($p,$userid,$generation,$question,$room)
{
	$OriginalProposal=GetOriginalProposal($p);
	$OPropID=$OriginalProposal["proposalid"];
	$OPropGen=$OriginalProposal["generation"];
		
	echo '<br />Written by: '.WriteUserVsReader(AuthorOfProposal($p),$userid);
	if ($OPropGen!=$generation)
	{
		echo "in ".WriteGenerationPage($question,$OPropGen,$room).".<br>";						
	}
}

function WriteProposalText($p,$question,$generation,$room,$userid)
{	
	WriteProposalOnlyText($p,$question,$generation,$room,$userid);
	WriteAuthorOfAProposal($p,$userid,$generation,$question,$room);
	WriteEndorsersToAProposal($p,$userid);
}



function WriteProposalRelation($proposal,$question,$generation,$userid,$room)
{
	$answer="";
	$RelatedProposals=CalculateProposalsRelationTo($proposal,$question,$generation);
	$DominatedProposals=$RelatedProposals["dominated"];
	$DominatingProposals=$RelatedProposals["dominating"];

	$sizeof_Below = sizeof($DominatedProposals);
	if ($sizeof_Below>0)
	{
		$answer.="The proposal dominated ";
		foreach ($DominatedProposals as $p)
		{
			$answer.=WriteProposalNumber($p,$room);
			#echo WriteWhyDomination($proposal,$p,$room,$userid);
		}
	}
	else
	{
		$answer.="The proposal did not dominate any other proposal, ";
	}
			
	$sizeof_Above = sizeof($DominatingProposals);
	if ($sizeof_Above>0)
	{
		$answer.="<br />and was dominated by ";
		foreach ($DominatingProposals as $p)
		{
			$answer.=WriteProposalNumber($p,$room);
			#echo WriteWhyDomination($p,$proposal,$room,$userid);
		}
		$answer.="<br>";			
	}
	else
	{
		$answer.="<br />and was not dominated by any other proposal,";
		if(CountEndorsersToAProposal($proposal)==CountEndorsers($question,$generation))
		{
			$answer.="<br />also since everybody who participated in that generation voted for it, it represented the reached agreement for the community";
			$answer.="<br />it was, nevertheless copied to the next generation, ".WriteGenerationPage($question,$generation+1,$room).", in case someone wanted to challenge it.";						
		}
		else
		{
			$answer.="<br />thus it was copied to the next generation (".WriteGenerationPage($question,$generation+1,$room).").";			
		}
	}
	return $answer;
}

function WriteProposalNumber($proposal,$room)
{
	$answer="";
	$urlquery = CreateProposalURL($proposal, $room);#echo $urlquery;
	$answer.='<a href="viewproposal.php'.$urlquery.'" title="'.SafeStringProposal($proposal).'">'.$proposal.'</a>';
	$answer.=" ";
	return $answer;
}



function WriteProposalPage($proposal,$room)
{
	return '<a href="viewproposal.php'.CreateProposalURL($proposal,$room).'">Proposal '.$proposal. '</a> ';
}

function WriteWhyDomination($proposalAbove,$proposalBelow,$room,$userid)
{
	$answer="";
	$EAbove=EndorsersToAProposal($proposalAbove);
	$EBelow=EndorsersToAProposal($proposalBelow);
	foreach($EBelow as $e)	{$answer.=WriteUserVsReader($e,$userid).", ";}
	$answer.=" support both ".WriteProposalNumber($proposalAbove,$room)."and".WriteProposalNumber($proposalBelow,$room).", but";
	foreach($EAbove as $e)  {$answer.=WriteUserVsReader($e,$userid).", ";}
	$answer.=" support ".WriteProposalNumber($proposalAbove,$room)." and do not support ".WriteProposalNumber($proposalBelow,$room).". ";
	$answer.="Since the supporters of ".WriteProposalNumber($proposalBelow,$room)." are a subset of the supporters of ".WriteProposalNumber($proposalAbove,$room).", ";
	$answer.=WriteProposalNumber($proposalAbove,$room)." dominates ".WriteProposalNumber($proposalBelow,$room).". Q.E.D.";
	return $answer;
}


function WriteGenerationPage($question,$generation,$room)
{
	return '<a href="vg.php'.CreateGenerationURL($question,$generation,$room).'">Generation '.$generation. '</a> ';
}

function TimeLastProposalOrEndorsement($question, $phase, $generation)
{
	if ($phase)
	{
		$sql = "SELECT endorse.endorsementdate FROM proposals, endorse WHERE proposals.experimentid = ".$question." and proposals.roundid = ".$generation." and proposals.id = endorse.proposalid  ORDER BY endorse.endorsementdate  LIMIT 100 ;";
		$response = mysql_query($sql);
		while ($row = mysql_fetch_array($response))
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
		while ($row = mysql_fetch_array($response))
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
	{
		return false;
	}
	
	if ($generation==1)
	{
		return false;
	}
	
	$nAuthorsNewProposals = count(AuthorsOfNewProposals($question,$generation));
	
	if ($nAuthorsNewProposals > 0)
	{
		return false;
	}
	
	$pastgeneration = $generation - 1;
	$nrecentendorsers = CountEndorsers($question,$pastgeneration);
	$nrecentparetofront = count(ParetoFront($question,$pastgeneration));

	$sql2 = "SELECT id,source  FROM proposals 
	WHERE experimentid = $question AND roundid = $pastgeneration AND dominatedby = 0 ";
	$row2= mysql_fetch_array(mysql_query($sql2));
	
	if(!$row2)
	{
		return false;
	}
	
	if ($nrecentendorsers==CountEndorsersToAProposal($row2[0]))
	{
		return true;
	}
	
	return false;
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
		$row = mysql_fetch_array(mysql_query($sql));
		$timefrom=strtotime( $row[0] );
	}

	$sql = "SELECT questions.minimumtime FROM questions WHERE questions.id = ".$question." ;";
	$row = mysql_fetch_array(mysql_query($sql));
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
		$row = mysql_fetch_array(mysql_query($sql));
		$timefrom=strtotime( $row[0] );
	}
	$sql = "SELECT questions.maximumtime FROM questions WHERE questions.id = ".$question." ;";
	$row = mysql_fetch_array(mysql_query($sql));
	$timepassed=time()-$timefrom;
	if ($timepassed>$row[0])
	{	return 1; }
	return 0;
}


 //Find position of Nth occurance of search string
function strposOffset($string, $search, $count)
{
    $arr = explode($search, $string);
    switch( $count )
    {
        case $count == 0:
        return false;
        break;
    
        case $count > max(array_keys($arr)):
        return false;
        break;

        default:
        return strlen(implode($search, array_slice($arr, 0, $count)));
    }
}

 //Limit word count in a string
function limitWordCount($string, $count)
{
	//$str = strip_tags($string);
	$arr = explode(' ', trim($string));
	
	if ($arr === false or count($arr) == 0)
	{
		return false;
	}

	if (count($arr) < $count)
	{
		return $string;
	}
	else
	{
		$limited_length = implode(' ', array_slice($arr, 0, $count));
		return $limited_length . ' .....';
	}
}

function CreateRSSLink()
{
	$rss_link = SITE_DOMAIN . '/rss.php';
	$room = GetParamFromQuery(QUERY_KEY_ROOM);
	if($room)
	{
		$rss_link .= "?room=$room";
	}
	
	return $rss_link;
}

function GetShortURL($question)
{
	$hash = GetBitlyHash($question);
	
	if (!hash or empty($hash))
	{
		return false;
	}
	else
	{
		$shorturl = BITLY_URL;
		$shorturl .= $hash;
		return $shorturl;
	}
}

function GetBitlyHash($question)
{	
	
	$sql = "SELECT bitlyhash FROM questions 
		WHERE id = $question";
	
	$result = mysql_query($sql);
	
	if (!result)
	{
		db_error($sql);
		return false;
	}
	
	else 
	{
		$row = mysql_fetch_assoc($result);
		return $row['bitlyhash'];
	}
}

function SetBitlyHash($question, $bitlyhash)
{	
	$sql = "UPDATE questions 
		SET bitlyhash = '$bitlyhash'
		WHERE id = $question";
	
	$result = mysql_query($sql);
	
	if (!$result)
	{
		db_error($sql);
		return false;
	}
	else
	{
		return true;
	}
}

function CreateVGADir($dirname)
{
	if (!is_dir($dirname)) 
	{
		return mkdir($dirname);
	}
	else
	{
		return true;
	}
}

/*
link 21
140 

title 90

New Question
How should we handle the wall of text? Propose at <bit.ly link

Now Voting!
[3] How should we handle the wall of text? 8 Proposals; Vote at <bit.ly link>

Now in Generation 4
[4] How should we handle the wall of text? 3 Hints; propose at <bit.ly link>

Agreement Found!
[5] How should we handle the wall of text? 1 Solution found in 5 steps <bit.ly link>
*/
function UpdateFeed($room='')
{	
	$domain = SITE_DOMAIN;
	$room_param;
	global $bitly_user, $bitly_key;
		
	if (empty($room))
	{
		$room_param = '';
		$room_title = 'Common';
		$room_link = SITE_DOMAIN . "/viewquestions.php";
		$question_link = SITE_DOMAIN . "/viewquestion.php?q=";
	}
	else
	{
		$room_param = "room=$room";
		$room_title = $room;
		$room_link = SITE_DOMAIN . "/viewquestions.php?" . $room_param;
		$question_link = SITE_DOMAIN . "/viewquestion.php?$room_param&q=";
	}
	
	$rss = new UniversalFeedCreator();
	$filename=RSS_DIR ."/" . $room_title . ".xml";
				
	// Use cached file?
	$rss->useCached(RSS_FEED_FORMAT, $filename, RSS_TIMEOUT);
	
	$limit_30day_sql = "SELECT questions.*, users.id AS userid, users.username AS author
		FROM questions, users
		WHERE room = '$room' AND date_sub(now(),interval 30 day) < lastmoveon
		AND questions.usercreatorid = users.id
		ORDER BY lastmoveon DESC";
		
	$sql = "SELECT questions.*, users.id AS userid, users.username AS author
		FROM questions, users
		WHERE room = '$room' AND questions.usercreatorid = users.id
		ORDER BY lastmoveon DESC";
	
	if ($response = mysql_query($sql))
	{
		$rss->title = "Vilfredo goes to Athens - Room: " . $room_title;
		$rss->description = "List of questions currently being addressed in Vilfredo in Room ".$room_title;
		$rss->link = $room_link;
		$rss->cssStyleSheet = "";
		$rss->category = 'eDemocracy';
		$rss->room = $room_title;
		
		$question_status;
		question_status_code;
		/*
		New Question = 0
		Writing = 1
		Voting = 2
		Concensus = 3
		*/

		while ($info = mysql_fetch_assoc($response))
		{
			$num_proposals = CountProposals($info['id'], $info['roundid']);
			
			// Set question status
			if ($info['phase'] == 0)
			{
				$consensus = HasConsensusBeenFound($info['id'], $info['phase'], $info['roundid']);
				$newquestion = ($num_proposals == 0);
				
				if ($consensus)
				{
					$question_status = "Agreement Found!";
					$question_status_code = 3;
				}
				elseif ($newquestion)
				{
					$question_status = "New Question";
					$question_status_code = 0;
				}
				else
				{
					$question_status = "Now in Generation " . $info['roundid'];
					$question_status_code = 1;
				}
			}
			else
			{
				$question_status = "Now Voting!";
				$question_status_code = 2;
			}
			
			// Set content
			$content = '<h3>' . 'Status: ' . $question_status . '</h3>';
			$content .= trim($info['question']);
			
			$item = new FeedItem();
			$item->title = $info['title'];
			$item->link = $question_link . $info['id'];
			$item->guid = $question_link . $info['id'];
			$item->description = $content;
			$item->source = $domain;
			$item->date = convertDateMySQLToRSS($info['lastmoveon']);
			
			//vga specific elemets
			$item->additionalElements["vga:generation"] = $info['roundid'];
			$item->additionalElements["vga:numberProposals"] = $num_proposals;
			$item->additionalElements["vga:status"] = $question_status;
			$item->additionalElements["vga:room"] = $room_title;
			
			// twitter post
			if (ADD_TWITTER_TO_RSS)
			{
				// Only add if shorturl exists
				if ($short_url = GetShortURL($info['id']))
				{					
					// Also add shorturl as an element
					$item->additionalElements["vga:shorturl"] = $short_url;
					
					$twitter_post = make_twitter_post($info['title'], $question_status_code, $info['roundid'], $num_proposals, $short_url);
					
					if ($twitter_post)
					{
						$item->additionalElements["vga:twitter"] = $twitter_post;
					}
				}
				
				// Try bitly
				else
				{
					$long_url = $question_link . $info['id'];
					
					if ($hash = make_bitly_hash($long_url, $bitly_user, $bitly_key))
					{
						// Add hash to DB
						set_log("Adding $hash to " . $info['id']);
						SetBitlyHash($info['id'], $hash);
						$short_url = BITLY_URL;
						$short_url .= $hash;
						// Also add shorturl as an element
						$item->additionalElements["vga:shorturl"] = $short_url;

						$twitter_post = make_twitter_post($info['title'], $question_status_code, $info['roundid'], $num_proposals, $short_url);

						if ($twitter_post)
						{
							$item->additionalElements["vga:twitter"] = $twitter_post;
						}
					}
				}
			}
			
			$rss->addItem($item);
		}
		
		$rss->saveFeed(RSS_FEED_FORMAT, $filename); 
	}
	else
	{
		db_error($sql);
	}
}

function make_twitter_post($title, $status, $generation, $proposals, $link)
{
	/*
	New Question = 0
	Writing = 1
	Voting = 2
	Concensus = 3
	*/
	
	if (strlen($title) > 90)
	{
		$title = substr($title, 0, 90);
	}
	
	switch($status)
	{
		case 0:
	        	return "$title Propose at $link";
        		break;
        	case 1:
			return "[$generation] $title $proposals Hints; Propose at $link";
        		break;
        	case 2:
			return "[$generation] $title $proposals Proposals; Vote at $link";
        		break;
        	case 3:
			return "[$generation] $title $proposals Solutions found in $generation steps $link";
        		break;
        	default:
        		return false;
	}
}

function make_bitly_hash($url, $login, $appkey, $format='xml', $history=1, $version='2.0.1')
{	
	//create the URL
	$bitly = 'http://api.bit.ly/v3/shorten';
	$param = 'version='.$version.'&longUrl='.urlencode($url).'&login='
	.$login.'&apiKey='.$appkey.'&format='.$format.'&history='.$history;

	//get the url
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $bitly . "?" . $param);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	curl_close($ch);

	//parse depending on desired format
	if(strtolower($format) == 'json') 
	{
		// JSON NOT TESTED!!!
		$json = @json_decode($response,true);
		print_r($json);
		exit;
		return $json['data'][$url]['hash'];
	} 
	else 
	{
		$xml = simplexml_load_string($response);
		
		if ($xml->status_code == 200)
		{
			return $xml->data->hash;
		}
		else
		{
			log_error("make_bitly_hash: " . $xml->status_txt);
			return false;
		}
	}
}

function convertDateMySQLToRSS($date)
{
	return date('r', strtotime($date));
}

function SendMails($question)
{
	$EmailsSent=array();

	$sql2 = "SELECT * FROM questions WHERE id = $question";
	$response2 = mysql_query($sql2);
	$row2 = mysql_fetch_array($response2);
	$content=wordwrap($row2[1], 70,"\n",true);
	$round=$row2[2];
	$phase=$row2[3];
	$questioner = $row2['usercreatorid'];
	$title=wordwrap($row2[5], 70,"\n",true);
	$room = $row2[9];
	$urlquery = CreateQuestionURL($question, $room);
	if ($phase == 0)
	{
		$consensus=HasConsensusBeenFound($question,$phase,$round);
		if ($consensus)
		{
			$subject="VgtA, Agreement Found on: ".$title."";
			$message="Hello, \n The question: ".$title."\n\n has just been updated, and we found an agreement!\n Please read the agreed answer here:\n".SITE_DOMAIN."/viewquestion.php".$urlquery." \n If you are not satisfied and want to reopen the question you can do so by proposing something better at:\n".SITE_DOMAIN."/viewquestion.php".$urlquery." ";
		}
		else
		{
			$subject="VgtA: ".$title."";
			$message="Hello, \n The question: ".$title."\n\n has just been updated.\nWe are now in Generation=".$round.".\n\nYou can now see the minimum set of proposal on which everybody agrees.\nIf you think you can propose something better,\nthat would satisfy more people,\nplease do so. Here:\n".SITE_DOMAIN."/viewquestion.php".$urlquery."";
		}
	}
	else
	{
		$subject="VgtA: ".$title."";
		$message="Hello, \n The question: ".$title."\n\n has just been updated.\nWe are now in Generation=".$round.".\n\n You can now see proposals that has been suggested. Please vote on ALL the ones you agree on here:\n".SITE_DOMAIN."/viewquestion.php".$urlquery."";
	}

	
	
	$sql = "SELECT user FROM updates WHERE question = ".$question." and how = 'asap' ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$user=$row[0];
		sendmail($user,$question,$subject,$message);
		array_push($EmailsSent,$user);
		set_log("Mail to ".$user." as for update in question ".$question." because he asked for them");
		
	}
	if ($phase == 0)
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
			set_log("Mail to ".$user." as for update in question ".$question." because he proposed");
			
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
			set_log("Mail to ".$user." as for update in question ".$question." because he endorsed");
			
		}
	}
	else
	{
		$proposers=AuthorsOfNewProposals($question,$round);
		foreach ($proposers as $user)
		{
			if (in_array($user,$EmailsSent))
			{
				continue;
			}
			sendmail($user,$question,$subject,$message);
			array_push($EmailsSent,$user);
			set_log("Mail to ".$user." as for update in question ".$question." because he proposed");
			
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
			set_log("Mail to ".$user." as for update in question ".$question." because he was endorsed");			
		}
		// Also send email to questioner, if not already sent
		if (!in_array($questioner,$EmailsSent))
		{
			sendmail($questioner,$question,$subject,$message);
			array_push($EmailsSent,$questioner);
			set_log("Mail to ".$user." as for update in question ".$question." because he was the question author");
		}
	}
}


function AwareAuthorOfNewProposal($question)
{
	$sql = "SELECT users.username, users.email, questions.title, questions.roundid, questions.phase, questions.room FROM questions, users WHERE questions.id = ".$question." AND questions.usercreatorid = users.id ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
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
		'.SITE_DOMAIN.'/viewquestion.php'.$urlquery.'
		and click on the moveon button.

		Please consider that until you do so,
		no one is allowed to vote on this question,
		and the question will just wait there.
		It is thus very important that after the question has waited enough you move it on.';
		$message=wordwrap  ( $message, 70,"\n",true);
		$result=mail($to,$subject, $message );
		set_log("Mail from AwareAuthorOfNewProposal to ".$username." on new proposal for question ".$question." ");
		
	}
}

function AwareAuthorOfNewEndorsement($question)
{
	$sql = "SELECT users.username, users.email, questions.title, questions.roundid, questions.phase, questions.room FROM questions, users WHERE questions.id = ".$question." AND questions.usercreatorid = users.id ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
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
'.SITE_DOMAIN.'/viewquestion.php'.$urlquery.'
and click on the "moveon" button.

	Please consider that until you do so, no one is allowed to post new solutions to this question, and the question will just wait there. It is thus very important that after the question has waited enough you move it on.';
		$message=wordwrap  ( $message, 70,"\n",true);
		$result=mail($to,$subject, $message );
		set_log("Mail from AwareAuthorOfNewEndorsement to ".$username." on new proposal for question ".$question." ");
		
	}
}

function InviteUserToQuestion($user,$question,$room,$userid)
{
    $question_url = CreateQuestionURL($question,$room);

    $sql = "SELECT users.username FROM users WHERE id = ".$userid." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	$authorusername=$row[0];

	$sql = "SELECT questions.title FROM questions WHERE questions.id = ".$question." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	$title=$row[0];

	$sql = "SELECT users.username, users.email FROM users WHERE id = ".$user." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	$to=$row[1];
	$username=$row[0];

	$subject="VgtA: Invitation for: ".$title."";
	$message='	Dear '.$username.'
user '.$authorusername.' would like to invite you to participate in the question:

'.$title.'

You can do this by going to the page
'.SITE_DOMAIN.'/viewquestion.php'.$question_url.'

If you would like not to receive any more invitations from '.$authorusername.' you can tell him directly.';

		$message=wordwrap  ( $message, 70,"\n",true);
		mail($to,$subject, $message );
		set_log("Mail from ".$authorusername." to ".$username." on new question ".$question." ");
}

function InviteKeyPlayersToRewriteProposals($question,$generation,$room)
{
	$ProposalsCouldDominate=CalculateKeyPlayers($question,$generation);
	
	log_error("Preparing to send invitations".$generation."");
	
	
	if (count($ProposalsCouldDominate) > 0)
	{
		log_error("There are invitations to send");
		
		$KeyPlayers=array_keys($ProposalsCouldDominate);
		foreach ($KeyPlayers as $KeyPlayer)
		{
			InviteKeyPlayerToRewriteProposals($KeyPlayer,$ProposalsCouldDominate[$KeyPlayer],$question,$room);
		}
	}
	
}

function InviteKeyPlayerToRewriteProposals($KeyPlayer,$proposals,$question,$room)
{	
	
	$question_url = CreateQuestionURL($question,$room);

	$sql = "SELECT questions.title FROM questions WHERE questions.id = ".$question." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	$title=$row[0];

	$sql = "SELECT users.username, users.email FROM users WHERE id = ".$KeyPlayer." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	$to=$row[1];
	$username=$row[0];

	$subject="VgtA: You are a Key Player on ".$title."";
	$message='	Dear '.$username.'
	After the last voting session, on the question
	
	'.$title.'
	
	it came out that you are a key player for this round in the game.
	(For an explanation of what a Key Player is, please look at the end of the email)
	
	As such we invite you to look at the following proposals. Those are proposals you did NOT support. 
	Instead we would like you to try to rewrite each of those proposals
	in a way that does not betray too much the aim of the original author,
	but also in a way that you feel you can (and will) support them.
	
	What follows is the list of question(s) that you should work on:
	';

	foreach ($proposals as $proposal)
	{
		$message.=InviteKeyPlayerToRewriteProposal($proposal,$room);
	}
	
	$message.='
	
	Thank you very much. 

	
	Vilfredo goes to Athens is not just a website that invites people to propose solution to open questions, 
	but also proactively tries to help participants look for where they can act for the maximum probability of 
	finding a workable compromise between all the participants.
	
	The search for the Key Players is part of this quest.
	
	For each generation, sometimes, there can be some people which happen to hold the key that can help 
	the whole community in their process of integrating their knowledge. 
	While everybody is invited to generally look for workable compromises, 
	key players are also invited to rewrite specific proposals.
	If the key players are able to produce a new version of a proposal that can satisfy the original supporters
	and satisfy themselves then the new proposal will win over the pervious 
	one and ultimately it will cause the Pareto Front to decrease of one unit. 


	';
	
	$message=wordwrap  ( $message, 70,"\n",true);
	mail($to,$subject, $message );
	
	set_log("Mail from InviteKeyPlayerToRewriteProposals to ".$username." as keyplayer for proposal ".$proposal." in question ".$question." ");
	
	
	
}

function InviteKeyPlayerToRewriteProposal($proposal,$room)
{	
	$urlquery = CreateProposalURL($proposal, $room);
	#Proposal Number <a href="viewproposal.php'.$urlquery.'">'.$proposal.'</a>;
	#If the above link does not work you can access this question at:
	
	$message='
	
	'.SITE_DOMAIN.'/viewproposal.php'.$urlquery.'

	You can also try our experimental new link (do not  hold your breath that it will work) which should send you directly to a page with 
	an edit box and the proposal inside, so you can just change it, and submit it
	'.SITE_DOMAIN.'/npv.php'.$urlquery.'
	
	';
	return $message;
}





function SendMail($user,$question,$subject,$message)
{
	$sql = "SELECT email FROM users WHERE id = ".$user." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$to=$row[0];
		if (!$to) continue;
#		$message=wordwrap  ( $message, 70,"\n",true);
		$message=wordwrap  ( $message, 70,"\n",false);
		mail($to,$subject, $message );
		
	}
}


function MoveOnToWriting($question)
{
	$sql2 = "SELECT phase, roundid FROM questions WHERE id = $question";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_array($response2))
	{
		$phase =	$row2[0];
		$generation= $row2[1];
	}
	if($phase==1)
	{
		$sql = "UPDATE questions 
		SET phase = 0, roundid = roundid + 1, lastmoveon = NOW()
		WHERE id = $question";
		mysql_query($sql);
		
		SelectParetoFront($question);
		
		SendMails($question);
		
		InviteKeyPlayersToRewriteProposals($question,$generation,GetRoom($question));
		
	}
}

function MoveOnToEndorse($question)
{
	$sql2 = "SELECT phase FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_array($response2))
	{
		$phase =	$row2[0];
	}

	if($phase==0)
	{
		$sql = "UPDATE questions 
		SET phase = 1, lastmoveon = NOW() WHERE id = ".$question." ";
		mysql_query($sql);
		SendMails($question);
	}
}

//  ********************************************/
//  This function recovers the ParetoFront of a question for a particular generation
//   This is defined as the proposals that are undominated
//  ********************************************/
function ParetoFront($question,$generation)
{
	$paretofront=array();
	$sql = "SELECT id FROM proposals WHERE experimentid = ".$question." and roundid= ".$generation." and dominatedby = 0";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($paretofront,$row[0]);
	}
	return $paretofront;
}

//  ********************************************/
//A function, that let you recalculate what the pareto front for a particular generation
//it is not that useful since we store that information
//so it is only useful the first time or to check if the pareto front is the same
//   ********************************************/
function CalculateParetoFront($question,$generation)
{
	$proposals=array();
	$dominated=array();
	$done=array(); //the list of the proposals already considered
	$sql = "SELECT id FROM proposals 
		WHERE experimentid = $question AND roundid = $generation";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($proposals,$row[0]);
	}
	foreach ($proposals as $p1)
	{
		array_push($done,$p1);
		foreach ($proposals as $p2)
		{
			if (in_array($p2,$done)) 
			{
				continue;
			}
			$dominating=WhoDominatesWho($p1,$p2);
			if ($dominating==$p1)
			{
				array_push($dominated,$p2);
				$sql = "UPDATE proposals SET dominatedby = ".$p1." WHERE id = ".$p2;
				mysql_query($sql);
				continue;
			}
			elseif ($dominating==$p2)
			{
				array_push($dominated,$p1);
				$sql = "UPDATE proposals SET dominatedby = ".$p2." WHERE id = ".$p1;
				mysql_query($sql);
				break;
			}
		}
	}
	
	$paretofront=array_diff($proposals,$dominated);
	
	return $paretofront;
}

//  ********************************************/
//Given a proposal this function finds all the proposal that dominates it or that are dominated by it
//   ********************************************/
function CalculateProposalsRelationTo($proposal,$question,$generation)
{
	$proposals=array();
	$dominated=array();
	$dominating=array();
	$RelatedProposals=array();
	$sql = "SELECT id FROM proposals WHERE experimentid = $question AND roundid = $generation";
#	echo $sql;
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		array_push($proposals,$row[0]);
	}
	
	foreach ($proposals as $p)
	{
		if ($proposal==$p) 
			{
				continue;
			}
		$WhichIsDominating=WhoDominatesWho($proposal,$p);
		if ($WhichIsDominating==$proposal)
		{
			array_push($dominated,$p);
		}
		elseif ($WhichIsDominating==$p)
		{
			array_push($dominating,$p);
		}
	}
	$RelatedProposals["dominated"]=$dominated;
	$RelatedProposals["dominating"]=$dominating;
	return $RelatedProposals;
}



//  ********************************************/
//  This function stores the calculated pareto front in the database as a new generation
//  it is meant to be used only once, just after the pareto front is calculated
//  ********************************************/
function StoreParetoFront($question,$generation,$paretofront)
{
	
	
	
	foreach ($paretofront as $p)
	{
		
		
		$sql = "SELECT * FROM proposals WHERE id = ".$p." LIMIT 1";
		$response = mysql_query($sql);

		while ($row = mysql_fetch_array($response))
		{

			if (!get_magic_quotes_gpc())
			{
				$blurb = addslashes($row['blurb']);
				$abstract = addslashes($row['abstract']);
			}
			else
			{
				$blurb = $row['blurb'];
				$abstract = $row['abstract'];
			}

			$sql = 'INSERT INTO `proposals` (`blurb`, `usercreatorid`, `roundid`, `experimentid`,`source`,`dominatedby`,`creationtime`, `abstract` ) VALUES (\'' . $blurb . '\', \'' . $row['usercreatorid'] . '\', \'' . $generation . '\', \'' . $question . '\', \''.$p.'\',\'0\', NOW(), \'' . $abstract .'\');';
			
			$add_pareto_to_nextgen = mysql_query($sql);
			if (!$add_pareto_to_nextgen)
			{
				handle_db_error($add_pareto_to_nextgen);
			}
			
		}
	}
}


//  ********************************************/
//  This function calculates what is the Pareto Front for the previous generation
// and then stores it in the database. It is meant to be used immediately after 
// a question has passed phase.
//  ********************************************/
function SelectParetoFront($question)
{
	$sql = "SELECT roundid FROM questions WHERE id = $question LIMIT 1";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$generation=$row[0];
	}
	
	
	$paretofront=CalculateParetoFront($question,$generation-1);
	StoreParetoFront($question,$generation,$paretofront);
}




//  ********************************************/
//  This function calculates what is the Pareto Front for the previous generation
// and then stores it in the database. It is meant to be used immediately after 
// a question has passed phase.
//  ********************************************/
//
//
//function SelectParetoFront($question)
//{
//	$sql = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1";
//	$response = mysql_query($sql);
//	while ($row = mysql_fetch_array($response))
//	{
//		$generation=$row[0];
//		$pastgeneration=$generation-1;
//	}
//	$proposals=array();
//	$dominated=array();
//	$done=array(); //the list of the proposals already considered
//	$sql = "SELECT id FROM proposals WHERE experimentid = ".$question." and roundid= ".$pastgeneration."";
//	$response = mysql_query($sql);
//	while ($row = mysql_fetch_array($response))
//	{
//		array_push($proposals,$row[0]);
//	}
//	foreach ($proposals as $p1)
//	{
//		array_push($done,$p1);
//		foreach ($proposals as $p2)
//		{
//			if (in_array($p2,$done))
//			{
//				continue;
//			}
//			$dominating=WhoDominatesWho($p1,$p2);
//			if ($dominating==$p1)
//			{
//				array_push($dominated,$p2);
//				$sql = "UPDATE proposals SET dominatedby = ".$p1." WHERE id = ".$p2." ";
//				mysql_query($sql);
//				continue;
//			}
//			elseif ($dominating==$p2)
//			{
//				array_push($dominated,$p1);
//				$sql = "UPDATE proposals SET dominatedby = ".$p2." WHERE id = ".$p1." ";
//				mysql_query($sql);
//				break;
//			}
//		}
//	}
//	$paretofront=array_diff($proposals,$dominated);
//	foreach ($paretofront as $p)
//	{
//		$sql = "SELECT * FROM proposals WHERE id = ".$p." LIMIT 1";
//		$response = mysql_query($sql);
//
//		while ($row = mysql_fetch_array($response))
//		{
//
//			if (!get_magic_quotes_gpc())
//			{
//				$blurb = addslashes($row[1]);
//				$abstract = addslashes($row['abstract']);
//			}
//			else
//			{
//				$blurb = $row[1];
//				$abstract = $row['abstract'];
//			}
//
//			$sql = 'INSERT INTO `proposals` (`blurb`, `usercreatorid`, `roundid`, `experimentid`,`source`,`dominatedby`,`creationtime`, `abstract` ) VALUES (\'' . $blurb . '\', \'' . $row[2] . '\', \'' . $generation . '\', \'' . $question . '\', \''.$p.'\',\'0\', NOW(), \'' . $abstract .'\');';
//			
//			$add_proposal_to_nextgen = mysql_query($sql);
//			if (!$add_proposal_to_nextgen)
//			{
//				handle_db_error($add_proposal_to_nextgen);
//			}
//		}
//	}
//}

/////////////////////////////////////////////////////////////////////////////////////
/////////////////This function takes two proposals, and returns 0 if neither dominates the other because they have different users
/////////////////returns -1 if neither dominates the other because they have the same users
/////////////////returns the id of the dominating proposal if one dominates the other
function WhoDominatesWho($proposal1,$proposal2)
{
	$sql1 = "SELECT userid FROM endorse WHERE endorse.proposalid = ".$proposal1." ";
	$response1 = mysql_query($sql1);
	$users1=array();
	while ($row1 = mysql_fetch_array($response1))
	{
		array_push($users1,$row1[0]);
	}
	$sql2 = "SELECT userid FROM endorse WHERE endorse.proposalid = ".$proposal2." ";
	$response2 = mysql_query($sql2);
	$users2=array();
	while ($row2 = mysql_fetch_array($response2))
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

/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
////////////////FUNCTIONS TO FIND THE KEY PLAYERS AND THEIR POSSIBLE EFFECTS/////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////



//****************************************************/
//   This function finds the key players and returns the list of them 
//****************************************************/
function CalculateKeyPlayers($question,$generation)
{	//$keyPlayers=array();
	$paretofront=ParetoFront($question,$generation);
	$users=Endorsers($question,$generation);//	$LightProposals=array();
	$CouldDominate=array();
	foreach ($users as $user)
	{
		$paretofrontexcluding=CalculateParetoFrontExcluding($paretofront,$user);
		if ($paretofrontexcluding!=$paretofront)
		{	//			array_push($keyPlayers,$user);
			$Diff1=array_diff($paretofront,$paretofrontexcluding); //proposals that are in the pareto front because of X //			$LightProposals[$user]=$Diff1;
			$CouldDominate[$user]=array();
			foreach ($Diff1 as $p)
			{
				$CouldDominate[$user]=array_merge($CouldDominate[$user],WhoDominatesThisExcluding($p,$paretofront,$user));
			}
			$CouldDominate[$user]=array_unique($CouldDominate[$user]);
		}
	}
	return $CouldDominate;//now we need to visualise the information and 
}


/////////////////////////////////////////////////////////////////////////////////////
/////////////////This function takes two proposals, and returns 0 if neither dominates the other because they have different users
/////////////////returns -1 if neither dominates the other because they have the same users
/////////////////returns the id of the dominating proposal if one dominates the other

function WhoDominatesWhoExcluding($proposal1,$proposal2,$userExcluded)
{
	$sql1 = "SELECT userid FROM endorse WHERE endorse.proposalid = ".$proposal1." ";
	$response1 = mysql_query($sql1);
	$users1=array();
	while ($row1 = mysql_fetch_array($response1))
	{
		if($row1[0]==$userExcluded)
			{continue;}
		else
			{array_push($users1,$row1[0]);}
	}
	$sql2 = "SELECT userid FROM endorse WHERE endorse.proposalid = ".$proposal2." ";
	$response2 = mysql_query($sql2);
	$users2=array();
	while ($row2 = mysql_fetch_array($response2))
	{
		if($row2[0]==$userExcluded)
			{continue;}
		else
			{array_push($users2,$row2[0]);}
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


// ****************************************************/
// How would shrink a Pareto Front if a particular User did not participate?
// This function is useful to find the key playes
// People whos absence would change the Pareto Front are KeyPeople
// ****************************************************/

function CalculateParetoFrontExcluding($paretofront,$userExcluded)
{
	$dominated=array();
	$done=array();
	foreach ($paretofront as $p1)
	{
		array_push($done,$p1);
		foreach ($paretofront as $p2)
		{
			if (in_array($p2,$done)) {continue;}
			$dominating=WhoDominatesWhoExcluding($p1,$p2,$userExcluded);
			if ($dominating==$p1)
			{
				array_push($dominated,$p2);
				continue;
			}
			elseif ($dominating==$p2)
			{	#echo "$p2 dominates $p1; P2=".$p2."P1=".$p1."<br />";
				array_push($dominated,$p1);
				break;
			}
		}
	}
	return array_diff($paretofront,$dominated);
}
// ****************************************************/
// How would shrink a Pareto Front if a particular User did not participate?
// This function is useful to find the key playes
// People whos absence would change the Pareto Front are KeyPeople
// ****************************************************/

function CalculateFullParetoFrontExcluding($proposals,$userExcluded)
{
	$dominated=array();
	$done=array();
	foreach ($proposals as $p1)
	{
		array_push($done,$p1);
		foreach ($proposals as $p2)
		{
			if (in_array($p2,$done)) {continue;}
			$dominating=WhoDominatesWhoExcluding($p1,$p2,$userExcluded);
			if ($dominating==$p1)
			{
				array_push($dominated,$p2);
				continue;
			}
			elseif ($dominating==$p2)
			{
				array_push($dominated,$p1);
				break;
			}
		}
	}
	return array_diff($proposals,$dominated);
}




/////////////////////////////////////////////////////////////////////////////////////
//////Given a first proposal stored in $proposalToBeDominate, and a user to be ignored, this function 
//////look for all the proposals in the Pareto Front and finds which could have dominated that proposal
/// returns the list of those proposals

function WhoDominatesThisExcluding($proposalToBeDominate,$paretofront,$userExcluded)
{
	$couldDominate=array();
	foreach ($paretofront as $p1)
	{
		if($p1==$proposalToBeDominate)
		{
			continue;
		}
		$dominating=WhoDominatesWhoExcluding($p1,$proposalToBeDominate,$userExcluded);
		if ($dominating==$p1)
		{
			array_push($couldDominate,$dominating);
		}
	}
	return $couldDominate;
}


/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
////////////////FUNCTIONS TO DRAW THE GRAPHVIZ MAP///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
function InsertMap2($question,$generation,$highlightuser1=0,$size="L",$highlightproposal1=0)
{
#	echo "highlightproposal1 in InsertMap=".$highlightproposal1;
	$filename=MapName($question,$generation,$highlightuser1,$size,$highlightproposal1);
	set_log($filename);
	$svgfile=WriteGraphVizMap($question,$generation,$highlightuser1,$size,$highlightproposal1);
	if ($svgfile)
	{
		return $filename;
	}
	else
	{
		return false;
	}
}

#$size="L"/Middle/Small = "11,5.5"=1100 550 
function InsertMap($question,$generation,$highlightuser1=0,$size="L",$highlightproposal1=0)
{
#	echo "highlightproposal1 in InsertMap=".$highlightproposal1;
	if (USE_GRAPHVIZ_MAPS)
	{
		$svgfile=WriteGraphVizMap($question,$generation,$highlightuser1,$size,$highlightproposal1);
		if ($svgfile)
		{
			$buf='<center><embed src="'.$svgfile.'" ';
			if($size=="L")      {	$buf.='width="1100" height="550" ';	}
			elseif($size=="M")	{	$buf.='width="800" height="400" ';	}
			elseif($size=="S")	{	$buf.='width="600" height="300" ';	}		
			elseif($size=="XS")	{	$buf.='width="400" height="200" ';	}		
			$buf.=' type="image/svg+xml" pluginspage="http://www.adobe.com/svg/viewer/install/" /></center>';
			echo $buf;
		}		
	}
	return;
}

#$ShowNSupporters=true,$ShowAllEndorsments=false,$size="7.5,10",$bundles=true,$highlightuser1=0)


function MapName($question,$generation,$highlightuser1=0,$size="L",$highlightproposal1=0)
{
#	echo "highlightproposal1 in MapName=".$highlightproposal1;
	
	$room=GetQuestionRoom($question);
	return "map/".$size."map_R".$room."_Q".$question."_G".$generation."_hl1u".$highlightuser1."_hl1p".$highlightproposal1;
}

function WriteGraphVizMap($question,$generation,$highlightuser1=0,$size="L",$highlightproposal1=0)
{
#	echo "<br />highlightproposal1 in WriteGraphVizMap = ".$highlightproposal1."<br />";
	
	$filename=MapName($question,$generation,$highlightuser1,$size,$highlightproposal1);
	if($size=="L")     { $sz="11,5.5";	}
	elseif($size=="M") { $sz= "8,4";	}
	elseif($size=="S") { $sz= "6,3";    }
	elseif($size=="XS"){ $sz= "4,2";    }
	
	if (file_exists ( $filename.".svg"))
	{
		return $filename.".svg";
	}
	if (file_exists ( $filename.".dot") && filesize($filename.".dot") !== 0)
	{
		system(GRAPHVIZ_DOT_ADDRESS." -Tsvg ".$filename.".dot >".$filename.".svg");
		if (file_exists ( $filename.".svg"))
		{
			return $filename.".svg";
		}
	}
	$MapFile = fopen($filename.".dot", "w+");
	$buf=MakeGraphVizMap($question,$generation,$highlightuser1,$highlightproposal1,$sz);
	if ($MapFile) {
		fputs($MapFile,$buf);
		fclose($MapFile);
		system(GRAPHVIZ_DOT_ADDRESS." -Tsvg ".$filename.".dot >".$filename.".svg");
		if (file_exists ( $filename.".svg"))
		{
			return $filename.".svg";
		}
		return;
	} else {
		echo "<br /><b>Error creating map file, please check write permissions.</b><br />";
		return;
	}	
}

function FindLevels($proposals_covered,$proposals)
{
	$proposalsToTest=$proposals;
	$Levels=array();
	$Level=0;
	$proposalsAdded=array();
	
	while(sizeof($proposalsAdded)<sizeof($proposals))
	{
		if ($Level==0)
		{
			$Levels["Pareto Fronts"]=array();
		}
		else
		{
			$Levels[$Level]=array();
		}
				
		foreach($proposalsToTest as $p)
		{
			$nextP=false;
			foreach($proposalsToTest as $q)
			{
				if(in_array($p, $proposals_covered[$q]))
				{
					$nextP=true;
					break;	
				}	
			}
			if ($nextP)
			{
				continue;
			}
			if ($Level==0)
			{
				array_push($Levels["Pareto Front"],$p);				
			}
			else
			{
				array_push($Levels[$Level],$p);							
			}			
			array_push($proposalsAdded,$p);
		}		
		$proposalsToTest= array_diff ($proposals, $proposalsAdded);
		$Level+=1;
	}
	return $Levels;
}

function FindLevelsBasedOnSize($proposals)
{
	$Levels=array();
	foreach($proposals as $p)
	{
		$l=CountEndorsersToAProposal($p);
		if (in_array($l, array_keys($Levels))==false)
		{
			$Levels[$l]=array();
		}
		array_push($Levels[$l],$p);							
	}
	krsort($Levels);
	return $Levels;
}



function GetCovered($proposals_below,$proposals)
{
	$Covered=array();
	foreach ($proposals as $p)
	{
		$cov1=array();
		$below=$proposals_below[$p];
		foreach ($below as $b)
		{
			foreach ($below as $q)
			{
				$nextB=false;
				$underq=$proposals_below[$q];
				if (in_array($b, $underq))
				{
					$nextB=true;
					break;
				}				
			}
			if ($nextB)
			{
				continue;
			}			
			array_push($cov1,$b);
		}
		$Covered[$p]=$cov1;		
	}
	return $Covered;
}

function NewEndorsersToAProposal($proposal,$proposals_covered)
{
	$below=$proposals_covered[$proposal];
	$VotersKnown=array();
	foreach ($below as $b)
	{
		$VotersKnown=array_merge($VotersKnown,EndorsersToAProposal($b));
	}
	return array_diff(EndorsersToAProposal($proposal), 	array_unique($VotersKnown));
}



function CombineProposals($proposals)
{
	$Combined2Proposals=array();
	$Proposals2Combined=array();
	sort($proposals);
	foreach ($proposals as $p)	{$Proposals2Combined[$p]=$p;}
		
	foreach ($proposals as $p)
	{
		if ($Proposals2Combined[$p]!=$p){continue;}#we have already done this bundle
		foreach ($proposals as $q)
		{
			if ($p>=$q OR $Proposals2Combined[$q]!=$q){continue;}	#we have already done this bundle
			if(HaveSameElements(EndorsersToAProposal($p),EndorsersToAProposal($q)))			
			{
				$Proposals2Combined[$q]=$Proposals2Combined[$p];#if P is already part of a bundle it will point to its lowest member, if not we point q to p (which is lower)
				$Combined2Proposals[$Proposals2Combined[$p]]=array();
			}
		}
	}
	foreach ($proposals as $p)
	{	
		if($Proposals2Combined[$p]!=$p)		
			{array_push($Combined2Proposals[$Proposals2Combined[$p]],$p);}
	}
	return $Combined2Proposals;
}


function WriteBundle($BundleName,$BundleContent,$room,$details,$detailsTable,$highlightproposal1=0)
{
	$answer="";
	$color="black";
	$peripheries=0;
	$BundleSize=sizeof($BundleContent);	
	$answer.=$BundleName.' [shape=plaintext '.$details.' fontsize=11 label=<<TABLE BORDER="0" '.$detailsTable.' CELLBORDER="1" CELLSPACING="0" CELLPADDING="4"><TR><TD COLSPAN="'.$BundleSize.'"></TD></TR><TR>';
	foreach ($BundleContent as $p)
	{
		$urlquery = CreateProposalURL($p, $room);
		$urlquery=str_replace ( "&" , "&amp;" , $urlquery );#This is weird, in all the rest of the map an & is an & but here he wants them as a &amp;	

		$tooltip=substr(SafeStringProposal($p), 0, 800);
		$tooltip=str_replace ( "&" , "&amp;" , $tooltip );#This is weird, in all the rest of the map an & is an & but here he wants them as a &amp;	
		$ToAdd='';
		if($highlightproposal1==$p)
		{
			$ToAdd=' BGCOLOR="red" ';
		}
		$answer.='<TD '.$ToAdd.' HREF="'.SITE_DOMAIN.'/viewproposal.php'.$urlquery.'" tooltip="'.$tooltip.'" target="_top">'.$p.'</TD>';	
	}
	$answer.='</TR><TR><TD COLSPAN="'.$BundleSize.'"></TD></TR></TABLE>>]';
	$answer.="\n";
	return $answer;
}



#Possible Values for $ShowNSupporters=true /false
#$size="7.5,10
#10,16.18
function MakeGraphVizMap($question,$generation,$highlightuser1=0,$highlightproposal1=0,$size="11,5.5",$ShowNSupporters=true,$ShowAllEndorsments=false,$bundles=true)
{
	$proposals_below=array();
	$proposals_above=array();
	$proposals_covered=array();
	$title=StringSafe(GetQuestionTitle($question));
	#echo $title;

	$buf='digraph "';
	$buf.=$title;
	$buf.='" {';
	$buf.="\n";
	$buf.='size="'.$size.'"';
	$buf.="\n";
	$proposals=GetProposalsInGeneration($question,$generation);
	$endorsers=Endorsers($question,$generation);
	$authors=array_merge(AuthorsOfInheritedProposals($question,$generation),AuthorsOfNewProposals($question,$generation));
	$pure_authors=array_diff($authors,$endorsers);
	$pf=ParetoFront($question,$generation);
	$room=GetQuestionRoom($question);
	$Bundled=array();
	
	foreach ($proposals as $p)
	{
		$RelatedProposals=CalculateProposalsRelationTo($p,$question,$generation);
		$proposals_below[$p]=$RelatedProposals["dominated"];
		$proposals_above[$p]=$RelatedProposals["dominating"];
	}
	
	$proposals_covered=GetCovered($proposals_below,$proposals);
	if ($ShowNSupporters)
	{
		$Levels=FindLevelsBasedOnSize($proposals);		
	}
	else
	{
		$Levels=FindLevels($proposals_covered,$proposals);
	}
	if($bundles)	
	{
		$Combined2Proposals=CombineProposals($proposals);
		$keys=array_keys($Combined2Proposals);
		foreach($keys as $kc2p)
		{
			$Bundled=array_merge($Bundled,$Combined2Proposals[$kc2p]);#Bundled elements don't need to be drawn
			array_push($Combined2Proposals[$kc2p],$kc2p);
		}
	}
	else  	
	{
		$Combined2Proposals=array();
	}
	$LevelsKeys=array_keys($Levels);#print_r($LevelsKeys);
	foreach ($LevelsKeys as $l)
	{	
		if($l==="Pareto Front")
		{
#			$buf.='"'.$l.'" [shape=box color=lightblue style=filled fontsize=14]';			
			$buf.='"Pareto\nFront" [shape=box color=lightblue style=filled fontsize=14]';			#$buf.='"'.$l.'" [shape=box color=white style=filled fontsize=14]';			
			$buf.="\n";			
		}
		else
		{			
			$buf.=$l." [shape=point fontcolor=white color=white fontsize=1] \n";
		}		
	}
#	$buf.='"Voters" [shape=egg color=lightpink3 style=filled fontsize=14]';			
	$buf.='"Voters" [shape=point color=white fontcolor=white style=filled fontsize=1]';			
	$buf.="\n";			
					
	foreach ($LevelsKeys as $l)
	{	
		$buf.='"'.$l.'" -> ';
	}
	$buf.='"Voters" ';			
#	$buf.='->"Authors who did not vote" ';			
	$buf.="[color=white] \n";			

	foreach ($LevelsKeys as $l)
	{
		$buf.='{rank=same; "'.$l.'" ';			
		foreach ($Levels[$l] as $p)
		{
			if(in_array($p,$Bundled)){	continue;}
			$buf.=" ".$p." ";			
		}		
		$buf.="}\n";					
	}

	$buf.="{rank=same; Voters ";
	foreach ($endorsers as $e)
	{
		$buf.='"'.WriteUserName($e).'" ';					
	}
	$buf.="}\n";					
	
	foreach ($endorsers as $e)
	{
		$color="lightpink3";		
		$fillcolor="lightpink3";
		$peripheries=1;

		if($highlightproposal1)
		{		
			if(in_array($e,EndorsersToAProposal($highlightproposal1)))		
			{
				$color="red";
				$peripheries=2;						
			}
		}
		if($highlightuser1===$e)
		{
			$color="red";
			$peripheries=2;			
		}
		$buf.='"'.WriteUserName($e).'" [shape=egg fillcolor='.$fillcolor.' style=filled color='.$color.' peripheries='.$peripheries.' style=filled  fontsize=11]';					
		$buf.="\n";					
	}
	
	#$buf.='{rank=same; "Authors who did not vote" ';		
	#foreach ($pure_authors as $pa)
	#{
#		$buf.='"'.WriteUserName($pa).'" ';					
#	}
#	$buf.="}\n";					
	$keys=array_keys($Combined2Proposals);
	foreach($keys as $kc2p)	
	{  
		$detailsTable='  ';				
		
		if(in_array ( $kc2p, $pf ))
		{
			$color="black";
			$peripheries=0;
			$endo=EndorsersToAProposal($kc2p);
			if(in_array($highlightuser1,$endo))
			{
				$color="red";
				$peripheries=1;
			}
			if ($highlightproposal1>0)	
			{
				if(in_array($kc2p,$proposals_below[$highlightproposal1]) OR in_array($kc2p,$proposals_above[$highlightproposal1])){
					$color="red";
					$peripheries=1;
				}			
			}
			if(Count($endo)===Count($endorsers))
			{
				$detailsTable=' BGCOLOR="gold" ';								
			}
			else
			{
				$detailsTable=' BGCOLOR="lightblue" ';								
			}
			$details=' fillcolor=white style=filled color='.$color.' peripheries='.$peripheries.' ';			
		}
		else
		{
			$color="black";
			$peripheries=0;
			if(in_array($highlightuser1,EndorsersToAProposal($kc2p)))
			{
				$color="red";
				$peripheries=1;
			}
			if ($highlightproposal1>0)	
			{
				if(in_array($kc2p,$proposals_below[$highlightproposal1]) OR in_array($kc2p,$proposals_above[$highlightproposal1])){
					$color="red";
					$peripheries=1;
				}			
			}
			
			$details=' fillcolor=white color='.$color.' peripheries='.$peripheries.' ';						
		}
		
		$buf.=WriteBundle($kc2p,$Combined2Proposals[$kc2p],$room,$details,$detailsTable,$highlightproposal1);
	}
	
	foreach ($proposals as $p)
	{
		if(in_array($p,$Bundled)){	continue;}
		if(in_array($p,array_keys($Combined2Proposals))){	continue;}
		$color="black";
		$peripheries=1;
		if ($highlightuser1>0)
		{	
			#echo "EndorsersToAProposal($p)=".EndorsersToAProposal($p);
			#echo "highlightuser1=".$highlightuser1;
			
			if(in_array($highlightuser1,EndorsersToAProposal($p)))
			{
				$color="red";
				$peripheries=2;
			}
		}
		if ($highlightproposal1>0)
		{
			if($highlightproposal1===$p )
			{
				$color="red";
				$peripheries=3;
			}			
			if(in_array($p,$proposals_below[$highlightproposal1]) 
				OR in_array($p,$proposals_above[$highlightproposal1]))
			{
				$color="red";
				$peripheries=2;
			}			
		}
		
		if(in_array ( $p, $pf ))
		{
			$urlquery = CreateProposalURL($p, $room);
			
			$endo=EndorsersToAProposal($p);
			
			if(Count($endo)===Count($endorsers))
			{
				$fillcolor='"gold"';								
			}
			else
			{
				$fillcolor='"lightblue" ';								
			}
			
			
			$buf.=$p.' [shape=box fillcolor='.$fillcolor.' style=filled color='.$color.' peripheries='.$peripheries.' tooltip="'.substr(SafeStringProposal($p), 0, 800).'"  fontsize=11 URL="'.SITE_DOMAIN.'/viewproposal.php'.$urlquery.'" target="_top"]';			
			
			$buf.="\n";			
		}
		else
		{	
			$urlquery = CreateProposalURL($p, $room);
			
			$buf.=$p.' [shape=box color='.$color.' peripheries='.$peripheries.' tooltip="'.substr(SafeStringProposal($p), 0, 800).'"  fontsize=11 URL="'.SITE_DOMAIN.'/viewproposal.php'.$urlquery.'" target="_top"]';			

			$buf.="\n";
		}
	}
	
	foreach ($proposals as $p)
	{
		$pcolor="black";
		if(in_array($p,$Bundled)){continue;}
		$pcs=$proposals_covered[$p];
		
		#if($highlightproposal1===$p OR in_array($p,$proposals_below[$highlightproposal1])) 			{$pcolor="red";}		
#		print_r($proposals_below[$highlightproposal1]);
#		echo "highlightproposal1=",$highlightproposal1;
		if($highlightproposal1)
		{
			if($highlightproposal1===$p OR in_array($p,$proposals_below[$highlightproposal1]))
			 			{$pcolor="red";}					
		}
		foreach ($pcs as $pc)
		{
			$color=$pcolor;
			if(in_array($pc,$Bundled)){	continue;}
			if(in_array($highlightuser1,EndorsersToAProposal($pc)))
			{
				$color="red";
			}
			
			if($highlightproposal1)
			{			
				if($highlightproposal1===$pc OR in_array($pc,$proposals_above[$highlightproposal1]))	{$color="red";}
			}
			$buf.=' '.$pc.' -> '.$p.' [color="'.$color.'"]';	
			$buf.=" \n";					
		}		
	}
	
	foreach ($proposals as $p)
	{
		if(in_array($p,$Bundled)){continue;}
		
		if($ShowAllEndorsments)	{$endorserstothis=EndorsersToAProposal($p);			}
		else{$endorserstothis=NewEndorsersToAProposal($p,$proposals_covered);}

		foreach ($endorserstothis as $e)
		{
			$color="blue";
			if($highlightuser1==$e){$color="red";	}
			if ($highlightproposal1>0)
			{
				if($highlightproposal1===$p OR in_array($p,$proposals_below[$highlightproposal1]))
					{$color="red";}
			}
			$buf.=' "'.WriteUserName($e).'" -> '.$p.' [color="'.$color.'"]';
			$buf.=" \n";
		}
	}
	
	#foreach ($proposals as $p)
	#{
	#	$buf.=' "'.WriteUserName(AuthorOfProposal($p)).'" -> '.$p.' [color=red]';
	#	$buf.=" \n";
	#}
	
	$buf.="\n}";
	return $buf;
}

function WriteIntergenerationalGVMap($question)
{
#	echo "<br />highlightproposal1 in WriteGraphVizMap = ".$highlightproposal1."<br />";
	$MapFile = fopen("map/IGMap".$question.".dot", "w+");
	
	$buf=MakeIntergenerationalGVMap($question);
	if ($MapFile) {
		fputs($MapFile,$buf);
		fclose($MapFile);
		#system("../local/bin/dot -Tsvg ".$filename.".dot >".$filename.".svg");
	} else {
		echo "<br /><b>Error creating map file, please check write permissions.</b><br />";
		return;
	}	
	
}

function MakeIntergenerationalGVMap($question,$size="11,5.5")  #,$highlightuser1=0,$highlightproposal1=0,$size="11,5.5",$ShowNSupporters=true,$ShowAllEndorsments=false,$bundles=true)
{
	$title=StringSafe(GetQuestionTitle($question));
	$buf='digraph "';
	$buf.=$title;
	$buf.='" {';
	$buf.="\n";
	$buf.='size="'.$size.'"';
	$buf.="\n";
	$room=GetQuestionRoom($question);
	$proposals=array();												#	
	$AllProposals=array();#ProposalsInQuestion($question);
	$generationNow=GetQuestionGeneration($question);
	$gen=1;
	while($gen<$generationNow)
	{
		$buf.="G".$gen." [shape=point fontcolor=white color=white fontsize=1] \n";		
		$gen+=1;
	}
	
	$gen=1;
	while($gen<$generationNow-1)
	{
		$buf.="G".$gen." -> ";		
		$gen+=1;
	}
	$buf.="G".$gen.";\n";		

	$gen=1;
	while($gen<$generationNow)
	{
		$proposals[$gen]=GetProposalsInGeneration($question,$gen);
		$AllProposals=array_merge($AllProposals,$proposals[$gen]);
		$buf.='{rank=same; "G'.$gen.'" ';
		foreach ($proposals[$gen] as $p) { $buf.=" ".$p." ";	}
		$buf.="}\n";
		$pf[$gen]=ParetoFront($question,$generation);
		foreach($proposals[$gen] as $p)
		{
			if(in_array ( $p, $pf[$gen] ))
			{
				$color="black";
				$peripheries=1;
				$urlquery = CreateProposalURL($p, $room);
				$buf.=$p.' [shape=box fillcolor=lightblue style=filled color='.$color.' peripheries='.$peripheries.' tooltip="'.substr(SafeStringProposal($p), 0, 800).'"  fontsize=11 URL="'.SITE_DOMAIN.'/viewproposal.php'.$urlquery.'" target="_top"]';			
				$buf.="\n";			
			}
			else
			{	
				$urlquery = CreateProposalURL($p, $room);
				$buf.=$p.' [shape=box color='.$color.' peripheries='.$peripheries.' tooltip="'.substr(SafeStringProposal($p), 0, 800).'"  fontsize=11 URL="'.SITE_DOMAIN.'/viewproposal.php'.$urlquery.'" target="_top"]';			
				$buf.="\n";
			}
		}
		$gen+=1;
	}
	foreach($AllProposals as $p)	
	{
		$pd=GetProposalDaughter($p);
		if($pd)
		{
			$buf.=$p.' -> '.$pd.' ';	
			$buf.="\n";
		}		
	}
	$buf.="\n}";
	return $buf;
}





?>