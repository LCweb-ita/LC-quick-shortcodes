<?php
/**
 * LC Quick Shortcodes v1.0
 * Tiny PHP class emulating WordPress shortcodes engine and supporting few BBcodes
 * 
 *
 * @author Luca Montanari aka LCweb
 * @copyright 2018 Luca Montanari - https://lcweb.it
 *
 * https://github.com/LCweb-ita/LC-quick-shortcodes 
 * Licensed under the MIT license
 */


namespace lcweb\quick_sc;


class lc_quick_shortcodes {
		
	/* (array) shortcodes database containing sc key, defaults and callback function 							
	 
	  array(
		  'key' => array(
			  'defaults'		=> (array), // expected parameters and default values
			  'has_contents'	=> (bool)
			  'callback'		=> anonymous function or function name - parameters array is passed 
		  )
	  )
	*/
	private $sc_db = array(); 
	
	
	
	
	
	
	/**
	 * Register a new shortcode  
	 * 
	 * @param (string) 			$name = shortcode's name
	 * @param (array) 			$defaults = shortcode parameters and their defaults
	 * @param (bool) 			$has_contents = whether shortcode will have contents or not
	 * @param (func|string) 	$callback = callback function name or anonymous function
	 */
	
	public function register($name, $defaults = array(), $has_contents = false, $callback) {
	
		$this->sc_db[ $name ] = array(
			'defaults'		=> $defaults,
			'has_contents'	=> $has_contents,
			'callback'		=> $callback
		);
	}
	
	
	
	
	
	/**
	 * Execute shortcodes in a text string
	 *
	 * @param (string) $txt = text
	 * @param (bool) $bbcodes = whether to execute found BBcodes first (see https://www.bbcode.org/reference.php )
	 *
	 * @return (string) executed text
	 */
	
	public function process($txt, $bbcodes = true) {
		
		if($bbcodes) {
			$txt = $this->bbcodes($txt);
		}
		

		// cycle shortcodes
		foreach($this->sc_db as $sc_name => $sc) {
		
			// find occurrences
			$regexp = ($sc['has_contents']) ? '~\['. $sc_name .'(.*?)\](.*?)\[/'. $sc_name .'\]~s' : '~\['. $sc_name .'(.*?)\]~s';
			preg_match_all($regexp, $txt, $matches);	 
			
			// nothing found - skip
			if(empty($matches[0])) {
				continue;	
			}
			
			// elaborate results and execute for each occurrence
			$managed = $this->manag_matches($matches, $sc['has_contents']);
			
			foreach($managed as $target => $match_data) {
				$replace_with = call_user_func(
									$sc['callback'], 
									$this->parse_atts($match_data['atts'], $sc['defaults']),
									$match_data['contents']
								);
				
				$txt = str_replace($target, $replace_with, $txt);	
			}
		}
		
		return $txt;
	}
	
	
	

	
	/**
	 * Wrap up matches cleaning repeated ones and returning a more useful array 
	 *
	 * @param (array)	$matches = preg_match_all results
	 * @param (bool)	$has_contents = whether we are elaborating a shortcode with contents
	 *
	 * @return (array)	array('to_be_replaced' => array('atts' => raw_atts_string, 'contents' => ''))
	 */
	
	private function manag_matches($matches, $has_contents = false) {
		$result = array();
		
		$matched_count = count($matches[0]); 
		for($a=0; $a < $matched_count; $a++) {
		
			$result[ $matches[0][$a] ] = array(
				'atts' 		=> $matches[1][$a],
				'contents'	=> ($has_contents) ? $matches[2][$a] : ''
			);
		}
		
		return $result;
	}
	
	
	
	
	
	/**
	 * Set up shortcode parameters overriding defaults with found ones
	 *
	 * @param (string) $raw = raw string containing shortcode parameters
	 * @param (array) $defaults = shortcode defaults
	 */
	
	private function parse_atts($raw, $defaults) {
		if(empty($raw)) {
			return $defaults;	
		}
	
		$atts = array();
		$raw_arr = explode(' ', substr($raw, 1));
		
		foreach($raw_arr as $raw_att) {
			$arr = explode('="', substr($raw_att, 0, -1));
			
			if(count($arr) == 2) { 
				$atts[ $arr[0] ] = $arr[1];
			}
		}
	
		return array_merge($defaults, $atts);
	}
	
	
	
	
	
	
	/**
	 * Turns BBcodes into HTML - actually supporting only few of them
	 	(thanks to Afsal Rahim @ http://digitcodes.com/create-simple-php-bbcode-parser-function/ )
	 
	 * @param (string) $txt = text to elaborate
	 */
	
	public function bbcodes($txt) {
		$find = array(
			'~\[b\](.*?)\[/b\]~s',
			'~\[i\](.*?)\[/i\]~s',
			'~\[u\](.*?)\[/u\]~s',
			'~\[code\](.*?)\[/code\]~s',
			'~\[size=(.*?)\](.*?)\[/size\]~s',
			'~\[color=(.*?)\](.*?)\[/color\]~s',
			'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
			'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
			'~\[ul\](.*?)\[/ul\]~s',
			'~\[ol\](.*?)\[/ol\]~s',
			'~\[\*\]~s',
		);

		$replace = array(
			'<b>$1</b>',
			'<i>$1</i>',
			'<span style="text-decoration:underline;">$1</span>',
			'<pre>$1</pre>',
			'<span style="font-size:$1px;">$2</span>',
			'<span style="color:$1;">$2</span>',
			'<a href="$1">$1</a>',
			'<img src="$1" alt="" />',
			'<ul>$1</li></ul>',
			'<ol>$1</li></ol>',
			'<li>'
		);
		
		return preg_replace($find, $replace, $txt);
	}
}