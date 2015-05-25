jQuery(document).ready(function(jQuery) {
    jQuery('#vgissave').live("click", function() {
        if (jQuery('#vgis-url').val() != '') {
            jQuery('#vgisnote').show();
            vgis_upload(jQuery('#vgis-url').val());
        } else {
            alert('Have an error! Please try again!');
        }
    });
});

function vgis_upload(imgurl) {
    data = {
        action: 'vgis_upload',
        imgurl: imgurl,
        vgis_nonce: vgis_vars.vgis_nonce
    };
    jQuery.post(vgis_vars.ajax_url, data, function(response) {
        if (response != '') {
            vinsert = '';
            if (jQuery('#vgislink').val() == 1) {
                vinsert = '<a href="' + jQuery('#vgis-site').val() + '" title="' + jQuery('#vgis-title').val() + '"';
            }
            if (jQuery('#vgislink').val() == 2) {
                vinsert = '<a href="' + jQuery('#vgis-url').val() + '" title="' + jQuery('#vgis-title').val() + '"';
            }
            if (jQuery('#vgisblank').is(':checked')) {
                vinsert += ' target="_blank"';
            }
            if (jQuery('#vgisnofollow').is(':checked')) {
                vinsert += ' rel="nofollow"';
            }
            if (jQuery('#vgislink').val() != 0) {
                vinsert += '>';
            }
            vinsert += '<img src="' + response + '" width="' + jQuery('#vgis-width').val() + '" height="' + jQuery('#vgis-height').val() + '" title="' + jQuery('#vgis-title').val() + '" alt="' + jQuery('#vgis-title').val() + '"/>';
            if (jQuery('#vgislink').val() != 0) {
                vinsert += '</a>';
            }
            if (!tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
                insertAtCaret('content', vinsert);
            } else {
                tinyMCE.activeEditor.execCommand('mceInsertContent', 0, vinsert);
            }
            jQuery('#vgisnote').hide();
            jQuery.colorbox.close();
        } else {
            jQuery('#vgisnote').hide();
            jQuery('#vgiserror').html('Have an error! Please try again!');
        }
    });
}