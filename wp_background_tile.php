<?php
/*
Plugin Name: WP Background Tile
Plugin URI: http://www.samburdge.co.uk/plugins/wp-background-tile-plugin/
Description: Insert a tiled background image into your wordpress template
Version: 1.0
Author: Sam Burdge
Author URI: http://www.samburdge.co.uk
*/
/*  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA



*/

$default_bg_img_file = '/wp-content/plugins/wp_background_tile/tiles/tile.png';

//add the javascript
function bgtile_css(){
global $default_bg_img_file;
global $wpdb;
$table_name = $wpdb->prefix . "bgtile";
$tile_url = $wpdb->get_var("SELECT tile_url FROM $table_name;");
if($tile_url){
$bg_img_file = $tile_url;
} else {
$bg_img_file=$default_bg_img_file;
}

echo '
<style type="text/css">
body {background-image: url('.$bg_img_file.');}
</style>
';
}


add_action('wp_head', 'bgtile_css');

$wp_bg_tile_db_version = '1.0';

function wp_bg_tile_install () {
   global $wpdb;
   global $wp_bg_tile_db_version;
   global $default_bg_img_file;

   $table_name = $wpdb->prefix . "bgtile";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  tile_url text NOT NULL,
	  db_version text NOT NULL
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      $insert = "INSERT INTO " . $table_name .
            " (tile_url, db_version) " .
            "VALUES ('" . $wpdb->escape($default_bg_img_file) . "','" . $wpdb->escape($wp_bg_tile_db_version) . "')";



      $results = $wpdb->query( $insert );
 
      add_option("wp_bg_tile_db_version", $wp_bg_tile_db_version);

   }
}

register_activation_hook(__FILE__,'wp_bg_tile_install');





function wp_bg_tile_options_page(){
global $wpdb;

   global $default_bg_img_file;

   $table_name = $wpdb->prefix . "bgtile";

//update options

if($_POST['bg_tile_updated']){
$updated_wpft = '<div class="updated"><p><strong>Options saved.</strong></p></div>';

$insert = "UPDATE " . $table_name . " SET tile_url = '" . $wpdb->escape($_POST['tile_url']) . "'";

      $results = $wpdb->query( $insert );

}

//database queries

$tile_url = $wpdb->get_var("SELECT tile_url FROM $table_name;");

if($tile_url){$tile_val = $tile_url;} else {$tile_val = $default_bg_img_file;}

print '

	<h2>WP Background Tile Options</h2>


<p><b>Paste the url of your background image into the box below:</b></p>

<form method="post" action="">
<table>
<tr><td align="right">
Image URL: </td><td><input type="text" value="'.$tile_val.'" name="tile_url" id="tile_url" /></td></tr>

<tr><td align="right" colspan="2">
<input type="hidden" value="true" name="bg_tile_updated" />
<input type="submit" value="Update" />
</td></tr></table>
</form>


<p>&nbsp;</p>
<h2>Instructions:</h2>
<p>Upload an image and paste it\'s url into the box above. It will be applied to your page as a repeated background tile.</p>


<h3>Hints &amp; Tips:</h3>
<ol>
<li>Generate background stripes using this free online tool - <a href="http://www.stripegenerator.com/" target="_blank">Stripe Generator</a></li>

<li><br />
<form method="post" action="https://www.paypal.com/cgi-bin/webscr">
<input type="hidden" value="_s-xclick" name="cmd"/>
<input type="image" border="0" alt="Make payments with PayPal - it\'s fast, free and secure!" name="submit" src="http://www.samburdge.co.uk/images/samburdge-donate-button-str.jpg"/>
<img width="1" height="1" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" alt=""/>
<input type="hidden" value="-----BEGIN PKCS7-----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-----END PKCS7----- " name="encrypted"/>
</form>

</li>
</ol>



<div style="width: 100%; text-align: left; clear: both;">
<p>WP Background Tile Plugin by <a href="http://www.samburdge.co.uk" target="_blank">Sam Burdge</a> 2009</p>
</div>

</div>';

}

function wp_bg_tile_admin_page(){
	add_submenu_page('options-general.php', 'WP Background Tile', 'WP Background Tile', 5, 'wp_background_tile.php', 'wp_bg_tile_options_page');
}


add_action('admin_menu', 'wp_bg_tile_admin_page');





?>