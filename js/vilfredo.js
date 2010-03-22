$(function() {
	$("#abstract_panel").accordion({
		collapsible: true,
		autoHeight: false,
		active: false
	});
	
	$(".view-all").hover(
		function() { $(this).addClass('view-all-hover'); },
		function() { $(this).removeClass('view-all-hover'); }
	);
	
	$(".viewall").hover(
		function() { $(this).addClass('viewall-hover'); },
		function() { $(this).removeClass('viewall-hover'); }
	);
	
	
	$(".expandabstract").hover(
		function() { $(this).addClass('expandabstract-hover'); },
		function() { $(this).removeClass('expandabstract-hover'); }
	);
	
	$(".expandabstract").click(function (e) {
	    var label = $(this).find('.show-full-label');
	    var fulltxt = $(this).parent().siblings("div.paretotext");
	    var expandbtn = $(this).parents('.paretoabstract').siblings("a.expandbtn");
	    var expandbtnlabel = $(this).parents('.paretoabstract').siblings("a.expandbtn").find('.show-full-label');
	    if (fulltxt.is(":hidden")) {
			fulltxt.slideDown("slow");
			label.text("Hide Full Text");
			// expand button
			expandbtn.addClass('expandbtn-open');
			expandbtnlabel.text("Hide Full Text");
	    } 
	    else {
		fulltxt.slideUp("slow");
		label.text("View Full Text");
		// expand button
		expandbtn.removeClass('expandbtn-open');
		expandbtnlabel.text("View Full Text");
	    }
	});
	
	$(".viewall").click(function (e) {
		    e.preventDefault();
		    var fulltxt = $(this).parent().siblings("div.paretotext");
		    var label = $(this).find('.viewall-label');
		    var bg = $(this).parents('.paretocell');
		    if (fulltxt.is(":hidden")) {
				fulltxt.slideDown("slow");
				label.text("show abstract only");
				//bg.css('background-color', '#D0F5A9');
				bg.addClass('fulltext');
		    } 
		    else {
			fulltxt.slideUp("slow");
			label.text("view full text");
			//bg.css('background-color', '#FFFFFF');
			bg.removeClass('fulltext');
		   }
	});

	$(".expandbtn").click(function (e) {
	    e.preventDefault();
	    var fulltxt = $(this).siblings("div.paretotext");
	    var viewallbtn = $(this).siblings("h3").find("span.view-all-label");
	    var label = $(this).find('.show-full-label');
	    if (fulltxt.is(":hidden")) {
	    		$(this).addClass('expandbtn-open');
			fulltxt.slideDown("slow");
			label.text("Hide Full Text");
			//view-all button
			viewallbtn.text("show abstract only");
	    } 
		else {
			$(this).removeClass('expandbtn-open');
			fulltxt.slideUp("slow");
			label.text("View Full Text");
			//view-all button
			viewallbtn.text("view full text");
	     }
	});
	
	$(".expandbtn").hover(
		function() { $(this).addClass('expandbtn-hover'); },
		function() { $(this).removeClass('expandbtn-hover'); }
	);
	
	$(".paretoabstractfulltextlink").hover(
		function() { $(this).addClass('paretoabstractfulltextlink-hover'); },
		function() { $(this).removeClass('paretoabstractfulltextlink-hover'); }
	);
	    
	/*
	*	Register & Login Dialog
	*/
	var cancelReg = function() {
		$(this).dialog("close");
	}
	
	var submit_form_handler = function() {
		$(this).parents("form").submit();
	}
	
	$("input.submit_ok").click( function() 
	{
		$(this).parents("form").submit();
	});
	
	var nowSubmit = function() {
		$(this).dialog("close");
		submit_form.submit();
	}
	
	var cancelLogin = function() {
		$(this).dialog("close");
	}
	
	var deleteDialog = function() {
		$(this).empty().remove();
	}
	
	var doLogin = function() {
		info = $("#login form").serialize();
		info += "&action=login";
		$.ajax({
			type: "POST",
			url: "loginuser.php",
			data: info,
			cache: false,
			error: ajax_error,
			dataType: 'html',
			success: function(response, status){
				if (status != 'success')
				{
					$('#msg').css('color', 'red').html(status);
				}
				else
				{
					if (response == '0')
					{
						$('#msg').css('color', 'blue').html('Sorry, login failed.');
					}
					else if (response == '1')
					{
						$("#login form").fadeOut(250);
						$('#msg').css('color', 'blue').html("Welcome back! Click OK to contine.");
						user_dialog.dialog('option', welcomeOptions);
					}
					else
					{
						$('#msg').css('color', 'blue').html(response);
					}
				}
			}
		});
	}
	
	var doFBConnect = function() {
		info = $("#connect form").serialize();
		$.ajax({
			type: "POST",
			url: "fbconnectuser.php",
			cache: false,
			data: info,
			error: ajax_error,
			dataType: 'html',
			success: function(response, status){
				if (status != 'success')
				{
					$('#msg').css('color', 'red').html(status);
				}
				else
				{
					if (response == '0')
					{
						$('#msg').css('color', 'blue').html('Sorry, connect failed.');
					}
					else if (response == '1')
					{
						$("#connect").fadeOut(250);
						$('#msg').css('color', 'blue').html("Welcome back! Click OK to contine.");
						user_dialog.dialog('option', welcomeOptions);
					}
					else if (response == '2')
					{
						$('#msg').css('color', 'blue').html('Sorry, could not get user data from Facebook.');
					}
					else
					{
						$('#msg').css('color', 'blue').html(response);
					}
				}
			}
		});
	}
	
	var registerUser = function() {
		info = $("#register form").serialize();
		info += "&action=register";
		$.ajax({
			type: "POST",
			url: "newuser.php",
			data: info,
			cache: false,
			error: ajax_error,
			dataType: 'html',
			success: function(response, status){
				if (status != 'success')
				{
					$('#msg').css('color', 'red').html(status);
				}
				else
				{
					if (response == '0')
					{
						$('#msg').css('color', 'blue').html('Sorry, could not register you at thie time.');
					}
					else if (response == '1')
					{
						$("#register").fadeOut(250);
						$('#msg').css('color', 'blue').html("Success! Welcome to Vilfredo!");
						user_dialog.dialog('option', welcomeOptions);
					}
					else
					{
						$('#msg').css('color', 'blue').html(response);
					}
				}
			}
		});
	}
	
	var ajax_error = function (xhr) {
		alert('Ajax Error: ' + xhr.statusText);
	}
	
	$("#acceptbtn").livequery("click", function(e){
		$(this).addClass("checked");
		// Display remaining fields
		var fields = $("#register form div.reg_form");
		if (fields.is(":hidden")) {
			fields.slideDown("slow");
		}
		// Disable userid field
		var userfield = $("#register form input#username");
		$(userfield).attr("disabled","disabled")
			.css('color', 'green')
			.css('font-weight', 'bold');
		user_dialog.dialog('option', registerUserOptions);
	});
	
	var checkIdExists = function() {
		var info = 'username=';
		var userfield = $("#register form input#username");
		var nameok = $("#register form input#usernameok");
		var savename = $(userfield).val();
		nameok.val(savename);
		info += $(userfield).val();
		info += "&action=checkname";
		$.ajax({
			type: "POST",
			url: "newuser.php",
			data: info,
			cache: false,
			error: ajax_error,
			dataType: 'html',
			success: function(response, status){
				if (status != 'success')
				{
					$('#msg').css('color', 'red').html(status);
				}
				else
				{
					if (response == '0')
					{
						$('#msg').css('color', 'blue').html('Username not available.');
					}
					else 
					{
						$('#msg').css('color', 'green').html('Username available: Accept?<span id="acceptbtn" class="btn"></span>');
					}
				}
			}
		});
	};
	
	var checkNameOptions = {
		buttons: 
		{    
			"Check ID Exists": checkIdExists,
			"Cancel": cancelReg
		}
	};
	
	var checkFBNameOptions = {
		buttons: 
		{    
			"Check ID Exists": checkIdExists,
			"Cancel": cancelReg
		}
	};
	
	var welcomeOptions = {
		buttons:
		{    
			"OK": nowSubmit
		}
	};
	
	var registerUserOptions = {
		buttons: 
		{   
			"Register": registerUser,
			"Cancel": cancelReg
		}
	};
	
	var externalOptions = {
		buttons: 
		{   
			"Register:": loadRegister,
			"Login": loadLogin,
			"Cancel": cancelReg
		}
	};
	
	var loginUserOptions = {
		buttons: 
		{   
			"Login": doLogin,
			"Cancel": cancelLogin
		}
	};
	
	var connectUserOptions = {
			buttons: 
			{   
				"Connect": doFBConnect,
				"Cancel": cancelLogin
			}
	};
	
	var fbUserOptions = {
		buttons: 
		{   
			"Login": doFBConnect,
			"Cancel": cancelLogin
		}
	};
	
	var loadRegister = function() {
		$.get("register_form.html", function(data){
			dialog_cont.html(data);
		});
		user_dialog.dialog('option', checkNameOptions);
	} 
	
	var getFBConnectForm = function() {
		$.get("fb_connect_form.php", function(data){
			dialog_cont.html(data);
		});
		setTimeout(function() {
			$('#fbregister').click(function(event) {
				event.preventDefault();
				doFBLogin();
			}); 
		}, 250);
		user_dialog.dialog('option', connectUserOptions);
	}
	
	var doFBLogin = function() {
		$.ajax({
			type: "POST",
			url: "fb_register_form.php",
			cache: false,
			error: ajax_error,
			dataType: 'html',
			success: function(response, status){
				if (status != 'success')
				{
					$('#msg').css('color', 'red').html(status);
				}
				else
				{
					if (response == '0')
					{
						$('#msg').css('color', 'blue').html('Sorry, login failed.');
					}
					else if (response == '1')
					{
						$('#msg').css('color', 'blue').html("Welcome back! Click OK to contine.");
						user_dialog.dialog('option', welcomeOptions);
						dialog_cont.html("");
					}
					else if (response == '2')
					{
						$('#msg').css('color', 'blue').html('Sorry, could not get user data from Facebook.');
					}
					else
					{
						$("#fb_button").fadeOut(250);
						user_dialog.dialog('option', checkFBNameOptions);
						dialog_cont.html(response);
						setTimeout(function() {
							$('#fbconnect').click(function(event) {
								event.preventDefault();
								getFBConnectForm();
							}); 
						}, 250);
					}
				}
			}
		});
	}
		
	var loadLogin = function() {
		$.get("login_form.html", function(data){
			dialog_cont.html(data);
		});
		user_dialog.dialog('option', loginUserOptions);
	} 
	
	var user_dialog;
	var dialog_cont;
	var submit_form;
	var blank = "";

	$("input.reg_submit").click(function (e) {
		submit_form = $(this).parents("form");
		user_dialog = $('<div id="dialog"><div id="loading"></div><p id="msg" class="message"></p><div id="data"></div></div>');
		setTimeout(function() {
			$('#loading').show();
			$("#loading").ajaxStart(function(){
				$(this).show();	
			});
			$("#loading").ajaxStop(function(){
				$(this).hide();
			});
		}, 250);
		$(user_dialog).bind('fbuserauthorized', doFBLogin);
		user_dialog.dialog({
			modal: true,
			position: 'top',
			title: 'Vilfredo',
			resizable: false,
			close: deleteDialog,
			autoOpen: false,
			buttons: 
			{    
				"Register": loadRegister,
				"Login": loadLogin,
				"Cancel": cancelReg
			}
		});
		dialog_cont = $('#dialog #data');
		$.get("dialog_splash.php", function(data){
			dialog_cont.html(data);
		});
		//dialog_cont.html('<h2>Please log in or register.</h2>');
		user_dialog.dialog('open');
	}); 
});