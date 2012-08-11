<?php
//******************************************
//
// Code snippets to enable Facebook Connect 
//                          V3
//
//****************************************

// Set Facebook permissions
$facebook_permissions = "user_groups,friends_groups,email,publish_stream"; // read_stream
// Set Facebook authorization link for canvas page
$facebook_canvas_auth_link = "https://www.facebook.com/dialog/oauth?client_id=$facebook_key&redirect_uri=$facebook_canvas&scope=$facebook_permissions";



function facebook_fbconnect_init_js($display=true)
{
	global $facebook_key, $fb, $facebook_permissions;

//channelUrl: 'localhost/vilfredo/channel.html',
$site_domain = SITE_DOMAIN;

$channel_url = '//' . $_SERVER['HTTP_HOST'] . '/channel.html';
set_log("Site Domain: " . SITE_DOMAIN);
if ($_SERVER['HTTP_HOST'] != 'localhost') {
$str = <<<_HTML_
<div id="fb-root"></div>
 <script>
        window.fbAsyncInit = function() {
          FB.init({
            appId: '$facebook_key',
			channelUrl: '$channel_url',
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true  // parse XFBML
          });
          // Additional initialization code here
        };
        // Load the SDK Asynchronously
        (function(d){
           var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/en_US/all.js";
           ref.parentNode.insertBefore(js, ref);
         }(document));
      </script>
_HTML_;
}
else {
$str = <<<_HTML_
<div id="fb-root"></div>
 <script>
        window.fbAsyncInit = function() {
          FB.init({
            appId: '$facebook_key',
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true  // parse XFBML
          });
          // Additional initialization code here
        };
        // Load the SDK Asynchronously
        (function(d){
           var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/en_US/all.js";
           ref.parentNode.insertBefore(js, ref);
         }(document));
      </script>
_HTML_;
}
	
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}


function facebook_fbconnect_init_js_V1($display=true)
{
	global $facebook_key, $fb, $facebook_permissions;

//channelUrl: 'localhost/vilfredo/channel.html',
$site_domain = SITE_DOMAIN;

$channel_url = '//' . $_SERVER['HTTP_HOST'] . '/channel.html';

/* HEREDOC Set output string containing javascript */
$str = <<<_HTML_
<div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '$facebook_key',
		  channelUrl: '$channel_url',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
_HTML_;
/* HEREDOC */
	
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}
//******************************************
//
// Code snippets to enable Facebook Connect 
//				V1
//****************************************
function facebook_fbconnect_init_js_off($display=true) {
global $facebook_key, $fb, $facebook_permissions;

$str = <<<_HTML_
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
     <script type="text/javascript">
       FB.init("$facebook_key", "xd_receiver.htm");
       </script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

// Display Facebook Connect button
/*
function facebook_connect_for_dialog_v3($display=true) {
global $VGA_CONTENT, $facebook_permissions, $fb;
$loginUrl = ""; 
$link = ""; 
$str = "";

try 
{
	$loginUrl = $fb->getLoginUrl(array( "display" => "popup", "scope" => $facebook_permissions, "onlogin" => "update_dialog()"));
}
catch (FacebookApiException $e) 
{
    log_error($e);
}

if (!empty($loginUrl)) 
{
	$link = '<span id="fb_button">';
	$link .= '<a href="'.$loginUrl.'">Login with Facebook</a>';
	$link .= '</span>';

$str = <<<_HTML_
$link

<script type="text/javascript">
function update_dialog() {
	$.event.trigger('fbuserauthorized');}
</script>
_HTML_;

}
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}
*/

function facebook_connect_for_dialog_v3($display=true) {
global $VGA_CONTENT, $facebook_permissions;

$str = <<<_HTML_
<fb:login-button scope="$facebook_permissions" v="2" size="medium" onlogin="update_dialog();">{$VGA_CONTENT['fb_or_login_button']}</fb:login-button>

<script type="text/javascript">
function update_dialog() {
	$.event.trigger('fbuserauthorized');
}
function parsed_done() {
	if (typeof console != 'undefined') console.log("Login button parsed...");
}
</script>

<script>
FB.XFBML.parse(document.getElementById('fb_button'), parsed_done());
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}


function facebook_connect_for_dialog($display=true) {
global $VGA_CONTENT, $facebook_permissions;

$str = <<<_HTML_
<div id="fb_button">
<fb:login-button scope="$facebook_permissions" v="2" size="medium" onlogin="update_dialog();">{$VGA_CONTENT['fb_or_login_button']}</fb:login-button>
</div>

<script type="text/javascript">
function update_dialog() {
	$.event.trigger('fbuserauthorized');}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : '';
}

function facebook_login_button_refresh_2_plugin($display=true) 
{
global $FACEBOOK_ID, $VGA_CONTENT, $facebook_permissions;
$button_txt = $VGA_CONTENT['fb_login_button'];

if ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	$goto = 'plugin_fb_login.php';
}
else
{
	$goto = 'plugin_fb_register.php';
}

$str = <<<_HTML_
{$VGA_CONTENT['or_use_fb_label']} <br/><br/>

<fb:login-button scope="$facebook_permissions" v="2" size="medium" onlogin="refresh_page();">$button_txt</fb:login-button>

<script type="text/javascript">
function refresh_page() {
	  window.location = "$goto";}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

function facebook_login_button_refresh_2($display=true) 
{

global $FACEBOOK_ID, $VGA_CONTENT, $facebook_permissions;
$button_txt = $VGA_CONTENT['fb_login_button'];

if ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	$goto = 'fb_login.php';
}
else
{
	$goto = 'fb_register.php';
}

$str = <<<_HTML_
{$VGA_CONTENT['or_use_fb_label']} <br/><br/>

<fb:login-button scope="$facebook_permissions" v="2" size="medium" onlogin="refresh_page();">$button_txt</fb:login-button>

<script type="text/javascript">
function refresh_page() {
	  window.location = "$goto";}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

function facebook_login_header_button_refresh($display=true) 
{

global $FACEBOOK_ID, $VGA_CONTENT, $facebook_permissions;
$button_txt = $VGA_CONTENT['fb_login_button'];

if ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	$goto = 'fb_login.php';
}
else
{
	$goto = 'fb_register.php';
}

$str = <<<_HTML_

<fb:login-button scope="$facebook_permissions" v="2" size="medium" onlogin="refresh_page();">$button_txt</fb:login-button>

<script type="text/javascript">
function refresh_page() {
	  window.location = "$goto";}
</script>
_HTML_;
return (USE_FACEBOOK_CONNECT && $display) ? $str : ''; 
}

function facebook_login_button_refresh($goto, $display=true) {

global $facebook_permissions;

$str = <<<_HTML_
Or <b>login</b> with Facebook:<br/><br/>

<fb:login-button scope="$facebook_permissions" v="2" size="medium" onlogin="refresh_page();">Connect with Facebook</fb:login-button>

<script type="text/javascript">
function refresh_page() {
	  window.location = "$goto";}
</script>
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