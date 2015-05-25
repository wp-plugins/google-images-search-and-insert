<?php
/*
  Plugin Name: Google Images Search And Insert
  Plugin URI: http://dunghv.com
  Description: This plugin help you search images on internet (powered by Google Images API) and insert to content or set featured image very quickly.
  Version: 1.0.2
  Author: Baby2j
  Author URI: http://dunghv.com
 */

add_action('media_buttons_context', 'add_vgis_button');
add_action('admin_footer', 'add_inline_popup_content');

function vgis_enqueue($hook) {
    if (('edit.php' != $hook) && ('post-new.php' != $hook) && ('post.php' != $hook))
        return;
    wp_enqueue_script('colorbox', plugin_dir_url(__FILE__) . '/js/jquery.colorbox.js', array('jquery'));
    wp_enqueue_style('colorbox', plugins_url('css/colorbox.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'vgis_enqueue');

function add_vgis_button($context) {
    $context = '<a href="#vgis_popup" id="vgis-btn" class="button add_media" title="Google Image"><span class="wp-media-buttons-icon"></span> Google Image</a><input type="hidden" id="vgis_featured_url" name="vgis_featured_url" value="" />';
    return $context;
}

function add_inline_popup_content() {
    ?>
    <style>
        .vgis-container{
            width: 640px;
            display: inline-block;
            margin-top: 10px;
        }
        .vgis-item{
            position: relative;
            display: inline-block;
            width: 150px;
            height: 150px;
            text-align: center;
            border: 1px solid #ddd;
            float: left;
            margin-right: 3px;
            margin-bottom: 3px;
            padding: 2px;
            background: #fff;
        }
        .vgis-item img{
            max-width: 150px;
            max-height: 150px;
        }
        .vgis-use-image{
            width: 100%;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dedede;
            display: none;
        }
        .vgis-item span{
            position: absolute;
            bottom: 2px;
            right: 2px;
            background: #000;
            padding: 0 4px;
            color: #fff;
            font-size: 10px;
        }
        .vgis-page{
            text-align: center;
        }
        .vgis-item-overlay{width: 150px;height: 150px;background: #000; position: absolute; top: 2px; left: 2px; z-index: 997; opacity:0.7; filter:alpha(opacity=70); display: none}
        .vgis-item-link{display: none; position: absolute; top: 50px; width: 100%; text-align: center; z-index: 998}
        .vgis-item-link a{
            display: inline-block;
            background: #fff;
            padding: 0 10px;
            height: 24px;
            line-height: 24px;
            margin-bottom: 5px;
            text-decoration: none;
            width: 90px;
            font-size: 12px;
        }
        .vgis-item:hover > .vgis-item-overlay{display: block}
        .vgis-item:hover > .vgis-item-link{display: block}
        .vgis-loading{display: inline-block; height: 20px; line-height: 20px; min-width:20px; padding-left: 25px; background: url("<?php echo plugin_dir_url(__FILE__) . '/images/spinner.gif'; ?>") no-repeat;}
    </style>
    <div style='display:none'>
        <div id="vgis_popup" style="width: 640px; height: 600px; padding: 10px; overflow: hidden">
            <select name="vgisimgsz" id="vgisimgsz" style="float:left">
                <option value="">All size</option>
                <option value="icon">icon</option>
                <option value="small">small</option>
                <option value="medium">medium</option>
                <option value="large">large</option>
                <option value="xlarge">xlarge</option>
                <option value="xxlarge">xxlarge</option>
                <option value="huge">huge</option>
            </select>
            <select name="vgisimgtype" id="vgisimgtype" style="float:left">
                <option value="">All type</option>
                <option value="face">face</option>
                <option value="photo">photo</option>
                <option value="clipart">clipart</option>
                <option value="lineart">lineart</option>
            </select>
            <select name="vgisfiletype" id="vgisfiletype" style="float:left">
                <option value="">All file type</option>
                <option value="jpg">jpg</option>
                <option value="png">png</option>
                <option value="gif">gif</option>
                <option value="bmp">bmp</option>
            </select> 
            <select name="vgisimgc" id="vgisimgc" style="float:left">
                <option value="">Colorization</option>
                <option value="gray">gray</option>
                <option value="color">color</option>
            </select> 
            <select name="vgisimgcolor" id="vgisimgcolor" style="float:left">
                <option value="">All color</option>
                <option value="black">black</option>
                <option value="blue">blue</option>
                <option value="brown">brown</option>
                <option value="gray">gray</option>
                <option value="green">green</option>
                <option value="orange">orange</option>
                <option value="pink">pink</option>
                <option value="purple">purple</option>
                <option value="red">red</option>
                <option value="teal">teal</option>
                <option value="white">white</option>
                <option value="yellow">yellow</option>
            </select> 
            <select name="vgissafe" id="vgissafe" style="float:left">
                <option value="">Safe search</option>
                <option value="active">active</option>
                <option value="moderate">moderate</option>
                <option value="off">off</option>
            </select> 
            <div style="width:98%; display: inline-block; margin-top: 5px; height:28px; line-height: 28px;"><span style="float:left; margin-right: 10px;"><input name="vgiscc" id="vgiscc" type="checkbox"/> Only Creative Commons</span> <input type="text" id="vgisinput" name="vgisinput" value="" size="30"/> <input type="button" id="vgissearch" class="button" value="Search"/> <span id="vgisspinner" style="display:none" class="vgis-loading"> </span></div>
            <div id="vgis-container" class="vgis-container"><br/><br/>WARNING: All images from Google Images (http://www.google.com/images) have reserved rights, so don't use images without license! Author of plugin are not liable for any damages arising from its use.</div>
            <div id="vgis-page" class="vgis-page"></div>
            <div id="vgis-use-image" class="vgis-use-image">
                <div class="vgis-item" id="vgis-view" style="margin-right: 20px;"></div>
                Title: <input type="text" id="vgis-title" size="42" value=""><br/><br/>
                Width: <input type="text" id="vgis-width" size="8" value="0"> x Height: <input type="text" id="vgis-height" size="8" value="0"><br/><br/>
                <input type="hidden" id="vgis-url" value="">
                <input type="button" id="vgisinsert" class="button button-primary" value="Insert">
                <a href="http://dunghv.com" title="Only available in full version!" target="_blank"><input type="button" id="vgisave" class="button button-disabled" value="Save & Insert"></a>
                <a href="http://dunghv.com" title="Only available in full version!" target="_blank"><input type="button" id="vgifeatured" class="button button-disabled" value="Set Featured Image"></a>
            </div>
        </div>
    </div>
    <script>
        function insertAtCaret(areaId, text) {
            var txtarea = document.getElementById(areaId);
            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
                    "ff" : (document.selection ? "ie" : false));
            if (br == "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            }
            else if (br == "ff")
                strPos = txtarea.selectionStart;

            var front = (txtarea.value).substring(0, strPos);
            var back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;
            if (br == "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                range.moveStart('character', strPos);
                range.moveEnd('character', 0);
                range.select();
            }
            else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }
            txtarea.scrollTop = scrollPos;
        }
        jQuery("#vgissearch").click(function() {
            vShowImages(0);
        });
        jQuery("#vgis-btn").colorbox({inline: true, width: "670px"});
        jQuery("#vgis-page a").live("click", function() {
            vShowImages(jQuery(this).attr("rel") - 1);
        });
        jQuery("#vgisinsert").live("click", function() {
            if (jQuery('#vgis-url').val() != '') {
                vinsert = '<img src="' + jQuery('#vgis-url').val() + '" width="' + jQuery('#vgis-width').val() + '" height="' + jQuery('#vgis-height').val() + '" title="' + jQuery('#vgis-title').val() + '" alt="' + jQuery('#vgis-title').val() + '"/>';
                if (!tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
                    insertAtCaret('content', vinsert);
                } else {
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, vinsert);
                }
                jQuery.colorbox.close();
            } else {
                alert('Have an error! Please try again!');
            }
        });
        jQuery("#vgisfeatured").live("click", function() {
            vffurl = jQuery('#vgis-url').val();
            jQuery('#vgis_featured_url').val(vffurl);
            jQuery('#postimagediv div.inside img').remove();
            jQuery('#postimagediv div.inside').prepend('<img src="' + vffurl + '" width="270"/>');
            jQuery.colorbox.close();
        });
        jQuery("#remove-post-thumbnail").live("click", function() {
            jQuery('#vgis_featured_url').val('');
        });
        jQuery(".vgis-item-use").live("click", function() {
            jQuery("#vgis-use-image").show();
            jQuery('#vgis-title').val(jQuery(this).attr('vgistitle'));
            jQuery('#vgis-width').val(jQuery(this).attr('vgiswidth'));
            jQuery('#vgis-height').val(jQuery(this).attr('vgisheight'));
            jQuery('#vgis-url').val(jQuery(this).attr('vgisurl'));
            jQuery('#vgis-view').html('<img src="' + jQuery(this).attr('vgistburl') + '"/>');
        });
        function vShowImages(page) {
            if (jQuery("#vgisinput").val() == '') {
                alert('Please enter keyword to search!');
            } else {
                jQuery('#vgisspinner').show();
                jQuery('#vgis-container').html("");
                vstart = page * 8;
                vcc = '';
                if (jQuery('#vgiscc').is(':checked')) {
                    vcc = 'cc_attribute';
                }
                vurl = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&rsz=8&restrict=" + vcc + "&start=" + vstart + "&as_filetype=" + jQuery("#vgisfiletype").val() + "&imgtype=" + jQuery("#vgisimgtype").val() + "&imgsz=" + jQuery("#vgisimgsz").val() + "&imgc=" + jQuery("#vgisimgc").val() + "&safe=" + jQuery("#vgissafe").val() + "&imgcolor=" + jQuery("#vgisimgcolor").val() + "&q=" + jQuery("#vgisinput").val();
                jQuery.ajax({
                    url: vurl,
                    dataType: "jsonp",
                    success: function(data) {
                        if (data.responseDetails === null) {
                            jQuery('#vgisspinner').hide();
                            for (var i = 0; i < data.responseData.results.length; i++) {
                                jQuery('#vgis-container').append('<div class="vgis-item"><div class="vgis-item-link"><a href="' + data.responseData.results[i].url + '" target="_blank" title="View this image in new windows">View</a><a class="vgis-item-use" vgistburl="' + data.responseData.results[i].tbUrl + '" vgisurl="' + data.responseData.results[i].url + '" vgisthumb="' + data.responseData.results[i].tbUrl + '" vgistitle="' + data.responseData.results[i].titleNoFormatting + '" vgiswidth="' + data.responseData.results[i].width + '" vgisheight="' + data.responseData.results[i].height + '" href="#">Use this image</a></div><div class="vgis-item-overlay"></div><img src="' + data.responseData.results[i].tbUrl + '"><span>' + data.responseData.results[i].width + ' x ' + data.responseData.results[i].height + '</span></div> ');
                            }
                            ;
                            var vpages = "About " + data.responseData.cursor.resultCount + " results / Pages: ";
                            for (var j = 1; j < data.responseData.cursor.pages.length + 1; j++) {
                                vpages += '<a href="#" rel="' + j + '" title="Page ' + j + '">' + j + '</a> ';
                            }
                            ;
                            jQuery('#vgis-page').html(vpages);
                        } else {
                            jQuery('#vgisspinner').hide();
                            jQuery('#vgis-container').html('No result! Please try again!');
                            jQuery('#vgis-page').html('');
                        }
                    }
                });
            }
        }
    </script>
    <?php
}
?>