<?php 

$redirect =  "";
?>
<?php
define('WP_USE_THEMES', false);
require_once(dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-blog-header.php');
//require_once(dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-includes/registration.php');
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
//Check whether the user is already logged in

function add_meta(){
	
	global $wpdb, $user_ID;
	
    $array = $_POST;
	$array_final = $_POST;
	
	unset($array_final['action']);
	unset($array_final['total']);
	unset($array_final['retype']);
	unset($array_final['myinputs']);
	
	
	foreach($array as $key => $value){
	   if(strpos($key, '_validate') !== false){
		   //$key1 .= ' | ' . $key . '=' . $value;
		   unset($array_final[$key]);
	    }
	}

	  //return $array_final;	
	  
	  
  foreach($array_final as $key => $value){

      $meta_key = $key;
      $meta_value = $wpdb->escape($value);
      $prev_value =  get_user_meta($user_ID, $meta_key, true);
      update_user_meta( $user_ID, $meta_key, $meta_value, $prev_value );

	}
	  
}

if (!is_user_logged_in()) {
//if (!$user_ID) {

	if($_POST && $_POST['action'] == "signup"){
			
	  global $wpdb, $user_ID;
	
      $array = $_POST;
	  $array_final = $_POST;
	
	  unset($array_final['action']);
	  unset($array_final['total']);
	  //unset($array_final['retype']);
	  unset($array_final['myinputs']);
	
	
	  //Start ajax callback ccheck
	  $error = 0;
	 $array_check = array();
	 $array_value = array();
     foreach($array as $key => $value){
	  
	   if(strpos($key, 'sb_') !== false){
		   //$key1 .= ' | ' . $key . '=' . $value;
		   $array_value[$key] =  $value;
		   
		 //}else 
		 if(strpos($key, '_validate') !== false){
		    
			  $array_check[$key] = (int)$value;
		   
		        if($array_check[$key] == 1){			   
			       
				   $post = str_replace('_validate', '', $key);
			        //$post =  $key;
			   
			         if($array_value[$post] == ""){				   
				          $error++;
				          $err[] = $post;				   
			         }
		    
			    }
				/**/
		   
	      } //else{
			  
	  }
	  
	}

			
			
			
			
			$username = $wpdb->escape($_POST['username']);
			if(empty($username)) { 
				echo "Username cannot be empty.";
				exit();
			}
			if(get_option('sb_key_option') <> "no_auth"){
			$password = $wpdb->escape($_POST['password']);
			if(empty($password)) { 
				echo "Password cannot be empty.";
				exit();
			}
			}
			
			if(get_option('sb_key_option') != "no_auth"){
				 if($_POST['retype'] != $_POST['password']){
				    echo "Both password must match and cannot be empty";
				    exit();
				  }	
			}
			
			$email = $wpdb->escape($_POST['email']);
			if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) { 
				echo "Please enter a valid email.";
				exit();
			}
			
			if($error > 0){
		         echo 'All fields marked with <em>*</em> must be entered';
		       exit();
	        }
			
			
			
			
			
			
			    //wpmu_signup_user( $username, $email, apply_filters( 'add_signup_meta', array() ) );		
		
				if(get_option('sb_key_option') == "yes_auth"){	
				  
				   //For activation
					 $user_activation_key = md5(uniqid(rand(), true));
					 $password = $wpdb->escape($_POST['password']);
					 $status = wp_create_user( $username, $password, $email );
					 $wpdb->update($wpdb->users, array('user_activation_key' => $user_activation_key),array('user_login' => $username));
				}else if(get_option('sb_key_option') == "no_auth"){
				     $password = wp_generate_password( 12, false );
				     $status = wp_create_user( $username, $password, $email );
				}else if(get_option('sb_key_option') == "basic_auth"){
				     $password = $wpdb->escape($_POST['password']);
				     $status = wp_create_user( $username, $password, $email );
				}
				
				if ( is_wp_error($status) ) 
					echo "Username or email exists. Please try another one.";
					//exit();
				else {
					 $array = $_POST;
	                 $array_final = $_POST;
	
	                 unset($array_final['action']);
	                 unset($array_final['total']);
	                 unset($array_final['retype']);
	                 unset($array_final['myinputs']);
					 unset($array_final['username']);
	                 unset($array_final['password']);
					 unset($array_final['email']);
	
	          foreach($array as $key => $value){
	              if(strpos($key, '_validate') !== false){
		             unset($array_final[$key]);
	               }
	          }

	  //return $array_final;	
	  
	          $new_user_id = $status;
              foreach($array_final as $key => $value){

                   $meta_key = $key;
                   $meta_value = $wpdb->escape( $value);
                   $prev_value =  get_user_meta($new_user_id, $meta_key, true);
                   update_user_meta( $new_user_id, $meta_key, $meta_value, $prev_value );

	          }
                    $from = get_option('admin_email');
					$headers = 'From: '.$from . "\r\n";
					
					
				if(get_option('sb_key_option') == "yes_auth"){											 
					 $subject = "Activation " . get_option('blogname');
					 
					 $searchArray = array("%USER%", "%PASS%", "%HOME_URL%", "%LINK%");
                     $replaceArray = array($username, $password, home_url(), home_url()."/?sb_key=$user_activation_key&sb_ul=$username");
                     $intoString = get_option('sb_email_yes_auth');
                     $msg = str_replace($searchArray, $replaceArray, $intoString);
				     wp_mail( $email, $subject, $msg, $headers );
					 $msg_display =  "<h3>Registration was successful<br />Check your email to verify your account or go <a href=". home_url() . ">home</a></h3>";
					 
				}else if(get_option('sb_key_option') == "no_auth"){	
					  $subject = "Registration: " . get_option('blogname');
					  $searchArray = array("%USER%", "%PASS%", "%HOME_URL%");
                      $replaceArray = array($username, $password, home_url());
                      $intoString = get_option('sb_email_no_auth');
                      $msg = str_replace($searchArray, $replaceArray, $intoString);
					 wp_mail( $email, $subject, $msg, $headers );
					 $msg_display =  "<h3>Registration was successful<br />Check your email to verify your account or go <a href=". home_url() . ">home</a></h3>";
					 
				}else if(get_option('sb_key_option') == "basic_auth"){	
					  $subject = "Registration: " . get_option('blogname');
					 
					  $searchArray = array("%USER%", "%PASS%", "%HOME_URL%");
                      $replaceArray = array($username, $password, home_url());
                      $intoString = get_option('sb_email_basic_auth');
                      $msg = str_replace($searchArray, $replaceArray, $intoString);
					  wp_mail( $email, $subject, $msg);
					  $msg_display =  "<h3>Registration was successful<br />You can now login or go <a href=". home_url() . ">home</a></h3>";
					 
				}
					 					
                    //mail( $email, $subject, $msg);
					echo $msg_display;
					//exit();
				}

			exit();
			
		} 
		
		
if($_POST && $_POST['action'] == "login"){
	
	global $wpdb, $user_table;
	//$user_table = $wpdb->prefix ."_users";
	$user_table = "wp_users";
		//We shall SQL escape all inputs
		$username = $wpdb->escape($_REQUEST['username_login']);
		$password = $wpdb->escape($_REQUEST['password_login']);
		$remember = $wpdb->escape($_REQUEST['remember']);
	
		if($remember) $remember = "true";
		else $remember = "false";
		$login_data = array();
		$login_data['user_login'] = $username;
		$login_data['user_password'] = $password;
		$login_data['remember'] = $remember;
		$user_verify = wp_signon( $login_data, false ); 
		//wp_signon is a wordpress function which authenticates a user. It accepts user info parameters as an array.
		
				
		if (is_wp_error($user_verify) ) 
		{
		   echo "Invalid username or password. Please try again!";
		   exit();
		 } else {
			 
			 	$sb_sql = "SELECT * FROM $wpdb->users WHERE  user_login = '" . $username . "' LIMIT 1";
		        $f_sql = $wpdb->get_results($sb_sql);
		        $user_activation_key = $f_sql[0]->user_activation_key;	
							
				
		    //if ( is_user_logged_in() ) {
			 if($user_activation_key == "") {  
								
			    echo "Login successful" . $f_sql[0]->user_activation_key;
			   //<script type='text/javascript'>window.location='". get_bloginfo('url') ."'<script>
			   exit();
				
			 }else{
				//log user out
				$email = $f_sql[0]->email;  
				wp_logout();
				echo "Check your email for activation link.  <a href='#'>Resend Activation</a>";
				exit();		
			    

		  }
		  
	    }
} 

}else {
echo "You are already logged in";
		
}

?>