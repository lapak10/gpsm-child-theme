<?php get_header(); ?>

<div id="main-content">
		<div class="container container_full">
<div id="content-area" class="clearfix">
<div id="">
<div class="facetwp-template">
		<div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_center pageHeader et_pb_text_0">
		<h1>Search Results fo<span style="letter-spacing: 0.1em;">r:</span> <?php echo esc_attr(get_search_query()); ?><br/><br/></h1>
	</div>
<?php
if ( have_posts() ) :
while ( have_posts() ) : the_post();
$post_format = get_post_format(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>



<?php if ( ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ) ) ) : ?>
<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) : ?>
<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<?php endif; ?>


<?php
et_divi_post_meta();

if ( 'on' !== et_get_option( 'divi_blog_style', 'false' ) || ( is_search() && ( 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true ) ) ) )
truncate_post( 270 );
else
the_content();
?>
<?php endif; ?>

</article> <!-- .et_pb_post -->
<?php
endwhile;

if ( function_exists( 'wp_pagenavi' ) )
wp_pagenavi();
else
get_template_part( 'includes/navigation', 'index' );
else :
get_template_part( 'includes/no-results', 'index' );
endif;
?>
</div>
</div> <!-- #left-area -->

</div> <!-- #content-area -->
</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>