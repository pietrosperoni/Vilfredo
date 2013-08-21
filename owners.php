<?php 
include('header.php'); 

?>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery-1.6.2.min.js"></script>
<?php

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
			$voting_settings['use_voting_comments'] =
				($_POST['use_voting_comments']) ? $_POST['use_voting_comments'] : 'No';
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

fieldset {
	border: 1px solid black;
	margin: 20px 0;
}

legend {
	font-weight: bold;
}

</style>
<div class="intro">
	<h3>Admin: Voting Options.</h3>
	<p>Set the voting options below then click save to make the changes live.</p>
</div>

<form autocomplete="off" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<div class="options">
	<p> Display Interactive Graphs <input type="checkbox" name="display_interactive_graphs" value="1" <?php if ($voting_settings['display_interactive_graphs']) echo "checked"; ?> /> </p>
	<p> Display Key Players <input type="checkbox" name="display_key_players" value="1" <?php if ($voting_settings['display_key_players']) echo "checked"; ?> /> </p>
	<p> Display Confused Voting Option <input type="checkbox" name="display_confused_voting_option" value="1" <?php if ($voting_settings['display_confused_voting_option']) echo "checked"; ?> /> </p>
	
	<p> Anonymize Votes in Graph <input type="checkbox" name="anonymize_graph" value="1" <?php if ($voting_settings['anonymize_graph']) echo "checked"; ?> /> </p>
	
	<fieldset>
	<legend>Voting Comments</legend>
	
	<p>Use Voting Comments
	<select name="use_voting_comments">
	<option value="No" <?php if ($voting_settings['pareto_proposal_node_layout']=="No") echo 'selected="selected"'; ?>>No</option>
	<option value="Optional" <?php if ($voting_settings['pareto_proposal_node_layout']=="Optional") echo 'selected="selected"'; ?>>Optional</option>
	<option value="Required" <?php if ($voting_settings['pareto_proposal_node_layout']=="Required") echo 'selected="selected"'; ?>>Required</option>
	</select>
	</p>
	</fieldset>
	
	<fieldset>
		<legend>All Proposals Graph</legend>
		
	<p>Proposal Node Layout
	<select name="proposal_node_layout">
	<option value="Layers" <?php if ($voting_settings['use_voting_comments']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['use_voting_comments']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['use_voting_comments']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</p>
	
	<p>User Node Layout
	<select name="user_node_layout">
	<option value="Layers" <?php if ($voting_settings['user_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['user_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['user_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</p>
	</fieldset>
	
	<fieldset>
		<legend>Pareto Graph</legend>
	<p>
		Proposal Node Layout
	<select name="proposal_node_layout">
	<option value="Layers" <?php if ($voting_settings['pareto_proposal_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['pareto_proposal_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['pareto_proposal_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</p>
	<p>User Node Layout
	<select name="user_node_layout">
	<option value="Layers" <?php if ($voting_settings['pareto_user_node_layout']=="Layers") echo 'selected="selected"'; ?>>Layers</option>
	<option value="NVotes" <?php if ($voting_settings['pareto_user_node_layout']=="NVotes") echo 'selected="selected"'; ?>>Num Votes</option>
	<option value="Flat" <?php if ($voting_settings['pareto_user_node_layout']=="Flat") echo 'selected="selected"'; ?>>Flat</option>
	</select>
	</p>
	</fieldset>
	
	</div>
	
	<input type="submit" name="submit" id="settings" value="Save Settings">
</form>

<?php	
}
include('footer.php');
?>