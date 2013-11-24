<?php

// tests/test-rock-paper-scissors.php
class Test_Rock_Paper_Scissors extends WP_UnitTestCase {

    function setUp() {
        parent::setUp();
    }

    function tearDown() {
        parent::tearDown();
    }

    function test_against_scissors() {
        $actual = rock_wins( 'scissors' );
        $this->assertTrue( $actual );
    }

    function test_against_paper() {
    	$actual = rock_wins( 'paper' );
    	$this->assertFalse( $actual );
    }

    function test_against_banana() {
    	$actual = rock_wins( 'banana' );
    	$this->assertTrue( $actual );
    }
}