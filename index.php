<?php
/*
  Plugin Name: WP Google Images Search And Insert
  Plugin URI: http://dunghv.com/downloads/wordpress-google-images-search-and-insert
  Description: This plugin help you search images on internet (powered by Google Images API) and insert to content or set as featured image quickly.
  Version: 2.2
  Author: Dunghv
  Author URI: http://dunghv.com
 */

function vgis_load_scripts( $hook ) {
	wp_enqueue_script( 'colorbox', plugin_dir_url( __FILE__ ) . 'js/jquery.colorbox.js', array( 'jquery' ) );
	wp_enqueue_style( 'colorbox', plugin_dir_url( __FILE__ ) . 'css/colorbox.css' );
}

add_action( 'admin_enqueue_scripts', 'vgis_load_scripts' );
add_action( 'wp_enqueue_scripts', 'vgis_load_scripts' );

function vgis_add_button( $editor_id ) {
	echo ' <a href="#vgis_popup" id="vgis-btn" data-editor="' . $editor_id . '" class="vgis-btn button add_media" title="Google Image"><span class="dashicons dashicons-camera vgis-dashicons"></span> Google Image</a><input type="hidden" id="vgis_featured_url" name="vgis_featured_url" value="" /> ';
}

add_action( 'media_buttons', 'vgis_add_button' );

function vgis_save_postdata( $post_id ) {
	if ( ! empty( $_POST['vgis_featured_url'] ) ) {
		if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}
			$vgisfurl = sanitize_text_field( $_POST['vgis_featured_url'] );
			vgis_save_featured( $vgisfurl );
		}
	}
}

add_action( 'save_post', 'vgis_save_postdata' );

function vgis_save_to_media( $vurl, $vtitle, $vfilename ) {
	global $post;
	$vthumbid = 0;
	$filename = pathinfo( $vurl, PATHINFO_FILENAME );
	if ( ( $vfilename == '1' ) && ( $vtitle != '' ) ) {
		$filename = sanitize_title( $vtitle );
	}

	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	@set_time_limit( 300 );
	if ( ! empty( $vurl ) ) {
		$tmp                    = download_url( $vurl );
		$ext                    = pathinfo( $vurl, PATHINFO_EXTENSION );
		$file_array['name']     = $filename . '.' . $ext;
		$file_array['tmp_name'] = $tmp;
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
		}
		$thumbid = media_handle_sideload( $file_array, $post->ID, $desc = null );
		if ( is_wp_error( $thumbid ) ) {
			@unlink( $file_array['tmp_name'] );

			return $thumbid;
		}
	}
	$vthumbid = $thumbid;

	echo wp_get_attachment_url( $vthumbid );
}

function vgis_save_featured( $vurl ) {
	global $post;
	$filename = pathinfo( $vurl, PATHINFO_FILENAME );

	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	@set_time_limit( 300 );
	if ( ! empty( $vurl ) ) {
		$tmp                    = download_url( $vurl );
		$ext                    = pathinfo( $vurl, PATHINFO_EXTENSION );
		$file_array['name']     = $filename . '.' . $ext;
		$file_array['tmp_name'] = $tmp;
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
		}
		$thumbid = media_handle_sideload( $file_array, $post->ID, $desc = null );
		if ( is_wp_error( $thumbid ) ) {
			@unlink( $file_array['tmp_name'] );

			return $thumbid;
		}
	}
	set_post_thumbnail( $post, $thumbid );

}

function vgis_popup_content() {
	?>
	<style>
	#vgis_popup {
		font-size: 13px !important;
		font-family: "Helvetica", helvetica, arial, sans-serif !important;
		color: #111 !important;
	}

	.vgis-dashicons {
		vertical-align: middle !important;
	}

	.vgis-container {
		width: 640px;
		display: inline-block;
		margin-top: 10px;
	}

	.vgis-item {
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
		box-sizing: content-box !important;
	}

	.vgis-input, .vgis-select {
		padding: 0 6px !important;
		border-color: #DDD !important;
		box-shadow: none !important;
		border-radius: 2px !important;
		border: 1px solid #DDD !important;
		background-color: #fff !important;
		color: #32373c !important;
		float: left !important;
		margin-right: 2px !important;
		outline: none !important;
		font-size: 13px !important;
		line-height: 28px !important;
		height: 28px !important;
	}

	.vgis-input {
		width: 100%;
	}

	.vgis-input-small {
		width: 60px !important;
	}

	.vgis-input-normal {
		width: 345px !important;
	}

	.vgis-textarea {
		padding: 6px !important;
		border-color: #DDD !important;
		box-shadow: none !important;
		border-radius: 2px !important;
		border: 1px solid #DDD !important;
		background-color: #fff !important;
		color: #32373c !important;
		float: left !important;
		margin-right: 2px !important;
		outline: none !important;
		font-size: 13px !important;
		width: 100%;
	}

	.vgis-button {
		padding: 0 6px !important;
		border-color: #00a0d2 !important;
		box-shadow: none !important;
		border-radius: 2px !important;
		border: 1px solid #00a0d2 !important;
		background-color: #00a0d2 !important;
		color: #fff !important;
		float: left !important;
		margin-right: 2px !important;
		cursor: pointer !important;
		outline: none !important;
		font-size: 13px !important;
		line-height: 26px !important;
		height: 28px !important;
	}

	.vgis-button-disable {
		padding: 0 6px !important;
		border-color: #dedede !important;
		box-shadow: none !important;
		border-radius: 2px !important;
		border: 1px solid #dedede !important;
		background-color: #dedede !important;
		color: #555 !important;
		float: left !important;
		margin-right: 2px !important;
		cursor: pointer !important;
		outline: none !important;
		font-size: 13px !important;
		line-height: 26px !important;
		height: 28px !important;
	}

	.vgis-table {
		display: table !important;
		border-collapse: separate !important;
		border-spacing: 2px !important;
		border-color: grey !important;
		vertical-align: middle !important;
	}

	.vgis-tr {
		display: table-row !important;
		vertical-align: middle !important;
		border-color: inherit !important;
	}

	.vgis-td {
		display: table-cell !important;
		vertical-align: middle !important;
	}

	.vgis-checkbox {
		border: 1px solid #DDDDDD;
		background: #fff;
		color: #32373c;
		clear: none;
		cursor: pointer;
		display: inline-block;
		line-height: 0;
		height: 16px;
		margin: -4px 4px 0 0;
		outline: 0;
		padding: 0 !important;
		text-align: center;
		vertical-align: middle;
		width: 16px;
		min-width: 16px;
		border-radius: 2px !important;
		-webkit-appearance: none;
		box-shadow: none !important;
		-webkit-transition: .05s border-color ease-in-out;
		transition: .05s border-color ease-in-out;
	}

	.vgis-button:hover {
		opacity: 0.7;
	}

	.vgis-item img {
		max-width: 150px;
		max-height: 150px;
	}

	.vgis-use-image {
		width: 100%;
		display: none;
	}

	.vgis-item span {
		position: absolute;
		bottom: 2px;
		right: 2px;
		padding: 0 4px;
		color: #fff;
		font-size: 10px;
		background: rgba(0, 0, 0, 0.65);
		z-index: 10;
	}

	.vgis-page {
		text-align: center;
	}

	.vgis-item-overlay {
		width: 150px;
		height: 150px;
		background: #000;
		position: absolute;
		top: 2px;
		left: 2px;
		z-index: 997;
		opacity: 0.7;
		filter: alpha(opacity=70);
		display: none
	}

	.vgis-item-link {
		display: none;
		position: absolute;
		top: 50px;
		width: 150px;
		text-align: center;
		z-index: 998
	}

	.vgis-item-link a {
		display: inline-block;
		background: #fff;
		padding: 0 10px;
		height: 24px;
		line-height: 24px;
		margin-bottom: 5px;
		text-decoration: none;
		width: 120px;
		font-size: 12px;
		outline: none !important;
	}

	p.vgis-p {
		margin: 0 0 5px 0
	}

	.vgis-item:hover > .vgis-item-overlay {
		display: block
	}

	.vgis-item:hover > .vgis-item-link {
		display: block
	}

	.vgis-item-single {
		width: 100%;
		height: auto;
		text-align: center;
	}

	.vgis-loading {
		display: inline-block;
		height: 20px;
		line-height: 20px;
		min-width: 20px;
		padding-left: 25px;
		background: url("<?php echo plugin_dir_url(__FILE__) . '/images/loading.gif'; ?>") no-repeat;
	}
	</style>
	<div style='display:none'>
		<div id="vgis_popup" style="width: 950px; height: 440px; position: relative; overflow: hidden">
			<div style="width: 640px;height: 420px; float: left; padding: 10px 0 10px 10px;">
				<select name="vgisimgsz" id="vgisimgsz" class="vgis-select">
					<option value="">All size</option>
					<option value="icon">icon</option>
					<option value="small">small</option>
					<option value="medium">medium</option>
					<option value="large">large</option>
					<option value="xlarge">xlarge</option>
					<option value="xxlarge">xxlarge</option>
					<option value="huge">huge</option>
				</select>
				<select name="vgisimgtype" id="vgisimgtype" class="vgis-select">
					<option value="">All type</option>
					<option value="face">face</option>
					<option value="photo">photo</option>
					<option value="clipart">clipart</option>
					<option value="lineart">lineart</option>
				</select>
				<select name="vgisfiletype" id="vgisfiletype" class="vgis-select">
					<option value="">All file type</option>
					<option value="jpg">jpg</option>
					<option value="png">png</option>
					<option value="gif">gif</option>
					<option value="bmp">bmp</option>
				</select>
				<select name="vgisimgc" id="vgisimgc" class="vgis-select">
					<option value="">Colorization</option>
					<option value="gray">gray</option>
					<option value="color">color</option>
				</select>
				<select name="vgisimgcolor" id="vgisimgcolor" class="vgis-select">
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
				<select name="vgissafe" id="vgissafe" class="vgis-select">
					<option value="">Safe search</option>
					<option value="active">active</option>
					<option value="moderate">moderate</option>
					<option value="off">off</option>
				</select>

				<div style="width:98%; display: inline-block; margin-top: 5px; height:28px; line-height: 28px;"><span
						style="float:left; margin-right: 10px;"><input name="vgiscc" id="vgiscc" type="checkbox"
				                                                       class="vgis-checkbox"/> Only Creative Commons</span>
					<input type="text" id="vgisinput" name="vgisinput" value=""
					       class="vgis-input vgis-input-normal" placeholder="keyword"/>
					<input type="button" id="vgissearch" class="vgis-button" value="Search"/>
					<span id="vgisspinner" style="display:none" class="vgis-loading"> </span></div>
				<div id="vgis-container" class="vgis-container"><br/><br/>WARNING: All images from Google Images
					(http://www.google.com/images) have reserved rights, so don't use images without license! Author of
					plugin are not liable for any damages arising from its use.
				</div>
				<div id="vgis-page" class="vgis-page"></div>
			</div>
			<div
				style="width: 274px; height: 420px; position: absolute; top: 0; right: 0; padding: 10px; border-left: 1px solid #ddd;background: #fcfcfc; box-sizing: content-box !important;">
				<div id="vgis-use-image" class="vgis-use-image">
					<div class="vgis-right" style="height: 366px; overflow-y: auto; overflow-x: hidden">
						<table class="vgis-table">
							<tr class="vgis-tr">
								<td colspan="2" class="vgis-td">
									<div class="vgis-item-single" id="vgis-view" style="margin-right: 20px;"></div>
								</td>
							</tr>
							<tr class="vgis-tr">
								<td class="vgis-td">Title</td>
								<td class="vgis-td"><input type="text" id="vgis-title" value="" class="vgis-input"
								                           placeholder="title"/>
								</td>
							</tr>
							<tr>
								<td class="vgis-td">Caption</td>
								<td class="vgis-td"><textarea id="vgis-caption" name="vgis-caption"
								                              class="vgis-textarea"></textarea>
								</td>
							</tr>
							<tr>
								<td class="vgis-td">File name</td>
								<td class="vgis-td">
									<select name="vgis-filename" id="vgis-filename" class="vgis-select">
										<option value="0">Keep original file name</option>
										<option value="1">Generate from title</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="vgis-td">Size</td>
								<td class="vgis-td"><input type="text" id="vgis-width" value="0"
								                           class="vgis-input vgis-input-small"
								                           placeholder="width"/>
									<input
										type="text" id="vgis-height" value="0" class="vgis-input vgis-input-small"
										placeholder="height"/>
								</td>
							</tr>
							<tr>
								<td class="vgis-td">Alignment</td>
								<td class="vgis-td">
									<select name="vgisalign" id="vgisalign" class="vgis-select">
										<option value="alignnone">None</option>
										<option value="alignleft">Left</option>
										<option value="alignright">Right</option>
										<option value="aligncenter">Center</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="vgis-td">Link to</td>
								<td class="vgis-td">
									<select name="vgislink" id="vgislink" class="vgis-select">
										<option value="0">None</option>
										<option value="1">Original site</option>
										<option value="2">Original image</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="vgis-td">&nbsp;</td>
								<td class="vgis-td"><input name="vgisblank" id="vgisblank" type="checkbox"
								                           class="vgis-checkbox"/> Open
									new
									windows
								</td>
							</tr>
							<tr>
								<td class="vgis-td">&nbsp;</td>
								<td class="vgis-td"><input name="vgisnofollow" id="vgisnofollow" type="checkbox"
								                           class="vgis-checkbox"/>
									Rel
									nofollow
								</td>
							</tr>
						</table>
					</div>
					<p class="vgis-p" style="margin-top: 20px;">
						<input type="hidden" id="vgis-site" value=""/>
						<input type="hidden" id="vgis-url" value=""/>
						<input type="hidden" id="vgis-eid" value=""/>
						<input type="button" id="vgisinsert" class="vgis-button" value="Insert"/>
						<input type="button" id="vgissave" class="vgis-button-disable" value="Save & Insert"
						       onclick="javascript:alert('This feature is only available in Premium Version\nBuy it on http://dunghv.com');"/>
						<input type="button" id="vgisfeatured" class="vgis-button" value="Set Featured"/>
					</p>

					<div style="display:inline-block">
						<span class="vgis-loading" id="vgisnote"
						      style="margin-top: 4px; display:none">Saving image to Media Library...</span>
						<span id="vgiserror"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		function vgis_insertatcaret(areaId, text) {
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
		jQuery("#vgissearch").click(function () {
			vgis_showimages(0);
		});
		jQuery('.vgis-btn').live('click', function () {
			eid = jQuery(this).attr('data-editor');
			jQuery('#vgis-eid').val(eid)
		});
		jQuery(".vgis-btn").colorbox({inline: true, scrolling: false, fixed: true, width: "664px", height: "465px"});
		jQuery("#vgis-page a").live("click", function () {
			vgis_showimages(jQuery(this).attr("rel") - 1);
		});
		jQuery("#vgisinsert").live("click", function () {
			if (jQuery('#vgis-url').val() != '') {
				vinsert = '';
				valign = '';
				valign2 = '';
				eid = jQuery('#vgis-eid').val();
				if (jQuery('#vgisalign').val() != '') {
					valign = ' align="' + jQuery('#vgisalign').val() + '"';
					valign2 = ' class="' + jQuery('#vgisalign').val() + '"';
				}
				if (jQuery('textarea#vgis-caption').val() != '') {
					vinsert = '[caption id="" ' + valign + ']';
					console.log(vinsert);
				}
				if (jQuery('#vgislink').val() == 1) {
					vinsert += '<a href="' + jQuery('#vgis-site').val() + '" title="' + jQuery('#vgis-title').val() + '"';
				}
				if (jQuery('#vgislink').val() == 2) {
					vinsert += '<a href="' + jQuery('#vgis-url').val() + '" title="' + jQuery('#vgis-title').val() + '"';
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
				vinsert += '<img ' + valign2 + ' src="' + jQuery('#vgis-url').val() + '" width="' + jQuery('#vgis-width').val() + '" height="' + jQuery('#vgis-height').val() + '" title="' + jQuery('#vgis-title').val() + '" alt="' + jQuery('#vgis-title').val() + '"/>';
				if (jQuery('#vgislink').val() != 0) {
					vinsert += '</a>';
				}
				if (jQuery('textarea#vgis-caption').val() != '') {
					vinsert += ' ' + jQuery('textarea#vgis-caption').val() + '[/caption]';
				}
				if (!tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
					vgis_insertatcaret(eid, vinsert);
				} else {
					tinyMCE.activeEditor.execCommand('mceInsertContent', 0, vinsert);
				}
				jQuery.colorbox.close();
			} else {
				alert('Have an error! Please try again!');
			}
		});
		jQuery("#vgisfeatured").live("click", function () {
			vffurl = jQuery('#vgis-url').val();
			jQuery('#vgis_featured_url').val(vffurl);
			jQuery('#postimagediv div.inside img').remove();
			jQuery('#postimagediv div.inside').prepend('<img src="' + vffurl + '" width="270"/>');
			jQuery.colorbox.close();
		});
		jQuery("#remove-post-thumbnail").live("click", function () {
			jQuery('#vgis_featured_url').val('');
		});
		jQuery(".vgis-item-use").live("click", function () {
			jQuery.colorbox.resize({width: "960px", height: "465px"});
			jQuery("#vgis-use-image").show();
			jQuery('#vgis-title').val(jQuery(this).attr('vgistitle'));
			jQuery('#vgis-caption').val('');
			jQuery('#vgis-width').val(jQuery(this).attr('vgiswidth'));
			jQuery('#vgis-height').val(jQuery(this).attr('vgisheight'));
			jQuery('#vgis-site').val(jQuery(this).attr('vgissite'));
			jQuery('#vgis-url').val(jQuery(this).attr('vgisurl'));
			jQuery('#vgis-view').html('<img src="' + jQuery(this).attr('vgistburl') + '"/>');
			jQuery('#vgiserror').html('');
		});
		function vgis_showimages(page) {
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
					success: function (data) {
						if (data.responseDetails === null) {
							jQuery('#vgisspinner').hide();
							for (var i = 0; i < data.responseData.results.length; i++) {
								ext = data.responseData.results[i].url.split('.').pop().toUpperCase().substring(0, 4);
								jQuery('#vgis-container').append('<div class="vgis-item"><div class="vgis-item-link"><a href="' + data.responseData.results[i].url + '" target="_blank" title="View this image in new windows">View</a><a class="vgis-item-use" vgistburl="' + data.responseData.results[i].tbUrl + '" vgissite="' + data.responseData.results[i].originalContextUrl + '" vgisurl="' + data.responseData.results[i].url + '" vgisthumb="' + data.responseData.results[i].tbUrl + '" vgistitle="' + data.responseData.results[i].titleNoFormatting + '" vgiswidth="' + data.responseData.results[i].width + '" vgisheight="' + data.responseData.results[i].height + '" href="javascript: void (0);">Use this image</a></div><div class="vgis-item-overlay"></div><img src="' + data.responseData.results[i].tbUrl + '"><span>' +
									ext + ' | ' + data.responseData.results[i].width + 'x' + data.responseData.results[i].height + '</span></div>'
								)
								;
							}
							;
							var vpages = "About " + data.responseData.cursor.resultCount + " results / Pages: ";
							for (var j = 1; j < data.responseData.cursor.pages.length + 1; j++) {
								vpages += '<a href="javascript: void (0);" rel="' + j + '" title="Page ' + j + '">' + j + '</a> ';
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

add_action( 'admin_footer', 'vgis_popup_content' );
add_action( 'wp_footer', 'vgis_popup_content', 100 );
?>