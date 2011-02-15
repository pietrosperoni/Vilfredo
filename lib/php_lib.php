<?php
// ****************************************
// Useful functions
// ****************************************
//
// For cleaning POST & GET before input into DB
function clean_input_string($input)
{
	if (is_string($input))
	{
		return mysql_real_escape_string($input);
	}
	else
	{
		return '';
	}
}

function clean_input_array($input)
{
	if (!is_array($input))
	{
		return false;
	}
	else
	{
		foreach($input as $key => $value) 
		{ 
			if (is_array($input[$key]))
			{
				clean_input_array($input[$key]);
			}
			else
			{
				$input[$key] = mysql_real_escape_string($value);			
			}
		} 
	}
	return $input;
}

function GetEscapedPostParam($key)
{
	if (empty($_POST[$key]))
		return '';
	$param = $_POST[$key];
	if (!get_magic_quotes_gpc()) 
	{
		$param = addslashes($param);
	}
	return $param;
}

function GetEscapedGetParam($key)
{
	if (empty($_GET[$key]))
		return '';
	$param = $_GET[$key];
	if (!get_magic_quotes_gpc()) 
	{
		$param = addslashes($param);
	}
	return $param;
}

function boolString($bValue = false) {                      
	// returns string
	return ($bValue ? 'true' : 'false');
}

/**
* Pumps all child elements of second SimpleXML object into first one.
*
* @param    object      $xml1   SimpleXML object
* @param    object      $xml2   SimpleXML object
* @return   void
*/
function simplexml_merge (SimpleXMLElement &$xml1, SimpleXMLElement $xml2)
{
    // convert SimpleXML objects into DOM ones
    $dom1 = new DomDocument();
    $dom2 = new DomDocument();
    $dom1->loadXML($xml1->asXML());
    $dom2->loadXML($xml2->asXML());

    // pull all child elements of second XML
    $xpath = new domXPath($dom2);
    $xpathQuery = $xpath->query('/*/*');
    for ($i = 0; $i < $xpathQuery->length; $i++)
    {
        // and pump them into first one
        $dom1->documentElement->appendChild(
            $dom1->importNode($xpathQuery->item($i), true));
    }
    $xml1 = simplexml_import_dom($dom1);
}

function printbrx($str='', $lines=2, $quit=FALSE)
{
	printbr($str, $lines, $quit);
	exit;
}

function printbr($str='', $lines=2, $quit=FALSE)
{
	if (is_null($str))
	{
		echo 'Null';
	}
	elseif (is_bool($str))
	{
		if ($str)
		{
			echo 'True';
		}
		else
		{
			echo 'False';
		}
	}
	elseif (is_array($str))
	{
		print_r($str);
	}
	else
	{
		echo $str;
	}
	
	if ($lines == 2)
	{
		echo '<br/><br/>';
	}
	else
	{
		for ($i=0; $i<$lines; $i++)
		{
			echo "<br/>";
		}
	}
	
	if ($quit) exit;
}

// prints out the contents of an array
function print_array($arr, $name='', $quit=FALSE) {
	if (is_array($arr))
	{
		print '<pre>';
		if (!empty($name))
			print $name.' :<br/>';
		print_r($arr);
		print '</pre>';
	}

	else printbr($name . ' ' . $arr);

	if ($quit) exit;
}
//

function reset_password($userid, $password = 'test123')
{
	// encrypt the password
	$password = md5($password);

	$sql = "UPDATE users
				SET password='$password'
				WHERE id = '$userid'";

	$update = mysql_query($insert);
}

//*********************vilfredo functions*************

// Load local libraries - Twitter, Facebook etc
if (defined('DP_LOCAL')) {
    $lib = dirname(dirname(__FILE__)) . "/lib";
	set_include_path(get_include_path() . PATH_SEPARATOR . $lib);
}

function getUniqueCode($length = "")
{
	$code = md5(uniqid(rand(), true));
	if ($length != "") return substr($code, 0, $length);
	else return $code;
}

//***********************************************
// function error($message, $level=E_USER_NOTICE) {
function checkerror($message, $level=E_USER_NOTICE) {
	$caller = next(debug_backtrace());
	trigger_error($message.' in <strong>'.$caller['function'].'</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."\n<br />error handler", $level);
}


function decode_unicode($str)
{
	if ($str == NULL || $str == "") return $str;

	return htmlentities($str, ENT_NOQUOTES, 'UTF-8');
}

// Return id's as sql list, eg '233445', '254676', '567565'
function db_make_id_list_2($id_array)
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

// 1d, 3d, 3w, etc
function days_to_seconds($days_str, $default=ONE_DAY)
{
	if (!is_numeric($days_str)) return $default;

	$days = (int)$days_str;

	if ($days < 1 || $days > 28) return $default;

	return $days * ONE_DAY;
}

function nav_trail()
{
	$query = $_SERVER['QUERY_STRING'];
	$params = explode('&', $query);
	print_array($params, 'nav_trail');
}

function gmt_timestamp()
{
	//$format="d-M-y H:i:s";
	$now_date = gmdate(ISO_8601_DATE);
	$now_date_stamp = strtotime($now_date);

	return $now_date_stamp;
}

function now_gmt()
{
	$gmt_time = gmdate(ISO_8601_DATE);

   return $gmt_time;
}

function gmt_time($time_str, $add=0)
{
	$stamp = strtotime($time_str) + $add;
	$gmt_time = gmdate (ISO_8601_DATE, $stamp);

   return $gmt_time;
}

function string_unique($str, $delimiter=" ")
{
	if (empty($str)) return "";

	$str_array = explode($delimiter, $str);
	$str_array = array_unique($str_array);
	$result = implode($delimiter, $str_array);
	return $result;
}

function array_unique_lib($arr)
{
	if (empty($arr)) return $arr;

	$arr = array_unique($arr);
	$str = implode(' ', $arr);
	$result = explode(' ', $str);
	return $result;
}

function safe_string($str, $limit=NULL)
{
	if (!isset($str) || !is_string($str)) return false;

	if ($str == "") return $str;

	$result = trim(strip_tags($str));

	if (isset($limit) && is_int($limit))
	{
		$result = substr($result, 0, $limit);
	}

	return $result;
}

// returns query string with named element removed
// -- uses GET query string if $str parameter not set
// ---- the PHP function parse_str is equivalent to the PHP
// ---- function explode('&', $_SERVER['QUERY_STRING'])
function query_remove_var($var, $str="")
{
	if (empty($str))
	{
		//printbr("Removing $name from Query String...");
		parse_str($_SERVER['QUERY_STRING'], $query_array);
	}
	else
		parse_str($str, $query_array);

	// remove named element from the array if set
	if (isset($query_array[$var])) unset($query_array[$var]);
	$query_string = http_build_query($query_array);
	if (!empty($query_string))
		$query_string = "?".$query_string;

	//printbr('query_string = ' . $query_string);

	return $query_string;
}

function uri_remove_var($var)
{
	$new_query = query_remove_str($var);
	return $_SERVER['SCRIPT_NAME'] . $new_query;
}

// removes a query variable from a URI -
//   defaults to server REQUEST_URI if no URI passed
function uri_remove_query_var($var, $uri="")
{
	if (empty($uri))
	{
		$uri_array = parse_url($_SERVER['REQUEST_URI']);
	}
	else
		$uri_array = parse_url($uri);

	$uri_array['query'] = query_remove_var($var, $uri_array['query']);

	$new_uri = implode("", $uri_array);

	return $new_uri;
}

function pagelink_remove_var($var, $show=false)
{
	if (!isset($_SESSION[PAGE_LINK]))
	{
	 	trigger_error('PageLink session variable not set', E_USER_WARNING);
		return false;
	}

	if ($show) printbr('PageLink = ' . $_SESSION[PAGE_LINK]);

	$_SESSION[PAGE_LINK] = uri_remove_query_var($var, $_SESSION[PAGE_LINK]);

	if ($show) printbr('PageLink = ' . $_SESSION[PAGE_LINK]);

	return true;
}

function uri_contains_var($var, $uri="")
{
	if (empty($uri))
	{
		if (strpos($_SERVER['REQUEST_URI'], $var) === false)
			return false;
		else
			return true;
	}
	else
	{
		if (strpos($uri, $var) === false)
			return false;
		else
			return true;
	}
}

// extracts key from top level of a multi-dimensional array into a multi-dim array
function extract_key_multi($array, $key, $results_param=NULL)
{
	$result = array();

	if ($results_param != NULL)
		$result = $results_param;

	if (empty($array)) return result;

	$i=0;
	foreach ($array as $entry)
	{
		$result[$i][$key] = $entry[$key];
		$i++;
	}
	return $result;
}

// extracts key from top level of a multi-dimensional array
function extract_key($array, $key)
{
	if (empty($array)) return false;

	$result = array();
	$i=0;
	foreach ($array as $entry)
	{
		$result[$i] = $entry[$key];
		$i++;
	}
	return $result;
}

function extract_item_ids($array, $key)
{
	$restrictions = array('type' => 'items');
	return extract_key_restrict($array, $key, $restrictions);
}

function extract_seller_ids($array, $key)
{
	$restrictions = array('type' => 'sellers');
	return extract_key_restrict($array, $key, $restrictions);
}

// extracts key from top level of a multi-dimensional array
function extract_key_restrict($array, $key, $restrictions=false)
{
	if (empty($array)) return false;

	$results = array();
	$i=0;
	foreach ($array as $entry)
	{
		$extract = true;
		if ($restrictions)
		{
			foreach ($restrictions as $key => $value)
			{
				if (!isset($entry[$key]))
				{
					$extract = false;
					break;
				}
				elseif ($entry[$key] != $value)
				{
					$extract = false;
					break;
				}
			}
		}
		if ($extract)
		{
			$results[$i] = $entry['val'];
			$i++;
		}
	}
	return $results;
}

// Sessions

function destroy_current_session()
{
	session_destroy();
	setcookie ("PHPSESSID", "", time() - 3600);
}

function clear_user_session()
{
	if (isset($_SESSION['member'])) unset($_SESSION['member']);
	if (isset($_SESSION['user_data'])) unset($_SESSION['user_data']);
	if (isset($_SESSION['UserLogged'])) unset($_SESSION['UserLogged']);
	if (isset($_SESSION['USER_LOGIN_ID'])) unset($_SESSION['USER_LOGIN_ID']);
}

//close any db connection before exiting
function exit_and_close_db()
{
	session_start();
	if (isset($GLOBALS['gDbManager'])) $GLOBALS['gDbManager']->DbDisconnect();
	exit;
}

function get_user_agent()
{
	if (preg_match("/MSIE/i", $_SERVER['HTTP_USER_AGENT']))
	{
		return "Exolorer";
	}
	elseif (preg_match("/Firefox/i", $_SERVER['HTTP_USER_AGENT']))
	{
		return "Firefox";
	}
}

function get_currency_symbol($currency)
{
    $symbol;

	switch ($currency)
	{
		case 'USD':
			$symbol = '$';
			break;
		case 'CAD':
			$symbol = '$';
			break;
		case 'GBP':
			$symbol = '�';
			break;
		default:
			$symbol = '';
	}

	return $symbol;
}

// ****************************************
// Useful classes
// ****************************************
//

class Data
{
	private $data = array();

	public function __construct($data)
	{
		if (is_array($data) == false)
			trigger_error("Data constructor: Parameter #1 invalid", E_USER_ERROR);

		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function text($delim="<br/>")
	{
		$txt = "";
		if (isset($this->data['text']))
		{
				$txt = $this->data['text'] . $delim;
		}
		return $txt;
	}

	public function all_text($delim="<br/>")
	{
		$txt = "";

		foreach($this->data as $val)
		{
			if (isset($val['text']))
			{
				$txt .= $val['text'] . $delim;
			}
		}
		return $txt;
	}

	public function __toString()
	{
		return $this->text();
	}
}//class
?>