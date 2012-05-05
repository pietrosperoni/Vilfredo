<?php
Header("content-type: application/x-javascript");
include '../vga_functions.php';
session_start();
if (isset($_SESSION["locale"]) and ($_SESSION["locale"] == 'en' or $_SESSION["locale"] == 'it' ))
{
	$locale = $_SESSION["locale"];
}
else
{
	$locale = fetch_preferred_language_from_client();
}
@include getLanguageForJS($locale);
?>
$(function() {
	$.ajaxSetup({cache: false});
	
	/*$(document).ajaxStart(function(){
		log('ajax start event caught');
		var loading = $("#loading");
		if (loading.length)
		{ 
			loading.show();
		}
	});
	$(document).ajaxStop(function(){
		log('ajax stop event caught');
		var loading = $("#loading");
		if (loading.length)
		{ 
			loading.hide();
		}
	});*/
	
	$('#show_table_link').click(function(event) {
		if ($('#questionmap').is(':visible'))
		{
			$('#show_table_link span').html('<?=$VGA_CONTENT['show_hist_table_txt']?>');
			$('#questionmap').slideUp(1000);
		}
		else
		{
			$('#show_table_link span').html('<?=$VGA_CONTENT['hide_hist_table_txt']?>');
			$('#questionmap').slideDown(1000);
		}
	});
	$("#show_table_link").mouseenter(function(event){
		$(this).addClass("over");
	});
	$("#show_table_link").mouseleave(function(event){
		$(this).removeClass("over");
	});

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
	
	/*
	$('.clickme').click(function() {
		var elements = $(this).parent().siblings('.target');
		//alert(elements.length);
		elements.css('color', 'red');
	});
	*/
	
	$(".expandabstract").click(function (e) {
	    var label = $(this).find('.show-full-label');
	    var parent = $(this).parent();
	    var fulltxt = $(this).parent().siblings('.paretotext');
	    var textelements = fulltxt.length;
	    if (fulltxt.is(":hidden")) {
			//alert('fullt text is hidden');
			fulltxt.slideDown("slow");
			label.text("<?=$VGA_CONTENT['hide_full_txt_link']?>");
	    } 
	    else {
	    	//alert('fullt text is displayed');
		fulltxt.slideUp("slow");
		label.text("<?= $VGA_CONTENT['view_full_txt_link'] ?>");
	    }
	});
	
	$(".viewall").click(function (e) {
		    e.preventDefault();
		    var fulltxt = $(this).parent().siblings("div.paretotext");
		    var label = $(this).find('.viewall-label');
		    var bg = $(this).parents('.paretocell');
		    if (fulltxt.is(":hidden")) {
				fulltxt.slideDown("slow");
				label.text("<?=$VGA_CONTENT['show_abs_only_txt']?>");
				//bg.css('background-color', '#D0F5A9');
				bg.addClass('fulltext');
		    } 
		    else {
			fulltxt.slideUp("slow");
			label.text("<?= $VGA_CONTENT['view_full_txt_link'] ?>");
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
			label.text("<?=$VGA_CONTENT['hide_full_txt_link']?>");
			//view-all button
			viewallbtn.text("show abstract only");
	    } 
		else {
			$(this).removeClass('expandbtn-open');
			fulltxt.slideUp("slow");
			label.text("<?= $VGA_CONTENT['view_full_txt_link'] ?>");
			//view-all button
			viewallbtn.text("<?= $VGA_CONTENT['view_full_txt_link'] ?>");
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
		$.ajax({
			type: "POST",
			url: "loginuser.php",
			data: info,
			cache: false,
			error: ajax_error,
			dataType: 'html',
			success: function(response, status){
				response = jQuery.trim(response);
				if (response == '0')
				{
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['login_fail_txt']?>");
				}
				else if (response == '1')
				{
					$("#login form").fadeOut(250);
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['welcome_txt']?>");
					user_dialog.dialog('option', welcomeOptions);
				}
				else
				{
					$('#msg').css('color', 'blue').html(response);
				}
			}
		});
	}
	
	function log(msg)
	{
		//if (console && console.log) console.log(msg);
		if (typeof console != 'undefined') console.log(msg);
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
				response = jQuery.trim(response);
				if (response == '0')
				{
					$('#msg').css('color', 'blue').html('<?=$VGA_CONTENT['conn_fail_txt']?>');
				}
				else if (response == '1')
				{
					$("#connect").fadeOut(250);
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['welcome_txt']?>");
					user_dialog.dialog('option', welcomeOptions);
				}
				else if (response == '2')
				{
					$('#msg').css('color', 'blue').html('<?=$VGA_CONTENT['fbdata_fail_txt']?>');
				}
				else
				{
					$('#msg').css('color', 'blue').html(response);
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
				response = jQuery.trim(response);
				if (response == '0')
				{
					$('#msg').css('color', 'blue').html('<?=$VGA_CONTENT['reg_fail_txt']?>');
				}
				else if (response == '1')
				{
					$("#register").fadeOut(250);
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['reg_succ_txt']?>");
					user_dialog.dialog('option', welcomeOptions);
				}
				else
				{
					$('#msg').css('color', 'blue').html(response);
					// reload recaptcha
					Recaptcha.reload();
				}
			}
		});
	}
	
	var ajax_error = function (xhr) {
		$('#msg').css('color', 'red').html("<?=$VGA_CONTENT['req_fail_txt']?>");
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
				response = jQuery.trim(response);
				if (response == '0')
				{
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['username_na_txt']?>");
				}
				else 
				{
					$('#msg').css('color', 'green').html('<?=$VGA_CONTENT['username_ok_txt']?><span id="acceptbtn" class="btn"></span>');
				}
			}
		});
	};
	
	var checkNameOptions = {
		buttons: 
		{    
			"<?=$VGA_CONTENT['check_id_button']?>": checkIdExists,
			"<?=$VGA_CONTENT['cancel_button']?>": cancelReg
		}
	};
	
	var checkFBNameOptions = {
		buttons: 
		{    
			"<?=$VGA_CONTENT['check_id_button']?>": checkIdExists,
			"<?=$VGA_CONTENT['cancel_button']?>": cancelReg
		}
	};
	
	var welcomeOptions = {
		buttons:
		{    
			"<?=$VGA_CONTENT['ok_button']?>": nowSubmit
		}
	};
	
	var registerUserOptions = {
		buttons: 
		{   
			"<?=$VGA_CONTENT['register_button']?>": registerUser,
			"<?=$VGA_CONTENT['cancel_button']?>": cancelReg
		}
	};
	
	var externalOptions = {
		buttons: 
		{   
			"<?=$VGA_CONTENT['register_button']?>": loadRegister,
			"<?=$VGA_CONTENT['login_button']?>": loadLogin,
			"<?=$VGA_CONTENT['cancel_button']?>": cancelReg
		}
	};
	
	var loginUserOptions = {
		buttons: 
		{   
			"<?=$VGA_CONTENT['login_button']?>": doLogin,
			"<?=$VGA_CONTENT['cancel_button']?>": cancelLogin
		}
	};
	
	var connectUserOptions = {
			buttons: 
			{   
				"<?=$VGA_CONTENT['connect_button']?>": doFBConnect,
				"<?=$VGA_CONTENT['cancel_button']?>": cancelLogin
			}
	};
	
	var fbUserOptions = {
		buttons: 
		{   
			"<?=$VGA_CONTENT['login_button']?>": doFBConnect,
			"<?=$VGA_CONTENT['cancel_button']?>": cancelLogin
		}
	};
	
	var loadRegister = function() {
		$.get("register_form.php", function(data){
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
				response = jQuery.trim(response);
				if (response == '0')
				{
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['login_fail_txt']?>");
				}
				else if (response == '1')
				{
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['welcome_txt']?>");
					user_dialog.dialog('option', welcomeOptions);
					dialog_cont.html("");
				}
				else if (response == '2')
				{
					$('#msg').css('color', 'blue').html("<?=$VGA_CONTENT['fbdata_fail_txt']?>");
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
		});
	}
		
	var loadLogin = function() {
		$.get("login_form.php", function(data){
			dialog_cont.html(data);
		});
		user_dialog.dialog('option', loginUserOptions);
	} 
	
	var user_dialog;
	var dialog_cont;
	var submit_form;
	var blank = "";

	$("input.reg_submit").click(function (e) 
	{
		submit_form = $(this).parents("form");
		
		if ($('#anon:checked').val() !== undefined) 
		{
			submit_form.submit();
		}
		else
		{
			user_dialog = $('<div id="dialog"><div id="loading"></div><p id="msg" class="message"></p><div id="data"></div></div>');
		
			/*
			$.get("js/popup.html", function(data){
				user_dialog = data;
				console.log("Popup dialog loaded");
			});*/
			
			setTimeout(function() {
				$('#loading').show();
				$("#loading").ajaxStart(function(){
					$(this).show();	
				});
				$("#loading").ajaxStop(function(){
					$(this).hide();
				});
			}, 250);
			
			//user_dialog = $(user_dialog);
			user_dialog.bind('fbuserauthorized', doFBLogin);
			user_dialog.dialog({
				modal: true,
				position: 'top',
				title: 'Vilfredo',
				width: 500,
				resizable: false,
				close: deleteDialog,
				autoOpen: false,
				buttons: 
				{    
					"<?=$VGA_CONTENT['register_button']?>": loadRegister,
					"<?=$VGA_CONTENT['login_button']?>": loadLogin,
					"<?=$VGA_CONTENT['cancel_button']?>": cancelReg
				}
			});
			dialog_cont = $('#dialog #data');
			$.get("dialog_splash.php", function(data){
				dialog_cont.html(data);
			});
			user_dialog.dialog('open');
		}
	}); 
});


function checklengths() {
		var limit = $("#content_rte").data('maxlength');
		var limit_abs = $("#abstract_rte").data('maxabslength');
		var abstract_txt = $("#abstract_rte").contents().find("body").text();
		var proposal_txt = $("#content_rte").contents().find("body").text();
		var abstract_length = abstract_txt.length;
		var content_length = proposal_txt.length;
		var title = $("#abstract_title");
		var content_msg = $("#content_rte_chars_msg");
	
		if ((content_length  > 0 && content_length <= limit && abstract_length <= limit_abs) || (content_length  > 0 && abstract_length > 0 && abstract_length <= limit_abs))
		{
			$("#submit_p").removeAttr("disabled");
		}
		else 
		{
			$("#submit_p").attr("disabled");
		}
	
		if (content_length  > limit)
		{
			if (abstract_length == 0)
			{
				title.html("<?=$VGA_CONTENT['abstract_req_ex_txt']?>")
					.css({"color": "red", "font-weight" : "bold"});
				content_msg.html("<?=$VGA_CONTENT['abstract_req_txt']?>")
					.css({'color' : 'red', 'font-weight' : 'bold'});
			}
			else
			{
				title.html("<?=$VGA_CONTENT['abstract_req_ex_txt']?> OK!")
					.css({"color" : "green", "font-weight" : "bold"}); 
				content_msg.html("Abstract OK!")
					.css({'color' : 'green', 'font-weight' : 'bold'});
			}
		}
		else if ( content_length  <= limit )
		{
			title.html("<?=$VGA_CONTENT['abs_opt_link']?>");
			title.css("color", "black"); 
			title.css("font-weight", "normal"); 
			$("#content_rte_chars_msg").html("");
		}
		// Set Abstract indicator
		var abs_remaining = limit_abs - abstract_length;
		var abs_indicator = $("#abstract_rte" + "_chars_remaining");
		abs_indicator.text(abs_remaining);
		if (abs_remaining < 0) {
			abs_indicator.addClass("length_not_ok");
		} else {
			abs_indicator.removeClass("length_not_ok");
		} 
		// Set Proposal Content indicator
		var prop_remaining = limit - content_length;
		var prop_indicator = $("#content_rte" +"_chars_remaining");
		prop_indicator.text(prop_remaining);
		if (prop_remaining < 0 && abstract_length == 0) {
			prop_indicator.addClass("length_not_ok");
		} else {
			prop_indicator.removeClass("length_not_ok");
		}
	}