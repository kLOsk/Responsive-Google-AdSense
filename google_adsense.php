<?php
/*
Plugin Name: Responsive Google AdSense
Description: Responsive Google AdSense is a free and open source plugin that automatically injects responsive Google AdSense ads into your page and post content.
Author: Daniel Klose
Version: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://www.daniel-klose.com
Text Domain: responsive-google-adsense
Domain Path: /languages
*/

define("NAME", "Responsive Google AdSense");
define("NAME_", "Responsive-Google-AdSense");

//*************** Admin function ***************
$directory = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

//Set language folder
function responsive_google_adsense_init() {
	load_plugin_textdomain('responsive-google-adsense', false, dirname( plugin_basename( __FILE__ )).'/languages' );
}
add_action( 'init', 'responsive_google_adsense_init' );

//Include admin panel code and JS when options page is generated
function garr_admin() {
	include('google_adsense_admin.php');
}

//Init Admin settings and create options page
function garr_admin_actions() {
	if(!get_option('garr_print_settings')){
		update_option('garr_print_settings','{"ads_per_page":"2","ads_per_post":"2","donation_percent":""}');
	}
	//verify_site_content();
	add_options_page(NAME, NAME, "edit_others_posts", NAME_, "garr_admin");
}
add_action('admin_menu', 'garr_admin_actions');

function garr_enqueue_scripts( $hook ) {
		if ( 'settings_page_Responsive-Google-AdSense' != $hook ) {
				return;
		}
    wp_enqueue_script(
        'garr_javascript',                         // Handle
        plugins_url( '/resources/javascript.js', __FILE__ ),  // Path to file
        array( 'jquery' )                             // Dependencies
    );
		wp_enqueue_script(
        'garr_adsblock_check',                         // Handle
        plugins_url( '/resources/ads.js', __FILE__ )                           // Dependencies
    );
}
add_action( 'admin_enqueue_scripts', 'garr_enqueue_scripts', 2000 );

/* Functions */

// Component block to check site for adult content using WOT API. Limit Api calls to once a day

function multiKeyExists(array $arr, $key) {
    // is in base array?
    if (array_key_exists($key, $arr)) {
        return true;
    }
    // check arrays contained in this array
    foreach ($arr as $element) {
        if (is_array($element)) {
            if (multiKeyExists($element, $key)) {
                return true;
            }
        }
    }
    return false;
}

register_activation_hook(__FILE__, 'garr_activation');
function garr_activation() {
	if(!get_option('garr_site_verified')){
		$url = get_site_url();
		$find = array( 'http://', 'https://' );
		$replace = '';
		$output = str_replace( $find, $replace, $url );

		$response = file_get_contents("http://api.mywot.com/0.4/public_link_json2?hosts=" .$output. "/&key=fee6d7d4926b860bc9f32a4d31c5f6bf9b58e37e");
		$json = json_decode($response, true);

		if (multiKeyExists($json, 501)) {
			update_option('garr_site_verified','safe');
		} else {
			update_option('garr_site_verified','unsafe');
		}
	}

  if (! wp_next_scheduled ( 'garr_daily_site_check' )) {
		wp_schedule_event(time(), 'daily', 'garr_daily_site_verification');
  }
}

add_action('garr_daily_site_verification', 'garr_daily_site_check');
function garr_daily_site_check() {
	$url = get_site_url();
	$find = array( 'http://', 'https://' );
	$replace = '';
	$output = str_replace( $find, $replace, $url );

	$response = file_get_contents("http://api.mywot.com/0.4/public_link_json2?hosts=" .$output. "/&key=fee6d7d4926b860bc9f32a4d31c5f6bf9b58e37e");
	$json = json_decode($response, true);

	if (multiKeyExists($json, 501)) { //501 means safe site
		update_option('garr_site_verified','safe');
	} else {
		update_option('garr_site_verified','unsafe');
	}
}

register_deactivation_hook(__FILE__, 'garr_deactivation');
function garr_deactivation() {
	wp_clear_scheduled_hook('garr_daily_site_verification');
}

function verify_site_content() {
	if( get_option('garr_site_verified') == "safe" || get_site_url() == "http://test-dash2.dev" ){
		return true;
	} else {
		return false;
	}
}

//Creates the ads
function garr_ad_gen_code($widget_custom_ad_unit_id){

	$print_settings = json_decode(get_option('garr_print_settings'));

	$garr_ads_id = get_option('garr_ads_id');
	if(substr($garr_ads_id, 0, 4) == 'pub-'){
		$garr_ads_id = str_replace('pub-', '', $garr_ads_id);
	}

	if(!$widget_custom_ad_unit_id || $widget_custom_ad_unit_id=='' )
		$ad_unit_id = get_option('garr_ad_unit_id');
	else
		$ad_unit_id = $widget_custom_ad_unit_id;

	if(!$garr_ads_id) {
		$flag = verify_site_content();
		if($flag==true){
			$garr_ads_id = '5076556968368457';
			$ad_unit_id = '7014111831';
		}
	}

	//Donation settings
  	$donation = intval($print_settings->donation_percent);
	if($donation=='')$donation=15;
	if($donation){
		$donation_rand = mt_rand(1,100);
		if($donation_rand <= $donation){
			$flag = verify_site_content();
			if($flag==true){
				$garr_ads_id = '5076556968368457';
				$ad_unit_id = '7014111831';
			}
		}
	}

	$retstr = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- Gooogle AdSense for WordPress -->
	<ins class="adsbygoogle"
	     style="display:block; background-color:initial;"
	     data-ad-client="ca-pub-'.$garr_ads_id.'"
	     data-ad-slot="'.$ad_unit_id.'"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>';

		if(!$garr_ads_id)//return empty if theres no adsense id
			$retstr = '';
  return $retstr;
}

//Content Filter to inject ads
$garr_adsused = 0;
function count_widget_ads() {
	$widgets = wp_get_sidebars_widgets();
	$out = implode("&",array_map(function($a) {return implode("~",$a);},$widgets));
	$widgetAds = substr_count($out, 'responsivegoogleadsense');
	return $widgetAds;
}


function garr_the_content($content){
	global $doing_rss;
	if(is_feed() || $doing_rss)
		return $content;
	if(strpos($content, "[garr_disable_adsense]") !== false)
		return $content;

	$print_settings = json_decode(get_option('garr_print_settings'));

	//Don't show on specific pages
	if(	is_home() 		&& isset($values->non_show_home) && $print_settings->non_show_home 		== "on") return $content;
	if(	is_page() 		&& isset($values->non_show_stats) && $print_settings->non_show_stats 		== "on") return $content;
	if(	is_single() 	&& isset($values->non_show_posts) && $print_settings->non_show_posts 		== "on") return $content;
	if(	is_category() 	&& isset($values->non_show_categories) && $print_settings->non_show_categories == "on") return $content;
	if(	is_archive() 	&& isset($values->non_show_archive) && $print_settings->non_show_archive 	== "on") return $content;

	global $garr_adsused; //Has to be outside the function to avoid loop issue

	//Read ads per page/post and create adsArray
	$adsArray = Array();
	$numAds = $print_settings->ads_per_page;
	if ( is_active_widget( false, false,'responsivegoogleadsense' ) && $numAds <= 2 ) {
		$widgetAds = count_widget_ads();
		if ($numAds == 1 && $widgetAds == 2 ) {
			$numAds = $numAds;
		} else {
			$numAds = $numAds - $widgetAds +1;
		}
	}
	for($i=1;$i<=$numAds;$i++){
		$adsArray[]='';
	}
	if(is_single()){
		$adsArray = Array();
		$numAds = $print_settings->ads_per_post;
		if ( is_active_widget( false, false,'responsivegoogleadsense' ) && $numAds <= 2 ) {
			$widgetAds = count_widget_ads();
			if ($numAds == 1 && $widgetAds == 2 ) {
				$numAds = $numAds; //fix issue of showing 1 ad in page when 2 widgets are showing
			} else {
				$numAds = $numAds - $widgetAds +1;
			}
		}
		for($i=1;$i<=$numAds;$i++){
			$adsArray[]='';
		}
	}

	$content_hold = "";//needed in case the next if is false
	$content_end = "";
	// Checks if adsensestart exists in $content
	if(strpos($content, "[garr_start_adsense]") != false){
		if(strpos($content, "[garr_stop_adsense]") != false){
			$content_hold = substr($content, 0, strpos($content, "[garr_start_adsense]"));//returns everything before start adsense
			$content_end = substr($content, strpos($content, "[garr_stop_adsense]"));//returns everything after stop adsense
			$content = substr_replace($content, "", 0, strpos($content, "[garr_start_adsense]"));//makes content empty up to start adsense
			$content = substr_replace($content, "", strpos($content, "[garr_stop_adsense]"));//makes content empty after stop adsense, content now only holds string between start and stop adsense
		}	else {
				$content_hold = substr($content, 0, strpos($content, "[garr_start_adsense]"));//returns everything before start adsense
				$content = substr_replace($content, "", 0, strpos($content, "[garr_start_adsense]"));//makes content empty up to start adsense
			}
	}

	while($garr_adsused < $numAds){
		$poses = array();
		$lastpos = -1;
		$repchar = "<p";
		if(strpos($content, "<p") === false)
		  $repchar = "<br";

		while(strpos($content, $repchar, $lastpos+1) !== false){
		  $lastpos = strpos($content, $repchar, $lastpos+1);
		  $poses[] = $lastpos;
		}

		$half = sizeof($poses);
		$adsperpost = $garr_adsused+1;
		if(!is_single() && !is_page())
		  $half = sizeof($poses)/2;

		while(sizeof($poses) > $half)
		  array_pop($poses);

		$pickme = $poses[rand(0, sizeof($poses)-1)];

		$replacewith = '<div style="text-align: center; margin-top: 15px; margin-bottom: 15px;">';
		$replacewith .= garr_ad_gen_code($adsArray[$garr_adsused])."</div>";

		$content = substr_replace($content, $replacewith.$repchar, $pickme, 2);
		$garr_adsused++;
		if(!is_single() && !is_page())
		  return $content_hold.$content.$content_end;
	}
	return $content_hold.$content.$content_end;

}
add_filter('the_content', 'garr_the_content');

/*Widget*/

/**
 * Responsive Google AdSense Class
 */
class ResponsiveGoogleAdSense extends WP_Widget {
	/** constructor */
  public function __construct() {
		$widget_ops = array(
			'classname' => 'ResponsiveGoogleAdSense',
			'description' => 'Responsive Google AdSense Ad Unit',
		);
		parent::__construct( 'ResponsiveGoogleAdSense', 'Responsive Google AdSense', $widget_ops );
	}

  /** Displays the widget on frontend */
  public function widget($args, $instance) {
		$print_settings = json_decode(get_option('garr_print_settings'));
		$ads_per_page = $print_settings->ads_per_page;
		$ads_per_post = $print_settings->ads_per_post;
    extract($args);
    $custom_ad_unit_id = apply_filters('widget_custom_ad_unit_id', $instance['custom_ad_unit_id']);
		if( (is_single() && $ads_per_post > 2) || (!is_single() && $ads_per_page > 2) || count_widget_ads() > 3 ) {
			return false;
			}
		else {
      echo $before_widget;
			//echo $before_title . $after_title;
			echo garr_ad_gen_code( $custom_ad_unit_id );
			echo $after_widget;
		}
  }

  /** When the widget is updated */
  public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['custom_ad_unit_id'] = strip_tags($new_instance['custom_ad_unit_id']);
	  return $instance;
  }

  /** Widget form in the dashboard */
  public function form($instance) {
    $custom_ad_unit_id = esc_attr($instance['custom_ad_unit_id']);
		$print_settings = json_decode(get_option('garr_print_settings'));
		$ads_per_page = $print_settings->ads_per_page;
		$ads_per_post = $print_settings->ads_per_post;
		if( $ads_per_post > 2 || $ads_per_page > 2 ) {
			echo "<p>"._e('Google only allows 3 ads per page/post','responsive-google-adsense')."</p>";
			echo "<p>"._e('To use this widget change Settings -> Responsive Google AdSense -> Ads per Page/Post to 2','responsive-google-adsense')."</p>";
		}
		else { ?>
		<p>
			<label><?php _e('Custom Ad Unit ID', 'responsive-google-adsense'); ?></label><br/>
			<input type="text" name="<?php echo $this->get_field_name('custom_ad_unit_id'); ?>" id="<?php echo $this->get_field_id('custom_ad_unit_id'); ?>" value="<?php if ($custom_ad_unit_id) echo $custom_ad_unit_id; ?>"/><br/><span style="color:#666;font-size:0.8em;"><?php _e('You can set a dedicated Ad Unit ID for the widget. If you leave it blank, the default Ad Unit will be used!','responsive-google-adsense');?></span>
		</p>
    <?php }
  }
} // Class End

// register ResponsiveGoogleAdSense widget
add_action('widgets_init', function(){
	register_widget( 'ResponsiveGoogleAdSense' );
});
/*Widget End*/

/**
 * Responsive Google AdSense TinyMCE Buttons
 */
//Init process for registering our tinymce button
add_action('init', 'garr_shortcode_button_init');
function garr_shortcode_button_init() {

		 //Abort early if the user will never see TinyMCE
		 if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
					return;

		 //Add a callback to regiser our tinymce plugin
		 add_filter("mce_external_plugins", "garr_register_tinymce_plugin");

		 // Add a callback to add our button to the TinyMCE toolbar
		 add_filter('mce_buttons', 'garr_add_tinymce_button');
}

//This callback registers our tinymce plug-in
function garr_register_tinymce_plugin($plugin_array) {
	 $plugin_array['garr_button'] = plugins_url( '/resources/shortcode.js',__FILE__ );
	 return $plugin_array;
}

//This callback adds our button to the tinymce toolbar
function garr_add_tinymce_button($buttons) {
					 //Add the button ID to the $button array
	 array_push($buttons, "garr_button_ad_off","garr_button_ad_start","garr_button_ad_stop");
	 return $buttons;
}

//Create Shortcodes
function garr_disable_adsense() {
	return '<!--noadsense-->';
}
add_shortcode( 'garr_disable_adsense', 'garr_disable_adsense' );

function garr_start_adsense( $atts ){
	return "<!--adsensestart-->";
}
add_shortcode( 'garr_start_adsense', 'garr_start_adsense' );

function garr_stop_adsense( $atts ){
	return "<!--adsensestop-->";
}
add_shortcode( 'garr_stop_adsense', 'garr_stop_adsense' );

?>
