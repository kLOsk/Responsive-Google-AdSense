jQuery(document).ready(function(){

	jQuery('#garr_form_save').click(function() { //execute saveAll() on click on save button
		saveAll();
		return false;
	});
});
function saveAll(){
	//buttons and loaders
	btn = jQuery('#garr_form_save');
	loader = jQuery('#garr_form_save_loader');
	alert1 = jQuery('#alert1');
	alert2 = jQuery('#alert2');
	btn.hide();
	loader.show();

	//Save AdSenseID and Data ID via Ajax
	jQuery.post("/wp-content/plugins/responsive-google-adsense/ajax.php",{
		action: 'save_garr_ads_general_settings',
		garr_ads_id: jQuery('#garr_ads_id').val(),
		garr_ad_unit_id: jQuery('#garr_ad_unit_id').val()
		} ,
		function(data){
			if(data.STATUS != 'ok'){
				alert('Error saving');
			}
			btn.show();
			loader.hide();
			alert1.hide();
			alert2.hide();
		 }
	, "json");

	//Save AdUnit settings via Ajax
	values_s = jQuery('#garr_form_print_settings').serialize();
	jQuery.post("/wp-content/plugins/responsive-google-adsense/ajax.php",{
		action: 'save_garr_print_settings',
		values: values_s
		} ,
		function(data){
			if(data.STATUS != 'ok'){
				alert('Error saving');
			}
		 }
	, "json");
}
