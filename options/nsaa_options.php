<?php
/* NSAA OPTIONS PAGE ====*/

class NSAA_options_settings {

public function __construct(){
add_action('admin_init', array($this, 'nsaa_do_register_settings'));
add_action('admin_menu', array($this,'nsaa_do_add_menu_page'));
}

public function nsaa_do_add_menu_page(){
add_options_page('Settings for No Spam At All ', 'No Spam At All', 'manage_options','nsaa_display_settings_page', array( $this,'nsaa_main_settings_page' ));
}

public function nsaa_do_register_settings(){
	register_setting( 'nsaa_display_settings_page','nsaa_schedule_token', array( $this, 'nsaa_schedule_token_callback' ));
	register_setting( 'nsaa_display_settings_page','nsaa_comment_author_url', array( $this, 'nsaa_comment_author_url_callback' ));
	register_setting( 'nsaa_display_settings_page','nsaa_success_comment', array( $this, 'nsaa_success_comment_cb' ));
	register_setting( 'nsaa_display_settings_page','nsaa_bad_comment', array( $this, 'nsaa_bad_comment_cb' ));

} 
public function nsaa_main_settings_page(){
$nsaa_schedule_token = get_option('nsaa_schedule_token');
$nsaa_comment_author_url =get_option('nsaa_comment_author_url');
$nsaa_success_comment = get_option('nsaa_success_comment');
$nsaa_bad_comment = get_option('nsaa_bad_comment');
//print_r($iap_add_post_id); 
//print_r($iap_remove_post_id);
?>
<div class="wrap nsaa_white">
<h1><img src="<?php echo plugins_url();  ?>/No-spam-at-all/img/settings.png"> <?php _e('"No Spam At All" Settings', 'no-spam-at-all'); ?>  </h1>
<hr>
<form action="options.php" method="POST">
<?php settings_fields( 'nsaa_display_settings_page' );?>
<p>
<label for ="nsaa_schedule_token"> <?php _e( 'How often should the comment token change' , 'no-spam-at-all'); ?> </label>

<select name="nsaa_schedule_token" class="widefat txt-fields" id="nsaa_schedule_token">
<option value ="hourly" <?php if($nsaa_schedule_token == "hourly") echo " selected";  ?>> <?php _e('   Every Hour', 'no-spam-at-all'); ?> </option>
<option value ="twicedaily"  <?php if($nsaa_schedule_token == "twicedaily") echo " selected";  ?>>  <?php _e('Twice Daily', 'no-spam-at-all'); ?> </option>
<option value ="daily" <?php if($nsaa_schedule_token == "daily")  echo " selected"; ?>> <?php _e('Once 
a day', 'no-spam-at-all'); ?> </option>


</select>
</p>
<p>
<label for ="iap_remove_post_id"><?php _e(' Include OR Exclude comment author URL', 'no-spam-at-all' ); ?>  </label>

<select name="nsaa_comment_author_url" id="nsaa_comment_author_url" class="txt-fields">
<option value ='included' <?php if(isset( $nsaa_comment_author_url )){ if( $nsaa_comment_author_url =='included') echo 'selected';}     ?>> Include Comment Author URL </option>
<option value='excluded' <?php if(isset( $nsaa_comment_author_url )){ if( $nsaa_comment_author_url =='excluded' ) echo 'selected';}     ?>> Exclude Comment Author URL </option>
</select>
<span class ="inote"> <?php _e( ' Removing Comment Author URL, according to research will reduce number of spam comments by 40%; ', 'no-spam-at-all'); ?></span>
</p>
<p>
<label for ="nsaa_success_comment"> <?php _e( 'Successfull comment message to real commenters', 'no-spam-at-all' ); ?> </label>
<input type ="text" name="nsaa_success_comment" placeholder ="Love your comment! Comment Again"
class="widefat txt-fields" id="nsaa_success_comment" value="
<?php echo $nsaa_success_comment ?>">
</p>

<p>
<label for ="nsaa_bad_comment"> <?php _e( 'Message to Evil commenters' , 'no-spam-at-all'); ?> </label>
<input type ="text" name="nsaa_bad_comment" placeholder ="Chei! Evil Genuis Again"
class="widefat txt-fields" id="nsaa_bad_comment" value="
<?php echo $nsaa_bad_comment ?>">
</p>
<input type ="submit" value="Save Changes" class="nsaa_clear_btn" name="submit" id="submit">
<?php //submit_button(); ?>
</form>
</div>
<div class="nsaa_clear_all">
<h1> <img src="<?php echo plugins_url();  ?>/No-spam-at-all/img/delete.png"> <?php _e('Manage Spam Comments pending Moderation', 'no-spam-at-all'); ?> </h1>
<hr>

<div class="clear_all_akis">
<span class='meta_display'> </span>
<h3> <?php _e('Clear Akismet Meta values (Recommended)', 'no-spam-at-all'); ?>  </h3>
<button name ="nsaa_clear_wp_c_meta" id="nsaa_clear_wp_c_meta" class="nsaa_clear_btn"> <?php _e(' Clear all comment meta' , 'no-spam-at-all'); ?> </button> 
<p> <?php _e( 'if you were using akismet comment plugin, click to clear, stored comment information.', 'no-spam-at-all'); ?> </p>

</div>

<div class="clear_by_http">
<span class='http_display'> </span>
<h3> <?php _e('Delete All Pending comments containing anchor tags' , 'no-spam-at-all'); ?></h3>
<button name ="nsaa_clear_wp_c_links" id="nsaa_clear_wp_c_links" class="nsaa_clear_btn"> <?php _e('Delete Has Link', 'no-spam-at-all'); ?>  </button> 
<p> <?php _e("Clear all queque comments containing  atleat one anchor tag (sign of spam). Use the bottom below to clear all pending comments, if you don't really give a damn.", 'no-spam-at-all'); ?>   </p>

</div>

<div class="clear_all_c">
<span class='pending_display'> </span>
<h3><?php _e('Delete All Pending comments' , 'no-spam-at-all'); ?></h3>
<button name ="nsaa_clear_wp_comment" id="nsaa_clear_wp_comment" class="nsaa_clear_btn"> <?php _e( '  Clear all pending comment' , 'no-spam-at-all'); ?>  </button> 
<p> <?php _e( '  This step is irreversible, It will delete all comment awaiting moderation' , 'no-spam-at-all'); ?>  </p>

</div>

<div class="donate">
<button class="nsaa_donate_btn_p" id="paypald" onclick ='location.href="http://goo.gl/WZ37di";'name="paypald"> <?php _e( '  Donate Via Paypal', 'no-spam-at-all'); ?> </button>
<div>
<hr>
<?php _e('Use', 'no-spam-at-all'); ?> Voguepay
<div width="120px">
<form method="post" action="https://voguepay.com/pay/">
<br />
<input type="text" name="total" style="width:120px" /><br />
<input type="hidden" name="v_merchant_id" value="12328-5208" />
<input type="hidden" name="memo" value="Plugin Development" />
<input type="image" src="http://voguepay.com/images/buttons/donate_green.png" alt="PAY" />
</form>
</div>

  </div>

</div>

</div>
 <?php
}
public function nsaa_comment_author_url_callback($input){
	return $input;
}

public function nsaa_schedule_token_callback($input){
 return $input;
}

public function nsaa_success_comment_cb($input){
 return $input;
}

public function nsaa_bad_comment_cb($input){
	return $input;

}



}
if(is_admin()) $nsaa_options = new NSAA_options_settings(); ?>