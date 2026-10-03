<?php
/**
 * Pattern content.
 */
return array(
	'title'      => __( 'Tourze Lite Core Page Hero', 'tourze-lite' ),
	'categories' => array( 'tourze-lite-core' ),
	'content'    => '<!-- wp:group {"style":{"spacing":{"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0px;margin-bottom:0px"><!-- wp:cover {"url":"' . esc_url( trailingslashit( get_template_directory_uri() ) ) . 'assets/img/water-architecture-bridge-chateau-palace-river-861263-pxhere.com_.webp","id":222,"dimRatio":80,"overlayColor":"gv-color-primary","isUserOverlayColor":true,"minHeight":570,"minHeightUnit":"px","sizeSlug":"large","style":{"spacing":{"padding":{"right":"15px","left":"15px","top":"140px","bottom":"80px"}}},"layout":{"type":"constrained","contentSize":"1290px"}} -->
<div class="wp-block-cover" style="padding-top:140px;padding-right:15px;padding-bottom:80px;padding-left:15px;min-height:570px"><img class="wp-block-cover__image-background wp-image-222 size-large" alt="" src="' . esc_url( trailingslashit( get_template_directory_uri() ) ) . 'assets/img/water-architecture-bridge-chateau-palace-river-861263-pxhere.com_.webp" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-gv-color-primary-background-color has-background-dim-80 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:post-title {"textAlign":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-secondary"}}},"typography":{"fontStyle":"normal","fontWeight":"600","lineHeight":"1.1"}},"textColor":"gv-color-secondary","fontSize":"heading-inner-page","fontFamily":"host-grotesk"} /--></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->',
	'is_sync' => false,
);
