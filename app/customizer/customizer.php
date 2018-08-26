<?php

/**
 * Ecomprende Customizer Class
 *
 * @author Nestor Gutierrez
 * @package ecomprende
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Storefront_Customizer')) {

    /**
     * The Ecomprende Customizer Class
     */

    class Ecomprende_Customizer
    {
        /**
         * Setup Class
         *
         * @since 1.0.0
         */
        public function __construct()
        {

        }

        public function register($wp_customize)
        {
            /**
             * Homepage Section
             */
            $wp_customize->add_section(
                'homepage_flower_shop',
                [
                    'title' => __('Homepage Template', 'ecomprende'),
                ]
            );

            /**
             * Text Control
             */
            $wp_customize->add_setting(
                'text_homepage',
                [
                    'default' => dirname( __FILE__ )
                ]
            );
            $wp_customize->add_control(
                'text_homepage',
                [
                    'label' => 'Text Homepage',
                    'section' => 'homepage_flower_shop',
                    'type' => 'text'
                ]
            );

            /**
             * Customs Constrols
             */
            require_once dirname( __FILE__ ) . '/class-ecomprende-multiple-control-image.php';

            $wp_customize->add_setting(
                'header_homepage_carousel', array(
                    'default-image'     => '',
                    'sanitize_callback' => 'esc_url_raw',
                )
            );

            $wp_customize->add_control(
                new \WP_Customize_Image_Control(
                    $wp_customize,
                    'header_homepage_carousel', array(
                        'label'       => __( 'Seleccione el Logo', 'logo' ),
                        'description' => __( 'Recomendación: Cargar la imagen del logo sin fondo para que este no interfiera con el background de la pagina', 'logo' ),
                        'type'        => 'image',
                        'section'     => 'homepage_flower_shop',
                        'priority'    => 11,
                    )
                )
            );
        }
    }

}

// Setup the Theme Customizer settings and controls...
add_action('customize_register', array('Ecomprende_Customizer', 'register'));
