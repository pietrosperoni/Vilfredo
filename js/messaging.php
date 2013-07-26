
var windowpanelAnimHeight = 205;
var msgwaitinterval = 5; //seconds
var messages = [];
var invites = [];
var currentinvite;
var currentinvitedisplay = 0;
var numinvites = 0;
var cookieexpires = 1; //days

$(function()
{
	if (!messagingOn)
	{
		$('#window_bar .cont .button').css('display', 'none');
		return;
	}
	
	$('.windowpanel .wplinks span').live('mouseover', function(event) {
		$(this).addClass("over");
	});
	$('.windowpanel .wplinks span').live('mouseout', function(event) {
		$(this).removeClass("over");
	});
	
	$('.windowpanel .wplinks .subscribe').live('click', function(event) 
	{
		var question = parseInt($('#site_message').data('data')['question']);
		
		if (parseInt($('#site_message').data('data')['subscribed']))
		{
			questionUnsubscribe(question);
		}
		else
		{
			result = questionSubscribe(question);
		}
	});
	$('.windowpanel .wplinks .propose').live('click', function(event) 
	{
		var question = $('#site_message').data('data')['question'];
		var room = $('#site_message').data('data')['room'];
		var query = "q="+question;
		if (room != '')
		{
			query += "&room="+room;
		}
		window.location = domain+"/viewquestion.php?"+query;
	});
	$('.windowpanel .wplinks .next').live('click', function(event) 
	{
		nextInvite();
	});
	$('.windowpanel .wplinks .prev').live('click', function(event) 
	{
		prevInvite();
	});
	
	$('.windowpanel .wplinks .ignore').live('click', function(event) 
	{
		var invite_id = parseInt($('#site_message').data('data')['id']);
		ignoreInvite(invite_id);		
	});
	
	$('.windowpanel .wpcontent p img.eye').live('click', function(event) 
	{
		showSiteMessageQuestionPopup();
	});
	
	$('.windowpanel .wpcontent p ..viewquestion').live('click', function(event) 
	{
		showSiteMessageQuestionPopup();
	});
	$('.windowpanel .wpcontent p .viewquestion').live('mouseover', function(event) 
	{
		$(this).addClass('over');
	});
	$('.windowpanel .wpcontent p .viewquestion').live('mouseout', function(event) 
	{
		$(this).removeClass('over');
	});
	
	
	/*
	$('.windowpanel .wplinks .ignore').live('click', function(event) 
	{
		$(this).parents('.windowpanel').animate({top: "-="+windowpanelAnimHeight}, 500, function() {
			$('#site_message .wpcontent').text('');
			clearMessageBoxLinks();
			$('#site_message').removeData('data');
			$(this).fadeOut(1);
		});			
	});
	*/
	$('.windowpanel .wplinks .close').live('click', function(event) 
	{
		closeWindowpanel();
	});
	$('.windowpanel .wplinks .blockuser').live('click', function(event) 
	{
		var from_user = parseInt($('#site_message').data('data')['sender']);
		blockUserInvites(from_user, userid);
	});
	
	$('#site_error').click(function(event) 
	{
		$(this).slideUp(500, function() {
			$(this).text('');
		});
	});
	$('#site_feedback').click(function(event) 
	{
		$(this).slideUp(500, function() {
			$(this).text('');
		});
	});
	$('#site_help').click(function(event) 
	{
		$(this).slideUp(500, function() {
			$(this).text('');
		});
	});
	$('#topmsgbutton').click(function(event) 
	{
		//displayQuestionInvite(0);
		setCurrentInviteFromCookie();
	});
	
	if (messagingOn)
	{
		fetchQuestionInvites();
	
		if (invites.length)
		{
			$('#window_bar .cont .button.msg').addClass('m').attr('title','You have invitations');
			
			if ($.cookie('invitetab'))
			{
				setCurrentInviteFromCookie();
			}
			
			/*
			displaymsgs = setInterval(function() {
				displayQuestionInvite(0);
				clearInterval(displaymsgs);
			}, msgwaitinterval * 1000);
			*/
		}
	}
});

function showSiteMessageQuestionPopup()
{
	var title = inviteDataValue('title')+' by '+inviteDataValue('username');
	setPopupTitle(title);
	setPopupContent(inviteDataValue('blurb'));
	displayPopup();
}
function hideSiteMessageQuestionPopup()
{
	hidePopup();
}

function inviteDataValue(key)
{
	return $('#site_message').data('data')[key];
}

function closeWindowpanel()
{
	$('.windowpanel').animate({top: "-="+windowpanelAnimHeight}, 500, function() {
		$('#site_message .wpcontent').text('');
		clearMessageBoxLinks();
		$(this).fadeOut(1);
	});
	$.cookie('invitetab', null);
}

function setCurrentInvite(index)
{
	currentinvite = index;
	currentinvitedisplay = currentinvite + 1;
}

function nextInvite()
{
	if (currentinvite < numinvites - 1)
	{
		currentinvite++;
		currentinvitedisplay = currentinvite+1;
		displayQuestionInvite(currentinvite);
		return true;
	}
	else
	{
		return false;
	}
}
function prevInvite()
{
	if (currentinvite > 0)
	{
		currentinvite--;
		currentinvitedisplay = currentinvite+1;
		displayQuestionInvite(currentinvite);
		return true;
	}
	else
	{
		return false;
	}
}

function displayQuestionInvite(index)
{
	index = (index === undefined) ? 0 : index;
	currentinvite = index;
	setInviteCookies();
	currentinvitedisplay = currentinvite+1;
	var msg = createQuestionInviteMessage(invites[index]);
	siteMessage(msg, 'invite', invites[index]);
}
function createQuestionInviteMessage(invite)
{
	var shorttitle = invite['title'];
	if (shorttitle.length > 30)
	{
		shorttitle = shorttitle.substring(0,30) + "...";
	}
		
	return sprintf('<p>You have just been invited by <b>%s</b> to participate in the question <span class="viewquestion" title="%s">%s</span></p> <p>Look at it and decide if you are interested. If you have something to propose you can do it immediately. If not you can subscribe to it, and when other people have written their proposals you can participate in the voting phase.</p>', invite['username'], invite['title'], shorttitle);
}

function setMessageBoxLinks(type)
{
	type = (type === undefined) ? '' : type;
	switch(type)
	{
	case "invite": 
	   $('#site_message .wplinks').append('<span class="subscribe">Subscribe</span> <span class="propose">Propose</span> <span class="blockuser">Block User</span> <span class="ignore">Ignore</span> <span class="prev"><<</span> <span class="curr"></span>/<span class="numinvites"></span> <span class="next">>></span> <span class="close">Close</span ');
	   break;
	default:
	   $('#site_message .wplinks').append('<span class="close">Close</span> <span class="next">Next</span>');
	}
}
function clearMessageBoxLinks()
{
	$('#site_message .wplinks').html('');
}

function setCurrentInviteFromCookie()
{
	var showindex = fetchInviteIndexCookieValue();
	displayQuestionInvite(showindex);
}
function clearInviteCookies()
{
	$.cookie('invitetabindex', null);
	$.cookie('inviteid', null);
}
function setInviteCookies()
{
	$.cookie('invitetabindex', currentinvite, {expires: cookieexpires, path: '/'});
	$.cookie('inviteid', invites[currentinvite]['id'], {expires: cookieexpires, path: '/'});
}
function fetchInviteIndexCookieValue()
{
	if ($.cookie('invitetabindex') && $.cookie('inviteid'))
	{
		var index = parseInt($.cookie('invitetabindex'));
		if (index > invites.length-1)
		{
			clearInviteCookies()
			return 0;
		}
		var id = parseInt($.cookie('inviteid'));
		var inviteid = parseInt(invites[index]['id']);
		if (index < invites.length && inviteid == id)
		{
			return index;
		}
		else
		{
			clearInviteCookies()
			return 0;
		}
	}
	else
	{
		clearInviteCookies()
		return 0;
	}
}
function inviteCookiesValid()
{
	if ($.cookie('invitetabindex') && $.cookie('inviteid'))
	{
		var index = parseInt($.cookie('invitetabindex'));
		var id = parseInt($.cookie('inviteid'));
		var inviteid = parseInt(invites[index]['id']);
		if (index < invites.length && inviteid == id)
		{
			return true;
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

function setNumInvites()
{
	numinvites = invites.length;
	
	if (invites.length)
	{
		$('#site_message .wplinks .numinvites').text(numinvites);
	}
	else
	{
		closeWindowpanel();
		$('#window_bar .cont .button.msg').removeClass('m').attr('title','No new invitations');
	}
}

function siteMessage(msg, type, data)
{
	type = (type === undefined) ? '' : type;
	data = (data === undefined) ? [] : data;
	
	$.cookie('invitetab', 1, {expires: cookieexpires, path: '/'});
	$('#site_message .wpcontent').html(msg);
	$('#site_message').data('data', data);
		
	if (!$('#site_message').is(':visible'))
	{
		$('#site_message').fadeIn(1, function(){
			setMessageBoxLinks(type);
			$('#site_message .wplinks .curr').text(currentinvitedisplay);
			$('#site_message .wplinks .numinvites').text(numinvites);
			if (data['subscribed'] == '0')
			{
				$('#site_message .wplinks .subscribe').text('Subscribe');
			}
			else
			{
				$('#site_message .wplinks .subscribe').text('Unsubscribe');
			}
			
			$('#site_message').animate({top: "+="+windowpanelAnimHeight}, 500);
		});
	}
	else
	{
		$('#site_message .wplinks .curr').text(currentinvitedisplay);
		$('#site_message .wplinks .numinvites').text(numinvites);
		if (data['subscribed'] == '0')
		{
			$('#site_message .wplinks .subscribe').text('Subscribe');
		}
		else
		{
			$('#site_message .wplinks .subscribe').text('Unsubscribe');
		}
	}
}
function siteError(msg)
{
	$('#site_error').text(msg).slideDown(500);
}
function siteFeedback(msg)
{
	$('#site_feedback').text(msg).slideDown(500);
}
function siteHelp(msg)
{
	$('#site_help').text(msg).slideDown(500);
}

// (e, xhr, settings, exception)
function refresh_ajax_error(jqxhr, status, error)
{
	log('error in: ' + status.url + ' \n'+'error:\n' + jqxhr.responseText );
}

function log(msg)
{
	//if (console && console.log) console.log(msg);
	if (typeof console != 'undefined') console.log(msg);
}


function ignoreInvite(invite_id)
{
	$.ajax({
		type: "POST",
		url: "../ignoreInvite.php",
		cache: false,
		async: false,
		data: ({tv_invite_id : invite_id}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("blockUserInvites: Could not ignore invite "+invite_id);
					return false;
				}
				else if (jQuery.trim(data) == '1')
				{
					log("blockUserInvites: Successfully ignored invite "+invite_id);
					//var ignore_index = currentinvite;
					/*
					if (!nextInvite())
					{
						prevInvite();
					}
					*/
					invites.splice(currentinvite, 1);
					setNumInvites();
					if (currentinvite > invites.length - 1)
					{
						setCurrentInvite(currentinvite-1);
					}
					displayQuestionInvite(currentinvite);
					return true;
				}
			}
		}
	});
}

function blockUserInvites(from_user, to_user)
{
	$.ajax({
		type: "POST",
		url: "../blockUser.php",
		cache: false,
		async: false,
		data: ({tv_from_user : from_user, tv_to_user : to_user}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("blockUserInvites: Could not block invites from user "+from_user);
					return false;
				}
				else if (jQuery.trim(data) == '1')
				{
					log("blockUserInvites: Successfully blocked invites from user "+from_user);
					var tmp = [];
					var i;
					for (i = 0; i < invites.length; ++i) 
					{
					    if (invites[i]['sender'] != from_user)
						{
							tmp.push(invites[i]);
						}
						invites = tmp;
						tmp = null;
						numinvites = invites.length;
						setNumInvites();
					}
					return true;
				}
			}
		}
	});
}
function unblockUserInvites(from_user, to_user)
{
	$.ajax({
		type: "POST",
		url: "../unblockUser.php",
		cache: false,
		async: false,
		data: ({tv_from_user : from_user, tv_from_user : to_user}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("blockUserInvites: Could not unblock invites from user "+from_user);
					return false;
				}
				else if (jQuery.trim(data) == '1')
				{
					log("blockUserInvites: Successfully unblocked invites from user "+from_user);
					return true;
				}
			}
		}
	});
}

function questionSubscribe(question)
{
	$.ajax({
		type: "POST",
		url: "../questionSubscribe.php",
		cache: false,
		async: false,
		data: ({tv_userid : userid, tv_question : question}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("questionSubscribe: Could not subscribe to question "+question);
					return false;
				}
				else if (jQuery.trim(data) == '1')
				{
					log("questionSubscribe: Successfully subscribed to question "+question);
					$('#site_message').data('data')['subscribed'] = '1';
					$('.windowpanel .wplinks .subscribe').html('Unsubscribe');
					return true;
				}
			}
		}
	});
}
function questionUnsubscribe(question)
{
	$.ajax({
		type: "POST",
		url: "../questionUnsubscribe.php",
		cache: false,
		async: false,
		data: ({tv_userid : userid, tv_question : question}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("questionSubscribe: Could not unsubscribe to question "+question);
					return false;
				}
				else if (jQuery.trim(data) == '1')
				{
					log("questionSubscribe: Successfully unsubscribed from question "+question);
					$('#site_message').data('data')['subscribed'] = '0';
					$('.windowpanel .wplinks .subscribe').html('Subscribe');
					return true;
				}
			}
		}
	});
}

function fetchQuestionInvites()
{
	$.ajax({
		type: "POST",
		url: "../fetchQuestionInvites.php",
		cache: false,
		async: false,
		data: ({tv_userid : userid}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("fetchItemGrid: Could not retrieve messages");
				}
			}
			else
			{
				invites = data;
				numinvites = invites.length;
			}
		}
	});
}

function fetchMessages()
{
	$.ajax({
		type: "POST",
		url: "../fetchMessages.php",
		cache: false,
		async: false,
		data: ({tv_userid : userid}),
		error: refresh_ajax_error,
		dataType: 'json',
		success: function(data, status)
		{
			if ((typeof data) == 'number')
			{
				if (jQuery.trim(data) == '0')
				{
					log("fetchItemGrid: Could not retrieve messages");
				}
			}
			else
			{
				messages = data;
			}
		}
	});
}