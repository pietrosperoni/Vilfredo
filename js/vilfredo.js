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
	*	Register Dialog
	*/
	var cancelReg = function() {
		$(this).dialog("close");
	}
	
	var cancelLogin = function() {
		$(this).dialog("close");
	}
	
	var deleteDialog = function() {
		$(this).empty().remove();
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
						$('#msg').css('color', 'blue').html("Success! Welcome to Vilfredo!");
						user_dialog.dialog('option', welcomeOptions);
						$("#register_request").fadeOut(1000, function () {
							$(this).remove();
						});
						$.ajax({
							type: "POST",
							url: "top_links.php",
							cache: false,
							error: ajax_error,
							dataType: 'html',
							success: function(response, status){
								if (status != 'success')
								{
									$('#msg').css('color', 'blue').html("Oops!");
								}
								else
								{
									$('#top_links').html(response);
									$("#" + target_btn).removeAttr("disabled");
											
								}
							}
						});
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
		//$("#acceptbtn").die()
	});
	
	/*
	$("#acceptbtn.checked").livequery("click", function(e){
	$("#acceptbtn.checked").live("click", function(e){
			$(this).removeClass("checked");	
	});*/
	
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
			"OK": cancelReg
		}
	};
	
	var registerUserOptions = {
		buttons: 
		{   
			"Register": registerUser,
			"Cancel": cancelReg
		}
	};
	
	var user_dialog = $("<div></div>");
	var target_btn = '';

	$("#ajax_register").click(function (e) {
		e.preventDefault();
		target_btn = $(this).attr("btn");
		// load form using ajax
		$.get("register_form.html", function(data){
			user_dialog.html(data);
		});
		user_dialog.dialog({
			modal: true,
			position: 'top',
			title: 'Register with Vilfredo',
			resizable: false,
			close: deleteDialog,
			buttons: 
			{	"Check ID Exists": checkIdExists,
				"Cancel": cancelReg
			}
		});
	}); 
	// Dialog
	
	/*
	Login form
	*/
	$("#ajax_login").click(function (e) {
		e.preventDefault();
		target_btn = $(this).attr("btn");
		// load form using ajax
		$.get("login_form.html", function(data){
			user_dialog.html(data);
		});
		user_dialog.dialog({
			modal: true,
			position: 'top',
			title: 'Vilfredo Login',
			resizable: false,
			close: deleteDialog,
			buttons: 
			{   "Login": doLogin,
				"Cancel": cancelLogin
			}
		});
	}); 
	
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
						$("#register_request").fadeOut(1000, function () {
							$(this).remove();
						});
						$.ajax({
							type: "POST",
							url: "top_links.php",
							cache: false,
							error: ajax_error,
							dataType: 'html',
							success: function(response, status){
								if (status != 'success')
								{
									$('#msg').css('color', 'blue').html("Oops!");
								}
								else
								{
									$('#top_links').html(response);
									$("#" + target_btn).removeAttr("disabled");
									user_dialog.dialog("close");
								}
							}
						});
					}
					else
					{
						$('#msg').css('color', 'blue').html(response);
					}
				}
			}
		});
	}
	
	
	/*
	"timeout"
	"error"
	"notmodified"
	"success"
	"parsererror"
	*/
});