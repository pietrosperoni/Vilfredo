<?php
//******************************************
//
// Code snippets to enable Facebook Connect 
//
//*****************************************
function facebook_fbconnect_init_email_js($display=true) {
global $facebook_key, $fb;

$str = <<<_HTML_
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
     <script type="text/javascript">
       FB.init("$facebook_key", "xd_receiver.htm", { permsToRequestOnConnect : "email"});
       </script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

function facebook_fbconnect_init_js($display=true) {
global $facebook_key, $fb;

$str = <<<_HTML_
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
     <script type="text/javascript">
       FB.init("$facebook_key", "xd_receiver.htm");
       </script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

// Display Facebook Connect button
function facebook_connect_for_dialog($display=true) {
global $VGA_CONTENT;

$str = <<<_HTML_
<fb:login-button v="2" size="medium" onlogin="update_dialog();">{$VGA_CONTENT['fb_or_login_button']}</fb:login-button>

<script type="text/javascript">
function update_dialog() {
	$.event.trigger('fbuserauthorized');}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}

function facebook_login_button_refresh_2($display=true) 
{

global $FACEBOOK_ID;
global $VGA_CONTENT;

if ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	$goto = 'fb_login.php';
	$button_txt = $VGA_CONTENT['fb_login_button'];
}
else
{
	$goto = 'fb_register.php';
	$button_txt = $button_txt = $VGA_CONTENT['fb_connect_button'];
}

$str = <<<_HTML_
{$VGA_CONTENT['or_use_fb_label']} <br/><br/>

<fb:login-button v="2" size="medium" onlogin="refresh_page();">$button_txt</fb:login-button>

<script type="text/javascript">
function refresh_page() {
	  window.location = "$goto";}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

function facebook_login_button_refresh($goto, $display=true) {
$str = <<<_HTML_
Or <b>login</b> with Facebook:<br/><br/>

<fb:login-button v="2" size="medium" onlogin="refresh_page();">Connect with Facebook</fb:login-button>

<script type="text/javascript">
function refresh_page() {
	  window.location = "$goto";}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

// Display logout link - redirect to logout page
function facebook_logout_link($redirect, $text='Logout', $display=true) {
$str = <<<_HTML_
<a href="javascript:FB.Connect.logoutAndRedirect('$redirect')">$text</a>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}
//---------------------------------------------------------------------------//
function facebook_request_permanent_session($text='Stay logged in', $display=true) {
$str = <<<_HTML_
<a href="javascript: FB.Connect.showPermissionDialog('offline_access', function(perms) {
   if (!perms) {
     continue_without_permission();
   } else {
     save_session();
   }
 })">$text</a>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

function facebook_connect_button_2($display=true) {
global $facebook_key;
$str = <<<_HTML_
FB.init("$facebook_key","xd_receiver.htm", {"ifUserConnected" : update_user_box});
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

// Display Profile Pic
function facebook_profile_pic($display=true) {
$str = <<<_HTML_
<fb:profile-pic uid="loggedinuser" size="square" facebook-logo="true"></fb:profile-pic>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}

// Display Username
function facebook_username($display=true) {
$str = <<<_HTML_
<fb:name uid="loggedinuser" useyou="false" linked="false"></fb:name>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}
//---------------------------------------------------------------------------//

// Display Friends List **Caution: contains widgets - May get errors with remote JS link**
function facebook_friends($display=true) {
$str = <<<_HTML_
<div id="profile_pics"></div>
<script type="text/javascript">
var widget_div = document.getElementById("profile_pics");
FB.ensureInit(function facebook_($display=true) {
  FB.Facebook.get_sessionState($display=true).waitUntilReady(function($display=true) {
  FB.Facebook.apiClient.friends_get(null, function(result) {
    var markup = "";
    var num_friends = result ? Math.min(5, result.length) : 0;
    if (num_friends > 0) {
      for (var i=0; i<num_friends; i++) {
        markup += 
          '<fb:profile-pic size="square" uid="'+result[i]+'" facebook-logo="true"></fb:profile-pic>';
      }
    }
    widget_div.innerHTML = markup;
    FB.XFBML.Host.parseDomElement(widget_div);
  });
  });
});
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}

/*
<script src="fbconnect.js" type="text/javascript"></script>
     <script type="text/javascript">window.onload = function() { facebook_onload($already_logged_in); };</script>
     
      FB.ensureInit(function() { 
            check_login_status();
            });
          </script>
           <script type="text/javascript">
           function check_login_status()
           {
           	// detect if the user is currently logged in,
            	session = FB.Facebook.apiClient.get_session();
            	if (session) {
               window.location = 'viewquestions.php'; }
      </script>
*/

function facebook_dummy($display=true) {
$str = <<<_HTML_

_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}
?>