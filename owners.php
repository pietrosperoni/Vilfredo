<?php 
include('header.php'); 
//header('Content-Type: text/html; charset=utf-8'); 

// Get user ID if logged in
$userid=isloggedin();

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

if (isAdmin($userid))
{
		$voting_settings;
		if (!empty($_POST))
		{
			$voting_settings['display_interactive_graphs'] = 
				($_POST['display_interactive_graphs']) ? 1 : 0;
			$voting_settings['display_key_players'] = 
				($_POST['display_key_players']) ? 1 : 0;
			$voting_settings['display_confused_voting_option'] =
				($_POST['display_confused_voting_option']) ? 1 : 0;
			$voting_settings['optional_voting_comments'] =
				($_POST['optional_voting_comments']) ? 1 : 0;
			$voting_settings['require_voting_comments'] =
				($_POST['require_voting_comments']) ? 1 : 0;
			$voting_settings['anonymize_graph'] =
				($_POST['anonymize_graph']) ? 1 : 0;
			$voting_settings['proposal_node_layout'] =
				($_POST['proposal_node_layout']) ? $_POST['proposal_node_layout'] : 'Layers';
			$voting_settings['user_node_layout'] =
				($_POST['user_node_layout']) ? $_POST['user_node_layout'] : 'Layers';
			$voting_settings['pareto_proposal_node_layout'] =
				($_POST['proposal_node_layout']) ? $_POST['proposal_node_layout'] : 'Layers';
			$voting_settings['pareto_user_node_layout'] =
				($_POST['user_node_layout']) ? $_POST['user_node_layout'] : 'Layers';
				
							
			foreach ($voting_settings as $setting)
			{
				if ($setting != 0 and $setting != 1)
				{
					setError("Invalid values passed.");
					header("Location: error_page.php");
					exit;
				}
			}
			if (!save_voting_settings($voting_settings))
			{
				printbr('Failed to update settings!');
				$voting_settings = fetch_voting_settings();
			}
			else
			{
				printbr('Settings saved!');
			}
		}
		else
		{
			$voting_settings = fetch_voting_settings();
		}
?>
<style type="text/css">
 td 
 {
	padding: 2px;
 }

 td.lang 
 {
	width: 200px;
 }

.links {
	height: 50px;
	font-size: 1.2em;
	margin-top: 25px;
}

.intro {
	font-size: 1.1em;
	width: 600px;
	background-color: #FFEFD5;
	padding: 10px;
}
.options {
	font-size: 1.1em;
	list-style-type: none;
}
.options li{
	padding: 5px;
}

input[type='submit']#settings {
	font-size: 1.1em;
	width: 125px;
	height: 50px;
	margin-left: 20px;
}

</style>
<div class="intro">
	<h3>Admin: Voting Options.</h3>
	<p>Set the voting options below then click save to make the changes live.</p>
</div>

<form autocomplete="off" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<ul class="options">
	<li> Display Interactive Graphs <input type="checkbox" name="display_interactive_graphs" value="1" <?php if ($voting_settings['display_interactive_graphs']) echo "checked"; ?> /> </li>
	<li> Display Key Players <input type="checkbox" name="display_key_players" value="1" <?php if ($voting_settings['display_key_players']) echo "checked"; ?> /> </li>
	<li> Display Confused Voting Option <input type="checkbox" name="display_confused_voting_option" value="1" <?php if ($voting_settings['display_confused_voting_option']) echo "checked"; ?> /> </li>
	<li> Optional Voting Comments <input type="checkbox" name="optional_voting_comments" value="1" <?php if ($voting_settings['optional_voting_comments']) echo "checked"; ?> /> </li>
	<li> Require Voting Comments <input type="checkbox" name="require_voting_comments" value="1" <?php if ($voting_settings['require_voting_comments']) echo "checked"; ?> /> </li>
	<li> Anonymize Votes in Graph <input type="checkbox" name="anonymize_graph" value="1" <?php if ($voting_settings['anonymize_graph']) echo "checked"; ?> /> </li>
	
	<li>Proposal Node Layout on <b>All Votes</b> Graph
	<select name="proposal_node_layout">
	<option value="Layers" <?php if ($voting_settings['proposal_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['proposal_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['proposal_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</li>
	<li>User Node Layout on <b>All Votes</b> Graph
	<select name="user_node_layout">
	<option value="Layers" <?php if ($voting_settings['user_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['user_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['user_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</li>
	
	<li>Proposal Node Layout on <b>Pareto</b> Graph
	<select name="proposal_node_layout">
	<option value="Layers" <?php if ($voting_settings['pareto_proposal_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['pareto_proposal_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['pareto_proposal_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</li>
	<li>User Node Layout on <b>Pareto</b> Graph
	<select name="user_node_layout">
	<option value="Layers" <?php if ($voting_settings['pareto_user_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['pareto_user_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['pareto_user_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</li>
	
	</ul>
	
	<input type="submit" name="submit" id="settings" value="Save Settings">
</form>

<?php	
}
include('footer.php');
?>