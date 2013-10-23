<?php
//header('Content-type: text/html; charset=UTF-8');
//include "config.inc.php";
include('header.php');

/*
if (!$userid)
{
	DoLogin();
}
elseif (!isAdmin($userid))
{
	header("Location: viewquestions.php");
	exit;
}
*/
set_log($snapshots);

// Hardwire for now - the only data we have
$question = 145;
$generation = 1;
$title = 'all';

$snapshots = load_snapshots($question, $generation, $title);

?>
<style>
#window_bar {
	display: none;
}

#desc .user {
	color: blue;
}

#controls {
	margin: 20px auto;
	width: 600px;
}
#controls input {
	height: 30px;
	font-size: 1.1em;
	margin: 0 20px;
	background-color: #C0D9D9;
}

#switcher {
	position: absolute;
	top:20;
	left: 20;
	width: 200px;
	height: 200px;
	z-index: 10;
	border: green solid 1px;
	padding: 3px;
	cursor: pointer;
}
#graphsbox {
	position: relative;
	width: 80%;
	height: 600px;
	margin: 0 auto;
	padding: 0;
	border: blue solid 1px;
	z-index: 1;
}
#voting, #parties, #swvoting, #swparties {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
#parties, #swvoting {
	display: none;
}
#graph {
	width: 80%;
	height: 600px;
	margin: 0 auto;
	padding: 0;
	border: blue solid 1px;
	margin-bottom: 20px;
}
.myprop {
	stroke: red;
}
.pf {
	stroke: green;
}
/* arrow */
.arrow.voted path {
	stroke: red;
}
.arrow.voted polygon {
	stroke: red;
	fill: red;
}
/* user */
.user.currentuser.voted polygon {
	fill: red
}
.user.currentuser.voted text {
	fill: white
}
/* proposal, proposalgroup */
.proposal.voted polygon, .proposalgroup.voted polygon {
	stroke: red;
}
/* usergroup */
/* .usergroup.voted > polygon:first-of-type { */
.usergroup.currentuser.voted title:first-child + polygon {
	stroke: red;
}
.usergroup.currentuser.voted a.currentuserlink polygon {
	fill: red;
} 
.usergroup.currentuser.voted a.currentuserlink text {
	fill: white
}
</style>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery.svg.min.js"></script>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery.svgdom.min.js"></script>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery.svganim.min.js"></script>
<script type="text/javascript" src="js/jquery-color.js"></script>

<script type="text/javascript">
function isArray(o) {
  return Object.prototype.toString.call(o) === '[object Array]';
}

var svggraph;
var userid = <?=json_encode($userid)?>;
var defaultColor = '#cd8c95';
var snapshots = <?=json_encode($snapshots)?>;
var snapshot_path = 'snapshots/';//<?=json_encode(SNAPSHOTS_PATH)?>;
var loaded = 0;
var last = snapshots.length - 1;

function activeButton($active)
{
	$active.data('bgcolor', $active.css("background-color"));
	$active.animate({backgroundColor: "#ADEAEA"},"slow");
}
function inactiveButton($active)
{
	$color = $active.data('bgcolor');
	$active.animate({backgroundColor: $color},"slow");
}
function buttonPressed_v1($active)
{
	origcolor = $active.css("background-color");
	$active.animate({backgroundColor: "#97FFFF"}, 1000, function(){
    	$(this).animate({backgroundColor: origcolor}, 2000);
	});
}
function buttonPressed($active)
{
	origcolor = $active.css("background-color");
	$active.css("background-color", "#97FFFF");
	$active.animate({backgroundColor: origcolor}, 2000);
}

function generateSnapshot(svgfile)
{
	console.log("generateSnapshot called....");
	console.log(svgfile);
	//return;
	$.ajax({
		type: "POST",
		url: "./generateSnapshot.php",
		async: false,
		error: function ajax_error(jqxhr, status, error)
				{
					return false;
				},
		data: ({hash : svgfile}),
		dataType: 'json',
		success: function(data, status)
		{
			data = jQuery.trim(data);
			console.log(data + " returned");

			if (parseInt(data) == 0)
			{
				console.log("Could not generate requested snapshot due to some error");
				return false;
			}
			else if (parseInt(data) == 1)
			{
				console.log("Snapshot image successfully generated");
				console.log('NOW the file exists....');
				loadSnapshot(svgfile);
			}
			else
			{
				console.log(data);
				return false;
			}
		}
	});
}


function setNode(node)
{
	//switch(node.data(''))
}

function setProposal(prop, color)
{
    if (typeof prop == 'string')
	{
		prop = $('#'+id);
	}
	if (prop instanceof jQuery)
	{
		prop.find('polygon').attr('stroke', color);
	}
}

function setProposalGroup(propgroup, color)
{
	if (typeof propgroup == 'string')
	{
		propgroup = $('#'+id);
	}
	if (propgroup instanceof jQuery)
	{
		propgroup.find('polygon').attr('stroke', color);
	}
}

function setArrow(arrow, color)
{
    if (typeof arrow == 'string')
	{
		arrow = $('#'+id);
	}
	if (arrow instanceof jQuery)
	{
		arrow.find('path').css('stroke', color);
	    arrow.find('polygon').css('stroke', color).css('fill', color);
	}
}

function setUser(user, color, textcolor)
{
    if (typeof user == 'string')
	{
		user = $('#'+user);// assume id?
	}
	if (user instanceof jQuery)
	{
		user.find('polygon').attr('fill', color);
	    user.find('text').attr('fill', textcolor);
	}  
}

function groupWithUser(usergroup, id, color, textcolor)
{
    if (usergroup instanceof jQuery)
	{
		usergroup.children('polygon').eq(0).attr('stroke', color);
		var user = usergroup.find('a').filter(function() {
		   return $(this).attr('xlink:href') == 'u'+id;
	    });
		user.find('polygon').attr('fill', color);
	    user.find('text').attr('fill', textcolor);
	}
}

function setUserInGroup(userid, color, textcolor)
{
	var user = $('a').filter(function() {
	   return $(this).attr('xlink:href') == 'u'+userid;
    });
    user.find('polygon').attr('fill', color);
    user.find('text').attr('fill', textcolor);
    user.parent('g').children('polygon').eq(0).attr('stroke', color);
}
//------------------------------------------------------------//
function getUser(graph, userid)
{
	return $('.user, .usergroup', graph.svg('get').root()).filter(function() {
		return jQuery.inArray('u'+userid, $(this).data('users')) > -1;
	});
}
function getProposal(graph, propid)
{
	return $('.proposal, .proposalgroup', graph.svg('get').root()).filter(function() {
		return jQuery.inArray('p'+propid, $(this).data('proposals')) > -1;
	});
}
function getNode(nodeid)
{
	return $('#'+nodeid).eq(0);
}
// returns JQuery object of class arrow
function getPathsOutOfNodeID(nodeid)
{
	return $('.arrow').filter(function() {
		return $(this).data('source_id') == nodeid;
	});
}
function getPathsOutOfNode(node)
{
	return getPathsOutOfNodeID(node.attr('id'));
}
//------------------------------------------------------------//
function showCurrentUser(graph)
{
	console.log("showCurrentUser called...");
	var path = getUserPath(graph, userid);
	showVotes(path);
}

function getUserPath(graph, userid)
{
	var path = [];
	var user = getUser(graph, userid);
	var nodeid = user.attr('id');
	getPath(nodeid, path);
	return path;
}
function showVotes(path)
{
	$.each(path, function(){
		$(this).addClass('voted');
	});
}
function hideVotes(path)
{
	$.each(path, function(){
		$(this).removeClass('voted');
	});
}

function getPath(nodeid, path)
{
	console.log("getPath called...");
	var node = getNode(nodeid);
	path.push(node);
	
	var pathsout = getPathsOutOfNode(node);
	if (pathsout.length == 0)
	{
		return;
	}
	else
	{
		pathsout.each(function(){
			path.push($(this));
			getPath($(this).data('target_id'), path);
		});
	}
}
//-----------------------------------------------------------//
function setCurrentUser(graph)
{
	var currentuser = getUser(graph, userid);
	currentuser.addClass('currentuser');
	if (currentuser.hasClass('usergroup'))
	{
		var userlink = currentuser.find('a').filter(function() {
		   return $(this).attr('xlink:href') == 'u'+userid;
	    });
		userlink.addClass('currentuserlink');
	}
}

// svg.toSVG()
function setGraphData(g)
{
	if (typeof g.data('dataset') != 'undefined')
	{
		// data set
		//console.log("Graph has data set - returning");
		return;
	}
	else
	{
		g.data('dataset', true);
	}

	var svg = g.svg('get');
	var checked = $('.node, .edge', svg.root());
	checked.each(function(i){
		var id = $(this).attr('id');
		if ($(this).hasClass('node'))
		{
			var pmatches = id.match(/p/g);
			if (pmatches)
			{
				if (pmatches.length > 1)
				{
					$(this).addClass('proposalgroup');
					$(this).data('nodetype', 'proposalgroup');
					// Set data
					var proposals = id.split('_');
					$(this).data('proposals', proposals);
				}
				else if (pmatches.length == 1)
				{
					$(this).addClass('proposal');
					$(this).data('nodetype', 'proposal');
					// Set data
					var proposals = [id];
					//console.log(proposals);
					$(this).data('proposals', proposals);
				}
			}
			else
			{
				var umatches = id.match(/u/g);
				if (umatches)
				{
					if (umatches.length > 1)
					{
						$(this).addClass('usergroup');
						$(this).data('nodetype', 'usergroup');
						// Set data
						var users = id.split('_');
						$(this).data('users', users);
					}
					else if (umatches.length == 1)
					{
						$(this).addClass('user');
						$(this).data('nodetype', 'user');
						// Set data
						var users = [id];
						$(this).data('users', users);
					}
				}
			}
		}
		else if ($(this).hasClass('edge') && id.match(/-/g))
		{	
			$(this).addClass('arrow');
			var data = id.split('--');
			$(this).data('source_id', data[0]);
			$(this).data('target_id', data[1]);
			var sources = data[0].split('_');
			var targets = data[1].split('_');
			$(this).data('targets', targets);
			$(this).data('sources', sources);
		}
	});
}

function fetchSnapshot(svgfile)
{
	$.ajax({
	    url:'http://vilfredo.local/'+snapshot_path+svgfile+".svg",
	    type:'HEAD',
	    error: function()
	    {
	        console.log('Oops...'+svgfile+'.svg does not exist! Generate it.');
			generateSnapshot(svgfile);
	    },
	    success: function()
	    {
	        console.log('file exists....');
			loadSnapshot(svgfile);
	    }
	});
}

function loadGraph(svgfile)
{
	console.log("loadGraph called. Loading "+svgfile);
	$('#graph').svg({loadURL: "maps/"+svgfile, onLoad: initGraph});
	svggraph = $('#graph').svg('get');
}

function loadSnapshot(svgfile)
{
	console.log("loadSnapshot called. Loading "+snapshot_path+svgfile+".svg");
	$('#graph').svg('destroy');
	$('#graph').svg();
	svggraph = $('#graph').svg('get');
	svggraph.load(snapshot_path+svgfile+".svg", {onLoad: initGraph});
	$('#desc').html("User <span class=\"user\">"+snapshots[loaded]['username']+"</span> votes...");
	//$('#graph').svg({loadURL: snapshot_path+svgfile+".svg", onLoad: initGraph});

}

function initGraph(svg, error) 
{
	setGraphSize(svg);
	var graph = $(this);
	setGraphData(graph);
	setCurrentUser(graph);
	showCurrentUser(graph);
}
function initInset(svg) {
	setGraphSize(svg);
	$('a').click(function(event) {
	   	event.preventDefault();
		switchGraphDisplay();
		switchInsetDisplay();
	});
}
function switchInsetDisplay()
{
	if ($('#swvoting').is(':visible'))
	{
		$('#swvoting').fadeOut(1000, function(){
			$('#swparties').fadeIn(1000);
		});
	}
	else
	{
		$('#swparties').fadeOut(1000, function(){
			$('#swvoting').fadeIn(1000);
		});
	}
}
function switchGraphDisplay()
{
	if ($('#voting').is(':visible'))
	{
		$('#voting').fadeOut(1000, function(){
			$('#parties').fadeIn(1000);
		});
	}
	else
	{
		$('#parties').fadeOut(1000, function(){
			$('#voting').fadeIn(1000);
		});
	}
}
function setGraphSize(svg, width, height) 
{
	gwidth = width || $(svg._container).innerWidth();
	gheight = height || $(svg._container).innerHeight();
	svg.configure({width: gwidth, height: gheight});
}

$(function() {
	$('#first').click(function(event){
		buttonPressed($(this));
		loaded = 0;
		console.log("Loading snapshot "+loaded);
		loadSnapshot(snapshots[0]['hash']);
		//fetchSnapshot(snapshots[0]['hash']);
	});
	$('#last').click(function(event){
		buttonPressed($(this));
		var last_id = snapshots.length - 1;
		console.log('Loading last snapshot with id = '+last_id+ ' : '+snapshots[last_id]['hash']+'.svg');
		loaded = last_id;
		console.log("Loading snapshot "+loaded);
		loadSnapshot(snapshots[last_id]['hash']);
		//fetchSnapshot(snapshots[last_id]['hash']);
	});
	$('#previous').click(function(event){
		if (loaded == 0)
		{
			return;
		}
		buttonPressed($(this));
		loaded = loaded - 1;
		console.log("Loading snapshot "+loaded);
		loadSnapshot(snapshots[loaded]['hash']);
		//fetchSnapshot(snapshots[loaded]['hash']);
	});
	$('#next').click(function(event){
		if (loaded == last)
		{
			return;
		}
		buttonPressed($(this));
		loaded = loaded + 1;
		console.log("Loading snapshot "+loaded);
		loadSnapshot(snapshots[loaded]['hash']);
		//fetchSnapshot(snapshots[loaded]['hash']);
	});
});
</script>


<h2>Timeline: Generation 1 : <span id="desc"></span></h2>
<h3>"How should Vilfredo handle participants who wish to remain anonymous?"</h3>

<div id="controls">
<input type="button" value="|< First" id="first">	
<input type="button" value="<< Previous" id="previous">	
<input type="button" value="Next >>" id="next">	
<input type="button" value="Last |>" id="last">	
</div>
<div id="graph"></div>


<?php
require_once 'footer.php';
?>