$(function() {
	$("#abstract_panel").accordion({
		collapsible: true,
		autoHeight: false,
		active: false
	});

	$(".expandbtn").click(function () {
	    var fulltxt = $(this).siblings("div.paretoabstract");
	    if (fulltxt.is(":hidden")) {
			fulltxt.slideDown("slow");
			$(this).text("Hide Full Text");
	    } 
		else {
			fulltxt.slideUp("slow");
			$(this).text("Show Full Text");
	     }
	});
	    
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
						$("#register form").fadeOut(250);
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
		$("#acceptbtn").expire();
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
	
	var nowSubmit = function() {
		//submit_form.removeClass('reg-only');
		$(this).dialog("close");
		//submit_form.submit();
		submit_form.unbind("click");
	}
	
	var loadRegister = function() {
		$.get("register_form.html", function(data){
			dialog_cont.html(data);
		});
		user_dialog.dialog('option', checkNameOptions);
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

	$("input.reg_submit").click(function (e) {
		submit_form = $(this).parents("form");
		user_dialog = $('<div id="dialog"><div id="data"></div></div>');
		user_dialog.dialog({
				modal: true,
				position: 'top',
				title: 'Vilfredo',
				resizable: false,
				close: deleteDialog,
				autoOpen: false,
				buttons: 
				{    
					"Register:": loadRegister,
					"Login": loadLogin,
					"Cancel": cancelReg
				}
		});
		dialog_cont = $('#dialog #data');
		dialog_cont.html('<h2>Please log in or register.</h2>');
		user_dialog.dialog('open');
	}); 
});