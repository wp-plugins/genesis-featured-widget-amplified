<?php
/**
 * undocumented
 *
 * @package Genesis
 */

/* Prevent direct access to the plugin */
if (!defined('ABSPATH')) {
	exit(__( "Sorry, you are not allowed to access this page directly.", 'GFWA' ));
}

add_action('widgets_init', create_function('', "register_widget('Genesis_Featured_Widget_Amplified');"));
class Genesis_Featured_Widget_Amplified extends WP_Widget {

	function Genesis_Featured_Widget_Amplified() {
		$widget_ops = array( 'classname' => 'featuredpost', 'description' => __('Displays featured posts with thumbnails', 'genesis') );
		$control_ops = array( 'width' => 505, 'height' => 350, 'id_base' => 'featured-widget-amplified' );
		$this->WP_Widget( 'featured-widget-amplified', __('Genesis - Featured Widget Amplified', 'GFWA'), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		extract($args);

		// defaults
		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
                        'post_type' => 'post',
			'posts_term' => '',
                        'exclude_terms' => '',
                        'exclude_cat' => '',
			'posts_num' => 1,
			'posts_offset' => 0,
			'orderby' => '',
			'order' => '',
                        'meta_key' => '',
                        'paged' => '',
                        'show_paged' => '',
			'show_image' => 0,
			'image_alignment' => '',
			'image_size' => '',
			'show_gravatar' => 0,
			'gravatar_alignment' => '',
			'gravatar_size' => '',
			'show_title' => 0,
			'show_byline' => 0,
			'post_info' => '[post_date] ' . __('By', 'genesis') . ' [post_author_posts_link] [post_comments]',
			'show_content' => 'excerpt',
			'content_limit' => '',
			'more_text' => __('[Read More...]', 'genesis'),
			'extra_num' => '',
			'extra_title' => '',
			'more_from_category' => '',
			'more_from_category_text' => __('More Posts from this Category', 'genesis')
		) );

		echo $before_widget;

			// Set up the author bio
			if (!empty($instance['title']))
				echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;

                        gfwa_before_loop($instance);

                        $term_args = array();
                        if(!empty($instance['posts_term'])){
                            $posts_term = explode(',', $instance['posts_term']);
                            if($posts_term['0']=='category') $posts_term['0'] = 'cat';
                            if($posts_term['0']=='post_tag') $posts_term['0'] = 'tag';
                            $term_args = array ($posts_term['0'] => $posts_term['1'] );
                           
                        }

                        if(!empty($instance['exclude_terms'])) {
                            if(!empty($posts_term['0'])) $taxonomy = $posts_term['0'];
                            else $taxonomy = 'category';
                            $exclude_terms = explode(',', str_replace(' ', '', $instance['exclude_terms'] ) );
                            $term_args[$taxonomy.'__not_in'] =  $exclude_terms;
                        }
                        $page = '';
                        if(!empty($instance['paged'])) $page = get_query_var('paged');
                        
                        
                        $query_args = array_merge($term_args, array('post_type' => $instance['post_type'], 'showposts' => $instance['posts_num'],'offset' => $instance['posts_offset'], 'orderby' => $instance['orderby'], 'order' => $instance['order'], 'meta_key' => $instance['meta_key'], 'paged' => $page));
                        $query_args = apply_filters('gfwa_query_args', $query_args, $instance);
                        //print_r($query_args);
			$featured_posts = new WP_Query($query_args);
			if($featured_posts->have_posts()) : while($featured_posts->have_posts()) : $featured_posts->the_post();

				echo '<div '; post_class(); echo '>';

                                    gfwa_before_post_content($instance);

                                    gfwa_post_content($instance);

                                    gfwa_after_post_content($instance);

				echo '</div><!--end post_class()-->'."\n\n";

			endwhile;

                        if(!empty($instance['show_paged'])) genesis_posts_nav();
                        gfwa_endwhile($instance);

                        endif;

                        gfwa_after_loop($instance);

			// The EXTRA Posts (list)
			if ( !empty( $instance['extra_num'] ) ) :

					if ( !empty($instance['extra_title'] ) )
						echo $before_title . esc_html( $instance['extra_title'] ) . $after_title;

					$offset = intval($instance['posts_num']) + intval($instance['posts_offset']);
					$extra_posts = new WP_Query( array( 'cat' => $instance['posts_cat'], 'showposts' => $instance['extra_num'], 'offset' => $offset ) );

					$listitems = '';
					if ( $extra_posts->have_posts() ) :

						while ( $extra_posts->have_posts() ) :

							$extra_posts->the_post();

                                                        gfwa_list_items($instance);
                                                        $listitems .= sprintf( '<li><a href="%s" title="%s">%s</a></li>', get_permalink(), the_title_attribute('echo=0'), get_the_title() );


						endwhile;

                                                if ( strlen($listitems) > 0 ) {
                                                printf( '<ul>%s</ul>', $listitems );
                                                }
                                                gfwa_print_list_items($instance);

					endif;

			endif;

			if(!empty($instance['more_from_category']) && !empty($instance['posts_cat'])) :

                            gfwa_category_more($instance);
                            echo '<p class="more-from-category"><a href="'.get_category_link($instance['posts_cat']).'" title="'.get_cat_name($instance['posts_cat']).'">'.esc_html($instance['more_from_category_text']).'</a></p>';
			endif;

                            gfwa_after_category_more($instance);

		echo $after_widget;
		wp_reset_query();
	}



	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {

		// ensure value exists
		$instance = wp_parse_args( (array)$instance, array(
			'title' => '',
                        'post_type' => 'post',
			'posts_term' => '',
                        'exclude_terms' => '',
			'posts_num' => 0,
			'posts_offset' => 0,
			'orderby' => '',
			'order' => '',
                        'meta_key' => '',
                        'paged' => '',
                        'show_paged' => '',
                    	'show_image' => 0,
			'image_alignment' => '',
			'image_size' => '',
			'show_gravatar' => 0,
			'gravatar_alignment' => '',
			'gravatar_size' => '',
			'show_title' => 0,
			'show_byline' => 0,
			'post_info' => '[post_date] ' . __('By', 'genesis') . ' [post_author_posts_link] [post_comments]',
			'show_content' => 'excerpt',
			'content_limit' => '',
			'more_text' => __('[Read More...]', 'genesis'),
			'extra_num' => '',
			'extra_title' => '',
			'more_from_category' => '',
			'more_from_category_text' => __('More Posts from this Category', 'genesis')
		) );

?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:99%;" /></p>

	<div style="float: left; width: 250px;">

		<div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px;">

                 <p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type', 'genesis'); ?>:</label>
		 <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
			<?php
                        $args=array(
                          'public'   => true
                        );
                        $output = 'names'; // names or objects
                        $operator = 'and'; // 'and' or 'or'
                        $post_types=get_post_types($args,$output,$operator);
                        $post_types= array_filter($post_types, 'gfwa_exclude_post_types');
                          foreach ($post_types  as $post_type ) {
                            ?><option style="padding-right:10px;" value="<?php echo $post_type ?>" <?php selected($post_type, $instance['post_type']); ?>><?php echo $post_type; ?></option><?php
                          }
                        ?>

		</select></p>

		<p><label for="<?php echo $this->get_field_id('posts_term'); ?>"><?php _e('Taxonomy and Terms', 'genesis'); ?>:</label>
		
                <select id="<?php echo $this->get_field_id('posts_term'); ?>" name="<?php echo $this->get_field_name('posts_term'); ?>">
                                  <option style="padding-right:10px;" value="" <?php selected('', $instance['posts_term']); ?>>All Taxonomies and Terms</option>
                <?php   $taxonomies=get_taxonomies(array('public' => true));
                        $taxonomies= array_filter($taxonomies, 'gfwa_exclude_taxonomies');
                        foreach($taxonomies as $taxonomy) {
                                  ?><option style="margin-left: 5px; padding-right:10px;" value="<?php echo $taxonomy.'[]'; ?>" <?php selected($taxonomy.'[]', $instance['posts_term']); ?>><?php echo $taxonomy; ?></option><?php
                            $terms = get_terms($taxonomy, 'orderby=name&hide_empty=1');
                            foreach( $terms as $term ) {
                                ?><option style="margin-left: 8px; padding-right:10px;" value="<?php echo $taxonomy.','.$term->term_id; ?>" <?php selected($taxonomy.','.$term->term_id, $instance['posts_term']); ?>><?php echo '-'.$term->name; ?></option><?php
                            }
                        }
                        
                ?>
                </select></p>

                <p><label for="<?php echo $this->get_field_id('exclude_terms'); ?>"><?php _e('Exclude Terms by ID<br /> (comma separated list)', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('exclude_terms'); ?>" name="<?php echo $this->get_field_name('exclude_terms'); ?>" value="<?php echo esc_attr($instance['exclude_terms']); ?>" style="width:95%;" /></p>

                <p><label for="<?php echo $this->get_field_id('meta_key'); ?>"><?php _e('Meta Key', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('meta_key'); ?>" name="<?php echo $this->get_field_name('meta_key'); ?>" value="<?php echo esc_attr($instance['meta_key']); ?>" style="width:95%;" /></p>

		<p><label for="<?php echo $this->get_field_id('posts_num'); ?>"><?php _e('Number of Posts to Show', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('posts_num'); ?>" name="<?php echo $this->get_field_name('posts_num'); ?>" value="<?php echo esc_attr( $instance['posts_num'] ); ?>" size="2" /></p>

		<p><label for="<?php echo $this->get_field_id('posts_offset'); ?>"><?php _e('Number of Posts to Offset', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('posts_offset'); ?>" name="<?php echo $this->get_field_name('posts_offset'); ?>" value="<?php echo esc_attr( $instance['posts_offset'] ); ?>" size="2" /></p>

		<p><label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By', 'genesis'); ?>:</label>
		<select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
			<option style="padding-right:10px;" value="date" <?php selected('date', $instance['orderby']); ?>><?php _e('Date', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="title" <?php selected('title', $instance['orderby']); ?>><?php _e('Title', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="parent" <?php selected('parent', $instance['orderby']); ?>><?php _e('Parent', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="ID" <?php selected('ID', $instance['orderby']); ?>><?php _e('ID', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="comment_count" <?php selected('comment_count', $instance['orderby']); ?>><?php _e('Comment Count', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="rand" <?php selected('rand', $instance['orderby']); ?>><?php _e('Random', 'genesis'); ?></option>
                        <option style="padding-right:10px;" value="meta_value" <?php selected('meta_value', $instance['orderby']); ?>><?php _e('Meta Value', 'genesis'); ?></option>
                        <option style="padding-right:10px;" value="meta_value_num" <?php selected('meta_value_num', $instance['orderby']); ?>><?php _e('Numeric Meta Value', 'genesis'); ?></option>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Sort Order', 'genesis'); ?>:</label>
		<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
			<option style="padding-right:10px;" value="DESC" <?php selected('DESC', $instance['order']); ?>><?php _e('Descending (3, 2, 1)', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="ASC" <?php selected('ASC', $instance['order']); ?>><?php _e('Ascending (1, 2, 3)', 'genesis'); ?></option>
		</select></p>

                <p><input id="<?php echo $this->get_field_id('paged'); ?>" type="checkbox" name="<?php echo $this->get_field_name('paged'); ?>" value="1" <?php checked(1, $instance['paged']); ?>/> <label for="<?php echo $this->get_field_id('paged'); ?>"><?php _e('Work with Pagination', 'genesis'); ?></label></p>

                <p><input id="<?php echo $this->get_field_id('show_paged'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_paged'); ?>" value="1" <?php checked(1, $instance['show_paged']); ?>/> <label for="<?php echo $this->get_field_id('show_paged'); ?>"><?php _e('Show Page Navigation', 'genesis'); ?></label></p>

		</div>
		<div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px; margin-top: 10px;">

		<p><input id="<?php echo $this->get_field_id('show_gravatar'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_gravatar'); ?>" value="1" <?php checked(1, $instance['show_gravatar']); ?>/> <label for="<?php echo $this->get_field_id('show_gravatar'); ?>"><?php _e('Show Author Gravatar', 'genesis'); ?></label></p>

		<p><label for="<?php echo $this->get_field_id('gravatar_size'); ?>"><?php _e('Gravatar Size', 'genesis'); ?>:</label>
		<select id="<?php echo $this->get_field_id('gravatar_size'); ?>" name="<?php echo $this->get_field_name('gravatar_size'); ?>">
			<option style="padding-right:10px;" value="45" <?php selected(45, $instance['gravatar_size']); ?>><?php _e('Small (45px)', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="65" <?php selected(65, $instance['gravatar_size']); ?>><?php _e('Medium (65px)', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="85" <?php selected(85, $instance['gravatar_size']); ?>><?php _e('Large (85px)', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="125" <?php selected(105, $instance['gravatar_size']); ?>><?php _e('Extra Large (125px)', 'genesis'); ?></option>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('gravatar_alignment'); ?>"><?php _e('Gravatar Alignment', 'genesis'); ?>:</label>
		<select id="<?php echo $this->get_field_id('gravatar_alignment'); ?>" name="<?php echo $this->get_field_name('gravatar_alignment'); ?>">
			<option style="padding-right:10px;" value="">- <?php _e('None', 'genesis'); ?> -</option>
			<option style="padding-right:10px;" value="alignleft" <?php selected('alignleft', $instance['gravatar_alignment']); ?>><?php _e('Left', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="alignright" <?php selected('alignright', $instance['gravatar_alignment']); ?>><?php _e('Right', 'genesis'); ?></option>
		</select></p>

		</div>
		
            <?php gfwa_form_first_column($instance); ?>

	</div>

	<div style="float: left; width: 250px; margin-left: 10px;">
                <div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px;">

		<p><input id="<?php echo $this->get_field_id('show_image'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_image'); ?>" value="1" <?php checked(1, $instance['show_image']); ?>/> <label for="<?php echo $this->get_field_id('show_image'); ?>"><?php _e('Show Featured Image', 'genesis'); ?></label></p>

		<p><label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size', 'genesis'); ?>:</label>
		<?php $sizes = genesis_get_additional_image_sizes(); ?>
		<select id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
			<option style="padding-right:10px;" value="thumbnail">thumbnail (<?php echo get_option('thumbnail_size_w'); ?>x<?php echo get_option('thumbnail_size_h'); ?>)</option>
			<?php
			foreach((array)$sizes as $name => $size) :
			echo '<option style="padding-right: 10px;" value="'.esc_attr($name).'" '.selected($name, $instance['image_size'], FALSE).'>'.esc_html($name).' ('.$size['width'].'x'.$size['height'].')</option>';
			endforeach;
			?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('image_alignment'); ?>"><?php _e('Image Alignment', 'genesis'); ?>:</label>
		<select id="<?php echo $this->get_field_id('image_alignment'); ?>" name="<?php echo $this->get_field_name('image_alignment'); ?>">
			<option style="padding-right:10px;" value="">- <?php _e('None', 'genesis'); ?> -</option>
			<option style="padding-right:10px;" value="alignleft" <?php selected('alignleft', $instance['image_alignment']); ?>><?php _e('Left', 'genesis'); ?></option>
			<option style="padding-right:10px;" value="alignright" <?php selected('alignright', $instance['image_alignment']); ?>><?php _e('Right', 'genesis'); ?></option>
		</select></p>

		</div>

		<div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px; margin-top: 10px;">

		<p><input id="<?php echo $this->get_field_id('show_title'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_title'); ?>" value="1" <?php checked(1, $instance['show_title']); ?>/> <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Show Post Title', 'genesis'); ?></label></p>

		<p><input id="<?php echo $this->get_field_id('show_byline'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_byline'); ?>" value="1" <?php checked(1, $instance['show_byline']); ?>/> <label for="<?php echo $this->get_field_id('show_byline'); ?>"><?php _e('Show Post Info', 'genesis'); ?></label>

		<input type="text" id="<?php echo $this->get_field_id('post_info'); ?>" name="<?php echo $this->get_field_name('post_info'); ?>" value="<?php echo esc_attr($instance['post_info']); ?>" style="width: 99%;" />

		</p>

		<p><label for="<?php echo $this->get_field_id('show_content'); ?>"><?php _e('Content Type', 'genesis'); ?>:</label>
		<select id="<?php echo $this->get_field_id('show_content'); ?>" name="<?php echo $this->get_field_name('show_content'); ?>">
			<option value="content" <?php selected('content' , $instance['show_content'] ); ?>><?php _e('Show Content', 'genesis'); ?></option>
			<option value="excerpt" <?php selected('excerpt' , $instance['show_content'] ); ?>><?php _e('Show Excerpt', 'genesis'); ?></option>
			<option value="content-limit" <?php selected('content-limit' , $instance['show_content'] ); ?>><?php _e('Show Content Limit', 'genesis'); ?></option>
			<option value="" <?php selected('' , $instance['show_content'] ); ?>><?php _e('No Content', 'genesis'); ?></option>
		</select>

		<br /><label for="<?php echo $this->get_field_id('content_limit'); ?>"><?php _e('Limit content to', 'genesis'); ?></label> <input type="text" id="<?php echo $this->get_field_id('image_alignment'); ?>" name="<?php echo $this->get_field_name('content_limit'); ?>" value="<?php echo esc_attr(intval($instance['content_limit'])); ?>" size="3" /> <?php _e('characters', 'genesis'); ?></p>

		<p><label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('More Text (if applicable)', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" value="<?php echo esc_attr($instance['more_text']); ?>" /></p>

		</div>
		<div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px; margin-top: 10px;">

		<p><?php _e('To display an unordered list of more posts from this category, please fill out the information below', 'genesis'); ?>:</p>

		<p><label for="<?php echo $this->get_field_id('extra_title'); ?>"><?php _e('Title', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('extra_title'); ?>" name="<?php echo $this->get_field_name('extra_title'); ?>" value="<?php echo esc_attr($instance['extra_title']); ?>" style="width:95%;" /></p>

		<p><label for="<?php echo $this->get_field_id('extra_num'); ?>"><?php _e('Number of Posts to Show', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('extra_num'); ?>" name="<?php echo $this->get_field_name('extra_num'); ?>" value="<?php echo esc_attr($instance['extra_num']); ?>" size="2" /></p>

		</div>
		<div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px; margin: 10px 0;">

		<p><input id="<?php echo $this->get_field_id('more_from_category'); ?>" type="checkbox" name="<?php echo $this->get_field_name('more_from_category'); ?>" value="1" <?php checked(1, $instance['more_from_category']); ?>/> <label for="<?php echo $this->get_field_id('more_from_category'); ?>"><?php _e('Show Category Archive Link', 'genesis'); ?></label></p>

		<p><label for="<?php echo $this->get_field_id('more_from_category_text'); ?>"><?php _e('Link Text', 'genesis'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('more_from_category_text'); ?>" name="<?php echo $this->get_field_name('more_from_category_text'); ?>" value="<?php echo esc_attr($instance['more_from_category_text']); ?>" style="width:95%;" /></p>

		</div>
                <?php gfwa_form_second_column($instance); ?>
	</div>

	<?php
	}
        function is_gfwa_show_title(){
            if(!empty($instance['show_title'])) :
                                return true;
                        endif;
}
}

add_action('gfwa_before_post_content', 'gfwa_do_post_image', 10, 1);
function gfwa_do_post_image($instance){
    if(!empty($instance['show_image'])) :
                                printf( '<a href="%s" title="%s" class="%s">%s</a>', get_permalink(), the_title_attribute('echo=0'), esc_attr( $instance['image_alignment'] ), genesis_get_image( array( 'format' => 'html', 'size' => $instance['image_size'] ) ) );
                        endif;
}

add_action('gfwa_before_post_content', 'gfwa_do_gravatar', 10, 1);
function gfwa_do_gravatar($instance){
    if(!empty($instance['show_gravatar'])) :
                                echo '<span class="'.esc_attr($instance['gravatar_alignment']).'">';
                                echo get_avatar( get_the_author_meta('ID'), $instance['gravatar_size'] );
                                echo '</span>';
                        endif;
}

add_action('gfwa_before_post_content', 'gfwa_do_post_title', 10, 1);
function gfwa_do_post_title($instance){
    //if(is_gfwa_show_title())
                                printf( '<h2><a href="%s" title="%s">%s</a></h2>', get_permalink(), the_title_attribute('echo=0'), the_title_attribute('echo=0') );
                        
}

add_action('gfwa_before_post_content', 'gfwa_do_byline', 10, 1);
function gfwa_do_byline($instance){
    if ( !empty( $instance['show_byline'] ) && !empty( $instance['post_info'] ) ) :
                                printf( '<p class="byline post-info">%s</p>', do_shortcode( esc_html( $instance['post_info'] ) ) );
                        endif;
}

add_action('gfwa_post_content', 'gfwa_do_post_content', 10, 1);
function gfwa_do_post_content($instance){
    if(!empty($instance['show_content'])) :

                                if($instance['show_content'] == 'excerpt') :
                                        the_excerpt();
                                elseif($instance['show_content'] == 'content-limit') :
                                        the_content_limit( (int)$instance['content_limit'], esc_html( $instance['more_text'] ) );
                                else :
                                        the_content( esc_html( $instance['more_text'] ) );
                                endif;

                        endif;
}





