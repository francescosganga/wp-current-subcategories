<?php
/**
* Plugin Name: WP Current Subcategories
* Plugin URI: http://www.francescosganga.it/wordpress/plugins/wp-current-subcategories/
* Description: Add a widget to show current subcategories
* Version: 1.0.1
* Author: Francesco Sganga
* Author URI: http://www.francescosganga.it/
**/

function wpsubcategories_options_panel(){
	add_menu_page('WP Subcategories', 'WP Subcategories', 'manage_options', 'wpsubcategories-options', 'wpsubcategories_options_settings');
	add_submenu_page('wpsubcategories-options', 'About', 'About', 'manage_options', 'wpsubcategories-option-about', 'wpsubcategories_options_about');
}
add_action('admin_menu', 'wpsubcategories_options_panel');

function wpsubcategories_options_settings(){
	?>
	<div class="wrap">
		<h1><?php print __('WP Current Subcategories', 'wpsubcategories_widget_domain') ?></h1>
		<h2><?php print __('How it works', 'wpsubcategories_widget_domain') ?></h2>
		<?php print __('WP Current Subcategories simply add a widget to show current subcategories only if:', 'wpsubcategories_widget_domain') ?>
		<ul>
			<li><?php print __('User in a category page', 'wpsubcategories_widget_domain') ?></li>
			<li><?php print __('Current category has subcategories', 'wpsubcategories_widget_domain') ?></li>
		</ul>
		<br />
		<?php print __('Go to Widget Areas and add WP Current Subcategories where you want, after you can set options for the widget:', 'wpsubcategories_widget_domain') ?>
		<ul>
			<li><?php print __('<strong>Title</strong> - Choose the Title of Widget Section', 'wpsubcategories_widget_domain') ?></li>
			<li><?php print __('<strong>Prefix</strong> - If checked add parent category name before subcategory name', 'wpsubcategories_widget_domain') ?></li>
		</ul>
	</div>
	<?php
}

function wpsubcategories_options_about(){
	?>
	<h1>About</h1>
	<?php
	$response = wp_remote_get("http://www.francescosganga.it/dev/about.html");
	$body = wp_remote_retrieve_body($response);

	print $body;
}

function wpsubcategories_load_widget() {
	register_widget('wpsubcategories_widget');
}
add_action('widgets_init', 'wpsubcategories_load_widget');
 
class wpsubcategories_widget extends WP_Widget {
	function __construct() {
		parent::__construct( 
			'wpsubcategories_widget', 
			__('WP Current Subcategories', 'wpsubcategories_widget_domain'),
			array('description' => __('Show current subcategories', 'wpsubcategories_widget_domain')) 
		);
	}
 
	public function widget($args, $instance) {
		if(is_category()) {
			$category = get_queried_object();
			$terms = get_term_children($category->term_id, "category");
			if(count($terms) != 0) {
				print $args['before_widget'];

				$title = apply_filters('widget_title', $instance['title']); 
				if (!empty($title))
					print $args['before_title'] . $title . $args['after_title'];
				
				if(isset($instance['prefix']) and $instance['prefix'] == "on")
					$instance['prefix'] = "{$category->name} ";
				else
					$instance['prefix'] = "";
				
				print "<ul>";
				foreach ($terms as $child) {
					$term = get_term_by('id', $child, "category");
					print "<li><a href=\"" . get_term_link($term->name, "category") . "\">{$instance['prefix']}{$term->name}</a></li>";
				}
				print "</ul>";

				print $args['after_widget'];
			}
		}
	}

	public function form($instance) {
		?>
		<p>
			<label for="<?php print $this->get_field_id('title'); ?>">
				<?php _e( 'Title:' ); ?>
			</label>
			<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('prefix'); ?>" name="<?php print $this->get_field_name('prefix'); ?>" type="checkbox"<?php if(esc_attr($instance['prefix'] == "on")) print " checked"; ?>/>
			<label for="<?php print $this->get_field_id('prefix'); ?>">
				<?php _e( 'Prefix' ); ?>
			</label>
		</p>
		<?php
	}
     
	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']): '';
		$instance['prefix'] = (!empty($new_instance['prefix'])) ? strip_tags($new_instance['prefix']): 0;

		return $instance;
	}
}