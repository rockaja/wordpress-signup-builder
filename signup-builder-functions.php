<?php //if ( is_user_logged_in() ) { ... }

function page_menu_args($args) {
	//Get the id of the post
	global $wpdb;	
	$guid = get_option('sb_top_widget');
	$sql = "SELECT * FROM " . $wpdb->prefix . "_posts WHERE guid = '" . $guid . "';";
		$g = $wpdb->get_results($sql);
		$exclude_id = $g[0]->ID;
	
	$args['exclude'] = $exclude_id;
    return $args;
}


function check_verification(){
     
	 global $wpdb, $user_ID;
	 $sb_key = $_REQUEST['sb_key'];
	 $user_login = $_REQUEST['sb_ul'];
	 if(!$user_ID){
		  
	 $query = "UPDATE ".$wpdb->prefix."users SET 
	                  user_activation_key = NULL 
					     WHERE 
					  user_login ='".$user_login."' AND 
					  user_activation_key='".$sb_key."' LIMIT 1";
	 
	
	 
	   if(($_REQUEST['sb_key'] != "")  && (strlen($_REQUEST['sb_key']) == 32)){
	    
		  if($wpdb->query($query)){
			   $message =  "&nbsp; | <span style=color:green>Your account has been activated!</span>";
	       }
	    //echo $_REQUEST['sb_key'];
       } 
	 
	 }else{
		 
		  $message =  "&nbsp; | <span  style=color:green>You are already logged in</span>";
		 
	 }
		
	 echo $message;
}


function load_top_widget($user_ID){
	
	if(get_option('sb_top_widget') != ""){ 
		$permalink = urlencode(get_option('sb_top_widget'));
	 
	  if($_REQUEST['logout'] == "true"){
	    wp_logout();
	  }
	  
	   $current_user = wp_get_current_user(); 
	   show_admin_bar(false);
	   add_filter( 'wp_page_menu_args', 'page_menu_args' );
?>

  
    <link href="<?php echo plugins_url('signup-builder/css/signup-builder.css')?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo plugins_url('signup-builder/3rdparty/colorbox/colorbox/colorbox.css')?>" rel="stylesheet" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="<?php echo plugins_url('signup-builder/3rdparty/colorbox/colorbox/jquery.colorbox.js')?>"></script>
    <script>
			$(document).ready(function(){
				$(".callbacks").colorbox({
				});
			});			
	</script>


 <?php
 
 $file_name = dirname(__FILE__).'/signup-builder-p.php';
if (file_exists($file_name) ){  
  $sb_menu_bg = get_option('sb_menu_bg');
  $sb_menu_text = get_option('sb_menu_text');
  $extra_style="style=";
  if(get_option('sb_align_menu') == 'right'){ $extra_style .= 'right:0px;';}
  $extra_style .= "background-color:".$sb_menu_bg.";color:".$sb_menu_text.";";                                 
 ?>

<style>
.floating-menu a{
	color:<?php echo $sb_menu_text ?>;
}
</style>
<?php } ?>
<div class="floating-menu" <?php echo $extra_style?>  >

<?php 
$floater = '<div class="floating-menu">';
if( $current_user->ID == 0){
          $salute = "Hello Guest!"; 
	   }else{
          $salute = $current_user->user_login;
       }
	
?>
<span style="color:#eee"><?php echo $salute; ?></span> <span style="color:#eee"> | </span>
<?php 

$floater .= '<span style="color:#eee">' . $salute . '</span> <span style="color:#eee"> | </span>';
 
if( $current_user->ID == 0){ 

if( get_option('sb_show_value') =="page"){ 

$floater .= '<span class="open">
                 <a id="open_1"  href="<?php echo urldecode($permalink); ?>" >LogIn | Signup</a>
                </span>';

?>                
 <span class="open">
                 <a id="open_1"  href="<?php echo urldecode($permalink); ?>" >Login | Signup</a>
                </span>
<?php }else{
	
$sb_signup =  plugins_url('signup-builder/signup-builder-access.php?access=top&type=login&permalink='. $permalink . '&'); 
$sb_login =  plugins_url( 'signup-builder/signup-builder-access.php?access=top&type=reg&permalink='. $permalink . '&');

$floater .= '<a id="callbacks_login" class="callbacks" href="'.$sb_login.'" >Login</a> <span style="color:#eee"> |</span> <a  id="callbacks_reg"  class="callbacks" href="'.$sb_signup.'" >Signup</a></span>';
?>

<a id='callbacks_login' class='callbacks' href="<?php echo plugins_url('signup-builder/signup-builder-access.php?access=top&type=login&permalink='. $permalink . '&')?>" >Login</a> <span style="color:#eee"> |</span> <a  id='callbacks_reg'  class='callbacks' href="<?php echo plugins_url( 'signup-builder/signup-builder-access.php?access=top&type=reg&permalink='. $permalink . '&')?>" >Signup</a></span>

  
<?php } 
}else{ 
$floater .= '<a id="callbacks_signout"  class="close" href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a>';
?>	
<a id="callbacks_signout"  class="close" href="<?php echo wp_logout_url( get_permalink() ); ?>">Logout</a>
<?php } 
$floater .= '</div>';
?>
<?php if($_REQUEST['sb_key'] != "" and  $_REQUEST['sb_ul'] != ""){  ?>
<?php echo check_verification() ?>
<?php } ?>
</div>
<?php
 
  }

}





function install_signup_builder()
	{
		//NB Always set wpdb globally!
		global $wpdb, $user_ID;
		
		//Set Table Name		
		$signup_builder_table = $wpdb->prefix . "signup_builder";
		
		//Check whether or not the table already exists
		if(!check_signup_builder_existance($signup_builder_table)) :
		
			// Create the main Table, don't forget the ( ` ) - MySQL Reference @ http://www.w3schools.com/Sql/sql_create_table.asp
			$signup_builder_table_install = "CREATE TABLE `".$signup_builder_table."` (			
			`id` MEDIUMINT(8) unsigned NOT NULL auto_increment PRIMARY KEY,
			`field_name` VARCHAR(255),
			`field_type` VARCHAR(255),
			`field_label` VARCHAR(255),
			`field_values` VARCHAR(255),
			`field_max_value` VARCHAR(255),
			`field_isEmpty` INT( 1 ),
			`field_can_delete` INT( 1 ) NOT NULL DEFAULT 1,
			`field_class` VARCHAR( 20 ),
			`field_order` INT( 1 ),
			`field_error_msg` VARCHAR(255),
			`field_sub_label` VARCHAR(255));";
			
			
			$signup_builder_table_add = " INSERT INTO `".$signup_builder_table."` 
			   (`id`, `field_name`, `field_type`, `field_label`, `field_values`, `field_max_value`, `field_isEmpty`, `field_can_delete`, `field_class`, `field_order`, `field_error_msg`, `field_sub_label`) 
	VALUES ('', 'username', 'text', 'Username', '', '50', 1, 0, '', 1,'','Type a username'), 
		   ('', 'password', 'password', 'Password', '', '50', 1, 0, '', 2,'','Type a password'),
		   ('', 'email', 'text', 'Email', '', '50', 1, 0, '', 3,'','Enter your valid email');";
			
			//Run the and validate that it was successful
			if(mysql_query($signup_builder_table_install) === true) :
				$tabe_created = 1;
			endif;
			
			if(mysql_query($signup_builder_table_add) === true) :
				$tabe_created = 1;
			endif;
			
			endif;
			
			//check if post space exist if not add new post and store data
			check_create_post();
			
			create_email_message();
			
	}
	
function check_create_post(){
	
	global $wpdb, $user_ID;
		
	$sb_post = get_option('sb_top_widget');
	
	//if($sb_post <> ''){
	  $use_sql = "SELECT * FROM ".$wpdb->prefix."posts WHERE guid = '" . $sb_post ."' and post_status = 'publish' and post_type = 'page';";
	  $get_next_id = $wpdb->get_results($use_sql);
	  $new_id = $get_next_id[0]->ID;
	
	   if($new_id < 1){		
	     $my_post = array(
          'post_title' => '',
          'post_content' => '<!-- Signup builder By Ola Apata -->[sb-signup]<!-- Do not remove this tag-->',
          'post_status' => 'publish',
		  'comment_status' => 'closed',
		  'post_type' => 'page',
          'post_author' => $user_ID
        );
       //Insert the post into the database
       $guid = wp_insert_post($my_post);
	   $sb_guid = get_permalink($guid);
	   update_option('sb_top_widget', $sb_guid);
      }
	  		
}

function create_email_message(){
	global $update_message;
	$sb_email_no_auth ='Registration successful. Your login details:
Username: %USER%
Password: %PASS%
Visit: %HOME_URL% to login to your account.';
	$sb_email_yes_auth ='Registration successful.Your login details
Username: %USER%
Password: %PASS%
Activate your account: %LINK%';
	$sb_email_basic_auth ='Thank you for registering. Here are details of your active account
Your login details:
Username: %USER%
Password: %PASS%
Visit: %HOME_URL%';	
	
	update_option('sb_email_no_auth', $sb_email_no_auth);
	update_option('sb_email_yes_auth', $sb_email_yes_auth);
	update_option('sb_email_basic_auth', $sb_email_basic_auth);	
	
	$update_message = 'Email Updated successfully';
}
	
function check_signup_builder_existance($new_table) {
	//NB Always set wpdb globally!
	global $wpdb;
	
	foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
		if ($table == $new_table) {
			return true;
		}
	}
	return false;
}


function build_input($type, $name, $class = "", $default_value = "", $max_value = ""){
	
	if($type == "text"){
		$object = '<input type="'.$type.'" id="'.$name.'" name="'.$name.'" class="'.$class.'" maxlength="'.$max_value.'" />';
	}
	if($type == "password"){
		$object = '<input type="'.$type.'" id="'.$name.'" name="'.$name.'" class="'.$class.'" />';
	}
    if($type == "textarea"){
		$object = '<textarea id="'.$name.'" name="'.$name.'" class="'.$class.'" ></textarea>';
	}
    if($type == "checkbox"){
		$object = '<input value="1" type="'.$type.'" id="'.$name.'" name="'.$name.'" class="'.$class.'" ' .$default_value.' onclick=\'cv("'.$name.'")\'  style="float:left;width:13px;height:13px;" />';
	}
	
	if($type == "dropdown"){
		
		$object = '<select id="'.$name.'" name="'.$name.'" class="'.$class.'" >';
		$split = explode(",", $default_value);
		$counter = count($split);
		$object .= '<option value="" class="'.$class.'">Choose ...</option>';
		for($i=0;$i < $counter; $i++){
			
			$object .= '<option value="'.$split[$i].'" class="'.$class.'">'.$split[$i].'</option>';
		}
		$object .= '</select>';
	}
	
	if($type == "button_signup"){
		
		if(get_option('sb_show_value') != "top") {
          $login_link = '&nbsp;&nbsp;<span class=form_link onclick="hide_signup()"><a>Login</a> </span>&nbsp;|';
          }
		
		$object = '<input type="button" id="'.$name.'" name="'.$name.'" class="'.$class.'" value="'.$default_value.'" onclick=submit_data("signup") />'. $login_link.'&nbsp;<span class=form_link><a href="'.wp_lostpassword_url( get_permalink() ).'">Reminder</a></span> ';
	}
	
	if($type == "button_login"){
		
		if(get_option('sb_show_value') != "top") {
          $signup_link = '&nbsp;&nbsp;<span class=form_link onclick="hide_login()"><a >Signup</a></span>&nbsp;|';
          }
		
		
		$object = '<input type="button" id="'.$name.'" name="'.$name.'" class="'.$class.'" value="'.$default_value.'" onclick=submit_data("login") />'.$signup_link.'&nbsp;<span class=form_link><a href="'.wp_lostpassword_url( get_permalink() ).'">Reminder</a></span>';
	}
	
	return $object;
}


function show_signup(){
	//NB Always set wpdb globally!
	global $wpdb, $signup_builder_table, $user_ID,$update_message,$disable_text;	
	$signup_builder_table = $wpdb->prefix . "signup_builder";
	
	?>
       
    <style>

	</style>
    
    
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="<?php echo plugins_url( 'signup-builder/3rdparty/farbtastic/farbtastic.js' ); ?>"></script>
<link href="<?php echo plugins_url('signup-builder/3rdparty/farbtastic/farbtastic.css')?>" rel="stylesheet" type="text/css" />  
    
<script>
	
	
	jQuery(document).ready(function() {
    jQuery('#ilctabscolorpicker_bg').hide();
    jQuery('#ilctabscolorpicker_bg').farbtastic("#color_bg");
    jQuery("#color_bg").click(function(){jQuery('#ilctabscolorpicker_bg').slideToggle()});
	
	jQuery('#ilctabscolorpicker_text').hide();
    jQuery('#ilctabscolorpicker_text').farbtastic("#color_text");
    jQuery("#color_text").click(function(){jQuery('#ilctabscolorpicker_text').slideToggle()});
	
  });
	
	
	function change_layout(value){
		if(value == "checkbox"){
			//alert(value);
			marker = $('<span />').insertBefore('#fm_field_values');
            $('#fm_field_values').detach().attr('type', 'checkbox').insertAfter(marker);
			$("#fm_field_values").attr("checked", "checked");
			$("#fm_field_values").attr("value", "checked");
			$('#checkbox_info').detach();
			$("#fm_field_values").after("<span id='checkbox_info' style='font-size:11px;color:#999;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Choose to have this checked or unchecked by default</span>");
            marker.remove();
		}
		
		if(value == "text" || value == "textarea"){
			marker = $('<span />').insertBefore('#fm_field_values');
            $('#fm_field_values').detach().attr('type', 'text').insertAfter(marker);
			$("#fm_field_values").attr("value", "");
			//$('#checkbox_info').detach()
			$('#checkbox_info').html("<span id='checkbox_info' style='font-size:11px;color:#999'>&nbsp;Initial default value (Will dissapear once user starts typing)</span>")
            marker.remove();
		}
		
		if(value ==  "dropdown"){
			marker = $('<span />').insertBefore('#fm_field_values');
            $('#fm_field_values').detach().attr('type', 'text').insertAfter(marker);
			$("#fm_field_values").attr("value", "");
			$('#checkbox_info').html("<span id='checkbox_info' style='font-size:11px;color:#999'>&nbsp;Use comma to seperate dropdown items. (i.e Red,blue,black etc)</span>")
            marker.remove();
		}
		
	}
	</script>
  
<div class='wrap' id="main_body" style="height:1400px;border:0 solid red">



<div id="icon-options-general" class="icon32"><br /></div>

	<h2><?php echo DISPLAY_NAME ?></h2>
<?php
//if (file_exists( dirname(__FILE__).'/signup-builder-access.php')  ){
 echo premium_msg();
//} 
if($update_message <> ""){?>
<div class="updated">&nbsp;&nbsp&nbsp<?php _e($update_message);?> </div>
<?php } ?>
<style>
.selected_auth{
background:#eee;	
}
.textarea_email{
	border:1px solid #999; width:95%;
}
label {font-size:11px;color:#999;}

.hide{display:none;}
.show{display:block;}
textarea{font-size:11px;color:#999; height:100px}
</style>
<script>
function hide_show(thisa){
	
	 $('#email_basic_auth_div').css({display:'none'});
	 $('#email_no_auth_div').css({display:'none'});
	 $('#email_yes_auth_div').css({display:'none'}); 
	 $('#'+thisa).css({display:'block'});
	
}
</script>

<h3>Registration Type</h3>  
    	<div class="tablenav" style="height:240px;width:95%">
        
			<form method="post" action="" id="reg_type" name="reg_type">
            <input type="hidden" value="auth_type" id="action" name="action" />
            <input type="hidden" value="<?php echo $user_ID?>" id="user_id" name="user_id" />
            <table class="widefat post fixed" cellspacing="0">
            	<thead>
                    <tr>
                        <th width=""><label>&nbsp;&nbsp;Basic</label></th>
                        <th width=""><label>&nbsp;&nbsp;Direct</label></th>
                        <th width=""><label>&nbsp;&nbsp;Authentication</label></th>
                    </tr>
                 </thead>
                    <tr>
                        <?php
						
						$no_auth_class =  'hide';
						$yes_auth_class = 'hide';
						$basic_auth_class = 'hide';
						$sc =  'Possible values %USER%, %PASS%, %HOME_URL%';
						$sc_1 =  'Possible values %USER%, %PASS%, %LINK';
						
						$meta_key = "sb_key_option";
						 //$checked = get_user_meta($user_ID, $meta_key, true);						   
						   $checked = get_option( $meta_key );
						if($checked == "no_auth"){	
						   $no_auth_class = "show"; 				   
						   $checked_direct = "checked='checked'";
						   
						}else if($checked == "yes_auth") {					
                           $yes_auth_class = "show";                          
						   $checked_auth = "checked='checked'";	
						   					
						}else if($checked == "basic_auth" || $checked == "") {
						   $checked_basic = "checked='checked'";
						   $basic_auth_class = "show";						
						}
						?>
                        <th class="">
                      
                        <input onclick="hide_show('email_basic_auth_div')" id="basic_auth" type="radio" name="auth_type_value" value="basic_auth" <?php  echo $checked_basic; ?>  />
                        <label for="basic_auth" onclick="hide_show('email_basic_auth_div')">
                        &nbsp;&nbsp;Basic: User Active immidiately </label>
                       
                        </th>
                        <th><input  onclick="hide_show('email_no_auth_div')" id="no_auth" type="radio" name="auth_type_value" value="no_auth" <?php  echo $checked_direct; ?>  />
                        <label for="no_auth"  onclick="hide_show('email_no_auth_div')">
                        &nbsp;&nbsp;Direct: Generated password sent to users email</label></th>
                        <th>
                        <input  onclick="hide_show('email_yes_auth_div')"  id="yes_auth" type="radio" name="auth_type_value" value="yes_auth" <?php  echo $checked_auth; ?>  />
                       <label for="yes_auth"  onclick="hide_show('email_yes_auth_div')">
                        &nbsp;&nbsp;Auth: Authentication link sent to users email</label></th>
                   </tr>
                    <tr>
                     <th colspan="3"  >
                     <div class="<?php  echo $basic_auth_class; ?>"  id="email_basic_auth_div"><label>Generated Email(Basic) <?php echo $sc ?> <?php echo $disable_text; ?></label><br />
                               <textarea name="email_basic_auth" id="email_basic_auth" class="textarea_email"><?php echo get_option('sb_email_basic_auth');?></textarea></div>
                               
<div class="<?php  echo $no_auth_class; ?>" id="email_no_auth_div"><label>Generated Email(Direct) <?php echo $sc ?> <?php echo $disable_text; ?></label><br />
                               <textarea   name="email_no_auth"  id="email_no_auth" class="textarea_email"><?php echo get_option('sb_email_no_auth')?></textarea></div>
                               
<div class="<?php  echo $yes_auth_class; ?>" id="email_yes_auth_div"><label>Generated Email(Authentication) <?php echo $sc_1 ?> <?php echo $disable_text; ?></label><br />
                               <textarea   name="email_yes_auth"  id="email_yes_auth" class="textarea_email"><?php echo get_option('sb_email_yes_auth')?></textarea></div>
                     </th>
                      
                     
                    </tr>
                   
             </table>
            
<div style="float:right;margin-top:10px">
<input id="reset_to_default" name="reset_to_default" type="button" value="Reset to Default" class="button-secondary" onclick="window.location.href='/wp-admin/admin.php?page=signup-builder/signup-builder.php&action=restore_default_email'">
<input id="add" name="add" type="submit" value="Save Changes" class='button-primary' /></div>
            </form>
            
        </div>


<h3>Floating menu customisation</h3>  
    	<div class="tablenav" style="height:90px;width:95%">        
			<form method="post" action="" id="floating_menu" name="floating_menu">
            <input type="hidden" value="floating_menu" id="action" name="action" />
            <input type="hidden" value="<?php echo $user_ID?>" id="user_id" name="user_id" />
            <table class="widefat post fixed" cellspacing="0">
            	<thead>
                    <tr>
                        <th ><label>&nbsp;&nbsp;Top Left</label></th>
                        <th width=""><label>&nbsp;&nbsp;Top Right</label></th>
                        <th width=""><label>&nbsp;&nbsp;Background Color</label></th>
                        <th width=""><label>&nbsp;&nbsp;Font Color</label></th>
                    </tr>
                 </thead>
                    <tr valign="top">
                        <?php 
						$meta_key = "sb_align_menu";
						//$checked = get_user_meta($user_ID, $meta_key, true);
						//$floating_menu = get_option( $meta_key );
						$checked_f = get_option( $meta_key );

						if($checked_f == "left" || $checked_f == ""){
						   $checked_left = "checked='checked'";
						}else if($checked_f == "right"){
							$checked_right = "checked='checked'";						
						}else if($checked_f == "center"){
							$checked_center = "checked='checked'";						
						}
						?>
                        <th>
                        <input id="align_value" type="radio" name="align_value" value="left" <?php  echo $checked_left; ?>  />
                        <span style='font-size:11px;color:#999'>&nbsp;Align top left </span>
                        
                        </th>
                      
                        
                         <th>
                        <input  id="align_value" type="radio" name="align_value" value="right"  <?php  echo $checked_right; ?> />
                        <span style='font-size:11px;color:#999'>&nbsp;Align top right</span>
                        </th>
                        
                         <th>
       <label for="color_bg"><input type="text" id="color_bg" name="color_bg" value="<?php echo get_option('sb_menu_bg')//$value['color_bg']; ?>" />
       
       </label><span style='font-size:11px;color:#999'><?php echo $disable_text; ?></span>
    <div id="ilctabscolorpicker_bg"></div>
                        
                        </th>
                         <th>
       <label for="color_text"><input type="text" id="color_text" name="color_text" value="<?php echo get_option('sb_menu_text') // $value['color_text']; ?>" /></label><span style='font-size:11px;color:#999'><?php echo $disable_text; ?></span>
    <div id="ilctabscolorpicker_text"></div>
                        
                        </th>
                    </tr>
                   
               
        	</table>
            
            
            
            
             <div style="float:right;margin-top:10px">
             <input id="reset_to_default" name="reset_to_default" type="button" value="Reset to Default" class="button-secondary" onclick="window.location.href='/wp-admin/admin.php?page=signup-builder/signup-builder.php&action=restore_default_menu'">
             <input id="add" name="add" type="submit" value="Save Changes" class='button-primary' /></div>
            </form>
            
        </div>







  
    	<div class="tablenav" style="height:90px;width:95%"> 
        <h3>GUI Types</h3>       
			<form method="post" action="" id="top_widget" name="top_widget">
            <input type="hidden" value="top_widget" id="action" name="action" />
            <input type="hidden" value="<?php echo $user_ID?>" id="user_id" name="user_id" />
            <table class="widefat post fixed" cellspacing="0">
            	<thead>
                    <tr>
                        <th colspan="2"><label>&nbsp;&nbsp;Signup and Login GUI</label></th>
                    </tr>
                 </thead>
                    <tr>
                        <?php 
						$meta_key = "sb_top_widget";
						//$checked = get_user_meta($user_ID, $meta_key, true);
						$top_widget = get_option( $meta_key );
						
						$meta_key = "sb_show_value";
						//$checked = get_user_meta($user_ID, $meta_key, true);
						$checked = get_option( $meta_key );

						if($checked == "top" || $checked == ""){
						   $checked_top = "checked='checked'";
						}else{
							$checked_page = "checked='checked'";						
						}
						?>
                        <th>
                        <input id="show_value" type="radio" name="show_value" value="top" <?php  echo $checked_top; ?>  />
                        <span style='font-size:11px;color:#999'>&nbsp;Use Jquery Overlay <?php echo $disable_text; ?> </span>
                        
                        </th>
                        <th>
                        <input  id="show_value" type="radio" name="show_value" value="page"  <?php  echo $checked_page; ?> />
                        <span style='font-size:11px;color:#999'>&nbsp;Use Wordpress Page</span>
                        </th>

                    </tr>
                    
                
        	</table>
            
             <div style="float:right;margin-top:10px"><input id="add" name="add" type="submit" value="Save Changes" class='button-primary' /></div>
            </form>
            
        </div>





  
    	<div class="tablenav" style="height:100px;width:95%">
        <h3>Add Form Field</h3>
			<form method="post" action="" id="add_field" name="add_field">
            <input type="hidden" value="add" id="action" name="action" />
            <table class="widefat post fixed" cellspacing="0">
            	<thead>
                    <tr>
                        <th width="120"><label>&nbsp;&nbsp;Type</label></th>
                        <th width="100"><label>&nbsp;&nbsp;Required</label></th>
                        <th width="150"><label>&nbsp;&nbsp;Label name</label></th>                       
                        <th width=""><label>&nbsp;&nbsp;Default Value</label></th>
                    </tr>
                 </thead>
                    <tr>
                        <th>
                        <select id="fm_field_type" name="fm_field_type" onchange="change_layout(document.getElementById('fm_field_type').value);" >
                          <option value="text">Textfield</option>
                          <option value="checkbox">Checkbox</option>
                          <option value="textarea">Textarea</option>
                          <option value="dropdown">Dropdown</option>
                        </select>
                        </th>
                        <th>
                        <input id="fm_field_isEmpty" type="checkbox" name="fm_field_isEmpty" value="1" />
                        <th><input id="fm_field_label" type="text" name="fm_field_label"  /></th>
                        
                        <th>
                        <span><input  id="fm_field_values" type="text" name="fm_field_values"  /></span>
                        </th>
                     </tr>
                    
                
        	</table>
            
             <div style="float:right;margin-top:10px"><input id="add" name="add" type="submit" value="Add form Input" class='button-primary' /></div>
            </form>
            
        </div>
        
        
        


<div class="tablenav"  style="width:95%">
<h3>Form Field Lists</h3>
			<table class="widefat post fixed" cellspacing="0">
            	<thead>
                    <tr>
                        <th width="120"><label>Move</label></th>
                        <th width="100"><label>Field type</label></th>
                        <th  width="150"><label>Label name</label></th>
                        <th><label>GUI Display</label></th>
                    </tr>
                </thead>
				<?php
				    
                    //Query the Twitter List Table
                    $use_sql = "SELECT * FROM ".$signup_builder_table." ORDER BY field_order ASC";
                    $table_Details =  $wpdb->get_results($use_sql);
					$max = count($table_Details);
					//Loop through the $table_Details Array
                    foreach ($table_Details as $builder_list) :
                    $i++;
					$field_id = "sb_input_" . (string) $i;
					$a = $builder_list->id;
					$b = $builder_list->field_order - 1;
					$c = $builder_list->field_order + 1;
					$e = $builder_list->field_order;
					$can_delete = $builder_list->field_can_delete;
				?>
                    <tr>
                        <td>
                        <?php if($i <> 1){ ?>
<a href="<?php echo '?page=signup-builder/signup-builder.php&amp;action=order&amp;id='.$a .'&amp;pos='. $e.'&amp;newpos='. $b ;?>"  >
                        <img title="move up" style="cursor:pointer" src="<?php echo plugins_url('signup-builder/images/up.jpeg')?>" width="16" height="16" alt="Move up" />
                        </a>
						<?php } ?>
                        <?php if($i <> $max){?>
<a href="<?php echo '?page=signup-builder/signup-builder.php&amp;action=order&amp;id='.$a .'&amp;pos='. $e.'&amp;newpos='. $c ;?>"  >
                        <img title="move down" src="<?php echo plugins_url('signup-builder/images/down.jpg')?>" width="16" height="16" alt="Move down" /></a>
                        <?php } ?>
                        <?php if($i > 2){ ?>
                        <a href="?page=signup-builder/signup-builder.php&amp;action=delete&amp;id=<?php echo $a ?>&amp;can_delete=<?php echo $can_delete ?>" ><img src="<?php echo plugins_url('signup-builder/images/delete.png')?>" width="16" height="16" alt="delete" />
                        </a>
                        <?php } ?>
                        
                      </td>
                        <td><?php echo $builder_list->field_type; ?></td>
                        <!-- <td><?php echo $builder_list->field_name; ?></td> 
                        <td><?php echo $builder_list->field_class; ?></td>-->
                        <td><?php echo $builder_list->field_label; ?></td>
                        
                        <td><?php echo build_input($builder_list->field_type,
						                           $field_id, 
												   $builder_list->field_class,
												   $builder_list->field_values, 
												   $builder_list->field_max_value);  ?>
                        </td>
                    </tr>
                <?php
                    endforeach;
                ?>	
        	</table>
            
        </div>
        </div>
  
    <?php

}

?>