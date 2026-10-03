<?php
/**
 * Pattern content.
 */
return array(
	'title'      => __( 'Tourze Lite Core Single Content', 'tourze-lite' ),
	'categories' => array( 'tourze-lite-core' ),
	'content'    => '<!-- wp:group {"tagName":"main","style":{"spacing":{"padding":{"top":"120px","bottom":"120px","left":"15px","right":"15px"}}},"layout":{"type":"constrained","contentSize":"825px"}} -->
<main class="wp-block-group" style="padding-top:120px;padding-right:15px;padding-bottom:120px;padding-left:15px"><!-- wp:post-content {"tagName":"article"} /-->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"40px"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:40px"><!-- wp:group {"style":{"spacing":{"padding":{"top":"10px","bottom":"10px","left":"20px","right":"20px"}},"border":{"radius":{"topLeft":"50px","topRight":"50px","bottomLeft":"50px","bottomRight":"50px"},"width":"1px"}},"borderColor":"gv-color-accent","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-gv-color-accent-border-color" style="border-width:1px;border-top-left-radius:50px;border-top-right-radius:50px;border-bottom-left-radius:50px;border-bottom-right-radius:50px;padding-top:10px;padding-right:20px;padding-bottom:10px;padding-left:20px"><!-- wp:post-terms {"term":"post_tag","textAlign":"center","separator":" | ","style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-accent"},":hover":{"color":{"text":"var:preset|color|gv-color-accent-hover"}}}},"spacing":{"padding":{"right":"0","left":"0"}},"typography":{"fontStyle":"normal","fontWeight":"600","lineHeight":"1"}},"textColor":"gv-color-accent","fontSize":"text-small","fontFamily":"host-grotesk"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"40px","bottom":"40px"}}},"backgroundColor":"gv-color-border-input"} -->
<hr class="wp-block-separator has-text-color has-gv-color-border-input-color has-alpha-channel-opacity has-gv-color-border-input-background-color has-background" style="margin-top:40px;margin-bottom:40px"/>
<!-- /wp:separator -->

<!-- wp:comments {"className":"wp-block-post-comments "} -->
<div class="wp-block-comments wp-block-post-comments"><!-- wp:comments-title {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-primary"}}},"typography":{"fontStyle":"normal","fontWeight":"600","lineHeight":"1.1"}},"textColor":"gv-color-primary","fontSize":"feature","fontFamily":"host-grotesk"} /-->

<!-- wp:comment-template {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"text","fontFamily":"poppins"} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40px"} -->
<div class="wp-block-column" style="flex-basis:40px"><!-- wp:avatar {"size":40,"style":{"border":{"radius":"20px"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:comment-author-name {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-primary"},":hover":{"color":{"text":"var:preset|color|gv-color-accent"}}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"gv-color-primary","fontSize":"text-hero","fontFamily":"host-grotesk"} /-->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"flex"}} -->
<div class="wp-block-group" style="margin-top:0px;margin-bottom:0px"><!-- wp:comment-date {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-text-primary"},":hover":{"color":{"text":"var:preset|color|gv-color-accent"}}}},"typography":{"fontStyle":"normal","fontWeight":"400"}},"textColor":"gv-color-text-primary","fontSize":"meta","fontFamily":"poppins"} /-->

<!-- wp:comment-edit-link {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-text-primary"},":hover":{"color":{"text":"var:preset|color|gv-color-accent"}}}}},"fontSize":"meta","fontFamily":"poppins"} /--></div>
<!-- /wp:group -->

<!-- wp:comment-content /-->

<!-- wp:comment-reply-link {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-accent"},":hover":{"color":{"text":"var:preset|color|gv-color-accent-hover"}}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"text-small","fontFamily":"host-grotesk"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-accent"},":hover":{"color":{"text":"var:preset|color|gv-color-accent-hover"}}}}},"textColor":"gv-color-primary"} -->
<!-- wp:comments-pagination-previous /-->

<!-- wp:comments-pagination-numbers /-->

<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form {"style":{"elements":{"link":{"color":{"text":"var:preset|color|gv-color-primary"}}}},"textColor":"gv-color-primary"} /--></div>
<!-- /wp:comments --></main>
<!-- /wp:group -->',
	'is_sync' => false,
);
