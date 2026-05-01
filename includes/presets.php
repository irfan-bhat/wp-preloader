<?php
/**
 * Preloader Presets Configuration
 * 
 * Define preset styles for preloader animations, SVG types, and progress bars
 */

if ( ! function_exists( 'lp_get_presets' ) ) {
    
    /**
     * Get all available preloader presets
     */
    function lp_get_presets() {
        return [
            'spinner' => [
                'label'      => __( 'Spinner Ring', 'wp-preloader' ),
                'svg'        => 'spinner.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( 'Classic rotating ring with smooth animation', 'wp-preloader' ),
            ],
            'dots' => [
                'label'      => __( 'Bouncing Dots', 'wp-preloader' ),
                'svg'        => 'dots.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '3 dots bouncing up and down', 'wp-preloader' ),
            ],
            'pulse' => [
                'label'      => __( 'Pulse', 'wp-preloader' ),
                'svg'        => 'pulse.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( 'Center dot with expanding pulse rings', 'wp-preloader' ),
            ],
            'bars' => [
                'label'      => __( 'Bars', 'wp-preloader' ),
                'svg'        => 'bars.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '5 bars with wave animation', 'wp-preloader' ),
            ],
            'spinner-dots' => [
                'label'      => __( 'Spinner Dots', 'wp-preloader' ),
                'svg'        => 'spinner-dots.svg',
                'supports'   => [ 'animation_speed', 'progress_bar' ],
                'description' => __( '8 dots rotating in a circle', 'wp-preloader' ),
            ],
        ];
    }
    
    /**
     * Get animation speed presets
     */
    function lp_get_animation_speeds() {
        return [
            'slow' => [
                'label'       => __( 'Slow', 'wp-preloader' ),
                'multiplier'  => 1.8,
                'description' => __( 'Relaxed animation pace', 'wp-preloader' ),
            ],
            'normal' => [
                'label'       => __( 'Normal', 'wp-preloader' ),
                'multiplier'  => 1,
                'description' => __( 'Standard animation speed', 'wp-preloader' ),
            ],
            'fast' => [
                'label'       => __( 'Fast', 'wp-preloader' ),
                'multiplier'  => 0.67,
                'description' => __( 'Quick animation pace', 'wp-preloader' ),
            ],
        ];
    }
    
    /**
     * Get progress bar style presets
     */
    function lp_get_progress_bar_styles() {
        return [
            'linear' => [
                'label'       => __( 'Linear Bar', 'wp-preloader' ),
                'description' => __( 'Horizontal progress bar at bottom', 'wp-preloader' ),
                'css_class'   => 'lp-progress-linear',
            ],
            'circular' => [
                'label'       => __( 'Circular Ring', 'wp-preloader' ),
                'description' => __( 'Circular progress ring around spinner', 'wp-preloader' ),
                'css_class'   => 'lp-progress-circular',
            ],
        ];
    }
    
    /**
     * Get default preset configuration
     */
    function lp_get_default_presets_config() {
        return [
            'spinner_type'      => 'spinner',
            'animation_speed'   => 'normal',
            'progress_bar_style' => 'linear',
            'show_progress'     => true,
        ];
    }
}
