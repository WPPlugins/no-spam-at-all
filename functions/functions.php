<?php
global $nsaa_xed_ips_db_version;
$nsaa_xed_ips_db_version = '1.0';


function nsaa_make_tokens_available( $length = 11 ) {
$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
    $count = mb_strlen($chars);

    for ($i = 0, $token = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $token .= mb_substr($chars, $index, 1);
    }

    return $token;
}
//$nsaa_often =get_option('nsaa_schedule_token');

function nsaa_create_legal_com_tokens() {
    if(get_option('nsaa_com_token') ==false ){  
add_option( 'nsaa_com_token', 'we4r5fre3');
}


$nsaa_check_sch = wp_next_scheduled('nsaa_tokens_generator');

if($nsaa_check_sch ==false){

wp_schedule_event( time(), get_option('nsaa_schedule_token'), 'nsaa_tokens_generator' );

}

}


function nsaa_token_maker_fxn(){

update_option('nsaa_com_token', nsaa_make_tokens_available() );

}


function nsaa_auto_destruct_schedule(){
  wp_clear_scheduled_hook( 'nsaa_tokens_generator' );
}




function nsaa_make_table_for_xed_ips(){
global $wpdb;
global $nsaa_xed_ips_db_version;
$table_name = $wpdb->prefix .'nsaa_xed_comments';

$charset_collate = $wpdb->get_charset_collate();

$sql =  "CREATE TABLE $table_name (
    xed_ip_id bigint(20) NOT NULL AUTO_INCREMENT,
    xed_comment_ip varchar(16) DEFAULT '' NOT NULL,
    xed_comment_post_content text NOT NULL,
    xed_time datetime DEFAULT '0000-00-00:00:00',
    UNIQUE KEY xed_ip_id (xed_ip_id)
) $charset_collate";
require_once(ABSPATH.'wp-admin/includes/upgrade.php');

dbDelta($sql);
add_option( 'nsaa_xed_ips_db_version', $nsaa_xed_ips_db_version );
}

function nsaa_xed_comment_to_base($commentdata){
global $wpdb;
    $table_name =$wpdb->prefix."nsaa_xed_comments";
$xed_comment_ip = $_SERVER['REMOTE_ADDR'];
$xed_comment_post_content = "<strong>Comment Author:</strong>".$commentdata['comment_author']. "<br> <strong>Comment Email:</strong>" . $commentdata['comment_author_email']."<br><strong> The comment: </strong>" . $commentdata['comment_content'];
$xed_time =current_time('mysql');

$wpdb->insert($table_name, array('xed_comment_ip'=>$xed_comment_ip, 'xed_comment_post_content'=>$xed_comment_post_content, 'xed_time'=> $xed_time));
    
}

function nsaa_get_those_spammers(){
global $wpdb;
$table= $wpdb->prefix.'nsaa_xed_comments';
$query= "SELECT * FROM ".$table." ORDER BY xed_ip_id DESC LIMIT 5";
$result_set = $wpdb->get_results( $query, OBJECT );

return $result_set;
}

//gives count of available xed since last delete
function nsaa_count_total_xed(){

global $wpdb;
$table= $wpdb->prefix.'nsaa_xed_comments';
$query= "SELECT count(xed_ip_id) as xed_no FROM ".$table;
$nsaa_counts = $wpdb->get_results( $query,OBJECT);

return $nsaa_counts;
}
//x the xed final burial
function nsaa_delete_all_xed(){

global $wpdb;
$table= $wpdb->prefix.'nsaa_xed_comments';
$query= "TRUNCATE TABLE ".$table;
$nsaa_x= $wpdb->query( $query );
if($nsaa_x):
return true;
else:
return false;
endif;
}

function nsaa_delete_all_on_queue(){
    global $wpdb;
$nsaatable= $wpdb->prefix .'comments';
$query ="DELETE FROM ". $nsaatable. " WHERE comment_approved='0'";
if($nsaa_xed_queue =$wpdb->query( $query )):
    return true;
else:

return false;
endif;

}
//akisba stuff in meta
function nsaa_delete_akis_meta_dump(){
    global $wpdb;
    $akis_met = "%akismet\\_%";
$table= $wpdb->prefix.'commentmeta';
$query= $wpdb->prepare( 'DELETE FROM '.$table .' WHERE meta_key LIKE  %s', $akis_met) ;
 if( $nsaa_xed_meta =$wpdb->query ( $query ) ):

    return true;
    else:
return false;
endif;
}
// comments with links
function nsaa_delete_c_with_links(){
    global $wpdb;
$table= $wpdb->prefix.'comments';
$query='DELETE FROM '.$table.' WHERE comment_approved ="0" AND comment_content REGEXP "[[.less-than-sign.]]a[[.space.]]href.*[[.>.]].*[[.<.]]/a[[.>.]]"';
 if( $nsaa_delete_linked =$wpdb->query ( $query ) ):

    return true;
    else:
return false;
endif;
}

//function added to admin-ajax.php manages links deletion
function nsaa_delete_has_links(){
if ( isset($_POST['delete_has_links']) ) {
    ob_clean();
    if($_POST['delete_has_links'] =='1') { 
  $nsaa_xed_links = nsaa_delete_c_with_links();

if($nsaa_xed_links ===true) {   
echo "<p class ='success'>". _e('Likely spam comments deleted, successfully!!!', 'no-spam-at-all')." </p>";
die();
}

else{
    echo "<p class ='error'>". _e('Nothing Found, Nothing to do. Sigh!', 'no-spam-at-all')." </p>";
die();
}

}
}

}


//function added to admin-ajax.php manages meta akis
function nsaa_clear_meta_akis(){
if ( isset($_POST['delete_ak_meta']) ) {
    ob_clean();
    if($_POST['delete_ak_meta'] =='1') { 
  $nsaa_xed_akis = nsaa_delete_akis_meta_dump();

if($nsaa_xed_akis ===true) {   
echo "<p class ='success'>". _e('Meta values taking database space deleted. successfully!!!', 'no-spam-at-all' )." </p>";
die();
}

else{
    echo "<p class ='error'>Nothing Found, Nothing to do. Sigh! </p>";
die();
}

}
}

}



//Function added to admin-ajax.php to delete all on queque
function nsaa_delete_queque(){
    if ( isset($_POST['delete_queue']) ) {
    ob_clean();
    if($_POST['delete_queue'] =='1') { 
  $nsaa_xed_deleted = nsaa_delete_all_on_queue();

if($nsaa_xed_deleted ===true) {   
echo "<p class ='success'>". _e( ' Comment Queque, Successfully Cleared', 'no-spam-at-all')." </p>";
die();
}

else{
    echo "<p class ='error'> ". _e("Sigh! Nothing to do.", "no-spam-at-all"). "</p>";
die();
}

}
}

}

function nsaa_clear_all_logged_xed(){  
 if($_POST['delete_xed_log']==1){  
 $xed_gone =nsaa_delete_all_xed(); 
 ob_clean();
if($xed_gone ==true){

echo '<p class ="success">'._e( ' Cleared all the log!!! Happy! ', 'no-spam-at-all').'</p>' ;
die();
   } 
   else{
echo '<p class ="error">'. _e(' Nothing to do! Sighs', 'no-spam-at-all'). '</p>' ;
die();
   }
}


}





//All comment log delete here
function nsaa_xed_for_view_delete_js(){ ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
$('#delete_all_logged_xed').click( function(e){ 
e.preventDefault();
var sendxed ={

    action:'nsaa_clear_all_logged_xed',
    delete_xed_log:'1'
    
}
var nsaa_clear =$.post(ajaxurl, sendxed);

nsaa_clear.success(function( oncourse ){
$('.n_xed_all_logs').html(oncourse);
//console.log( oncourse );

    });
nsaa_clear.fail( function( timeout ){
//console.log( timeout );
$('.n_xed_all_logs').html(timeout);


});
});
});

</script>
<?php
}





// Delete js for under moderation containing links 
function nsaa_delete_wp_c_linked(){ ?>
<script>
    jQuery(document).ready(function($) {

   $('#nsaa_clear_wp_c_links').click(function(e){ 
    e.preventDefault();
$('.http_display').html('<img id="pend-load" src="<?php echo plugins_url(); ?>/no-spam-at-all/img/loading_options.gif"/>');
var akis_meta= {
action:   'nsaa_delete_has_links',
delete_has_links:'1'
}

    var  del_akis =$.post(ajaxurl, akis_meta);
    
del_akis.done(function(ppp_success) {
$('.http_display').html(ppp_success);
//console.log(ppp_success);

});

del_akis.fail(function(link_erro){
$('.http_display').html(link_erro);

//console.log(link_erro);

});
});
});
    </script>
<?php


}


/*    Js function for akisba meta               */
function nsaa_clear_wp_c_meta_js(){ ?>
<script>
    jQuery(document).ready(function($) {

   $('#nsaa_clear_wp_c_meta').click(function(e){ 
    e.preventDefault();
$('.meta_display').html('<img id="pend-load" src="<?php echo plugins_url(); ?>/no-spam-at-all/img/loading_options.gif"/>');
var akis_meta= {
action:   'nsaa_clear_meta_akis',
delete_ak_meta:'1'
}

    var  del_akis =$.post(ajaxurl, akis_meta);
    
del_akis.done(function(ppp_success) {

$('.meta_display').html(ppp_success);
//console.log(ppp_success);

});

del_akis.fail(function(akis_erro){
$('.meta_display').html(akis_erro);

//console.log(akis_erro);
});
});
});
    </script>
<?php


}

//all under moderation delete js
function nsaa_delete_quequed_commentx_js(){ ?>
<script>
    jQuery(document).ready(function($) {

   $('#nsaa_clear_wp_comment').click(function(e){ 
    e.preventDefault();
    $('.pending_display').html('<img id="pend-load" src="<?php echo plugins_url(); ?>/no-spam-at-all/img/loading_options.gif"/>');
var commentxed = {
action:   'nsaa_delete_queque',
delete_queue:'1'
}

    var  queue_del =$.post(ajaxurl, commentxed);
    
queue_del.done(function(ppp) {
$('.pending_display').html(ppp);


//console.log(ppp);

});

queue_del.fail(function(cccx){
$('.pending_display').html(cccx);

//console.log(cccx);
});
});
});
    </script>
<?php
}



function nsaa_ajax_comment_do_check_fo_x(){ ?>
<script>
    jQuery(document).ready(function($) {

    	var vform= jQuery('#commentform');
   vform.submit(function(e){ 
   	e.preventDefault();
var formURL = vform.attr('action'); 
var commentdata = {

    author : $('#author').val(),
    email : $('#email').val(),
    comment : $('#comment').val(),
    comment_post_ID: $('input[name=comment_post_ID]').val(),
    comment_parent: $('input[name=comment_parent]').val(),
    i_token: '<?php echo get_option("nsaa_com_token");     ?>'
}


    var   created =$.post(formURL,commentdata);
	    
$('#commentform').html('<img id="pend-load" src="<?php echo plugins_url(); ?>/no-spam-at-all/img/loading.gif"/>');

created.done(function(thetext) {
if(thetext =="too much tag"){ 
$('#commentform').html('<p class="error"> Not All tags are allowed! Please remove html tags from your comments and try again </p>');
//console.log( thetext );
}
else{ 
$('#commentform').html('<p class="error"> <span> <img src="<?php echo plugins_url(); ?>/no-spam-at-all/img/good.png"> </span><?php if(get_option("nsaa_success_comment") ==""){  echo "Love your comment"; }else { echo get_option("nsaa_success_comment"); } ?> </p>');
//console.log(thetext);
}

});

created.fail(function( failtext ){
   // console.log( failtext );

$('#commentform').html('<p class="error"><?php if(get_option("nsaa_bad_comment") ==""){  echo "Lover your ruins"; }else { echo get_option("nsaa_bad_comment"); } ?></p>');

});



       

    });

});



</script>
<?php
}