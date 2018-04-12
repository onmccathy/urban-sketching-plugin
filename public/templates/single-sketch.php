<?php

/* 
 * Template for single sketch post.
 *
 * Notes:
 * 
 * 1. Gets list of Artists using $sketcher->sket_get_sketcher_post_details($post->ID)
 * and displays the names below the header.
 *
 * 2. Displays a Map with a single icon where the sketch was done.
 * 
 * 
 */

get_header(); 



?><div id="prmary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.
                
		while ( have_posts() ) : the_post();
                
                 ?>    
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        
                            <header class="entry-header">
                            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

                            <!-- Get the post's date sketched and an array artist/sketchers names-->

                            <?php $details = Sket_Sketch_Manager_Sketch::sket_get_sketcher_post_details($post->ID);?>
                            <div class ="entry-meta" ><?php printf($details->posted_on);?></div>

                            <!-- Display the artist/sketcher names -->

                            <div class="sket-meta"><span class="title">Artist:</span><p><?php

                            foreach( $details->additional_artists as $sketcher ) {
                                printf(esc_attr($sketcher->post_title).'; '); 
                            }
                            ?></p></div>

                            <!-- Display the date sketched -->
                            
                            <div class="sket-meta"><h3 class="title">Date Sketched :</h3><p><?php printf(esc_attr($details->date_sketched)) ?></p>
                            </header><!-- .entry-header -->

                            <!-- Display the thumbnail and post content-->

                            <?php if ( has_post_thumbnail()&& siteorigin_setting('blog_featured_image')) {
                                the_post_thumbnail();
                            }
                            ?>
                            <div class="sket-entry-content"><?php the_content() ?></div>
                            
                            <!-- Display the post's location / address where the sketch was done. -->
                            <?php $geo_data = Sket_db::sket_get_geo_data_object($post->ID);?>
                            <div class="sket-sketch-address" ><h3 class="title">These sketches were sketched at: </h3><p><?php printf(esc_attr($geo_data->address))?></p></div>
                            <!-- Display the post's Map -->
                            <div id="map" style="clear:both;  height:300px;"></div> 
                          
                    </article> 
                    <?php
		endwhile;
		?>

	</main><!-- .site-main -->

	

</div><!-- .content-area -->


<?php get_footer(); 
