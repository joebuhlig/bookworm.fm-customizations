<?php
/**
 * Plugin Name: Bookworm Plugin
 * Plugin URI: http://bookworm.fm
 * GitHub Plugin URI: https://github.com/joebuhlig/bookworm.fm-customizations
 * Description: This plugin adds some custom abilities for Bookworm.
 * Version: 1.0.0
 * Author: Joe Buhlig
 * Author URI: http://joebuhlig.com
 * License: GPL2
 */

// create two taxonomies, genres and writers for the post type "book"
function create_author_taxonomies() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => _x( 'Authors', 'taxonomy general name' ),
    'singular_name'     => _x( 'Author', 'taxonomy singular name' ),
    'search_items'      => __( 'Search Authors' ),
    'all_items'         => __( 'All Authors' ),
    'parent_item'       => __( 'Parent Author' ),
    'parent_item_colon' => __( 'Parent Author:' ),
    'edit_item'         => __( 'Edit Author' ),
    'update_item'       => __( 'Update Author' ),
    'add_new_item'      => __( 'Add New Author' ),
    'new_item_name'     => __( 'New Author Name' ),
    'menu_name'         => __( 'Authors' ),
  );

  $args = array(
    'hierarchical'      => true,
    'public'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'authors' )
  );

  register_taxonomy( 'authors', array( 'book' ), $args );
}

function create_book_posttype() {
// set up labels
  $labels = array(
    'name' => 'Books',
      'singular_name' => 'Book',
      'add_new' => 'Add New Book',
      'add_new_item' => 'Add New Book',
      'edit_item' => 'Edit Book',
      'new_item' => 'New Book',
      'all_items' => 'All Books',
      'view_item' => 'View Book',
      'search_items' => 'Search Books',
      'not_found' =>  'No Books Found',
      'not_found_in_trash' => 'No Books found in Trash', 
      'parent_item_colon' => '',
      'menu_name' => 'Books',
      );
  register_post_type( 'book',
    array(
  'labels' => $labels,
  'has_archive' => true,
  'public' => true,
  'publicly_queryable' => false,
  'query_var' => true,
  'supports' => array( 'title', 'editor', 'excerpt', 'custom-fields', 'thumbnail','page-attributes' ),
  'taxonomies' => array( 'authors' ), 
  'exclude_from_search' => false,
  'capability_type' => 'post',
  'rewrite' => array( 'slug' => 'books' ),
      'menu_icon' => 'dashicons-book-alt',
    )
  );
}


add_action( 'init', 'create_book_posttype' );
add_action( 'init', 'create_author_taxonomies', 0 );

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'bookworm_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'bookworm_post_meta_boxes_setup' );

/* Meta box setup function. */
function bookworm_post_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'bookworm_add_post_meta_boxes' );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function bookworm_add_post_meta_boxes() {

  add_meta_box(
    'bookworm-post-class',      // Unique ID
    esc_html__( 'Bookworm Settings', 'example' ),    // Title
    'bookworm_post_class_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
  
  add_meta_box(
    'bookworm-book-status',      // Unique ID
    esc_html__( 'Bookworm Settings', 'example' ),    // Title
    'bookworm_book_status_meta_box',   // Callback function
    'book',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
}

/* Display the post meta box. */
function bookworm_post_class_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'bookworm_nonce' ); ?>

  <p>
    <label for="bookworm-mike-rating"><?php _e( "Mike's Rating", 'example' ); ?></label>
    <br />
    <input class="widefat" type="text" name="bookworm-mike-rating" id="bookworm-mike-rating" value="<?php echo esc_attr( get_post_meta( $object->ID, 'bookworm_mike_rating', true ) ); ?>" size="30" />
    <label for="bookworm-joe-rating"><?php _e( "Joe's Rating", 'example' ); ?></label>
    <br />
    <input class="widefat" type="text" name="bookworm-joe-rating" id="bookworm-joe-rating" value="<?php echo esc_attr( get_post_meta( $object->ID, 'bookworm_joe_rating', true ) ); ?>" size="30" />
    <label for="bookworm-amazon-link"><?php _e( "Amazon Link", 'example' ); ?></label>
    <br />
    <input class="widefat" type="text" name="bookworm-amazon-link" id="bookworm-amazon-link" value="<?php echo esc_attr( get_post_meta( $object->ID, 'bookworm_amazon_link', true ) ); ?>" size="30" />
    <label for="bookworm-book-id"><?php _e( "Book", 'example' ); ?></label>
    <br /><?php
    $bookworm_book_id = get_post_meta( $object->ID, 'bookworm_book_id', true);
    $args = array( 'post_type' => 'book', 'posts_per_page' => 500 );
    $books = get_posts( $args ); ?>
    <select class="widefat" name="bookworm-book-id" id="bookworm-book-id">
      <option value="null">Select a Book</option>
      <?php foreach ( $books as $book ) : setup_postdata($book); ?>
          <option id="<?php $bookID = $book->ID; echo $bookID; ?>" value="<?php $bookTitle = $book->post_title; echo $bookTitle; ?>" <?php if($bookID == $bookworm_book_id) echo "selected='selected'";?>><?php echo $bookTitle; ?></option>
      <?php endforeach; ?>
    </select>
  </p>
<?php };

function bookworm_book_status_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'bookworm_nonce' ); ?>
  <label for="bookworm-amazon-link"><?php _e( "Amazon Link", 'example' ); ?></label>
    <br />
    <input class="widefat" type="text" name="bookworm-amazon-link" id="bookworm-amazon-link" value="<?php echo esc_attr( get_post_meta( $object->ID, 'bookworm_amazon_link', true ) ); ?>" size="30" />
  <p>
    <select class="widefat" name="bookworm-book-status" id="bookworm-book-status"><?php
  $bookworm_book_status = get_post_meta( $object->ID, 'bookworm_book_status', true);
  echo '<option value="Planned"';
  if ($bookworm_book_status == "Planned")
    echo " selected";
    
  echo '>Planned</option>';
  echo '<option value="Recommended"';
  if ($bookworm_book_status == "Recommended")
    echo " selected";
    
  echo '>Recommended</option>';
  echo '<option value="Read"';
  if ($bookworm_book_status == "Read")
    echo " selected";
    
  echo '>Read</option>';
   ?>
    </select>
  </p>
<?php };

add_action('save_post', 'bookworm_save_post_class_meta');

/* Save the meta box's post metadata. */
function bookworm_save_post_class_meta() {
  global $post;
  
  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['bookworm_nonce'] ) || !wp_verify_nonce( $_POST['bookworm_nonce'], basename( __FILE__ ) ) )
    return;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post->ID ) )
    return;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_mike_rating_value = ( isset( $_POST['bookworm-mike-rating'] ) ? $_POST['bookworm-mike-rating'] : '' );
  $new_joe_rating_value = ( isset( $_POST['bookworm-joe-rating'] ) ? $_POST['bookworm-joe-rating'] : '' );
  $new_amazon_link_value = ( isset( $_POST['bookworm-amazon-link'] ) ? $_POST['bookworm-amazon-link'] : '' );
  $new_book_id_value = ( isset( $_POST['bookworm-book-id'] ) ? $_POST['bookworm-book-id'] : '' );
  $new_book_status_value = ( isset( $_POST['bookworm-book-status'] ) ? $_POST['bookworm-book-status'] : '' );

  update_bookworm_meta($post->ID, 'bookworm_mike_rating', $new_mike_rating_value);
  update_bookworm_meta($post->ID, 'bookworm_joe_rating', $new_joe_rating_value);
  update_bookworm_meta($post->ID, 'bookworm_amazon_link', $new_amazon_link_value);
  update_bookworm_meta($post->ID, 'bookworm_book_id', $new_book_id_value);
  update_bookworm_meta($post->ID, 'bookworm_book_status', $new_book_status_value);
}

function update_bookworm_meta($post_id, $meta_key, $new_meta_value){
  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

function set_book_post_to_read($ID, $post){
  $bookworm_book_id = get_post_meta( $ID, 'bookworm_book_id', true);
  update_post_meta( $bookworm_book_id, 'bookworm_book_status', "Read");
}

add_action( 'publish_post', 'set_book_post_to_read', 10, 2 );

function ratings_func (){
  global $post;
  $mike_rating = get_post_meta( $post->ID, 'bookworm_mike_rating', true );
  $joe_rating = get_post_meta( $post->ID, 'bookworm_joe_rating', true );
  return "<p>Mike's Rating: " . $mike_rating . "<br />Joe's Rating: " . $joe_rating . "</p>";
}

add_shortcode( 'ratings', 'ratings_func' );

function booklist_func (){
  global $wpdb;
  $booklist = "";
  $args = array( 'post_type' => 'post', 'posts_per_page' => 5000 );
  $loop = new WP_Query( $args );
  while ( $loop->have_posts() ) : $loop->the_post();
    $post_id = get_the_ID();
    $book_id = get_post_meta($post_id, 'bookworm_book_id', true);
    $terms = get_the_terms( $book_id, 'authors' );
    $authors = "";
    $i = 0;
    foreach ( $terms as $term ) {
      if ($i == 0) {
        $authors .= $term->name;
      } else {
        $authors .= ", ";
        $authors .= $term->name;
      }
      $i++;
    }
    $amazon = get_post_meta($post_id, 'bookworm_amazon_link', true);
    $link = get_permalink($post_id);
    $title = get_the_title($post_id);
    $booklist .= '<p><a href="' . $link . '">' . $title . '</a> (<a href="' . $amazon . '">Amazon</a>)</p>';
  endwhile;
  return $booklist;
}

function booklist_planned_func (){
  global $wpdb;
  $booklist = "";
  $args = array( 'post_type' => 'book', 'posts_per_page' => 5000 );
  $loop = new WP_Query( $args );
  while ( $loop->have_posts() ) : $loop->the_post();
    $id = get_the_ID();
    $status = get_post_meta($id, 'bookworm_book_status', true);
    $terms = get_the_terms( $post, 'authors' );
    $authors = "";
    $i = 0;
    foreach ( $terms as $term ) {
      if ($i == 0) {
        $authors .= $term->name;
      } else {
        $authors .= ", ";
        $authors .= $term->name;
      }
      $i++;
    }
    if ($status == "Planned"){
      $amazon = get_post_meta($id, 'bookworm_amazon_link', true);
      $booklist .= '<p><strong>' . get_the_title() . '</strong> by ' . $authors . ' (<a href="' . $amazon . '">Amazon</a>)</p>';
    }
  endwhile;
  return $booklist;
}

function booklist_recommended_func (){
  global $wpdb;
  $args = array( 'post_type' => 'book', 'posts_per_page' => 5000 );
  $loop = new WP_Query( $args );
  while ( $loop->have_posts() ) : $loop->the_post();
    $id = get_the_ID();
    $status = get_post_meta($id, 'bookworm_book_status', true);
    $terms = get_the_terms( $post, 'authors' );
    $authors = "";
    $i = 0;
    foreach ( $terms as $term ) {
      if ($i == 0) {
        $authors .= $term->name;
      } else {
        $authors .= ", ";
        $authors .= $term->name;
      }
      $i++;
    }
    if ($status == "Recommended"){
      $booklist .= '<p><strong>' . get_the_title() . '</strong> by ' . $authors . '</p>';
    }
  endwhile;
  return $booklist;
}

add_shortcode( 'booklist', 'booklist_func' );
add_shortcode( 'booklist-planned', 'booklist_planned_func' );
add_shortcode( 'booklist-recommended', 'booklist_recommended_func' );
