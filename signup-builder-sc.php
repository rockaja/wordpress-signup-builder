<?php
function shortcode_reg(){
 //return "This is a test for signup-builder";
 global $wpdb, $signup_builder_table, $user_ID;
 
 if($user_ID){ 
 
  echo "<p>You are already logged in</p>";
  
 }else{
 if (!get_option('users_can_register')) { 
 
 echo "<p>User registration is disabled at the moment</p>";

 }else{
	
	
	$signup_builder_table = $wpdb->prefix . "signup_builder";				    
                    $use_sql = "SELECT * FROM ".$signup_builder_table." ORDER BY field_order ASC";
                    $table_Details =  $wpdb->get_results($use_sql);
					$max = count($table_Details);
					?>
<script>


function login_ajax(type){
	
   if(type =='ajax'){				
   var_form_data = $('#sb_login_form').serialize();
   $("#stylized").fadeOut();
   $.ajax({
            type: "POST",
            url: "<?php echo plugins_url( 'signup-builder/signup-builder-ajax.php' ); ?>",
            data: var_form_data,
            success: function(data){
			var str=data;
              var n=str.indexOf("successful");
			   
			  
			 if(n > 0){
				 $("#sb_result_login").html("");
                      window.location.href='<?php echo home_url(); ?>';
						  
			 }else{
				  
				  var str=data;
                  var n=str.indexOf("logged in"); 
				   if(n > 0){
					 $("#stylized").addClass("sb_success");
				     $("#stylized").html("<h3>Oops!<br />You are already logged in. Go <a href=<?php echo home_url(); ?>>home</a></h3>");  					   
				    }else{
			
			       $("#sb_result_login").addClass('sb_error');
			       $("#sb_result_login").css({padding:"5px"});
                   //alert( "Data Saved: " + data );
			       $("#sb_result_login").html(data);
				 
				 }
            }
			
			
		}
    });
	 $("#stylized").fadeIn();	
   }else{
	   
	   document.sb_login_form.submit();
	   
   }
}

function reg_ajax(type){
	
   if(type =='ajax'){				
   var_form_data = $('#sb_reg_form').serialize();
   $("#stylized").fadeOut();
	$.ajax({
            type: "POST",
            url: "<?php echo plugins_url('signup-builder/signup-builder-ajax.php' ); ?>",
            data: var_form_data,
            success: function(data){
			  var str=data;
              var n=str.indexOf("successful");
			   
			  
			 if(n > 0){
				 $("#sb_result").html("");
				 
				  $("#stylized").addClass("sb_success");
				 
				  $("#stylized").html(data);
						  
			 }else{
				  
				  var str=data;
                  var n=str.indexOf("already");			 
				   if(n > 0){
					 $("#stylized").addClass("sb_success");
				     $("#stylized").html("<h3>Oops!<br />You are already logged in. Go <a href=<?php echo home_url(); ?>>home</a></h3>");  
					}else{
			
			       $("#sb_result").addClass('sb_error');
			       $("#sb_result").css({padding:"5px"});
                   //alert( "Data Saved: " + data );
			       $("#sb_result").html(data);
				 
				 }
            }
			 
			
		}
    });
	$("#stylized").fadeIn();	
   }else{
	   
	   document.sb_reg_form.submit();
	   
   }
}


function submit_data(type){
	
 if( type == "signup"){
	       reg_ajax('ajax');	
	}
	
 if( type == "login"){
	       login_ajax('ajax');	
	}

}
					


function cv(id){
    //alert (object.val());
	if($('#'+ id).attr('checked') == true){
		($('#'+ id).val('true'));
	}else{
		($('#'+ id).val('false'));
	}
}



function hide_login(){
    $('#sb_login_form').css({display:'none'});
    $('#sb_reg_form').css({display:'block'});
}

function hide_signup(){
    $('#sb_login_form').css({display:'block'});
    $('#sb_reg_form').css({display:'none'});
}



<?php
if($_REQUEST['type'] == "login"){ ?>
   hide_signup();
<?php } ?>
<?php if($_REQUEST['type'] == "reg"){ ?>
   hide_login();
<?php } ?>

<?php if(get_option('sb_show_value') != "top") { ?>
	$(document).ready(function(){
	$('#sb_login_form').css({display:'block'});
    $('#sb_reg_form').css({display:'none'})
			});			 
<?php } ?>
</script>
                    
 <div id="stylized" class="myform" style="height:display:block;clear:both;" >                   
                       
<form method="POST" action="<?php echo plugins_url( 'signup-builder/signup-builder-ajax.php' ); ?>" id="sb_login_form" name="sb_login_form"><?php //echo $_SERVER['REQUEST_URI']; ?>
                        
                     <div><h2 class="widget-title">Login &nbsp;&nbsp;<span id="sb_result_login"></span></h2></div>
                     <!--<h3>Login by filling the form below</h3>-->
                     <hr />
                    
                 <?php
                    foreach ($table_Details as $builder_list) :
                    $i++;
					$a = $builder_list->id;
					//$field_id = "sb_input_" . (string)$i;
					
					$field_id = "sb_input_" . (string)$a;
					#Do not generate ids for these
					if($builder_list->field_name == "password" or $builder_list->field_name == "username"){
						 $field_id = $builder_list->field_name;
					?>
                    
                     <div class="field"><label for="<?php echo $field_id ?>"><?php echo $builder_list->field_label; ?>:<em>*</em>
                     
                     <span class="small" id="small_<?php echo $field_id ?>"><?php echo $builder_list->field_sub_label; ?></span></label>
<?php echo build_input($builder_list->field_type,
						                           //$builder_list->field_name,
												   $field_id . "_login", 
												   //$builder_list->field_class,
												   $required,
												   $builder_list->field_values,
												   $builder_list->field_max_value);  ?>
                                                   <span id="<?php echo $field_id ?>_login_info"></span>
                                                   </div>
                   
					<?php }
                 
                
                    endforeach;
                ?>
                
                <div class="field"><label for="<?php echo $field_id ?>">Remember me:</em>
<span class="small"></span></label>
                          <?php echo build_input('checkbox',
						                         'remember', 
												  //$builder_list->field_class,
												  'class="required"',
												   '',
												   1);  ?>
                                                   <span  id="remember_info"></span>
                                                   </div>
                
				<div class="field"><label for="<?php echo $field_id ?>">&nbsp;</label>
				              <?php _e(build_input('button_login',
						                           'login_btn', 
												   'sb_default',
												   'Login Now','')); ?> 
                </div>
                
                
           <input type="hidden" name="action" id="action" value="login" />
</form>
          
<div class="spacer"></div>  


<form method="POST" action="<?php echo plugins_url( 'signup-builder/signup-builder-ajax.php' ); ?>" id="sb_reg_form" name="sb_reg_form"><?php //echo $_SERVER['REQUEST_URI']; ?>
                        
                     <div><h2 class="widget-title">Signup &nbsp;&nbsp;<span id="sb_result"></span></h2></div>
                     <hr />
                    
                 <?php
                    foreach ($table_Details as $builder_list) :
                    $i++;
					$a = $builder_list->id;
					
					$field_id = "sb_input_" . (string)$a;
					
					if($builder_list->field_name == "password" or $builder_list->field_name == "username" or $builder_list->field_name == "email"){
						 $field_id = $builder_list->field_name;
						 $i--;
						
						}else{
							
							//$myinputs[] = "'". $field_id ."'";
							$myinputs[] = $field_id;
							//$myinputs .= $field_id . ',';
							
						}
					//}
					
					if(get_option('sb_key_option') =="no_auth" and $builder_list->field_name == "password" ){
						
					//}else if(get_option('sb_key_option') =="basic_auth" and $builder_list->field_name == "password" ){
					 }else{
					
					?>
                
                    
                     <div class="field"><label for="<?php echo $field_id ?>"><?php echo $builder_list->field_label; ?>: 
                     <?php
                     
					 if($builder_list->field_isEmpty == 1){
						 
						 $required = "required";
					 ?>
                     <em>*</em>
                     <?php } 
					 ?>
                     <span class="small" id="small_<?php echo $field_id ?>"><?php echo $builder_list->field_sub_label; ?></span></label>
<?php echo build_input($builder_list->field_type,
						                           //$builder_list->field_name,
												   $field_id, 
												   //$builder_list->field_class,
												   $required,
												   $builder_list->field_values,
												   $builder_list->field_max_value);  ?>
                                                   <span id="<?php echo $field_id ?>_info"></span>
                                                   </div>
                   
                    <input type="hidden" name="<?php echo $field_id ?>_validate" id="<?php echo $field_id ?>_validate" value="<?php echo $builder_list->field_isEmpty ?>" />
					<?php
                    
					
					 }
					 
					if(get_option('sb_key_option') =="no_auth" and $builder_list->field_name == "password" ){
						$i--;
					 }else{
					if($builder_list->field_type == "password"){
						$i--;
					?>
                    
                     <div class="field"><label for="<?php echo $field_id ?>">Retype Password:<em>*</em>
<span class="small">Retype</span></label>
                          <?php echo build_input('password',
						                         'retype', 
												  //$builder_list->field_class,
												  'class=""',
												   '',
												   $builder_list->field_max_value);  ?>
                                                   <span  id="retype_info"></span>
                                                   </div>
                     <input type="hidden" name="retype_validate" id="retype_validate" value="1" />
                                        
                    <?php  
                    }
				}
                    endforeach;
                ?>
                
				<div class="field"><label for="<?php echo $field_id ?>">&nbsp;</label><?php _e(build_input('button_signup',
						                           'submit_btn', 
												   'sb_default',
												   'Signup Now','')); ?> 
                </div>
                <input type="hidden" name="total" id="total" value="<?php echo $i ?>" />                
                <input type="hidden" name="myinputs" id="myinputs" value="<?php echo implode(',', $myinputs); ?>"  />             
                <input type="hidden" name="action" id="action" value="signup" />
           
          </form>
          
          
          <div class="spacer"></div>        
          </div>
          
		
		 <?php
		 
    }
	
  }
  
  

}
add_shortcode( 'sb-signup', 'shortcode_reg' );	
?>