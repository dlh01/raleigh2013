<?php

class R_Last_Updated {

  /* ==========================================================================
     Set up Singleton
     ========================================================================== */

  /**
   * Instance of this class
   */
  protected static $instance = null;

  /**
   * Return an instance of this class
   */
  public static function get_instance() {
    if ( null == self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  /* ==========================================================================
     Plugin-specific functionality
     ========================================================================== */

  /**
   * The last-updated time, stored as a Unix timestamp
   * @var string
   */
  private $last_updated;

  /**
   * The default display of the last-updated time
   */
  private $format = 'M j, Y \a\t g:i a T';

  public $option_name = 'r_last_updated';

  function __construct() {
    $this->convert_old_option();
    $this->last_updated = $this->get_option();
    add_action( 'publish_post', array( $this, 'process_new_post' ), 300 );
  }

  public function set_option( $value ) {
    return update_option( $this->option_name, $value );
  }

  public function get_option() {
    return get_option( $this->option_name );
  }

  public function process_new_post( $post_id ) {
    $new_post_time = $this->determine_most_recent_time_of_post( $post_id );
    if ( $this->is_newer_time_than_db( $new_post_time ) )
      $this->set_option( $new_post_time );
  }

  public function determine_most_recent_time_of_post( $post_id ) {
    $post = get_post( $post_id );
    $post_date = mysql2date( 'U', $post->post_date );
    $modified_date = mysql2date( 'U', $post->post_modified );
    return max( $post_date, $modified_date );
  }

  public function is_newer_time_than_db( $time ) {
    $db_time = $this->get_option();
    if ( $time > $db_time )
      return true;

    return false;
  }

  public function get_last_updated_time( $format = null ) {
    if ( is_null( $format ) )
      $format = $this->format;

    return date_i18n( $format, $this->get_option() );
  }

  public function last_updated_time( $format = null ) {
    if ( is_null( $format ) )
      $format = $this->format;

    echo $this->get_last_updated_time( $format );
  }

  public function convert_old_option() {
    if ( false !== strpos( $this->get_option(), ' at ' ) ) {
      $posts = get_posts();
      $latest = $posts[0];
      $time = $this->determine_most_recent_time_of_post( $latest->ID );
      $this->set_option( $time );
    }
  }

}

R_Last_Updated::get_instance();
