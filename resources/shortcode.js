jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.garr_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('garr_insert_shortcode_ad_off', function() {
                    content =  '[garr_disable_adsense]';
                    tinymce.execCommand('mceInsertContent', false, content);
                });
                ed.addCommand('garr_insert_shortcode_ad_start', function() {
                    content =  '[garr_start_adsense]';
                    tinymce.execCommand('mceInsertContent', false, content);
                });
                ed.addCommand('garr_insert_shortcode_ad_stop', function() {
                    content =  '[garr_stop_adsense]';
                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('garr_button_ad_off', {title : 'Disable AdSense For This Post', cmd : 'garr_insert_shortcode_ad_off', text: 'Disable AdSense', label: 'Disable AdSense for this Post' });
            ed.addButton('garr_button_ad_start', {title : 'Start AdSense From Here', cmd : 'garr_insert_shortcode_ad_start', text: 'Start AdSense', label: 'Start AdSense From Here' });
            ed.addButton('garr_button_ad_stop', {title : 'Stop AdSense From Here', cmd : 'garr_insert_shortcode_ad_stop', text: 'Stop AdSense', label: 'Stop AdSense From Here' });
        },
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('garr_button', tinymce.plugins.garr_plugin);
});
