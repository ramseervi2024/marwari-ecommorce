<?php
/**
 * Template Name: Marwari Storefront Blank Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php
    // Render the storefront content directly
    echo do_shortcode( '[marwari_storefront]' );
    ?>
    <?php wp_footer(); ?>
</body>
</html>
