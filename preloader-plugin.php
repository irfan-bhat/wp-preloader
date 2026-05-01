<?php
/**
 * Plugin Name: Logo Preloader
 * Plugin URI:  https://github.com/your-username/logo-preloader
 * Description: A customisable full-screen preloader with your logo, spinner, and progress bar.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPL-2.0+
 * Text Domain: logo-preloader
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LP_VERSION',  '1.0.0' );
define( 'LP_DIR',      plugin_dir_path( __FILE__ ) );
define( 'LP_URL',      plugin_dir_url( __FILE__ ) );
define( 'LP_OPT',      'logo_preloader_options' );

/* ---------------------------------------------------------------
   Default options
--------------------------------------------------------------- */
function lp_defaults() {
    return [
        'bg_color'       => '#111111',
        'accent_color'   => '#DC501E',
        'logo_url'       => '',
        'logo_width'     => 64,
        'show_bar'       => true,
        'show_ring'      => true,
        'fade_duration'  => 500,
        'min_display'    => 800,
        'enable_mobile'  => true,
    ];
}

function lp_options() {
    return wp_parse_args( get_option( LP_OPT, [] ), lp_defaults() );
}

/* ---------------------------------------------------------------
   Activation — save defaults
--------------------------------------------------------------- */
register_activation_hook( __FILE__, function() {
    if ( ! get_option( LP_OPT ) ) {
        update_option( LP_OPT, lp_defaults() );
    }
});

/* ---------------------------------------------------------------
   Front-end: inject preloader HTML right after <body>
--------------------------------------------------------------- */
add_action( 'wp_body_open', 'lp_render_preloader' );
function lp_render_preloader() {
    $o        = lp_options();
    $logo_src = esc_url( $o['logo_url'] );
    $logo_w   = absint( $o['logo_width'] );
    $show_img = $logo_src ? "<img src=\"{$logo_src}\" alt=\"\" style=\"width:{$logo_w}px;\">" : '';
    ?>
    <div id="lp-preloader" role="status" aria-label="<?php esc_attr_e( 'Loading', 'logo-preloader' ); ?>">
        <div class="lp-inner">
            <?php if ( $o['show_ring'] ) : ?>
            <div class="lp-wrap">
                <div class="lp-ring"></div>
                <?php echo $show_img; ?>
            </div>
            <?php elseif ( $show_img ) : ?>
            <div class="lp-wrap"><?php echo $show_img; ?></div>
            <?php endif; ?>

            <?php if ( $o['show_bar'] ) : ?>
            <div class="lp-bar"><div class="lp-fill"></div></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* ---------------------------------------------------------------
   Front-end: enqueue CSS + JS (with inline config)
--------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'lp_enqueue_frontend' );
function lp_enqueue_frontend() {
    $o = lp_options();

    // Skip on mobile if disabled
    if ( ! $o['enable_mobile'] && wp_is_mobile() ) return;

    wp_enqueue_style(
        'logo-preloader',
        LP_URL . 'css/preloader.css',
        [],
        LP_VERSION
    );

    // Inline dynamic CSS vars
    $bg      = sanitize_hex_color( $o['bg_color'] );
    $accent  = sanitize_hex_color( $o['accent_color'] );
    $logo_w  = absint( $o['logo_width'] );
    $inline  = "#lp-preloader{--lp-bg:{$bg};--lp-accent:{$accent};--lp-logo-w:{$logo_w}px;}";
    wp_add_inline_style( 'logo-preloader', $inline );

    wp_enqueue_script(
        'logo-preloader',
        LP_URL . 'js/preloader.js',
        [],
        LP_VERSION,
        true   // footer
    );

    // Pass config to JS
    wp_localize_script( 'logo-preloader', 'lpConfig', [
        'fade'       => absint( $o['fade_duration'] ),
        'minDisplay' => absint( $o['min_display'] ),
    ]);
}

/* ---------------------------------------------------------------
   Admin: settings page
--------------------------------------------------------------- */
add_action( 'admin_menu', function() {
    add_options_page(
        __( 'Logo Preloader', 'logo-preloader' ),
        __( 'Logo Preloader', 'logo-preloader' ),
        'manage_options',
        'logo-preloader',
        'lp_settings_page'
    );
});

add_action( 'admin_init', 'lp_register_settings' );
function lp_register_settings() {
    register_setting( 'logo_preloader_group', LP_OPT, [
        'sanitize_callback' => 'lp_sanitize_options',
    ]);
}

function lp_sanitize_options( $input ) {
    $d   = lp_defaults();
    $out = [];
    $out['bg_color']      = sanitize_hex_color( $input['bg_color']      ?? $d['bg_color'] )      ?: $d['bg_color'];
    $out['accent_color']  = sanitize_hex_color( $input['accent_color']  ?? $d['accent_color'] )  ?: $d['accent_color'];
    $out['logo_url']      = esc_url_raw( $input['logo_url']             ?? '' );
    $out['logo_width']    = max( 20, min( 300, absint( $input['logo_width']   ?? $d['logo_width'] ) ) );
    $out['fade_duration'] = max( 0,  min( 3000, absint( $input['fade_duration'] ?? $d['fade_duration'] ) ) );
    $out['min_display']   = max( 0,  min( 5000, absint( $input['min_display']   ?? $d['min_display'] ) ) );
    $out['show_bar']      = ! empty( $input['show_bar'] );
    $out['show_ring']     = ! empty( $input['show_ring'] );
    $out['enable_mobile'] = ! empty( $input['enable_mobile'] );
    return $out;
}

/* ---------------------------------------------------------------
   Admin: enqueue media uploader
--------------------------------------------------------------- */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( $hook !== 'settings_page_logo-preloader' ) return;
    wp_enqueue_media();
    wp_enqueue_style( 'lp-admin', LP_URL . 'admin/admin.css', [], LP_VERSION );
    wp_enqueue_script( 'lp-admin', LP_URL . 'admin/admin.js', [ 'jquery' ], LP_VERSION, true );
});

/* ---------------------------------------------------------------
   Settings page HTML
--------------------------------------------------------------- */
function lp_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $o = lp_options();
    ?>
    <div class="wrap lp-wrap-admin">
        <h1><?php esc_html_e( 'Logo Preloader Settings', 'logo-preloader' ); ?></h1>

        <div class="lp-admin-layout">
            <!-- ── Form ── -->
            <div class="lp-form-col">
                <form method="post" action="options.php">
                    <?php settings_fields( 'logo_preloader_group' ); ?>

                    <div class="lp-card">
                        <h2><?php esc_html_e( 'Logo', 'logo-preloader' ); ?></h2>

                        <div class="lp-field">
                            <label><?php esc_html_e( 'Logo image', 'logo-preloader' ); ?></label>
                            <div class="lp-media-row">
                                <input type="text" id="lp-logo-url" name="<?php echo LP_OPT; ?>[logo_url]"
                                    value="<?php echo esc_attr( $o['logo_url'] ); ?>" class="regular-text">
                                <button type="button" id="lp-upload-btn" class="button">
                                    <?php esc_html_e( 'Choose image', 'logo-preloader' ); ?>
                                </button>
                                <button type="button" id="lp-remove-btn" class="button lp-remove"
                                    <?php echo $o['logo_url'] ? '' : 'style="display:none"'; ?>>
                                    <?php esc_html_e( 'Remove', 'logo-preloader' ); ?>
                                </button>
                            </div>
                            <?php if ( $o['logo_url'] ) : ?>
                            <div id="lp-preview-wrap">
                                <img id="lp-img-preview" src="<?php echo esc_url( $o['logo_url'] ); ?>" alt="">
                            </div>
                            <?php else : ?>
                            <div id="lp-preview-wrap" style="display:none">
                                <img id="lp-img-preview" src="" alt="">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="lp-field lp-two-col">
                            <div>
                                <label for="lp-logo-width"><?php esc_html_e( 'Logo width (px)', 'logo-preloader' ); ?></label>
                                <input type="number" id="lp-logo-width" name="<?php echo LP_OPT; ?>[logo_width]"
                                    value="<?php echo esc_attr( $o['logo_width'] ); ?>" min="20" max="300" class="small-text"> px
                            </div>
                        </div>
                    </div>

                    <div class="lp-card">
                        <h2><?php esc_html_e( 'Colors', 'logo-preloader' ); ?></h2>
                        <div class="lp-field lp-two-col">
                            <div>
                                <label for="lp-bg"><?php esc_html_e( 'Background', 'logo-preloader' ); ?></label>
                                <input type="color" id="lp-bg" name="<?php echo LP_OPT; ?>[bg_color]"
                                    value="<?php echo esc_attr( $o['bg_color'] ); ?>">
                                <input type="text" class="lp-hex-text" data-for="lp-bg"
                                    value="<?php echo esc_attr( $o['bg_color'] ); ?>" maxlength="7">
                            </div>
                            <div>
                                <label for="lp-accent"><?php esc_html_e( 'Accent (ring & bar)', 'logo-preloader' ); ?></label>
                                <input type="color" id="lp-accent" name="<?php echo LP_OPT; ?>[accent_color]"
                                    value="<?php echo esc_attr( $o['accent_color'] ); ?>">
                                <input type="text" class="lp-hex-text" data-for="lp-accent"
                                    value="<?php echo esc_attr( $o['accent_color'] ); ?>" maxlength="7">
                            </div>
                        </div>
                    </div>

                    <div class="lp-card">
                        <h2><?php esc_html_e( 'Elements', 'logo-preloader' ); ?></h2>
                        <div class="lp-field lp-checks">
                            <label>
                                <input type="checkbox" name="<?php echo LP_OPT; ?>[show_ring]" value="1"
                                    <?php checked( $o['show_ring'] ); ?>>
                                <?php esc_html_e( 'Show spinner ring', 'logo-preloader' ); ?>
                            </label>
                            <label>
                                <input type="checkbox" name="<?php echo LP_OPT; ?>[show_bar]" value="1"
                                    <?php checked( $o['show_bar'] ); ?>>
                                <?php esc_html_e( 'Show progress bar', 'logo-preloader' ); ?>
                            </label>
                            <label>
                                <input type="checkbox" name="<?php echo LP_OPT; ?>[enable_mobile]" value="1"
                                    <?php checked( $o['enable_mobile'] ); ?>>
                                <?php esc_html_e( 'Show on mobile devices', 'logo-preloader' ); ?>
                            </label>
                        </div>
                    </div>

                    <div class="lp-card">
                        <h2><?php esc_html_e( 'Timing', 'logo-preloader' ); ?></h2>
                        <div class="lp-field lp-two-col">
                            <div>
                                <label for="lp-fade"><?php esc_html_e( 'Fade-out duration (ms)', 'logo-preloader' ); ?></label>
                                <input type="number" id="lp-fade" name="<?php echo LP_OPT; ?>[fade_duration]"
                                    value="<?php echo esc_attr( $o['fade_duration'] ); ?>" min="0" max="3000" class="small-text"> ms
                            </div>
                            <div>
                                <label for="lp-min"><?php esc_html_e( 'Minimum display time (ms)', 'logo-preloader' ); ?></label>
                                <input type="number" id="lp-min" name="<?php echo LP_OPT; ?>[min_display]"
                                    value="<?php echo esc_attr( $o['min_display'] ); ?>" min="0" max="5000" class="small-text"> ms
                                <p class="description"><?php esc_html_e( 'Preloader stays visible for at least this long even if the page loads faster.', 'logo-preloader' ); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php submit_button( __( 'Save settings', 'logo-preloader' ) ); ?>
                </form>
            </div>

            <!-- ── Live preview ── -->
            <div class="lp-preview-col">
                <div class="lp-card lp-sticky">
                    <h2><?php esc_html_e( 'Preview', 'logo-preloader' ); ?></h2>
                    <div id="lp-live-preview" style="background:<?php echo esc_attr( $o['bg_color'] ); ?>">
                        <div class="lp-prev-inner">
                            <div class="lp-prev-wrap" id="lpv-wrap">
                                <div class="lp-prev-ring" id="lpv-ring"></div>
                                <?php if ( $o['logo_url'] ) : ?>
                                <img id="lpv-img" src="<?php echo esc_url( $o['logo_url'] ); ?>"
                                    style="width:<?php echo esc_attr( $o['logo_width'] ); ?>px;">
                                <?php else : ?>
                                <div id="lpv-placeholder" class="lp-prev-placeholder">LOGO</div>
                                <img id="lpv-img" src="" style="display:none;width:<?php echo esc_attr( $o['logo_width'] ); ?>px;">
                                <?php endif; ?>
                            </div>
                            <div class="lp-prev-bar" id="lpv-bar">
                                <div class="lp-prev-fill" id="lpv-fill"
                                    style="background:<?php echo esc_attr( $o['accent_color'] ); ?>"></div>
                            </div>
                        </div>
                    </div>
                    <p class="description" style="text-align:center;margin-top:.5rem">
                        <?php esc_html_e( 'Updates as you change settings', 'logo-preloader' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
