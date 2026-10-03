<?php
/**
 * Pattern content.
 */
return array(
	'title'      => __( 'Tourze Lite Core Home Post Block', 'tourze-lite' ),
	'categories' => array( 'tourze-lite-core' ),
	'content'    => '<!-- wp:group {"tagName":"main","style":{"spacing":{"padding":{"top":"120px","bottom":"120px","left":"15px","right":"15px"}}},"layout":{"type":"constrained","contentSize":"1290px"}} -->
<main class="wp-block-group" style="padding-top:120px;padding-right:15px;padding-bottom:120px;padding-left:15px"><!-- wp:query {"queryId":13,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"metadata":{"categories":["posts"],"patternName":"core/query-medium-posts","name":"Image at left"},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:group {"style":{"spacing":{"blockGap":"40px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:post-template {"style":{"spacing":{"blockGap":"32px"}},"layout":{"type":"grid","columnCount":2}} -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"verticalAlignment":"center","width":"37%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:37%"><!-- wp:post-featured-image {"isLink":true,"width":"","sizeSlug":"full","align":"center","style":{"border":{"radius":"20px"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:post-title {"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-primary"}}},"typography":{"fontSize":"28px","fontStyle":"normal","fontWeight":"600","lineHeight":"1.3"},"spacing":{"margin":{"bottom":"8px"}}},"textColor":"gv-color-primary","fontFamily":"host-grotesk"} /-->

<!-- wp:post-excerpt {"moreText":"Read More","excerptLength":12,"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-accent"},":hover":{"color":{"text":"var:preset|color|gv-color-accent-hover"}}}},"typography":{"fontSize":"18px","fontStyle":"normal","fontWeight":"400","lineHeight":"1.6"}},"textColor":"gv-color-text-primary","fontFamily":"poppins"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-accent"},":hover":{"color":{"text":"var:preset|color|gv-color-accent-hover"}}}}},"textColor":"gv-color-primary"} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->',
	'is_sync' => false,
);
