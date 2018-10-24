# LC-quick-shortcodes
WordPress shortcodes don't need any presentation: they simply are the most used and effective way to turn short texts into complex structures.
Using this tiny PHP class you'll be able to use the same engine also on your project!

As little extra, the class already executes few common __[BBcodes](https://en.wikipedia.org/wiki/BBCode)__ (continue reading to know which ones)

* It is just 5KB big and __doesn't have any dependency__.<br/>
* Supports any server running __PHP 5.4__ and later.<br/>
* Shortcodes can be __nested__ and you can use __HTML__ within.


How to use
---

Include the class in the project and initialize it with its namespace:
	
``` php
include_once('lc_quick_shortcodes.php');
$lcqs = new lcweb\quick_sc\lc_quick_shortcodes;
```
<br/>   
   
### Shortcodes registration
Here are two examples: the first says Hello!, while the second prints a code block.

``` php
/**
 * Register a new shortcode  
 * 
 * @param (string) 			$name = shortcode's name
 * @param (array) 			$defaults = shortcode parameters and their defaults
 * @param (bool) 			$has_contents = whether shortcode will have contents or not
 * @param (func|string) 	$callback = callback function name or anonymous function
 */

$lcqs->register('hello', array(), false, function($atts, $contents) {	
	return '<h1>Hello!</h1>';
});

$lcqs->register('title', array('lang'=>'html'), true, function($atts, $contents) {	
	return '<pre class="language-'. $atts['lang'] .'"><code>'. $contents .'</code></pre>';
});
```
	
Analyzing function parameters:

1. sets shortcode's name. Must not have spaces in it.<br/><br/>
2. defines shortcode parameters and their defaults. In this example we expect a parameter called _lang_ and it has a default value of _html_.<br/><br/>Then using _[code][/code]_ the resulting code will still use _html_, while using _[code lang="php"][/code]_ you will override the default value.<br/><br/>
3. FALSE if there are no contents (first example), TRUE if contents will be used within (second example)<br/><br/>
4. May be a function name triggering a callback or an [anonymous function](http://php.net/manual/en/functions.anonymous.php) (as used in the example).<br/>The function will have two parameters: and contents.<br/><br/>
	1. __*shortcode attributes*__ - an associative array containing every attribute found in the shortcode implementation. It contains also custom ones not declared in shortcode registration (eg. _[hello param1="hey!"]_ ) 
	2. __*contents*__ - this is empty if $has_contents is set to false  


<br/><br/>   
   
### Shortcodes execution
Once everything is properly registered, just let the class execute your string

``` php
/**
 * Execute shortcodes in a text string
 *
 * @param (string) $txt = text
 * @param (bool) $bbcodes = whether to execute found BBcodes first (see https://www.bbcode.org/reference.php )
 *
 * @return (string) executed text
 */

$string = 'Lorem ipsum [code lang="php"]dolor sit amet[/code]';
echo $lcqs->process($string, $bbcodes = true);

/* Resulting string:
 * Lorem ipsum <pre class="language-php"><code>dolor sit amet</code></pre>
 */
```

First parameter is your string, while second ones sets whether to execute also BBcodes or not.

Here's the list of supported BBcodes:

| Example       | Description  |
| ------------- |:-------------:|
| [b] _test_ [/b] | bold text |
| [i] _test_ [/i] | italic text |
| [u] _test_ [/u] | underlined text |
| [code] _test_ [/code] | PRE code block |
| [size=20] _test_ [/size] | sets font size (in pixels) |
| [color=#ff0000] _test_ [/color] | sets text color (hex vlue) |
| [url] _http://mypage.com_ [/url] | creates a link |
| [img] _http://mypage.com/myimage.jpg_ [/img] | creates an image |
| [ul]<br/>[\*] test 1 <br/>[\*] test 2 <br/>[/ul] | unordered list |
| [ol]<br/>[\*] test 1 <br/>[\*] test 2 <br/>[/ol] | ordered list |

<br/><br/>


### Summarizing

``` php
include_once('lc_quick_shortcodes.php');
$lcqs = new lcweb\quick_sc\lc_quick_shortcodes;

$lcqs->register('title', array('lang'=>'html'), true, function($atts, $contents) {	
	return '<pre class="language-'. $atts['lang'] .'"><code>'. $contents .'</code></pre>';
});


$string = 'Lorem ipsum [code lang="php"]dolor sit amet[/code]';
echo $lcqs->process($string, $bbcodes = true);
```

<br/><br/>

* * *

Copyright &copy; Luca Montanari (aka LCweb)