<?php 
/**
 * Plugin Name: L + R Contact Form DB
 * Plugin URI: https://leftplusright.com
 * Description: A simple contact form built to make our lives easier.
 * Version: 1.0.0
 * Author: Chigozie Orunta
 * Author URI: https://linkedin.com/in/chigozieorunta
 * Text Domain: leftplusright
 * Released under the GNU General Public License (GPL)
 * http://www.gnu.org/licenses/gpl.txt
 */

add_action( 'init', 'lrcf_post_type' );
add_action( 'init', 'lrcf_form_submit' );
add_action( 'add_meta_boxes', 'lrcf_meta_boxes' );
add_action( 'manage_lrcf_posts_custom_column', 'lrcf_column', 10, 2 );
add_filter( 'manage_lrcf_posts_columns', 'lrcf_posts_columns' );
add_filter( 'pre_get_posts' , 'lrcf_posts' );
add_shortcode( 'LRcontactform', 'LRshortCode' );

/*
 * ON FORM SUBMIT
*/

function lrcf_form_submit() {
	global $feedback;
	if(isset($_POST['et_builder_submit_button'])) {
		$contact_name = ucwords($_POST['et_pb_contact_name_0']);
		$contact_company = ucwords($_POST['et_pb_contact_company_0']);
		$contact_phone = $_POST['et_pb_contact_phone_0'];
		$contact_email = strtolower($_POST['et_pb_contact_email_0']);
		$contact_interests = $_POST['et_pb_contact_interests_0'];
		$contact_message = $_POST['et_pb_contact_message_0'];

		$lrcf = array(
			'post_type'		=> 'lrcf',
			'post_title' 	=> $contact_name,
			'post_content'	=> $contact_message,
			'post_status'	=> 'publish',
			'post_author'   => get_current_user_id(),
			'meta_input'   	=> array(
				'lrcf_company' 		=> $contact_company,
				'lrcf_phone'   		=> $contact_phone,
				'lrcf_email' 		=> $contact_email,
				'lrcf_interests'   	=> $contact_interests,
			),
		);

		//Feedback Message
		if(($contact_name != '') && ($contact_message != '') && (($contact_phone != '') || ($contact_email != ''))) {
		 	wp_insert_post( $lrcf );
			$feedback = "Thanks for getting in touch with us. Our team will get back to you as soon as we can.";
		} else {
			$feedback = "Please fill in your name, message and at least your phone or email. Thank you.";
		}
	}
}

/*
 * CUSTOM META BOXES
*/

function lrcf_meta_boxes() {
   add_meta_box('lrcf_contact_email', 'Contact E-mail', 'lrcf_contact_email_html', 'lrcf', 'normal', 'high');
   add_meta_box('lrcf_contact_phone', 'Contact Phone', 'lrcf_contact_phone_html', 'lrcf', 'normal', 'high');
   add_meta_box('lrcf_contact_company', 'Contact Company', 'lrcf_contact_company_html', 'lrcf', 'normal', 'high');
   add_meta_box('lrcf_contact_interests', 'Contact Interests', 'lrcf_contact_interests_html', 'lrcf', 'normal', 'high');
}

function lrcf_contact_email_html() {
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
    ?>
    <input type="text" style="width: 100%;" value="<?= get_post_meta($post->ID, 'lrcf_email', true); ?>">
    <?php
}

function lrcf_contact_company_html() {
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
    ?>
    <input type="text" style="width: 100%;" value="<?= get_post_meta($post->ID, 'lrcf_company', true); ?>">
    <?php
}

function lrcf_contact_phone_html() {
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
    ?>
    <input type="text" style="width: 100%;" value="<?= get_post_meta($post->ID, 'lrcf_phone', true); ?>">
    <?php
}

function lrcf_contact_interests_html() {
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
    ?>
    <input type="text" style="width: 100%;" value="<?= get_post_meta($post->ID, 'lrcf_interests', true); ?>">
    <?php
}

/*
 * REGISTER POST TYPE
*/

function lrcf_post_type() {
    $labels = array(
        'name'                  => _x( 'L + R Contact Form DB', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'LR Form', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'L + R Form DB', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'LR Form', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New LR Form', 'textdomain' ),
        'new_item'              => __( 'New LR Form', 'textdomain' ),
        'edit_item'             => __( 'Edit LR Form', 'textdomain' ),
        'view_item'             => __( 'View LR Form', 'textdomain' ),
        'all_items'             => __( 'All LR Forms', 'textdomain' ),
        'search_items'          => __( 'Search LR Forms', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent LR Forms:', 'textdomain' ),
        'not_found'             => __( 'No LR Form found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No LR Forms found in Trash.', 'textdomain' ),
        'featured_image'        => _x( 'LR Form Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
        'archives'              => _x( 'LR Form archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
        'insert_into_item'      => _x( 'Insert into LR Form', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this LR Form', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
        'filter_items_list'     => _x( 'Filter LR Forms list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
        'items_list_navigation' => _x( 'LR Forms list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
        'items_list'            => _x( 'LR Forms list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'lrform' ),
        'capability_type'    => 'post',
		//'capabilities' 		 => array( 'create_posts' => false ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
		'menu_icon'	 		 => 'dashicons-admin-users',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
    );
 
    register_post_type( 'lrcf', $args );
}

/*
 * SORT BY DATE ON ADMIN AREA
*/

function lrcf_posts($query) {
    if (is_admin()) {
        if (isset($query->query_vars['post_type'])) {
            if ($query->query_vars['post_type'] == 'lrcf') {
                $query->set('orderby', 'date');
                $query->set('order', 'DESC');
            }
        }
    }
}

/*
 * CUSTOM COLUMNS 
*/

function lrcf_posts_columns( $columns ) {
    $new_columns = array();
    foreach($columns as $key => $title) {
    	$new_columns[$key] = $title; 
		if($key=="title") {
			$new_columns['email'] = 'E-mail';
			$new_columns['phone'] = 'Phone Number';
		}
	}
	unset($new_columns['author']);
	unset($new_columns['comments']);
	return $new_columns;
}

function lrcf_column( $column, $post_id ) {
  if ( 'email' === $column ) {
    echo get_post_meta( $post_id, 'lrcf_email', true );
  }
  if ( 'phone' === $column ) {
    echo get_post_meta( $post_id, 'lrcf_phone', true );
  }
}

/*
 * REGISTER SHORT CODE
*/

function LRshortCode() { 
	ob_start();
	global $feedback;
?>		
<div class="et-pb-contact-message" style="margin-bottom: 1.5em;">
	<?= ($feedback) ? $feedback : ''; ?>
</div>
<div class="et_pb_contact">
	<form class="et_pb_contact_form clearfix" method="post" action="<?= $_SERVER['REQUEST_URI']; ?>">
		<p class="et_pb_contact_field et_pb_contact_field_0 et_pb_contact_field_half" data-id="name" data-type="input">
			<label for="et_pb_contact_name_0" class="et_pb_contact_form_label">Name</label>
			<input type="text" id="et_pb_contact_name_0" class="input" value="" name="et_pb_contact_name_0" data-required_mark="required" data-field_type="input" data-original_id="name" placeholder="Name" style="background: #fff;">
		</p>

		<p class="et_pb_contact_field et_pb_contact_field_1 et_pb_contact_field_half et_pb_contact_field_last" data-id="comany" data-type="input">
			<label for="et_pb_contact_company_0" class="et_pb_contact_form_label">Company Name</label>
			<input type="text" id="et_pb_contact_company_0" class="input" value="" name="et_pb_contact_company_0" data-required_mark="required" data-field_type="input" data-original_id="comany" placeholder="Company Name" style="background: #fff;">
		</p>

		<p class="et_pb_contact_field et_pb_contact_field_2 et_pb_contact_field_half" data-id="phone" data-type="input">
			<label for="et_pb_contact_phone_0" class="et_pb_contact_form_label">Phone</label>
			<input type="text" id="et_pb_contact_phone_0" class="input" value="" name="et_pb_contact_phone_0" data-required_mark="required" data-field_type="input" data-original_id="phone" placeholder="Phone" style="background: #fff;">
		</p>

		<p class="et_pb_contact_field et_pb_contact_field_3 et_pb_contact_field_half et_pb_contact_field_last" data-id="email" data-type="email">
			<label for="et_pb_contact_email_0" class="et_pb_contact_form_label">Email Address</label>
			<input type="text" id="et_pb_contact_email_0" class="input" value="" name="et_pb_contact_email_0" data-required_mark="required" data-field_type="email" data-original_id="email" placeholder="Email Address" style="background: #fff;">
		</p>

		<p class="et_pb_contact_field et_pb_contact_field_4 et_pb_contact_field_last" data-id="interests" data-type="select">
			<label for="et_pb_contact_interests_0" class="et_pb_contact_form_label">Interested In</label>
			<select id="et_pb_contact_interests_0" class="et_pb_contact_select input" name="et_pb_contact_interests_0" data-required_mark="required" data-field_type="select" data-original_id="interests" style="background: #fff;">
				<option value="">Interested In</option>
				<option value="Strategy">Strategy</option>
				<option value="Design">Design</option>
				<option value="Development">Development</option>
				<option value="Growth">Growth</option>
				<option value="All of it">All of it</option>
				<option value="Just want to chat">Just want to chat</option>
			</select>
		</p>

		<p class="et_pb_contact_field et_pb_contact_field_5 et_pb_contact_field_last" data-id="message" data-type="text">
			<label for="et_pb_contact_message_0" class="et_pb_contact_form_label">Message</label>
			<textarea name="et_pb_contact_message_0" id="et_pb_contact_message_0" class="et_pb_contact_message input" data-required_mark="required" data-field_type="text" data-original_id="message" placeholder="Message" style="background: #fff;"></textarea>
		</p>
		
		<div class="et_contact_bottom_container">
			<button type="submit" name="et_builder_submit_button" class="et_pb_contact_submit et_pb_button" style="background-color: #ff5f55; color: #FFFFFF; border: none !important;">Send</button>
		</div>
	</form>
</div>
<?php
	return ob_get_clean();
}

?>
