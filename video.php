<?php   
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$main_video = '';
if (isset( $_POST["pippin_video_nonce"] ) && wp_verify_nonce($_POST['pippin_video_nonce'], 'pippin-video-nonce')) {
    	   $main_video	= sanitize_text_field($_POST['main_video']);
    	   update_option( 'rcode_main_video', $main_video );
    	   $msg 	= "Information updated successfully.";						
    	   echo "<p style=color:green;>".$msg."</p>";
}
$main_video = get_option('rcode_main_video');
?>
<div class="section_area_white">
<div class="form_area">
<form method="post" action="">
   <div class="fields_row">
     <h3>Main Video URL</h3>
     <input type="text" name="main_video"  id="main_video" value="<?php echo $main_video; ?>"  placeholder="Video URL" size="120px;" required/>
   </div><br/>     
   
   <div class="fields_row">
	  <button type="submit" name="submit" class="button button-primary">Save</button>
   </div>
  <input type="hidden" name="pippin_video_nonce" value="<?php echo wp_create_nonce('pippin-video-nonce'); ?>"/>
</form>
<div id="response_msg"></div>
</div>
</div>