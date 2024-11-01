<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
  global $wpdb;
  $dom = new DOMDocument('1.0','UTF-8');
  $dom->formatOutput = true;

  $root = $dom->createElement('VAST');
  $root->setAttribute('version', '2.0');
  $dom->appendChild($root);
  // Video  
  //$rows 		= get_field('video_ads');	
  $video_skip_time  = 5000;
  $ads_skip_option  = 1;
  $ads_table 	      = $wpdb->prefix."rcode_ads";
  $rows 	 	        = $wpdb->get_results( "SELECT * FROM $ads_table ORDER BY id ASC");
  $total_ads 	      = count($rows);
  if($rows){
	  $i = 0;
	  foreach($rows as $row){
		  $result = $dom->createElement('Ad');
		  $result->setAttribute('id', 'mid-roll-'.$i);
		  $root->appendChild($result);
	
		  $result1 = $dom->createElement('InLine');
		  $result->appendChild($result1);
		  $result1->appendChild( $dom->createElement('AdSystem', '2.0') );
		  $result1->appendChild( $dom->createElement('AdTitle', 'Sample') );

		  $Impression = $dom->createElement('Impression');
		  $result1->appendChild($Impression);

		  $Creatives = $dom->createElement('Creatives');
		  $result->appendChild($Creatives);
	
		  $Creative = $dom->createElement('Creative');
		  $Creative->setAttribute('sequence', '1');
		  $Creative->setAttribute('id', '2');
		  $Creatives->appendChild($Creative);
	
		  $Linear = $dom->createElement('Linear');
		  $Creative->appendChild($Linear);
		  $Linear->appendChild( $dom->createElement('Duration', '00:02:00') );
		  $Linear->appendChild( $dom->createElement('AdParameters','') );
	
		  $MediaFiles = $dom->createElement('MediaFiles');
		  $Linear->appendChild($MediaFiles);
		 
		  $MediaFile = $dom->createElement('MediaFile');
		  $MediaFile->setAttribute('delivery', 'progressive');
		  $MediaFile->setAttribute('bitrate', '400');
		  $MediaFile->setAttribute('type', 'video/mp4');
		  $MediaFiles->appendChild($MediaFile);		  
		 
		  //$page_id = get_the_ID();
		  $MediaFile->appendChild( $dom->createElement('URL', $row->ads_url ) );
		  $i++;
	 }	
	  //echo '<xmp>'. $dom->saveXML() .'</xmp>';
	  $page_id    = get_the_ID();
	  $file_name  = $page_id.'.xml';
	  $folder_url = RCODE_VWA_DIR_PATH ."/assets/xml/".$file_name;
	  $dom->save( $folder_url ) or die('XML Create Error');	
 }
?>
<div class="video_section">
<div>
				<?php 
          $page_id           = get_the_ID();
          $main_video 	     = get_option('rcode_main_video');
          $file_name         = $page_id.'.xml';
          $folder_url 	     = RCODE_VWA_URL_PATH ."assets/xml/".$file_name;
          $video_skip_time   = 5000;                
          ?>

<video controlsList="nodownload"  onpause="trackVedioWatchTime()"  id="example_video_1"  width = "640" height="480"  controls      
                ads =     '{"servers":       
                  [   
                                        
        <?php 
					foreach($rows as $row){
                ?>
                {"apiAddress": "<?php echo $folder_url; ?>"},
                
          <?php
               }
          ?>                    
                ], 
                "schedule":
                [
                                     
                <?php 
                	foreach($rows as $row){
                ?>
                {  
                "position": "mid-roll",
                "startTime": "00:<?php echo $row->ads_time; ?>"
                }, 
                  
                <?php
                }
                ?> 
                
                ],    
                }' 
                src="<?php echo $main_video; ?>"></video>
             </div>
             
        <span class="skipBtn" style="display: none;"></span>
            
            <?php
			//if($ads_skip_option){
				?>
				<span class="advertisement" id="advertisement">
					Advertisement (0:<span id="adver-div">00</span>)
				</span>
            <?php //} ?>
            
            <span id="countAdNumberAndTime"></span>
            <input type="hidden" name="videoSkipTime" id="videoSkipTime" value="<?php if(isset($video_skip_time) && !empty($video_skip_time)){ echo $video_skip_time; } else { echo 0; } ?>"  />
            <input type="hidden" name="currentTime" id="currentTime" value="0" />
            <input type="hidden" name="totalTime" id="totalTime" value="0" />   
            <input type="hidden" name="sumofAddVideo" id="sumofAddVideo" value="0" />
            <input type="hidden" name="randomTimeValues" id="randomTimeValues"  />
            <input type="hidden" name="site_url" id="site_url" value="<?php echo site_url(); ?>"  />
         	  <input type="hidden" name="total_ads" id="total_ads" value="<?php echo $total_ads; ?>"  />   
            <input type="hidden" name="videoLoadTime" id="videoLoadTime" value="0" />
           </div>


                
        
<script>
initAdsFor("example_video_1");
jQuery('video#example_video_1').bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function(e) {
    var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;
    var event = state ? 'FullscreenOn' : 'FullscreenOff';
    if(event=='FullscreenOn'){
        jQuery( "body" ).addClass( "fullScreen" );
    }else if(event=='FullscreenOff'){
        jQuery( "body" ).removeClass( "fullScreen" );
    }  
});



var video = document.getElementById('example_video_1');
var supposedCurrentTime = 0;
video.addEventListener('timeupdate', function() {
  if (!video.seeking) {
	var countAdTime = jQuery('#countAdNumberAndTime').attr("data-countadtime");
	if(typeof countAdTime === "undefined") {
		supposedCurrentTime = video.currentTime;
	}else{
		supposedCurrentTime = video.currentTime+countAdTime; //video.currentTime;
	}
  }
});



// prevent user from seeking
video.addEventListener('seeking', function() {
  var delta = video.currentTime - supposedCurrentTime;
  if (Math.abs(delta) > 0.01) {
    video.currentTime = supposedCurrentTime;
  }
});



video.addEventListener('ended', function() { 
    var totalTime     = jQuery('#totalTime').val();
    var sumofAddVideo = jQuery('#sumofAddVideo').val();
    var finalVal      = Number(totalTime) + Number(sumofAddVideo);
    jQuery('#sumofAddVideo').val(finalVal);
    //hide skip button
    jQuery('.skipBtn').hide();
    jQuery('.skipBtn1').hide();
});   



setInterval(function(){
    var cTime 			= jQuery('.video_section').find('video').get(0).currentTime; 
    var tTime 			= jQuery('.video_section').find('video').get(0).duration;  
    var counDownTime 	= tTime - cTime;
    counDownTime 	 	= Number(counDownTime).toFixed(0); 
    if(tTime < 30){
        jQuery('#advertisement').show();
        jQuery('#adver-div').html(counDownTime);
    		//Skip time from admin
    		//if skip time and timer is equal like 5=5
    		var videoSkipTime   = jQuery('#videoSkipTime').val(); 
    		var readySkip = (videoSkipTime/1000) - cTime;
    		if(readySkip.toFixed(0) > 0){
    			jQuery(".skipBtn").removeClass("skipBtn1").addClass("disabled").show();
    			jQuery('.skipBtn').html('You can skip in <br />'+readySkip.toFixed(0)+' sec');
    		}else{
          jQuery(".skipBtn").removeClass("disabled").addClass("skipBtn1");
          jQuery('.skipBtn1').html("Skip Ad &raquo;");	
    		}		    
    }else{
        jQuery('#advertisement').hide();
    }	
    jQuery('#currentTime').val(jQuery('.video_section').find('video').get(0).currentTime);
    jQuery('#totalTime').val(jQuery('.video_section').find('video').get(0).duration);
},500);


video.onplay = function() {
    var totalTime   = jQuery('#totalTime').val();
    //if(totalTime > 400){  
        var randomTimeValues = Math.random();
        jQuery('#randomTimeValues').val(randomTimeValues);
    //}
};


//Save video track
function trackVedioWatchTime(){
    var totalTime   = jQuery('#totalTime').val();
    var randomTimeValues = Math.random();
    jQuery('#randomTimeValues').val(randomTimeValues);
	//Disable ads pause
	<?php if($ads_skip_option){ ?>
	if(totalTime < 30){  
		video.play();
	}
	<?php } ?>
} 
</script>