<?php
/*
Plugin Name: Rock-paper-scissors
Version: 0.1-alpha
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: rock-paper-scissors
Domain Path: /languages
*/

function rock_wins( $opponent ) {
    $valid = array( 'scissors', 'paper', 'rock' );
    if ( ! in_array( $opponent, $valid ) )
        return true;

    if ( 'scissors' == $opponent ) {
        return true;
    } else {
        return false;
    }
}
