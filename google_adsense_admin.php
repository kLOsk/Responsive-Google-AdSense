<?php  ?>

<div class="wrap">

	<h1><?php echo NAME; ?></h1>

	<div id="uxzBlnDPUAJq">
	  <h2><?php _e( 'AD BLOCKER DETECTED!' , 'responsive-google-adsense' );?></h2>
		<strong><?php _e( 'Please make sure to disable your ad blocker when setting up this plugin.' , 'responsive-google-adsense' );?></strong>
	</div>

	<script>
	  if( window.canRunAds === undefined ){
	    // adblocker detected, show fallback
	    document.getElementById('uxzBlnDPUAJq').style.display='block';
	  }
	</script>

	<!--Google Adsense Configurations-->
	<div style="display: block;">
		<div class="card" id="garr_settings">

		<h2 class="title"><?php _e( 'AdSense Account Settings' , 'responsive-google-adsense' );?></h2>
		<table class="form-table">
			<tr>
				<th>
					<label><?php _e( 'AdSense Publisher ID' , 'responsive-google-adsense' );?></label>
				</th>
				<td>
					<input type="text" size="40" name="garr_ads_id" id="garr_ads_id" value="<?=get_option('garr_ads_id')?>"/>
					<?php if(!get_option('garr_ads_id')){ ?>
						<span class="explain alert" id="alert1"><?php _e( 'To display ads from Google AdSense it is required to enter your AdSense Publisher ID. Your ID can be found in your <a href="https://www.google.com/adsense">AdSense panel</a> in the "Settings" under "Account Information" (<a href="https://support.google.com/adsense/answer/105516">More Help</a>). Eg: pub-4268725654361605' , 'responsive-google-adsense' );?></span>
					<?php } else {?>
						<span class="explain"><?php _e( 'The AdSense Publisher ID is the unique identifier of your account at Google AdSense.' , 'responsive-google-adsense' );?></span>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e( 'Ad Unit ID' , 'responsive-google-adsense' );?></label>
					</th>
				<td>
					<input type="text" size="40" name="garr_ad_unit_id" id="garr_ad_unit_id" value="<?=get_option('garr_ad_unit_id')?>"/>
					<?php if(!get_option('garr_ad_unit_id')){ ?>
						<span class="explain alert" id="alert2"><?php _e( 'In order to show your ads, you are required to enter the Ad Unit ID. Your ID can be found in your <a href="https://www.google.com/adsense">AdSense panel</a> -> My ads -> Content -> Ad units. In order to show responsive ads it is important to create a responsive Ad unit first. Eg: 1883482543' , 'responsive-google-adsense' );?></span>
					<?php } else {?>
						<span class="explain"><?php _e( 'The Ad Unit ID is the identifier for your AdSense Ad Unit.' , 'responsive-google-adsense' );?></span>
					<?php } ?>
				</td>
			</tr>
		</table>


		<!--Print Settings-->
		<h2 class="title"><?php _e( 'Display Settings' , 'responsive-google-adsense' );?></h2>

		<form name="garr_form_print_settings" id="garr_form_print_settings" method="" action="">
		<?php
			$values = json_decode(get_option('garr_print_settings'));
		?>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e( 'Number of ads per Page' , 'responsive-google-adsense' );?></label>
					</th>
					<td>
						<select name="ads_per_page" id="ads_per_page">
							<option value="0" <?php if($values->ads_per_page==0) echo "selected='selected'" ?>>0</option>
							<option value="1" <?php if($values->ads_per_page==1) echo "selected='selected'" ?>>1</option>
							<option value="2" <?php if($values->ads_per_page==2) echo "selected='selected'" ?>>2</option>
							<option value="3" <?php if($values->ads_per_page==3) echo "selected='selected'" ?>>3</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e( 'Number of ads per Post' , 'responsive-google-adsense' );?></label>
					</th>
					<td>
						<select name="ads_per_post" id="ads_per_post">
							<option value="0" <?php if($values->ads_per_post==0) echo "selected='selected'" ?>>0</option>
							<option value="1" <?php if($values->ads_per_post==1) echo "selected='selected'" ?>>1</option>
							<option value="2" <?php if($values->ads_per_post==2) echo "selected='selected'" ?>>2</option>
							<option value="3" <?php if($values->ads_per_post==3) echo "selected='selected'" ?>>3</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e( 'Do not show ads on these Pages' , 'responsive-google-adsense' );?></label>
					</th>
					<td>
						<input type="checkbox" name="non_show_home" id="non_show_home" <?php if(isset($values->non_show_home) && $values->non_show_home=='on')echo "checked='checked'"; ?>/> <?php _e( 'Home Page' , 'responsive-google-adsense' );?><br />
						<input type="checkbox" name="non_show_stats" id="non_show_stats" <?php if(isset($values->non_show_stats) && $values->non_show_stats=='on')echo "checked='checked'"; ?>/> <?php _e( 'Static Pages' , 'responsive-google-adsense' );?> <br />
						<input type="checkbox" name="non_show_posts" id="non_show_posts" <?php if(isset($values->non_show_posts) && $values->non_show_posts=='on')echo "checked='checked'"; ?>/> <?php _e( 'Posts Pages' , 'responsive-google-adsense' );?> <br />
						<input type="checkbox" name="non_show_categories" id="non_show_categories" <?php if(isset($values->non_show_categories) && $values->non_show_categories=='on')echo "checked='checked'"; ?>/> <?php _e( 'Category Pages' , 'responsive-google-adsense' );?> <br />
						<input type="checkbox" name="non_show_archive" id="non_show_archive" <?php if(isset($values->non_show_archive) && $values->non_show_archive=='on')echo "checked='checked'"; ?>/> <?php _e( 'Archive Pages' , 'responsive-google-adsense' );?> <br />
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e( 'Donation' , 'responsive-google-adsense' );?></label>
						</th>
					<td>
						<input type="text" id="donation_percent" name="donation_percent" size="3" value="<?php echo $values->donation_percent;?>"/> %
						<span class="explain"><?php _e( 'The default donation rate is 15%.' , 'responsive-google-adsense' );?></span>
					</td>
				</tr>
			</table>
		</form>
		<p class="submit">
			<span class="submit" id="garr_form_save_loader" style="display:none">
				<img src="/wp-admin/images/wpspin_light-2x.gif" width="16" height="16"> <?php _e( 'Wait' , 'responsive-google-adsense' );?>...
			</span>
			<input class="button-primary" type="button" value="<?php _e( 'Save Changes' , 'responsive-google-adsense' );?>" id="garr_form_save">
		</p>
		</div>
		<div class="card author-box">
			<img src="<?php global $directory; echo $directory; ?>resources/author_ring.png" alt="author" id="garr_author_img" class="author-image" />
			<h2>HI THERE</h2>
			<p>
				I'm <a href="http://www.daniel-klose.com">Daniel</a> and I made this plugin! I hope you enjoy using it and generate some passive income from your website.
			</p>
			<p>
				If you are ever in need of a WordPress Developer you can find me on <a href="https://app.codeable.io/tasks/new?ref=PjT3q&preferredContractor=24408">Codeable.io</a> the leading WordPress Outsourcing Service. Get in touch and we can build something great together!
			</p>
			<p><center>
				<a href="https://app.codeable.io/tasks/new?ref=PjT3q&preferredContractor=24408" class="button-primary" type="button"><?php _e( 'Hire Me' , 'responsive-google-adsense' );?></a> or <a href="http://pay.daniel-klose.com/" class="button-primary" type="button"><?php _e( 'Make A Donation' , 'responsive-google-adsense' );?></a>
			</center></p>
		</div>
	</div><!--block div-->

</div><!--wrap-->

<style>
	.card {display: inline-block;}
	.author-box {display:none; position: fixed; max-width: 255px; margin-left: 30px; text-align: justify;}
	.author-box h2 {margin-top: 50px; text-align: center;}
	@media only screen and (min-width:1120px){
        .author-box {display:initial;}
    }
	.card .author-image {position: absolute; top: -52px; left: 107px;}
	.alert {display:none; padding:2px 5px;border:2px solid #d54e21;background:#F1F1F1;margin-top:3px;line-height:18px !important;}
	.explain {font-weight: normal;	font-size: 11px;	margin-top: 3px;	color:#777; padding-left:5px; display:block}
	.optional {font-weight: normal;	font-size: 11px;	line-height: 12px;	color:#777; padding-left:5px;}
	#uxzBlnDPUAJq {display: none; margin-bottom: 30px; padding: 20px 10px; background: #D30000; text-align: center; font-weight: bold; color: #fff;}
	#uxzBlnDPUAJq h2 {color: #fff;}
</style>
