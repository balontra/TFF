<?php
/**
 * The template for displaying full width pages.
 *
 * Template Name: All Products
 *
 * @package storefront
 */

$url = get_template_directory_uri().'/assets/css/all-products.css'; wp_enqueue_style( 'style', $url );

get_header(); ?>

<?php
    $args = array(
         'taxonomy'     => 'product_cat',
         'parent' => 0,
         'hide_empty' => 0,
         'tax_query'             => array(
    	    array(
            'taxonomy'  => 'product_cat',
            'field'     => 'tag_ID',
            'terms'     => array( 99 ),
            'operator'  => 'NOT IN'
          )
          )
    );
    $subcats = get_categories($args);
    echo '<ul class="categories-link-wrapper">';
      echo '<li class="categories-link selected" data-value="all"><a href="">All</a></li>';
      foreach ($subcats as $sc) {
        if ($sc->cat_name != 'NAF') {
            $link = get_term_link( $sc->slug, $sc->taxonomy );
            echo '<li class="categories-link" data-value="'.$sc->slug.'"><a href="'. $link .'">'.$sc->name.'</a></li>';
        }
      }
    echo '</ul>';
?>


<div class="section all-products" style="min-height: 200px;">
    <ul class="products">
        <?php
        foreach ($subcats as $sc) {
            if ($sc->cat_name != 'NAF') {
                echo '<h3 class="section-title">'.$sc->name.'</h3>';
                $args = array( 
                    'post_type' => 'product',
                    'posts_per_page' => 100,
                    'orderby' => 'date',
                    'tax_query' => array(
        			    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'slug',
                        'terms'     => array( $sc->name ),
                        )
                    ) 
                );

                $loop = new WP_Query( $args );
        
                while ( $loop->have_posts() ) : $loop->the_post(); 
                    global $product; 
        
                    wc_get_template_part( 'content', 'product' );
        
                endwhile; 
        
        
                wp_reset_query();
            }
        }
        ?>
    </ul>
</div>

<script>

jQuery(document).ready(function() {
  jQuery(".categories-link-wrapper").on("click","li.categories-link", function(event) { 
     event.preventDefault();
     jQuery(".categories-link.selected").removeClass("selected");
     jQuery(this).addClass("selected");
     jQuery.ajax({
         type: "POST",
         url: "<?php bloginfo('template_directory') ?>/assets/backfiles/all-products-process.php",
         data: {'postdata': jQuery(this).attr('data-value')},
         beforeSend: function() {
            jQuery('.products').html( "<div><img src='<?php bloginfo('template_directory') ?>/assets/images/tff_homepage/loading.gif' style='display:block;margin:auto;'></div>" );
         },
         success: function(result){
           jQuery('.products').html( result );
         }
     });
     return false;
  });
});
</script>

<?php get_footer(); ?>