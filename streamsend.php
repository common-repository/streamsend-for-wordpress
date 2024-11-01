<?php 
/**
Plugin Name: StreamSend for Wordpress
Plugin URI: http://www.seodenver.com/streamsend/
Description: Add the StreamSend signup form to your sidebar and easily update the display settings & convert the form from Javascript to faster-loading HTML.
Version: 1.0
Author: Katz Web Services, Inc.
Author URI: http://www.katzwebservices.com
*/

/*
Copyright 2010 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
	
function kws_streamsend( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'url' => '',
      ), $atts ) );
      
	if(!$url || empty($url)) { return false; }
 	
 	echo kwd_process_streamsend_form($url);
}
add_shortcode('streamsend', 'kws_streamsend');
add_shortcode('StreamSend', 'kws_streamsend');
	
function streamsend_curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function kwd_curl_streamsend($src) {
		$error = false;
		
		if (preg_match('/^https?:\/\/.+/', $src)) {
			$ch = curl_init(); 
			
			curl_setopt ($ch, CURLOPT_URL, $src); 
			if(isset($_POST['commit'])) {
				$vars = http_build_query($_POST);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
			
			$code = curl_exec($ch); 
			curl_close($ch);
			
			if(!$code) {
				$error = true;
				$errormsg = "\t\t\t===\nYour server configuration does not support this plugin.\n\nAsk your host to enable curl()\n\t\t\t===";
			}
		} else {
			$errormsg = 'You did not enter a valid URL for the Forms page.';
			$error = true;
		}
		
		if(!$error) {
			return $code;
		} else {
			return $errormsg;
		}
	}
	
	
	
	function kwd_process_streamsend_form($src, $https = false, $submit = 'Submit', $inputsize = '', $width = '260', $kwd_number = 1) {
		$output = kwd_curl_streamsend($src);

		preg_match('/(?:href\=\")(.+)(?:\"\>)/', $output, $href);
		if(!empty($href[1]) && strpos($href[1], 'thank_you')) {
			$output = kwd_curl_streamsend($href[1]); //header(string string [, bool replace [, int http_response_code]])
		} else {
			$output = str_replace('http://app.streamsend.com/public/z3du/8KJ/subscribe', streamsend_curPageURL(), $output);
		}
		$output = str_replace('"http://www.streamsend.com"', '"http://www.streamsend.com/733.html"', $output);
		return $output;
	}


?>