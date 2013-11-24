<?php

class Tests_R_Last_Updated extends WP_UnitTestCase {

  function setUp() {
    parent::setUp();
    $this->test_object = new R_Last_Updated();
    $this->test_timestamp = 1378361839;
  }

  function test_get_and_set_options() {
    $hypothetical_updated_time = '0123456789';

    // set the option
    $set_result = $this->test_object->set_option( $hypothetical_updated_time );
    $this->assertTrue( $set_result );

    // get the option
    $get_result = $this->test_object->get_option();
    $this->assertEquals( $hypothetical_updated_time, $get_result );
  }

	function test_determining_most_recent_time_of_post() {
    // create and insert a test post
    $args = array(
      'post_status' => 'publish',
      'post_content' => rand_str(),
      'post_title' => rand_str(),
      'post_date' => '2012-10-11 13:10:56',
    );
    $id = wp_insert_post($args);

    // we can't insert a post with the modified date
    // so update the date fields of the test post here
    $date_args = array(
      'ID' => $id,
      'post_modified' => '2013-06-18 20:02:37',
    );
    wp_update_post( $date_args );

    // the modified date should be the most recent
    $post = get_post($id);
    $expected_result = mysql2date( 'U', $post->post_modified );

    // pass the new post to the method
    $actual_result = $this->test_object->determine_most_recent_time_of_post( $id );

    $this->assertEquals( $expected_result, $actual_result );
	}

  function test_is_newer_time_than_db() {
    $time_in_database = '1000';
    $older_time = '500';
    $newer_time = '2000';
    $same_time = '1000';

    // populate the database
    $this->test_object->set_option( $time_in_database );

    // test each of our times
    $older_time_result = $this->test_object->is_newer_time_than_db( $older_time );
    $newer_time_result = $this->test_object->is_newer_time_than_db( $newer_time );
    $same_time_result = $this->test_object->is_newer_time_than_db( $same_time );

    // evaluate the result
    $this->assertFalse( $older_time_result );
    $this->assertTrue( $newer_time_result );
    $this->assertFalse( $same_time_result );
  }

  function test_last_updated_time_display() {
    // populate the database
    $this->test_object->set_option( $this->test_timestamp );

    // the method should return this display given the above time
    // and using the system's UTC date setting
    $expected_default_display = 'Sep 5, 2013 at 6:17 am UTC';

    // test the method
    $actual_default_display = $this->test_object->get_last_updated_time();

    // evaluate the result
    $this->assertEquals( $expected_default_display, $actual_default_display );

    // test and evaluate the method with a custom display parameter
    $expected_custom_display = '5 Sep 2013';
    $actual_custom_display = $this->test_object->get_last_updated_time( 'j M Y' );
    $this->assertEquals( $expected_custom_display, $actual_custom_display );

  }

  function test_echo_default_last_updated_time() {
    $this->test_object->set_option( $this->test_timestamp );
    // if i echo the time without a parameter, i should get this
    $this->expectOutputString( 'Sep 5, 2013 at 6:17 am UTC' );
    // evaluate the method
    $this->test_object->last_updated_time();
  }

  function test_echo_custom_last_updated_time() {
    $this->test_object->set_option( $this->test_timestamp );
    // if i echo the time with a parameter, i should get this
    $this->expectOutputString( '5 Sep 2013' );
    $this->test_object->last_updated_time( 'j M Y' );
  }

  function test_newer_publish_post() {
    // given a time in the database
    $this->test_object->set_option( $this->test_timestamp );

    // and a new post inserted into the database
    $args = array(
      'post_content' => rand_str(),
      'post_title' => rand_str(),
      'post_date' => '2013-09-06 00:00:00',
    );
    $id = wp_insert_post($args);
    // directly inserting a published post doesn't seem to fire `publish_post`
    wp_publish_post( $id );

    // the time in the database should be the timestamp of the new post
    $post = get_post($id);
    $expected_result = mysql2date( 'U', $post->post_date );
    $actual_result = $this->test_object->get_option();

    $this->assertEquals( $expected_result, $actual_result );

    // update the inserted post
    $args = array (
      'ID' => $id,
      'post_content' => rand_str(),
    );
    $id = wp_update_post( $args );

    // the option should now be the modified date
    $post = get_post($id);
    $expected_result = mysql2date( 'U', $post->post_modified );
    $actual_result = $this->test_object->get_option();
    $this->assertEquals( $expected_result, $actual_result );

  }

  function test_older_publish_post() {
    // same as above, but this should not update the option
    $this->test_object->set_option( $this->test_timestamp );

    $args = array(
      'post_content' => rand_str(),
      'post_title' => rand_str(),
      'post_date' => '2011-01-01 00:00:00',
    );
    $id = wp_insert_post($args);
    wp_publish_post( $id );

    $expected_result = $this->test_timestamp;
    $actual_result = $this->test_object->get_option();
    $this->assertEquals( $expected_result, $actual_result );
  }

  function test_overwriting_old_option() {
    // set the option to be the old text version
    $old_option = 'Aug 9, 2013 at 1:44 pm PDT';
    $this->test_object->set_option( $old_option );

    // insert a post into the database so we can query
    $args = array(
      'post_status' => 'publish',
      'post_content' => rand_str(),
      'post_title' => rand_str(),
      'post_date' => '2013-01-01 00:00:00',
    );
    $id = wp_insert_post($args);

    // by the end, we should have a unix timestamp
    $post = get_post( $id );
    $expected_result = mysql2date( 'U', $post->post_date );

    $this->test_object->convert_old_option();
    $actual_result = $this->test_object->get_option();

    $this->assertEquals( $expected_result, $actual_result );
  }

  function test_dont_overwrite_option() {
    $this->test_object->set_option( $this->test_timestamp );
    $this->assertEquals( $this->test_timestamp, $this->test_object->get_option() );
  }

}

