<?php
/*
Plugin Name: No Spam At All
Plugin URI: mrparagon.me/no-spam-at-all/
Description: "No Spam At All" prevents spam comments on your wordpress website/blog and Provides you with options to manage comments. You can go from 3,000 Spam comments per day to zero spam comments. 
Author: Kingsley Paragon
Version: 1.1
Author URI: mrparagon.me
license: GPLV2
*/

//init things happening    
require_once('options/nsaa_options.php');
require_once('functions/functions.php');
require_once("NSAA_language_maker_Kulasi.php");
//end init

//activation base creation 
register_activation_hook( __FILE__, 'nsaa_make_table_for_xed_ips' );
register_activation_hook(__FILE__, 'nsaa_create_legal_com_tokens');
add_action('nsaa_tokens_generator', 'nsaa_token_maker_fxn');
//style thing here 
function nsaa_add_style_option_page(){
wp_register_style('nsaa_style',plugins_url('/css/nsaa_style.css', __FILE__));
wp_enqueue_style('nsaa_style');

}

//add style to admin and to other pages
add_action('admin_enqueue_scripts','nsaa_add_style_option_page');
add_action('wp_enqueue_scripts', 'nsaa_add_style_option_page');

function nsaa_display_killed(){ 
?>
<div>    
<h2> <?php _e('  "No Spam At All" Said No', 'no-spam-at-all' ); ?></h2>

<?php 
$nsaa_total_xed =nsaa_count_total_xed(); ?>
<p> Total number stopped: <?php echo $nsaa_total_xed[0]->xed_no; ?>  

<button name ="delete_all_logged_xed" id ="delete_all_logged_xed" class="btn-log"> Delete All</button>

</p>
<p class="n_xed_all_logs"> </p>
<div class="cls"> </div>
<?php $nsaa_xed_violaters =nsaa_get_those_spammers(); ?>
<table class="xed">
<thead>
<th> SN</th>	<th>IP</th> <th><?php _e('Content', 'no-spam-at-all'); ?></th> <th> <?php _e('Time', 'no-spam-at-all'); ?></th>
</thead>

<tbody>

<?php 
//print_r($nsaa_xed_violaters);
$i =1;
if(!empty( $nsaa_xed_violaters )):
foreach ($nsaa_xed_violaters as $xed_violaters){  

	echo "<tr><td>". $i ."</td><td>".$xed_violaters->xed_comment_ip."</td><td>".$xed_violaters->xed_comment_post_content."</td><td>".$xed_violaters->xed_time."</td></tr>";
	$i++;
}
endif;
?>

</tbody>
</table>


</div>
<?php
}

//display dashboard shows stopped spams
function nsaa_register_stopped_comment_displayer(){
	wp_add_dashboard_widget('display_killed_widget','Displays spams stopped','nsaa_display_killed');
}

add_action('wp_dashboard_setup', 'nsaa_register_stopped_comment_displayer');



//remove url if user says so
if(get_option('nsaa_comment_author_url') =='excluded'){ 
add_filter('comment_form_field_url', 'nsaa_delete_url');
}
function nsaa_delete_url(){
	return;
}

//comment checkx
function nsaa_reread_commentdata($commentdata){	
ob_flush();
$nsaa_token = get_option('nsaa_com_token');
//echo $nsaa_token;


if( !isset( $_POST['i_token'] ) AND !is_admin() ) {
 nsaa_xed_comment_to_base($commentdata);
	die( __('Wait for the page to load before posting comments! Manners ', 'no-spam-at-all') );
}

if(isset($_POST['i_token'])){
if( ($_POST['i_token'] == $nsaa_token) ){ 

//echo $_POST['i_token'];
	//lets check the content for anchor tags
	$pattern ="#(?:<script.+>.+</script>|<script>.+</script>|<a href.+>.+</a>)#i";
	if( preg_match($pattern, $commentdata['comment_content'] )  ) {
		nsaa_xed_comment_to_base($commentdata);
		echo "too much tag";
		die();
	}
	else{
		//print_r($commentdata);
		return $commentdata;
		echo'success';
	}

}

else { 

nsaa_xed_comment_to_base($commentdata);
die();
}
//die();
}
elseif(is_admin() )
{
		return $commentdata;
		echo 'success';


}
}

add_filter( 'preprocess_comment', 'nsaa_reread_commentdata' );
add_action( 'admin_footer','nsaa_delete_quequed_commentx_js' );
add_action( 'admin_footer', 'nsaa_clear_wp_c_meta_js' );
add_action( 'admin_footer', 'nsaa_delete_wp_c_linked' );

add_action('admin_footer', 'nsaa_xed_for_view_delete_js');
//settling thing 
add_action('wp_footer', 'nsaa_ajax_comment_do_check_fo_x');

add_action( 'wp_ajax_nsaa_delete_queque', 'nsaa_delete_queque' );
//add_action( 'wp_ajax_nopriv_nsaa_delete_queque', 'nsaa_delete_queque' );

add_action( 'wp_ajax_nsaa_clear_meta_akis', 'nsaa_clear_meta_akis' );
add_action( 'wp_ajax_nsaa_delete_has_links', 'nsaa_delete_has_links' );

add_action('wp_ajax_nsaa_clear_all_logged_xed', 'nsaa_clear_all_logged_xed');

//little clean up 
register_deactivation_hook(__FILE__,'nsaa_auto_destruction_options_value');

register_deactivation_hook( __FILE__, 'nsaa_auto_destruct_schedule' );


function nsaa_auto_destruction_options_value(){
	unregister_setting('nsaa_display_settings_page', 'nsaa_schedule_token');
	unregister_setting('nsaa_display_settings_page', 'nsaa_comment_author_url');
	unregister_setting('nsaa_display_settings_page', 'nsaa_success_comment');
	unregister_setting('nsaa_display_settings_page', 'nsaa_bad_comment');

}
?>