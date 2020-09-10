<?php
/*
Template Name: ROI Calc Page 2020
*/

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

<div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">



<div id="app">
	<div style="height: calc(100vh - 126px); display: flex; flex-direction: column; justify-content: center; align-items: center">
		<p>Loading...</p>
	</div>
</div>



		</div> <!-- #content-area -->
	</div> <!-- .container -->

<?php endif; ?>

</div> <!-- #main-content -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/inc/fuelcalc_react/src.daf4d335.js"></script>

<?php

get_footer();
