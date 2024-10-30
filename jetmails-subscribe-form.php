<?php
/*
Plugin Name: JetMails Subscribe Form
Plugin URI: http://www.jetmails.com/
Description: The jetMails plugin allows you to easily setup a subscribe form for your list.
Version: 1.3.1
Author: JetMails.com
Author URI: http://www.jetmails.com/
*/

/*  Copyright 2010-2011  JetMails.com <info@jetmails.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!class_exists('JetMailsAPI')) {
	require_once( str_replace('//','/',dirname(__FILE__).'/') .'JetMailsAPI.class.php');
}

function jetmails_subscribe_form_display_widget($args=array()){
	extract($args);

	$apikey = get_option('jetmails_apikey');

	if (!isset($apikey) || ($apikey == "")){
		echo $before_widget;
		echo '<div class="jetmails_error_msg">There was a problem loading your JetMails config. Please run the setup process under Settings->JetMails Form Setup</div>';
		echo "$after_widget\n";
		return;
	}
	$msg = '';
	if (isset($_REQUEST['jetmails_signup_submit'])){
		$failed = false;
		$listId = get_option('jetmails_list_id');
		$email = $_REQUEST['jetmails_var_email_address'];
		$data = Array();
		if (isset($_REQUEST['jetmails_var_first_name']))
			$data["first_name"] = $_REQUEST['jetmails_var_first_name'];
		if (isset($_REQUEST['jetmails_var_last_name']))
			$data["last_name"] = $_REQUEST['jetmails_var_last_name'];

		$errs = array();
		if (trim($email)==''){
			$failed = true;
			$errs[] = __("You must fill in", 'jetmails_i18n').' '.__("Email Address", 'jetmails_i18n').'.';
		} else {
			$api = new JetMailsAPI($apikey);
			$retval = $api->listSubscribe( $listId, $email, $data);
			if (!$retval){
				switch($api->errorCode){
					case '103' :
						$errs[] = __("That email address is already subscribed to the list", 'jetmails_i18n').'.';
						break;
					case '102' : $errs[] = __("That email address is invalid", 'jetmails_i18n').'.'; break;
					default:
						$errs[] = $api->errorCode.": ".$api->errorMessage; break;
				}
				$failed = true;
			} else {
				$msg = "<strong class='jetmails_success_msg'>".__("Success, you've been signed up! Please look for our confirmation email!", 'jetmails_i18n')."</strong>";
			}
		}
		if (sizeof($errs)>0){
			$msg = '<span class="jetmails_error_msg">';
			foreach($errs as $error){
				$msg .= "» ".htmlentities($error, ENT_COMPAT, 'UTF-8').'<br/>';
			}
			$msg .= '</span>';
		}
	}
	$uid = get_option('jetmails_user_id');
	$list_name = get_option('jetmails_list_name');
	$header =  get_option('jetmails_header_content');

	echo $before_widget;
	?>
<div id="jetmails_signup_container"><a name="jetmails_signup_form"></a>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>#jetmails_signup_form" id="jetmails_signup_form">
<div class="jetmails_custom_border"><?php
	if ($header != '') {
 		echo '<legend>'.$header.'</legend>';
	} 
 ?><div class="updated" id="jetmails_message"><?php echo $msg; ?></div>
	<?php
	foreach(Array('email_address','first_name','last_name') as $var){  #,'first_name','last_name'
		$opt = 'jetmails_var_'.$var;
		$var_name =  str_replace ("_", " ", $var);
		if (sizeof($errs) == 0) {
			$var_value = '';
		} else {
			$var_value = $_REQUEST['jetmails_var_'.$var];
		}
		echo '<div class="jetmails_merge_var">';
		echo '<label for="'.$opt.'" class="jetmails_var_label">'.__(ucwords($var_name), 'jetmails_i18n');
		if ($var == "email_address"){ echo ' *'; }
		echo '</label>';
		echo '<input type="text" size="18" value="'.$var_value.'" name="'.$opt.'" id="'.$opt.'" class="jetmails_input" />';
		echo '</div>';
	}
	//echo '<div id="jetmails_required">* = '.__('required field', 'jetmails_i18n').'</div>';
	$apikey = get_option('jetmails_apikey');
	$listId = get_option('jetmails_list_id');
	$api = new JetMailsAPI($apikey);
	
	?>
<div class="jetmails_signup_submit"><input type="submit" name="jetmails_signup_submit" id="jetmails_signup_submit"
	value="<?php echo htmlentities(get_option('jetmails_submit_text'), ENT_COMPAT, 'UTF-8'); ?>" class="button" /></div>
</div>
</form>
</div>
	<?php
	echo $after_widget;

}

function jetmails_subscribe_form_admin_css() {
echo "
<style type='text/css'>
.error_msg { color: red; }
.success_msg { color: green; }
</style>
";
}


function jetmails_subscribe_form_main_css() {
echo "
<style type='text/css'>
.jetmails_error_msg { color: red; }
.jetmails_success_msg { color: green; }
.jetmails_custom_border{ padding: 5px; }
.jetmails_custom_border legend {}
#jetmails_signup_form .jetmails_var_label, 
#jetmails_signup_form .jetmails_input { float:left; clear:both; }
#jetmails_signup_form legend { padding:.5em; margin:0; }
#jetmails_subscriber_count {float:left; clear:both;}
#jetmails_required { float:left; clear:both; }
.jetmails_signup_submit { width:100%; text-align:center; clear:both; padding:.2em; }
</style>
<!--[if IE]>
<style type='text/css'>
#jetmails_signup_form fieldset { position: relative; }
#jetmails_signup_form legend {padding:.3em;position: absolute;top: -1em;left: .2em;}
#jetmails_message { padding-top:1em; }
</style>
<![endif]--> 
";
}//jetmails_main_css


// Hook for initializing the plugins, mostly for i18n
add_action( 'init', 'jetmails_subscribe_form_plugin_init' );
function jetmails_subscribe_form_plugin_init(){
  load_plugin_textdomain( 'jetmails_i18n', str_replace(ABSPATH,'',dirname(__FILE__).'/po') );
}


// Hook for our css
add_action('admin_head', 'jetmails_subscribe_form_admin_css');
add_action('wp_head', 'jetmails_subscribe_form_main_css');

// Hook for adding admin menus
add_action('admin_menu', 'jetmails_subscribe_form_add_pages');
function jetmails_subscribe_form_add_pages(){
	add_options_page( __( 'JetMails Subs Form Setup', 'jetmails_i18n' ), __( 'JetMails Form Setup', 'jetmails_i18n' ), 7, 'jetmails_subscribe_form_setup_page', 'jetmails_subscribe_form_setup_page');  
}

function jetmails_subscribe_form_setup_page(){

$msg = '';
?>
<div class="wrap">
<h2><?php echo __( 'JetMails List Setup', 'jetmails_i18n');?> </h2>
<?php
if ($_REQUEST['action']==='logout'){
    update_option('jetmails_apikey', '');
}
if (isset($_REQUEST['jetmails_username']) && isset($_REQUEST['jetmails_password'])){
	$delete_setup = false;
	$api = new JetMailsAPI($_REQUEST['jetmails_username'], $_REQUEST['jetmails_password']);
	if ($api->errorCode == ''){
		$msg = "<span class='success_msg'>".htmlentities(__( "Success! We were able to verify your username & password! Let's continue, shall we?", 'jetmails_i18n' ))."</span>";
		update_option('jetmails_username', $_REQUEST['jetmails_username']);
		update_option('jetmails_apikey', $api->apikey);
		if (get_option('jetmails_list_id')!=''){
			$lists = $api->lists();
            $delete_setup = true;
			foreach($lists as $list){ 
				if ($list['id']==get_option('jetmails_list_id')){ 
					$list_id = $_REQUEST['jetmails_list_id']; 
					$delete_setup=false; 
				} 
			}
		}
	} else {
		$msg .= "<span class='error_msg'>".htmlentities(__( 'Uh-oh, we were unable to login and verify your username & password. Please check them and try again!', 'jetmails_i18n' ))."<br/>";
		$msg .= __( 'The server said:', 'jetmails_i18n' )."<i>".$api->errorMessage."</i></span>";
		if (get_option('jetmails_username')==''){
			$delete_setup = true;
		}
	}
	if ($delete_setup){
		delete_option('jetmails_list_id');
		delete_option('jetmails_list_name');
	}
	$user = $_REQUEST['jetmails_username'];
} else {
	$user = get_option('jetmails_username');
	$pass = get_option('jetmails_password');
}
if (get_option('jetmails_apikey')!=''){
    $apikey = get_option('jetmails_apikey');
	$api = new JetMailsAPI($apikey);
	$lists = $api->lists();
	
	foreach($lists as $list){ 
		if ($list['id']==$_REQUEST['jetmails_list_id']){ 
			$list_id = $_REQUEST['jetmails_list_id']; 
			$list_name = $list['name']; 
		} 
	}
	$orig_list = get_option('jetmails_list_id');
	if ($list_id != ''){
        update_option('jetmails_list_id', $list_id);
	    update_option('jetmails_list_name', $list_name);
        if ($orig_list != $list_id){
	        update_option('jetmails_header_content',__( 'Sign up for', 'jetmails_i18n' ).' '.$list_name);
	        update_option('jetmails_submit_text',__( 'Subscribe', 'jetmails_i18n' ));
        }

	    $msg = '<span class="success_msg">'.
	        sprintf(__( 'Success!')).
	        ' "'.$list_name.'"<br/><br/>'.
		    __('Now you should either Turn On the JetMails Widget or change your options below, then turn it on.', 'jetmails_i18n').'</span>';
    }

}
if (isset($_REQUEST['reset_list'])){
	delete_option('jetmails_list_id');
	delete_option('jetmails_list_name');
	delete_option('jetmails_header_content');
	delete_option('jetmails_submit_text');
	$msg = '<span class="success_msg">'.__('Successfully Reset your List selection... Now you get to pick again!', 'jetmails_i18n').'</span>';
}
if (isset($_REQUEST['change_form_settings'])){
	$content = stripslashes($_REQUEST['jetmails_header_content']);
	$content = str_replace("\r\n","<br/>", $content);
	update_option('jetmails_header_content', $content );
	$submit_text = stripslashes($_REQUEST['jetmails_submit_text']);
	$submit_text = str_replace("\r\n","", $submit_text);
	update_option('jetmails_submit_text', $submit_text);
    if ($msg) $msg .= '<br/>';
	$msg .= '<span class="success_msg">'.__('Successfully Updated your List Subscribe Form Settings!', 'jetmails_i18n').'</span>';

}
if ($msg){
    echo '<div id="jetmails_message" class=""><p><strong>'.$msg.'</strong></p></div>';
}
?>
<?php 
if (get_option('jetmails_apikey')==''){
?>
<div>
<form method="post" action="options-general.php?page=jetmails_subscribe_form_setup_page">
<h3><?php echo __('Login Info', 'jetmails_i18n');?></h3>
<?php echo __('To start using the JetMails plugin, we first need to login and get your API Key. Please enter your JetMails username and password below.', 'jetmails_i18n'); ?>
<br/>
<?php echo __("Don't have a JetMails account? <a href='http://www.jetmails.com/' target='_blank'>Register here</a>", 'jetmails_i18n'); ?>
<br/>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Username', 'jetmails_i18n');?>:</th>
<td><input name="jetmails_username" type="text" id="jetmails_username" class="code" value="<?php echo $user; ?>" size="20" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php echo __('Password', 'jetmails_i18n');?>:</th>
<td><input name="jetmails_password" type="password" id="jetmails_password" class="code" value="<?php echo $pass; ?>" size="20" /></td>
</tr>
</table>
<input type="hidden" name="action" value="update"/>
<input type="hidden" name="page_options" value="jetmails_username,jetmails_password" />
<input type="submit" name="Submit" value="<?php echo htmlentities(__('Save & Check'));?>" class="button" />
</form>
</div>
<?php 
    if (get_option('jetmails_username')!=''){
	    echo '<strong>'.__('Notes', 'jetmails_i18n').':</strong><ul>
		    <li><i>'.__('Changing your settings at JetMails.com may cause this to stop working.', 'jetmails_i18n').'</i></li>
		    <li><i>'.__('If you change your login to a different account, the info you have setup below will be erased.', 'jetmails_i18n').'</i></li>
		    <li><i>'.__('If any of that happens, no biggie - just reconfigure your login and the items below...', 'jetmails_i18n').'</i></li></ul>
	    <br/>';
    }
echo '</p>';
} else {
?>
<table style="min-width:400px;"><tr><td><h3><?php echo __('Logged in as', 'jetmails_i18n');?>: <?php echo get_option('jetmails_username')?></h3>
</td><td>
<form method="post" action="options-general.php?page=jetmails_subscribe_form_setup_page">
<input type="hidden" name="action" value="logout"/>
<input type="submit" name="Submit" value="<?php echo __('Logout', 'jetmails_i18n');?>" class="button" />
</form>
</td></tr></table>
<?php
}
?>
<?php

if (get_option('jetmails_apikey') == '') return;

if (get_option('jetmails_apikey')!=''){
?>
 
<h3><?php echo __('Your Lists', 'jetmails_i18n')?></h3>
<div>
<?php echo __('Please select the list you wish to create a subscribe form for:', 'jetmails_i18n');?><br/>
<form method="post" action="options-general.php?page=jetmails_subscribe_form_setup_page">
<?php
    $apikey = get_option('jetmails_apikey');
	$api = new JetMailsAPI($apikey);
	$lists = $api->lists();
	rsort($lists);
	if (sizeof($lists)==0){
		echo "<span class='error_msg'>".
		       sprintf(__("Uh-oh, you don't have any lists defined! Please visit %s, login, and setup a list before using this tool!", 'jetmails_i18n'),
                    "<a href='http://www.jetmails.com/'>JetMails</a>")."</span>";
	} else {
	    echo '<table style="min-width:400px"><tr><td>
    	    <select name="jetmails_list_id" style="min-width:200px;">
            <option value=""> --- '.__('Select A List','jetmails_i18n').' --- </option>';
	    foreach ($lists as $list){
	        if ($list['id'] == get_option('jetmails_list_id')){
	            $sel = ' selected="selected" ';
	        } else {
	            $sel = '';
	        }
		    echo '<option value="'.$list['id'].'" '.$sel.'>'.htmlentities($list['name']).'</option>';
	    }
?>
</select></td><td>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="jetmails_list_id" />
<input type="submit" name="Submit" value="<?php echo __('Update List', 'jetmails_i18n');?>" class="button" />
</td></tr>
<tr><td colspan="2">
<strong><?php echo __('Note:', 'jetmails_i18n');?></strong> <em><?php echo __('Changing to a new list will cause settings below to be lost.', 'jetmails_i18n');?></em>
</td></tr>
</table>
</form>
</div>
<br/>
<?php
    } //end select list
} else {
//display the selected list...
?>

<?php 
//wp_nonce_field('update-options'); ?>
<p class="submit">
<form method="post" action="options-general.php?page=jetmails_subscribe_form_setup_page">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="jetmails_list_id" />
<input type="submit" name="reset_list" value="<?php echo __('Reset List Options and Select again', 'jetmails_i18n');?>" class="button" />
</form>
</p>
<h3><?php echo __('Subscribe Form Widget Settings for this List', 'jetmails_i18n');?>:</h3>
<h4><?php echo __('Selected JetMails List', 'jetmails_i18n');?>: <?php echo get_option('jetmails_list_name'); ?></h4>
<?php
}
//Just get out if nothing else matters...
if (get_option('jetmails_list_id') == '') return;

?>

<div>
<form method="post" action="options-general.php?page=jetmails_subscribe_form_setup_page">
<div style="width:600px;">
<br>&nbsp;</br>
<table class="widefat">
    <tr valign="top">
	<th scope="row"><?php echo __('Header content', 'jetmails_i18n');?>:</th>
	<td>
	<textarea name="jetmails_header_content" rows="2" cols="50"><?php echo htmlentities(get_option('jetmails_header_content'));?></textarea><br/>
	<i><?php echo __('You can fill this with your own Text, HTML markup (including image links), or Nothing!', 'jetmails_i18n');?></i>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row"><?php echo __('Submit Button text', 'jetmails_i18n');?>:</th>
	<td>
	<input type="text" name="jetmails_submit_text" size="30" value="<?php echo get_option('jetmails_submit_text');?>"/>
	</td>
	</tr>

	
</table>
</div>
<input type="submit" name="change_form_settings" value="<?php echo __('Update Subscribe Form Settings', 'jetmails_i18n');?>" class="button" />
</form>
</div>
</div>
<?php
}//jetmails_subscribe_form_setup_page()


add_action('plugins_loaded', 'jetmails_subscribe_form_register_widgets');
function jetmails_subscribe_form_register_widgets(){
	if (!function_exists('register_sidebar_widget')) {
		return;
	}
	register_sidebar_widget( 'JetMails Subscribe Form Widget', 'jetmails_subscribe_form_display_widget');
}



function jetmails_subscribe_form_shortcode($atts){
	jetmails_subscribe_form_display_widget();
}

add_shortcode('jetmails_subscribe_form', 'jetmails_subscribe_form_shortcode');


?>