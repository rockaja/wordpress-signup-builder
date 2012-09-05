<?php

if($_POST["action"] == "add"){add_action("init", "sb_add");}
if($_REQUEST["action"] == "delete"){add_action("init", "sb_delete");}
if($_REQUEST["action"] == "order"){add_action("init", "sb_order");}
if($_REQUEST["action"] == "auth_type"){add_action("init", "sb_auth_type");}
if($_REQUEST["action"] == "auth_type"){add_action("init", "sb_auth_type_premium");}
if($_REQUEST["action"] == "top_widget"){add_action("init", "sb_top_widget");}
if($_REQUEST["action"] == "restore_default_email"){add_action("init", "create_email_message");}
if($_REQUEST["action"] == "floating_menu"){add_action("init", "sb_align_widget");}
if($_REQUEST["action"] == "floating_menu"){add_action("init", "sb_align_widget_premium");}
if($_REQUEST["action"] == "restore_default_menu"){add_action("init", "sb_default_menu");}

function sb_add(){
	//$field_name = $_POST['fm_field_name'];
	$field_name = '';
	$field_label = $_POST['fm_field_label'];
	$field_values = $_POST['fm_field_values'];
	$field_type = $_POST['fm_field_type'];
	$field_class = $_POST['fm_field_class'];
	
	$field_isEmpty = (int)$_POST['fm_field_isEmpty'];
	
	global $wpdb, $signup_builder_table,$update_message;		
	$signup_builder_table = $wpdb->prefix . "signup_builder";
	
	
	$signup_count_order = " SELECT * FROM `".$signup_builder_table."`";
	$field_order =  count($wpdb->get_results($signup_count_order));
	$field_order++;
					
	
    
	$signup_builder_table_add = " INSERT INTO `".$signup_builder_table."` 
			   (`id`, `field_name`, `field_type`, `field_label`, `field_values`, `field_max_value`, `field_isEmpty`, `field_can_delete`, `field_class`, `field_order`) 
			   VALUES ('', '" . $field_name ."', '" . $field_type ."', '" . $field_label ."', '" . $field_values ."', '50', " . $field_isEmpty .", 1, '" . $field_class ."', " . $field_order .");";
			
			//Run the and validate that it was successful
			if(mysql_query($signup_builder_table_add) === true) :
				$tabe_created = 1;
			endif;
			
			$update_message = 'Field added Successfully';
}


function sb_delete(){
	global $update_message;
	$id = $_REQUEST['id'];
	$can_delete = $_REQUEST['can_delete'];
	
	global $wpdb, $signup_builder_table;		
	$signup_builder_table = $wpdb->prefix . "signup_builder";
    if($can_delete == "1"):
	$signup_builder_table_delete = " DELETE FROM `".$signup_builder_table."` WHERE id = " . $id;
			
			//Run the and validate that it was successful
			if(mysql_query($signup_builder_table_delete) === true) :
				$tabe_created = 1;
				$update_message = 'Field deleted Successfully';
			endif;
			endif;
}

function sb_default_menu(){
	global $update_message;
	update_option('sb_menu_bg', '#999');
	update_option('sb_menu_text', '#eee');
	
	$update_message = 'Floating menu set to default';
}



function sb_auth_type(){
	global $update_message;
	$update_message_p = '';
	$action = $_POST['auth_type'];
	$auth_type_value = $_POST['auth_type_value'];
	$new_user_id = $_POST['user_id'];
	$email_value = $_POST['email_'.$auth_type_value];
	 
	 
	 $option_name = 'sb_key_option' ;
     $newvalue = $auth_type_value ;

     if ( get_option( $option_name ) != $newvalue ) {
          update_option( $option_name, $newvalue );
     } else {
       $deprecated = ' ';
       $autoload = 'yes';
         add_option( $option_name, $newvalue, $deprecated, $autoload );
     }
	//if(strlen(get_option( 'sb_email_'.$auth_type_value )) == strlen($email_value)){
		$update_message_p = ' NOTE: Generated Email text can only be updated with Premium version. Please <a>Upgrade</a>';
	//}//
	$update_message = 'Options Updated Successfully.' . $update_message_p;	 	
}


function sb_top_widget(){
	global $update_message;
	$action = $_POST['top_widget'];
	 $show_value = $_POST['show_value'];
	 $option_name = 'sb_show_value' ;
     $newvalue = $show_value ;

     if ( get_option( $option_name ) != $newvalue ) {
        update_option( $option_name, $newvalue );
     } else {
       $deprecated = ' ';
       $autoload = 'yes';
       add_option( $option_name, $newvalue, $deprecated, $autoload );
     }
	 
	 
	 $update_message = 'URL Updated successfully';
	 	
}

function sb_align_widget(){
	global $update_message;
	 //$action = $_POST['action'];
	 $show_value = $_POST['align_value'];
	 $option_name = 'sb_align_menu' ;
 
     $newvalue = $show_value ;

     if ( get_option( $option_name ) != $newvalue ) {
        update_option( $option_name, $newvalue );
     } else {
       $deprecated = ' ';
       $autoload = 'yes';
       add_option( $option_name, $newvalue, $deprecated, $autoload );
     }
	 
	 $update_message = 'Floating menu updated successfully. NOTE: Background and font color can be customised with Premium versions <a>Upgrade</a>';
	 	
}


function sb_order()
	{
       		global $wpdb, $update_message;
		$signup_builder_table = $wpdb->prefix . "signup_builder";
	   
	    $id = (int)$_REQUEST['id'];
        $newpos = (int)$_REQUEST['newpos'];
		$pos = (int)$_REQUEST['pos'];
		//NB Always set wpdb globally!	
		$use_sql = "SELECT * FROM ".$signup_builder_table." WHERE field_order = " . $newpos;
		$get_next_id = $wpdb->get_results($use_sql);
		$new_id = $get_next_id[0]->id;
		 
			$update_sql = "UPDATE ".$signup_builder_table."  
							SET field_order = ".$newpos." 
							WHERE id = ".$id."";
							
			$update_sql1 = "UPDATE ".$signup_builder_table."  
							SET field_order = ". $pos . " 
							WHERE id = ". $new_id;

			if($wpdb->query($update_sql) === true and $wpdb->query($update_sql1) === true) :
			  // return true;
			  $update_message = 'Order Updated successfully';
	        endif;
	}
	
?>