<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb;
$ads_id = '';    
//Save data
if (isset( $_POST["pippin_adsList_nonce"] ) && wp_verify_nonce($_POST['pippin_adsList_nonce'], 'pippin-adsList-nonce')) {
	$ads_url 	= sanitize_text_field($_POST['ads_url']);
	$ads_time	= sanitize_text_field($_POST['ads_time']);
	$ads_id	 	= sanitize_text_field($_POST['ads_id']);
	$ads_table  = $wpdb->prefix."rcode_ads";	
	if($ads_url!='' && $ads_time!=''){
		//update
		if($ads_id) { 			
			$sql = "UPDATE $ads_table
						SET  
							ads_url  = '$ads_url',
							ads_time = '$ads_time'
						WHERE id = '$ads_id'";
			$msg = "Info updated successfully.";			
		}else{											
			$sql 	= "INSERT INTO $ads_table(
							ads_url,ads_time
						) 
						VALUES (
							'$ads_url','$ads_time'
						)";
			$msg = "Information added successfully.";	
		}						
		$wpdb->query($sql);
		echo "<p style=color:green;>".$msg."</p>";
	}else{
		echo "<p style=color:red;>Please fill the information.</p>";
	}
}
$ads 		= $wpdb->get_results( "SELECT * FROM $ads_table WHERE id = '$id'");	
$ads_table 	= $wpdb->prefix."rcode_ads";
$ads 	 	= $wpdb->get_results( "SELECT * FROM $ads_table ORDER BY id ASC");	
?>
<div class="ads-list">	
<h3>Ads List</h3>
<a href="#TB_inline?width=300&height=300&inlineId=add-ads-div" class="thickbox button button-primary button-large">Add New Add</a>
<?php
if($ads){ 
?>
	<table class="adlist-tbl">
	<tr>
	<th>Id</th>
	<th>Ads</th>
	<th>Time</th>
	<th>Action</th>
	</tr>
	<?php																
		foreach ( $ads as $adsVal ){  
		?>
	    <tr>
	    	<td><?php echo $adsVal->id; ?></td>
	        <td><?php echo $adsVal->ads_url; ?></td>
	        <td><?php echo $adsVal->ads_time; ?></td>
	        <td>
	        <a href="#TB_inline?width=300&height=300&inlineId=add-ads-div" onclick="RCODE_VWA_AdsUpdateValue('<?php echo $adsVal->id; ?>','<?php echo $adsVal->ads_url; ?>','<?php echo $adsVal->ads_time; ?>');" class="thickbox">Edit</a> | 
	        <a href="#TB_inline?width=300&height=300&inlineId=add-ads-div" onclick="RCODE_VWA_DeleteAds('<?php echo $adsVal->id; ?>');">Delete</a>
	        </td>
	    </tr>
	    <?php
		}
}else{
	?>
	<tr><td><h4>No ads found.</h4></td></tr>
	<?php
}
add_thickbox();
?>
</table>
</div>


<div id="add-ads-div" style="display:none;">
<p>
<div class="ads-list-add">
<h2>Add new </h2>
<form method="post" action="">
   <div class="fields_row">
     <label>Ads Video URL</label><br />
     <input type="text" name="ads_url"  id="ads_url" value="<?php if($ads[0]->ads) { echo $ads[0]->ads; } ?>"  placeholder="Ads URL" size="50px;" required/>
   </div><br />   
   
   <div class="fields_row">
     <label>Ads Time (MM:SS)</label><br />
     <input type="text" name="ads_time"  id="ads_time" value="<?php if($ads[0]->ads) { echo $ads[0]->ads; } ?>"  placeholder="mm:ss" size="10px;" required/>
   </div><br />     
   <input type="hidden" name="ads_id" id="ads_id">
   <div class="fields_row"><br />
	  <button type="submit" name="submit" class="button button-primary">Save!</button>
   </div>
   <input type="hidden" name="pippin_adsList_nonce" value="<?php echo wp_create_nonce('pippin-adsList-nonce'); ?>"/>
</form>
</div>
</p>
</div>
<script type="text/javascript">
function RCODE_VWA_AdsUpdateValue(id,url,time){
	jQuery('#ads_id').val(id);
	jQuery('#ads_url').val(url);
	jQuery('#ads_time').val(time);
}
function RCODE_VWA_DeleteAds(id){
	if (confirm("Are you sure want to delete!")) {
		var form_data 	= new FormData();
		form_data.append('action', 'delete_ads_action');
		form_data.append('ads_id', id);
		form_data.append('pippin_adsDelete_nonce', '<?php echo wp_create_nonce('pippin-adsDelete-nonce'); ?>');
		jQuery.ajax({ 
			 data		: form_data,
			 type		: 'POST',
			 url		: '<?php echo admin_url( 'admin-ajax.php' );?>',
			 contentType: false,
			 processData: false,
			 success	: function(response){
			 	alert('AD Deleted Successfully.');  
			 	location.reload();
			 }
		});
    } 
}
</script>