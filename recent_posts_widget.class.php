<?php

class Recent_Posts_Widget extends WP_Widget_Recent_Posts {

	public function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;
        $show_title = isset( $instance['show_title'] ) ? $instance['show_title'] : true;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
        $show_excerpt = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : false;
        $show_thumb = isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : false;
        $thumb_size = empty( $instance['thumb_size'] ) ? 'thumbnail' : $instance['thumb_size'];
        $post_type = empty( $instance['post_type'] ) ? 'post' : $instance['post_type'];
        $show_archive_link = isset( $instance['show_archive_link'] ) ? $instance['show_archive_link'] : false;

		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page' => $number,
            'post_type' => $post_type,
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        ) ) );
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>">
            <?php if ($show_thumb) : ?>
                    <?php the_post_thumbnail(array($thumb_size), array('class' => 'alignleft'));?>
            <?php endif; ?>
            <?php if ($show_title): ?>
                    <span class="entry-title"><?php if ( get_the_title() ) the_title(); elseif (!$show_excerpt) the_ID(); ?></span>
            <?php endif; ?>
            <?php if ($show_excerpt): ?>
                    <span class="entry-summary"><?php if ( get_the_excerpt() ) the_excerpt(); elseif (!$show_title && get_the_title()) the_title(); elseif (!$show_title) the_ID(); ?>
            <?php endif; ?>
                </a>
			<?php if ( $show_date ) : ?>
				<span class="post-date"><?php echo get_the_date(); ?></span>
			<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
        <?php if ($show_archive_link) : ?>
        <a href="<?=$this->get_post_type_archive_link( $post_type ); ?>" class="archive-link"><?php _e('Archive')?> &raquo;</a>
        <?php endif; ?>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_posts', $cache, 'widget');
	}

	public function update( $new_instance, $old_instance ) {
        $instance = parent::update($new_instance, $old_instance);
        
        $instance['show_title'] = (bool) $new_instance['show_title'];
		$instance['show_date'] = (bool) $new_instance['show_date'];
        $instance['show_excerpt'] = (bool) $new_instance['show_excerpt'];
        $instance['show_thumb'] = (bool) $new_instance['show_thumb'];
        $instance['thumb_size'] = strip_tags($new_instance['thumb_size']);
        $instance['post_type'] = strip_tags($new_instance['post_type']);
        $instance['show_archive_link'] = (bool) $new_instance['show_archive_link'];

		return $instance;
	}

	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_title = isset( $instance['show_title'] ) ? (bool) $instance['show_title'] : true;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        $show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
        $show_thumb = isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : false;
        $thumb_size = isset( $instance['thumb_size'] ) ? $instance['thumb_size'] : '';
        $post_type	= esc_attr($instance['post_type']);
        $post_types = get_post_types('', 'objects');
        $show_archive_link = isset( $instance['show_archive_link'] ) ? (bool) $instance['show_archive_link'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_title ); ?> id="<?php echo $this->get_field_id( 'show_title' ); ?>" name="<?php echo $this->get_field_name( 'show_title' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Display post title?' ); ?></label></p>
        
		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_excerpt ); ?> id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Display post excerpt?' ); ?></label></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_thumb ); ?> id="<?php echo $this->get_field_id( 'show_thumb' ); ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_thumb' ); ?>"><?php _e( 'Display thumbnail?' ); ?></label></p>
        
        <p><label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbnail size:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" type="text" value="<?php echo $thumb_size; ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Choose the Post Type to display:'); ?></label> 
        <select name="<?php echo $this->get_field_name('post_type'); ?>" id="<?php echo $this->get_field_id('post_type'); ?>" class="widefat">
            <?php foreach ($post_types as $option) : ?>
                <option value="<?=$option->name?>" id="<?=$option->name?>" <?=$post_type == $option->name ? ' selected="selected"' : ''?>><?=$option->name?></option>
            <?php endforeach; ?>
        </select></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_archive_link ); ?> id="<?php echo $this->get_field_id( 'show_archive_link' ); ?>" name="<?php echo $this->get_field_name( 'show_archive_link' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_archive_link' ); ?>"><?php _e( 'Display archive link?' ); ?></label></p>
<?php
	}
    
    protected function get_post_type_archive_link($post_type) {
        if ($post_type == 'post' && 'page' == get_option( 'show_on_front') && ($page_id = get_option('page_for_posts'))) {
            return get_permalink($page_id);
        }
        return get_post_type_archive_link($post_type);
    }
}

?>
