<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add a global class to everything.
     *  We want it to come first, so stuff its filter does can be overridden.
     */
    array_unshift($classes, 'app');

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__.'\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory().'/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $theme_template = locate_template(["views/{$comments_template}", $comments_template]);

    if ($theme_template) {
        echo template($theme_template);
        return get_stylesheet_directory().'/index.php';
    }

    return $comments_template;
}, 100);

/**
 * Render WordPress searchform using Blade
 */
add_filter('get_search_form', function () {
    return template('partials.searchform');
});

/**
 * Collect data for searchform.
 */
add_filter('sage/template/app/data', function ($data) {
    return $data + [
        'sf_action' => esc_url(home_url('/')),
        'sf_screen_reader_text' => _x('Search for:', 'label', 'sage'),
        'sf_placeholder' => esc_attr_x('Search &hellip;', 'placeholder', 'sage'),
        'sf_current_query' => get_search_query(),
        'sf_submit_text' => esc_attr_x('Search', 'submit button', 'sage'),
    ];
});

/**
 * Use our woocommerce.blade.php as default woocommerce root template
 * 17.07.18: tested and required for Woocommerce 3.4.3 integration with sage 9.0.1 (WP 4.9.7)
 *
 * It seems that since woocommerce 3.4.X (not sure since, but was not needed on my latest tests):
 * - woocommerce don't see our /ressources/views/woocommerce.blade.php
 * - it search and correctly find a /ressources/woocommerce.php
 * And so, you got your resources/views/content-single-product.blade.php working, but not using your blade app main layout
 * - here we put the path of our 'woocommerce.blade.php' as first into the Woocommerce default template array
 *   - wp-content/plugins/woocommerce/includes/class-wc-template-loader.php l68 template_loader() calls ->
 *   - wp-content/plugins/woocommerce/includes/class-wc-template-loader.php l129 get_template_loader_files() that uses 'woocommerce_template_loader_files' filter
 *   - but in template_loader() the call to locate_template() fails to find our woo.blade.php... only a /resources/woocommerce.php because of STYLESHEETPATH & TEMPLATEPATH templating variables (wp-includes/default-constants.php l344)
 *   - our override filter_templates() method will return a correct path for woocommerce.blade.php
 *
 * Added a comment here: https://github.com/roots/sage/issues/1429
 */
add_filter( 'woocommerce_template_loader_files', function ( $templates, $default_file ) {
	return filter_templates(array_merge($templates, array_filter([$default_file, 'woocommerce'])));
}, PHP_INT_MAX, 2 );

/**
 * Render page using Blade (and get data from controller from sage/template/{$class}/data filter)
 * 19.05.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: fix for single-product.php and archive-product to get correct template
 * todo: is there other "woocommerce root" template that should be hot fixed here ?
 */
add_filter( 'template_include', function ( $template ) {
	//fix for single-product.php and archive-product templates
	$template = str_contains( $template, 'single-product.php' ) ? get_stylesheet_directory() . '/views/woocommerce/single-product.blade.php' : $template;
	$template = str_contains( $template, 'archive-product.php' ) ? get_stylesheet_directory() . '/views/woocommerce/archive-product.blade.php' : $template;
	$data = collect( get_body_class() )->reduce( function ( $data, $class ) use ( $template ) { //use body class to get correct controller data
		return apply_filters( "sage/template/{$class}/data", $data, $template ); //get data from controller
	}, [] );
	if ( $template ) {
		echo template( $template, $data );
		return get_stylesheet_directory() . '/index.php';
	}
	return $template;
}, PHP_INT_MAX );

/**
 * Render page using Blade
 * 19.05.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: locate blade or php template file and render it
 * Triggers just before woocommerce actually render PHP template, but return blade and null, or php template like woocommerce would do
 * wp-content/plugins/woocommerce/includes/wc-core-functions.php:l158
 */
add_filter( 'wc_get_template_part', function ( $template, $slug, $name, $args = [] ) {
	$bladeTemplate = false;
	// Look in yourtheme/slug-name.blade.php and yourtheme/woocommerce/slug-name.blade.php
	if ( $name && ! WC_TEMPLATE_DEBUG_MODE ) {
		$bladeTemplate = locate_template( [ "{$slug}-{$name}.blade.php", WC()->template_path() . "{$slug}-{$name}.blade.php" ] );
	}
	// If template file doesn't exist, look in yourtheme/slug.blade.php and yourtheme/woocommerce/slug.blade.php
	if ( ! $template && ! WC_TEMPLATE_DEBUG_MODE ) {
		$bladeTemplate = locate_template( [ "{$slug}.blade.php", WC()->template_path() . "{$slug}.blade.php" ] );
	}
	if ( $bladeTemplate ) {
		echo template( $bladeTemplate, $args );
		return null;
	}
	//try to look for PHP files within resources/views/woocommerce
	$normalTemplate = false;
	if ( $name && ! WC_TEMPLATE_DEBUG_MODE ) {
		$normalTemplate = locate_template( [ "{$slug}-{$name}.php", WC()->template_path() . "{$slug}-{$name}.php" ] );
	}
	if ( ! $normalTemplate && ! WC_TEMPLATE_DEBUG_MODE ) {
		$normalTemplate = locate_template( [ "{$slug}.php", WC()->template_path() . "{$slug}.php" ] );
	}
	if ( $normalTemplate ) {
		return get_theme_file_path( $normalTemplate ); //work even without
	}
	return $template;
}, PHP_INT_MAX, 4 );

/**
 * Render woocommerce template parts using Blade
 * 19.05.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: Woocommerce support to use resources/views/woocommerce/* templates when using template parts
 * (wc_get_template_part & template_include filters will search for .blade.php)
 */
add_filter( 'wc_get_template', function ( $located, $template_name, $args = [], $template_path, $default_path ) {
	$bladeTemplate = locate_template( [ $template_name, 'resources/views/' . WC()->template_path() . $template_name ] );
	if ( $bladeTemplate ) {
		return template_path( $bladeTemplate, $args );
	}
	return $located;
}, PHP_INT_MAX, 5 );





/**
 * Add class active tu current-menu-item
 */
add_filter('nav_menu_css_class' , function ($classes, $item) {
    if( in_array('current-menu-item', $classes) ){
        $classes[] = 'active ';
    }
    return $classes;
}, 10 , 2);

/**
 * Add nav-item to <li> tag into primary_navigation
 */
add_filter('nav_menu_css_class', function ($classes, $item, $args) {
    if($args->theme_location == 'primary_navigation') {
        $classes[] = 'nav-item';
    }
    return $classes;
}, 1, 3);

/**
 * Add class `nav-link` to link
 * Condition: If theme location is auth_menu add class text-green-flower
 */
add_filter( 'nav_menu_link_attributes', function($atts, $item, $args) {
    $class = ($args->theme_location == 'auth_menu') ? 'text-green-flower' : 'nav-link' ;
    $atts['class'] = $class;
    return $atts;
}, 10, 3 );
