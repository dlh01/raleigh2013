<?php

class SampleTest extends WP_UnitTestCase {

  function test_true() { $this->assertTrue(true); }
  function test_false() { $this->assertFalse(false); }

	function test_names() {
	    $expected = "WordPress";
	    $this->assertEquals( $expected, 'Wordpress' );
	}
}

