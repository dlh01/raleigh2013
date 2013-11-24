<?php
/*
Plugin Name: My-demo-plugin
Version: 0.1-alpha
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: my-demo-plugin
Domain Path: /languages
*/

function say_hello( $name ) {
    /* fixed comma */
    return "Hello, " . $name . "!";
}
