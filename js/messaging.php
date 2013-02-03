
var windowpanelAnimHeight = 165;
var msgwaitinterval = 5; //seconds
var messages = [];
var invites = [];

$(function()
{
	$('.windowpanel .wplinks span').live('mouseover', function(event) {
		$(this).addClass("over");
	});
	$('.windowpanel .wplinks span').live('mouseout', function(event) {
		$(this).removeClass("over");
	});
	
	$('.windowpanel .wplinks .ignore').live('click', function(event) 
	{
		$(this).parents('.windowpanel').animate({top: "-="+windowpanelAnimHeight}, 500, function() {
			$('#site_message .wpcontent').text('');
			clearMessageBoxLinks();
		});			
	});
	$('.windowpanel .wplinks .ok').live('click', function(event) 
	{
		$(this).parents('.windowpanel').animate({top: "-="+windowpanelAnimHeight}, 500, function() {
			$('#site_message .wpcontent').text('');
			clearMessageBoxLinks();
		});			
	});
	$('.windowpanel .wplinks .block_user').click(function(event) 
	{
		var from_user = parseInt($(this).data('from_user'));			
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
	
	//fetchMessages();
	fetchQuestionInvites();
	
	if (invites.length)
	{
		displaymsgs = setInterval(function() {
			displayQuestionInvite(0);
			clearInterval(displaymsgs);
		}, msgwaitinterval * 1000);
	}
});

function displayQuestionInvite(index=0)
{
	var msg = createQuestionInviteMessage(invites[0]);
	siteMessage(msg, 'invite', invites[0]);
}
function createQuestionInviteMessage(invite)
{
	return sprintf('<p>You have just been invited by <b>%s</b> to participate in the question <b>%s</b>.</p> <p>Look at it and decide if you are interested. If you have something to propose you can do it immediately. If not you can subscribe to it, and when other people have written their proposals you can participate in the voting phase.</p>', invite['username'], invite['title']);
}

function setMessageBoxLinks(type='')
{
	switch(type)
	{
	case "invite": 
	   $('#site_message .wplinks').append('<span class="subscribe">Subscribe</span> <span class="propose">Propose</span> <span class="blockuser">Block User</span> <span class="ignore">Ignore</span> <span class="next">Next</span>');
	   break;
	default:
	   $('#site_message .wplinks').append('<span class="ok">OK</span> <span class="next">Next</span>');
	}
}
function clearMessageBoxLinks()
{
	$('#site_message .wplinks').html('');
}

function siteMessage(msg, type='', data=[])
{
	setMessageBoxLinks(type);
	$('#site_message .wpcontent').html(msg);
	if (data.length)
	{
		$('#site_message').data('data', data);
	}
	$('#site_message').animate({top: "+="+windowpanelAnimHeight}, 500);
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


