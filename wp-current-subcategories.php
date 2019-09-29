<?php
/**
* Plugin Name: WP Current Subcategories
* Plugin URI: http://www.francescosganga.it/wordpress/plugins/wp-current-subcategories/
* Description: Add a widget to show current subcategories
* Version: 1.0.0
* Author: Francesco Sganga
* Author URI: http://www.francescosganga.it/
**/

function wpb_load_widget() {
	register_widget('sc_widget');
}
add_action('widgets_init', 'wpb_load_widget');
 
class sc_widget extends WP_Widget {
	function __construct() {
		parent::__construct( 
			'wpb_widget', 
			__('WP Current Subcategories', 'sc_widget_domain'),
			array('description' => __('Show current subcategories', 'sc_widget_domain')) 
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