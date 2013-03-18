<?php
if (!$userid)
{
	setError("You must be logged in to access this page.");
	header("Location: error_page.php");
	exit;
}
elseif (!isAdmin($userid))
{
	setError("Only Administrators may access this page.");
	header("Location: error_page.php");
	exit;
}

// returns md array of key and text values from marked file
function get_delimiters($filename)
{
	$contents = file_get_contents($filename);
	$open = "_{";
	$close = "}_";
	$opentype = "\(";
	$closetype = "\)";
	preg_match_all("/_\{(.*?)\}/", $contents, $phrases);
	
	//preg_match_all("/_{\((.*?)\)(.*?)}_/", $contents, $result);
	//preg_match_all("/(?<=$open)\((.*?)\)(.*?)(?=$close)/", $contents, $result);
	return $phrases;
}

function get_phrases($lang, $onlypages)
{
	$sql = '';
	if (isset($onlypages))
	{
		// Get selected pages
		$pages = stripslashes(implode(',', $onlypages));
		$sql = "SELECT var_key, text FROM vga_content WHERE lang = '$dictfilelang'
				AND page IN ($pages)";
	}
	else
	{
		// Get entire dictionary
		$sql = "SELECT var_key, text FROM vga_content WHERE lang = '$lang'";
	}
}

function generate_translation_file($dictlang='it', $langfile)
{
	// set to 'it' for now
	if(!isset($dictlang))
	{
		return false;
	}
	elseif ($dictlang == 'en')
	{
		$sql .= "SELECT var_key, text
			FROM `vga_content`";
		if ($result = mysql_query($sql))
		{
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_assoc($result))
				{		
					$str = $row['text'];
					$str = stripslashes($str);
					$dictphrases[] = $str;
					$keys[] = $row['var_key'];
				}
			}
			else
			{
				set_log(__FUNCTION__ . " No phrases found");
				return false;
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
		$sql .= "SELECT lang, var_key, text, $dictlang
			FROM `vga_content`";
		
		if ($result = mysql_query($sql))
		{
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					if (!empty($row['it']))
					{
						$str = $row['it'];
					}
					else
					{
						$str = $row['text'];
					}
					$str = stripslashes($str);
					$dictphrases[] = $str;
					$keys[] = $row['var_key'];
				}
			}
			else
			{
				set_log(__FUNCTION__ . " No phrases found");
				return false;
			}
		}
		else
		{
			db_error($sql);
			return false;
		}
	}
	
	// export array
	$all = array_combine($keys, $dictphrases);
	//print_array($all);
	//exit;
	$dictionary = var_export($all, true);
	
	if (!empty($langfile))
	{
		$newdictfile = LANG_FILES_DIRECTORY.'/' . $langfile . '_'. $dictlang . '.php';
	}
	else
	{
		$newdictfile = LANG_FILES_DIRECTORY.'/language_'. $dictlang . '.php';
	}
	
	//printbrx($newdictfile);
	// create lang dir if not exists
	if (!is_dir("lang"))
	{
		set_log("creating site lang dir");
		mkdir("lang");
	}
			
	if (file_exists($newdictfile))
	{
		// create name for backup of current dictionary file
		$saveas = substr($newdictfile, 0, strripos($newdictfile, '.php'));
		
		if (strripos(date(DATE_ATOM), '+'))
		{
			$date = substr(date(DATE_ATOM), 0, strripos(date(DATE_ATOM), '+'));
		}
		elseif (strripos(date(DATE_ATOM), '-'))
		{
			$date = substr(date(DATE_ATOM), 0, strripos(date(DATE_ATOM), '-'));
		}
		else
		{
			$date = date(DATE_ATOM);
		}
		
		$saveas .= '_' . $date . '.php';
		$saveas = str_replace(":", "_", $saveas);

		// rename current dictionary file
		if (!$rename_file = rename($newdictfile, $saveas))
		{
			set_log(__FUNCTION__ . "Could not rename current dictionary file");
			return false;
		}
	}
			
	$dict_array_name = 'VGA_CONTENT';
	if (file_put_contents($newdictfile, '<?php $' . $dict_array_name . ' = ' . $dictionary . '; ?>') !== false)
	{
		return true;
	}
	else
	{
		set_log(__FUNCTION__ . "Could not create new dictionary file");
		return false;
	}
}

function generate_translation_file_old($langfile, $dictlang='it')
{
	if (!isset($langfile))
	{
		return false;
	}
	// set to 'it' for now
	elseif(!isset($dictlang) or $dictlang != 'it')
	{
		return false;
	}
	else
	{
		$sql .= "SELECT lang, var_key, text, $dictlang
			FROM `vga_content`";
		
		if ($result = mysql_query($sql))
		{
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					if (!is_null($row['it']))
					{
						$str = $row['it'];
					}
					else
					{
						$str = $row['text'];
					}
					$str = stripslashes($str);
					$xmlsafe = utf8_for_xml($str);
					if (strlen($xmlsafe) != strlen($str))
					{
						$xml_error[$row['var_key']] = "'$str' was edited to '$xmlsafe'";
					}
					//$str2 = htmlentities($str, ENT_QUOTES, "ISO-8859-1"); 
					$str2 = htmlspecialchars($str, ENT_QUOTES);
					//$str2 = preg_replace('/&#0*39;/', '&apos;', $str2);
					if (empty($str2))
					{
						$encoding_error[$row['var_key']] = "'$str' was deleted by htmlentities";
					}
					$dictphrases[] = $str2;
					$keys[] = $row['var_key'];
				}
				// try exporting array
				$all = array_combine($keys, $dictphrases);
				$save = var_export($all, true);
				file_put_contents(LANG_FILES_DIRECTORY.'/language_it.php', '<?php $phrases = ' . $save . '; ?>');
				//
				if ($encoding_error)
				{
					printbr('Attention: Some strings are missing from the file!');
					print_array($encoding_error);
				}
				$numelements = count($keys);
				$dictionary = "<phrases>\n\n";
				for ($i=0; $i<$numelements; $i++)
				{
					 $dictionary .= "\t<phrase id=\"{$keys[$i]}\">{$dictphrases[$i]}</phrase>\n";		
				}
				$dictionary .= "\n</phrases>\n";
				
				$newdictfile = LANG_FILES_DIRECTORY."/$dictlang/$langfile.xml";
				
				//printbr($dictionary);
				//printbr($newdictfile);
				
				// create lang dir if not exists
				if (!is_dir("dictlang"))
				{
					mkdir("dictlang");
					set_log("creating lang dir");
				}
				// create language sub dir if not exists
				if (!is_dir("lang/$dictlang"))
				{
					set_log("creating ".LANG_FILES_DIRECTORY."/$dictlang dir");
					mkdir(LANG_FILES_DIRECTORY."/$dictlang");
				}
				
				if (file_exists($newdictfile))
				{
					// create name for backup of current dictionary file
					$saveas = substr($newdictfile, 0, strripos($newdictfile, '.xml'));
					$date = substr(date(DATE_ATOM), 0, strripos(date(DATE_ATOM), '+'));
					$saveas .= '_' . $date . '.xml';
					$saveas = str_replace(":", "_", $saveas);

					// rename current dictionary file
					if (!$rename_file = rename($newdictfile, $saveas))
					{
						set_log(__FUNCTION__ . "Could not rename current dictionary file");
						return false;
					}
				}
				
				//printbr($file_exists);
				//printbr($rename_file);
				
				$dictfile = fopen($newdictfile, "w+");
				if ($dictfile) 
				{
					fputs($dictfile, $dictionary);
					fclose($dictfile);
					return true;
				}
				else
				{
					set_log(__FUNCTION__ . "Could not create new dictionary file");
					return false;
				}
			}
			else
			{
				set_log(__FUNCTION__ . " No phrases found");
				return false;
			}
		}
		else
		{
			db_error($sql);
			return false;
		}
	}
}


function get_phrase_keys()
{
	$sql = "SELECT var_key FROM vga_content WHERE lang = 'en'";
	
	if ($result = mysql_query($sql))
	{
		if (mysql_num_rows($result) > 0)
		{
			$keys = array();
			while ($row = mysql_fetch_assoc($result))
			{
				$keys[] = $row['var_key'];
			}
			return $keys;
		}
		else
		{
			set_log(__FUNCTION__ . " No keys found!");
			return false;
		}
	}
	else
	{
		db_error($sql);
		return false;
	}
}

function generate_dictionary_file($dictfilelang='en', $langfilename, $onlypages)
{
	if (!isset($_POST['langfilename']))
	{
		return false;
	}
	else
	{
		$defaultlang = 'en';
		
		$sql = '';
		if (isset($onlypages))
		{
			// Get selected pages
			$pages = stripslashes(implode(',', $onlypages));
			$sql = "SELECT var_key, text FROM vga_content WHERE lang = '$dictfilelang'
					AND page IN ($pages)";
		}
		else
		{
			// Get entire dictionary
			$sql = "SELECT var_key, text FROM vga_content WHERE lang = '$dictfilelang'";
		}
		
		if ($result = mysql_query($sql))
		{
			if (mysql_num_rows($result) > 0)
			{
				$keys = array();
				$dictphrases = array();
				while ($row = mysql_fetch_assoc($result))
				{
					$keys[] = $row['var_key'];
					$row['text'] = stripslashes($row['text']);
					$row['text'] = htmlentities($row['text'], ENT_QUOTES, "UTF-8");
					$dictphrases[] = $row['text'];
				}
				
				$numelements = count($keys);
				$dictionary = "<phrases>\n\n";
				for ($i=0; $i<$numelements; $i++)
				{
					 $dictionary .= "\t<phrase id=\"{$keys[$i]}\">{$dictphrases[$i]}</phrase>\n";		
				}
				$dictionary .= "\n</phrases>\n";
				
				$newdictfile = LANG_FILES_DIRECTORY."/$dictfilelang/$langfilename.xml";
				
				//printbr($dictionary);
				//printbr($newdictfile);
				
				// create lang dir if not exists
				if (!is_dir("dictfilelang"))
				{
					mkdir("dictfilelang");
					set_log("creating lang dir");
				}
				// create language sub dir if not exists
				if (!is_dir(LANG_FILES_DIRECTORY."/$dictfilelang"))
				{
					set_log("creating lang/$dictfilelang dir");
					mkdir(LANG_FILES_DIRECTORY."/$dictfilelang");
				}
				
				if (file_exists($newdictfile))
				{
					// create name for backup of current dictionary file
					$saveas = substr($newdictfile, 0, strripos($newdictfile, '.xml'));
					$date = substr(date(DATE_ATOM), 0, strripos(date(DATE_ATOM), '+'));
					$saveas .= '_' . $date . '.xml';
					$saveas = str_replace(":", "_", $saveas);

					// rename current dictionary file
					if (!$rename_file = rename($newdictfile, $saveas))
					{
						set_log(__FUNCTION__ . "Could not rename current dictionary file");
						return false;
					}
				}
				
				//printbr($file_exists);
				//printbr($rename_file);
				
				$dictfile = fopen($newdictfile, "w+");
				if ($dictfile) 
				{
					fputs($dictfile, $dictionary);
					fclose($dictfile);
					return true;
				}
				else
				{
					set_log(__FUNCTION__ . "Could not create new dictionary file");
					return false;
				}
			}
			else
			{
				set_log(__FUNCTION__ . " No phrases found");
				return false;
			}
		}
		else
		{
			db_error($sql);
			return false;
		}
	}
}

function check_phrases_exist(&$phrases)
{
	$numelements = count($phrases);
		
	for ($i=0;$i<$numelements;$i++)
	{
		$text = clean_input_string($phrases[$i]['text']);
		
		$sql = "SELECT `page`, `var_key` FROM `vga_content` 
		WHERE `text` = '$text'
		AND `lang` = 'en'";
		
		if ($result = mysql_query($sql))
		{
			$phrases[$i]['defined'] = mysql_num_rows($result);
			
			if ($phrases[$i]['defined'])
			{
				$row = mysql_fetch_assoc($result);
				$phrases[$i]['def_key'] = $row['var_key'];
				$phrases[$i]['def_page'] = $row['page'];
			}
		}
		else
		{
			db_error($sql);
			return false;
		}
	}
	return true;
}

function check_new_ids_against_database()
{
	$lang = "en";
	$table = 'vga_content';
	
	$_POST = clean_input_array($_POST);
	
	$text = $_POST['text'];
	$key = $_POST['key'];
	$type = $_POST['type'];
	
	$page = $_POST['page'];
	$phrases = array();
	$numelements = count($_POST['key']);
	
	for ($i=0;$i<$numelements;$i++)
	{
		$phrases[$i]['key'] = $key[$i];
		$phrases[$i]['type'] = $type[$i];
		$phrases[$i]['var'] = $key[$i] . '_' . $type[$i];
		$sql = "SELECT `var_key` FROM `$table` 
		WHERE `var_key` = '{$phrases[$i]['var']}' 
		AND `lang` = '$lang' 
		LIMIT 1";
		
		if ($result = mysql_query($sql))
		{
			$phrases[$i]['exists'] = mysql_num_rows($result) > 0 ? true : false;
		}
		else
		{
			db_error($sql);
			echo "There was a problem accessing the database.";
			exit;
		}
	}
}
function create_new_dictionary_elements()
{
	$lang = "en";
	$page = "footer";
	$table = 'vga_content';
	
	$sql = "INSERT INTO `$table` 
	(`lang`, `page`, `var_key`, `text`) VALUES ";
	
	$_POST = clean_input_array($_POST);
	
	$text = array();
	$key = array();	
	$type = array();
	
	foreach($_POST['text'] as $value)
	{
		$text[] = $value;
	}

	foreach($_POST['key'] as $value)
	{
		$key[] = $value;
	}
	
	foreach($_POST['type'] as $value)
	{
		$type[] = $value;
	}
	
	$numelements = count($text);
	
	$values = '';
	for ($i=0;$i<$numelements;$i++)
	{
		$key[$i] .= '_' . $type[$i];
		$values .= " ('$lang', '$page', '{$key[$i]}', '{$text[$i]}') ";
		$values .= ($i < $numelements-1) ? ',' : '';
	}
	
	$sql .= $values;

	if (mysql_query($sql))
	{
		$added = mysql_affected_rows();
		$msg = "$added new entries were added.";
	}
	else
	{
		db_error($sql);
		$msg = 'There was a problem accessing the database that time. Please try again.';
	}
	
	echo "<p>$msg</p>";
}

function insert_variables_into_file($filename)
{

}
function scan_file_for_delimited_text($filename)
{
	$contents = file_get_contents($filename);
	//preg_match_all("/_\((.*?)\)/", $contents, $phrases);
	//preg_match_all("/(?<=_\()(.*?)(?=\))/", $contents, $result);

	$open = "_{";
	$close = "}_";
	$opentype = "\(";
	$closetype = "\)";

	// Format: _{(link)Rooms}_
	//preg_match_all("/(?<=$open)\((.*?)\)(.*?)(?=$close)/", $contents, $result);
	preg_match_all("/(?<=$open)$opentype(.*?)$closetype(.*?)(?=$close)/", $contents, $result);
	//print_array($result);

	$types = $result[1];
	$text = $result[2];
	$phrases = array();
	$numphrases = count($text);
	for ($i=0;$i<$numphrases;$i++)
	{
		$phrases[$i]['type'] = $types[$i];
		$phrases[$i]['text'] = $text[$i];
	}
	return $phrases;
}
?>