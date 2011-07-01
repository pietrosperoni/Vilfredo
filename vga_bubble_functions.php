<?php
function GetQuestionRoomTV($question)
{
	 $sql="SELECT room FROM questions_tv 
	 WHERE id = $question";
	
	if ($result = mysql_query($sql))
	{
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_assoc($result);
			return $row['room'];
		}
		else
		{
			set_log(__FILE__.'::'.__FUNCTION__ . "::Question $question does not exit");
			return false;
		}
	}
	else
	{
		db_error(__FILE__.'::'.__FUNCTION__ . " SQL: " . $sql);
		return false;
	}
}
function HasQuestionAccessTV()
{
	$question;
	$room = '';

	if (isset($_GET[q]))
             $question = (int)$_GET[q];
	else
            return false;

	$room_id = GetQuestionRoomTV($question);

	// Return false if question does not exist
	if ($room_id === false) return false;
	
	if (isset($_GET[QUERY_KEY_ROOM]))
		$room_param = $_GET[QUERY_KEY_ROOM];
	else
		$room_param = '';
	
	// rooms are case sensitive
	if ($room_param == $room_id)
	{
		return true;
	}
	else
	{
        	return false;
        }
}
?>