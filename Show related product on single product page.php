add_filter( 'woocommerce_product_related_posts_query', 'bbloomer_related_products_by_same_title', 9999, 3 ); 
 
function bbloomer_related_products_by_same_title( $related_posts, $product_id, $args ) {
   $product = wc_get_product( $product_id );
   $title = $product->get_name();
   $related_posts = get_posts( array(
      'post_type' => 'product',
      'post_status' => 'publish',
      'title' => $title,
      'fields' => 'ids',
      'posts_per_page' => -1,
      'exclude' => array( $product_id ),
   ));
   return $related_posts;
}


///////////////////////////////////////////////////////////////////
// Show related product in single product page
add_filter('woocommerce_related_products', 'add_related_products');
function add_related_products($related_product_ids)
{
	global $post;
 
// get the cats this product is in
$terms = get_the_terms($post->ID, 'product_cat');
 
// if there is only one category jump out.
if (count($terms) === 1) {
	return $args;
}
 
$cats = array();
		// remove anything that is a parent cat
		foreach ($terms as $k => $term) {
			if ($term->parent === 0) {
				unset($terms[$k]);
			} else {
				// build list of terms we do want (children)
				$cats[] = $term->term_id;
			}
}
 
$post_ids = get_posts(array(
	'post_type' => 'product',
	'post_status' => 'publish',
	'value'      => 'instock',
	'orderby'   => 'rand',
	'numberposts' => -1, // get all posts.
	'tax_query' => array(
		array(
			'taxonomy' => 'product_cat',
			'field' => 'term_id',
			'terms' => $cats,
),
// Remove current product
		array(
			'taxonomy' => 'product_cat',
			'field' => 'term_id',
			'terms' => $post->ID,
			'operator' => 'NOT IN',
		),
),
'fields' => 'ids', // Only get post IDs
));
 
return $post_ids;
}