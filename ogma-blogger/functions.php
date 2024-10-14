<?php
/**
 * Describe child theme functions
 *
 * @package Ogma Blog
 * @subpackage Ogma Blogger
 * @since 1.0.0
 */

/*-------------------------------------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'ogma_blogger_setup' ) ) :
    
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function ogma_blogger_setup() {

        $ogma_blogger_theme_info = wp_get_theme();
        $GLOBALS['ogma_blogger_version'] = $ogma_blogger_theme_info->get( 'Version' );
    }

endif;
add_action( 'after_setup_theme', 'ogma_blogger_setup' );

/*-------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Managed the theme customizer
 */
if( ! function_exists( 'ogma_blogger_customize_register' ) ) :

    function ogma_blogger_customize_register( $wp_customize ) {

        global $wp_customize;
        
        /**
         * Ogma Blogger Default Primary Color.
         *
         * @since 1.0.0
         */ 
        $wp_customize->get_setting( 'ogma_blog_primary_theme_color' )->default = '#00796b';
        
        /**
         * Ogma Blogger Default Link Color.
         *
         * @since 1.0.0
         */ 
        $wp_customize->get_setting( 'ogma_blog_link_color' )->default = '#00796b';
        
         /**
         * Ogma Blogger Default Link Hover Color.
         *
         * @since 1.0.0
         */ 
        $wp_customize->get_setting( 'ogma_blog_link_hover_color' )->default = '#006b5e';

        /**
         * Toggle option for background animation.
         *
         * General Settings > Site Style
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'ogma_blogger_background_animation',
            array(
                'default'           => true,
                'sanitize_callback' => 'ogma_blog_sanitize_checkbox'
            )
        );
        $wp_customize->add_control( new Ogma_Blog_Control_Toggle(
            $wp_customize, 'ogma_blogger_background_animation',
                array(
                    'priority'      => 60,
                    'section'       => 'ogma_blog_section_site_style',
                    'settings'      => 'ogma_blogger_background_animation',
                    'label'         => __( 'Enable Background Animation', 'ogma-blogger' )
                )
            )
        );

    }

endif;

add_action( 'customize_register', 'ogma_blogger_customize_register', 20 );


/*-------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Ogma Blogger Fonts
 */
if ( ! function_exists( 'ogma_blogger_fonts_url' ) ) :
    
    /**
     * Register Google fonts
     *
     * @return string Google fonts URL for the theme.
     */
    function ogma_blogger_fonts_url() {

        $fonts_url = '';
        $font_families = array();

        /*
         * Translators: If there are characters in your language that are not supported
         * by Raleway, translate this to 'off'. Do not translate into your own language.
         */
        if ( 'off' !== _x( 'on', 'Raleway font: on or off', 'ogma-blogger' ) ) {
            $font_families[] = 'Raleway:700,900';
        }
        
         /*
         * Translators: If there are characters in your language that are not supported
         * by Roboto, translate this to 'off'. Do not translate into your own language.
         */
        if ( 'off' !== _x( 'on', 'Roboto font: on or off', 'ogma-blogger' ) ) {
            $font_families[] = 'Roboto:400,600,700';
        }

        if( $font_families ) {
            $query_args = array(
                'family' => urlencode( implode( '|', $font_families ) ),
                'subset' => urlencode( 'latin,latin-ext' ),
            );

            $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
        }

        return $fonts_url;
    }

endif;

/*-------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Enqueue child theme styles and scripts
 */
add_action( 'wp_enqueue_scripts', 'ogma_blogger_scripts', 99 );

function ogma_blogger_scripts() {
    
    global $ogma_blogger_version;
    
    wp_enqueue_style( 'ogma-blogger-google-font', ogma_blogger_fonts_url(), array(), null );
    
    wp_dequeue_style( 'ogma-blog-style' );

    wp_dequeue_style( 'ogma-blog-responsive-style' );
    
    wp_enqueue_style( 'ogma-blog-parent-style', get_template_directory_uri() . '/style.css', array(), esc_attr( $ogma_blogger_version ) );
    
    wp_enqueue_style( 'ogma-blog-parent-responsive', get_template_directory_uri() . '/assets/css/ogma-blog-responsive.css', array(), esc_attr( $ogma_blogger_version ) );
    
    wp_enqueue_style( 'ogma-blogger-style', get_stylesheet_uri(), array(), esc_attr( $ogma_blogger_version ) );
}   

/*-------------------------------------------------------------------------------------------------------------------------------*/
if ( ! function_exists ( 'ogma_blogger_background_animation' ) ):
    /**
     * Footer Hook Handling
     * Background Animation
     * 
     * @since 1.0.0
     */
    function ogma_blogger_background_animation() {

        $background_animation = get_theme_mod( 'ogma_blogger_background_animation' , true );

        if ( $background_animation == false ) {
            return;
        }

        echo '
          <div class="ogma-blogger-background-animation"><ul class="ogma-blogger-circles"> <li></li> <li></li> <li></li> <li></li> <li></li> <li></li> <li></li> <li></li><li></li><li></li><li></li><li></li> </ul> </div>
          ' ;
    }
endif;

add_action ( 'ogma_blog_after_page', 'ogma_blogger_background_animation', 20 );

if ( ! function_exists( 'ogma_blogger_general_css' ) ) :

    function ogma_blogger_general_css( $output_css ) {

        $ogma_blogger_primary_theme_color    = get_theme_mod( 'ogma_blog_primary_theme_color', '#00796b' );
        $ogma_blogger_primary_darker_color   = ogma_blog_darker_color( $ogma_blogger_primary_theme_color, '-20' );
        $ogma_blogger_link_color = get_theme_mod( 'ogma_blog_link_color', '#00796b' );
        $ogma_blogger_link_hover_color = get_theme_mod( 'ogma_blog_link_hover_color', '#006b5e' );

        //define variable for custom css
        $custom_css = '';

        // Background Color
        $custom_css .= ".navigation .nav-links a:hover,.bttn:hover,button,input[type='button']:hover,input[type='reset']:hover,input[type='submit']:hover,.reply .comment-reply-link,.widget_search .search-submit,.widget_search .search-submit,.widget_search .search-submit:hover,.widget_tag_cloud .tagcloud a:hover,.widget.widget_tag_cloud a:hover,#site-navigation .menu-item-description,.header-search-wrapper .search-form-wrap .search-submit,.sticky-sidebar-close,.custom-button-wrap.ogma-blog-icon-elements a,.news-ticker-label,.single-posts-layout--two .post-cats-wrap li a,.error-404.not-found .error-button-wrap a,#ogma-blog-scrollup,.trending-posts .post-thumbnail-wrap .post-count,.trending-posts-wrapper .lSAction a:hover,#site-navigation ul li a.ogma-blog-sub-toggle:hover,#site-navigation ul li a.ogma-blog-sub-toggle:focus {background-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

        // Color
           $custom_css .= "a,a:hover,a:focus,a:active,.entry-cat .cat-links a:hover,.entry-cat a:hover,.byline a:hover,.posted-on a:hover,.entry-footer a:hover,.comment-author .fn .url:hover,.commentmetadata .comment-edit-link,#cancel-comment-reply-link,#cancel-comment-reply-link:before,.logged-in-as a,.widget a:hover,.widget a:hover::before,.widget li:hover::before,.header-news-ticker-wrapper .posted-on a,.breadcrumb-trail.breadcrumbs ul li a:hover,.ogma-blog-post-content-wrap .entry-meta span:hover a,.ogma-blog-post-content-wrap .entry-meta span:hover:before,.site-footer .widget_archive a:hover,.site-footer .widget_categories a:hover,.site-footer .widget_recent_entries a:hover,.site-footer .widget_meta a:hover,.site-footer .widget_recent_comments li:hover,.site-footer .widget_rss li:hover,.site-footer .widget_pages li a:hover,.site-footer .widget_nav_menu li a:hover,.site-footer .wp-block-latest-posts li a:hover,.site-footer .wp-block-archives li a:hover,.site-footer .wp-block-categories li a:hover,.site-footer .wp-block-page-list li a:hover,.site-footer .wp-block-latest-comments li:hover,.ogma-blog-post-title-wrap .entry-meta span:hover a,.ogma-blog-post-title-wrap .entry-meta span:hover:before,.dark-mode .ogma-blog-button a:hover,.dark-mode .widget_archive a:hover,.dark-mode .widget_categories a:hover,.dark-mode .widget_recent_entries a:hover,.dark-mode .widget_meta a:hover,.dark-mode .widget_recent_comments li:hover,.dark-mode .widget_rss li:hover,.dark-mode .widget_pages li a:hover,.dark-mode .widget_nav_menu li a:hover,.dark-mode .wp-block-latest-posts li a:hover,.dark-mode .wp-block-archives li a:hover,.dark-mode .wp-block-categories li a:hover,.dark-mode .wp-block-page-list li a:hover,.dark-mode .wp-block-latest-comments li:hover,.dark-mode .header-news-ticker-wrapper .post-title a:hover,.dark-mode .post-meta-wrap span a:hover,.dark-mode .post-meta-wrap span:hover,.dark-mode .ogma-blog-post-content-wrap .entry-meta span a:hover,.ogma-blog-banner-wrapper .slide-title a:hover,.ogma-blog-post-content-wrap .entry-title a:hover,.trending-posts .entry-title a:hover, .latest-posts-wrapper .posts-column-wrapper .entry-title a:hover,.ogma-blog-banner-wrapper .post-meta-wrap > span:hover,.ogma-blog-icon-elements-wrap .search-icon a:hover,.single-posts-layout--two .no-thumbnail .ogma-blog-post-title-wrap .entry-meta span:hover a,.single-posts-layout--two .no-thumbnail .ogma-blog-post-title-wrap .entry-meta span:hover:before,.ogma-blog-search-results-wrap .ogma-blog-search-article-item .ogma-blog-search-post-element .ogma-blog-search-post-title a:hover,.single .related-posts-wrapper .entry-title a:hover{color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // Border Color
            $custom_css .= ".navigation .nav-links a:hover,.bttn:hover,button,input[type='button']:hover,input[type='reset']:hover,input[type='submit']:hover,.widget_search .search-submit,.widget_search .search-submit,.widget_search .search-submit:hover,.widget_tag_cloud .tagcloud a:hover,.widget.widget_tag_cloud a:hover,.trending-posts-wrapper .lSAction a:hover {border-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // Border Left Color
            $custom_css .= ".page-header .page-title,.block-title,.related-post-title,.widget-title{border-left-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // RTL Border Right Color
            $custom_css .= ".rtl .page-header .page-title,.rtl .block-title,.rtl .related-post-title,.rtl .widget-title{border-right-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // Border Top Color
            $custom_css .= "#site-navigation .menu-item-description::after,.search-form-wrap{border-top-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // Woocommerce Dynamic color

            $custom_css .= ".woocommerce ul.products li.product .price,.woocommerce div.product p.price, .woocommerce div.product span.price,.woocommerce .product_meta a:hover,.woocommerce-error:before, .woocommerce-info:before, .woocommerce-message:before{color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            $custom_css .= ".woocommerce #respond input#submit,.woocommerce a.button,.woocommerce button.button,.woocommerce input.button,.woocommerce ul.products li.product:hover .button,.woocommerce ul.products li.product:hover .added_to_cart,.woocommerce #respond input#submit.alt,.woocommerce a.button.alt,.woocommerce button.button.alt,.woocommerce input.button.alt.woocommerce nav.woocommerce-pagination ul li a,.woocommerce nav.woocommerce-pagination ul li span.woocommerce span.onsale,.woocommerce div.product .woocommerce-tabs ul.tabs li.active,.woocommerce #respond input#submit.disabled,.woocommerce #respond input#submit:disabled,.woocommerce #respond input#submit:disabled[disabled],.woocommerce a.button.disabled, .woocommerce a.button:disabled,.woocommerce a.button:disabled[disabled],.woocommerce button.button.disabled,.woocommerce button.button:disabled,.woocommerce button.button:disabled[disabled],.woocommerce input.button.disabled,.woocommerce input.button:disabled,.woocommerce input.button:disabled[disabled].woocommerce #respond input#submit.alt.disabled,.woocommerce #respond input#submit.alt.disabled:hover,.woocommerce #respond input#submit.alt:disabled,.woocommerce #respond input#submit.alt:disabled:hover,.woocommerce #respond input#submit.alt:disabled[disabled],.woocommerce #respond input#submit.alt:disabled[disabled]:hover,.woocommerce a.button.alt.disabled,.woocommerce a.button.alt.disabled:hover,.woocommerce a.button.alt:disabled,.woocommerce a.button.alt:disabled:hover,.woocommerce a.button.alt:disabled[disabled],.woocommerce a.button.alt:disabled[disabled]:hover,.woocommerce button.button.alt.disabled,.woocommerce button.button.alt.disabled:hover,.woocommerce button.button.alt:disabled,.woocommerce button.button.alt:disabled:hover,.woocommerce button.button.alt:disabled[disabled],.woocommerce button.button.alt:disabled[disabled]:hover,.woocommerce input.button.alt.disabled,.woocommerce input.button.alt.disabled:hover,.woocommerce input.button.alt:disabled,.woocommerce input.button.alt:disabled:hover,.woocommerce input.button.alt:disabled[disabled],.woocommerce input.button.alt:disabled[disabled]:hover.woocommerce,.widget_price_filter .ui-slider .ui-slider-range,.woocommerce-MyAccount-navigation-link a,.woocommerce-store-notice, p.demo_store{background-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            $custom_css .= ".woocommerce ul.products li.product:hover,.woocommerce-page ul.products li.product:hover.woocommerce #respond input#submit,.woocommerce a.button,.woocommerce button.button,.woocommerce input.button,.woocommerce ul.products li.product:hover .button,.woocommerce ul.products li.product:hover .added_to_cart,.woocommerce #respond input#submit.alt,.woocommerce a.button.alt,.woocommerce button.button.alt,.woocommerce input.button.alt.woocommerce #respond input#submit.alt.disabled,.woocommerce #respond input#submit.alt.disabled:hover,.woocommerce #respond input#submit.alt:disabled,.woocommerce #respond input#submit.alt:disabled:hover,.woocommerce #respond input#submit.alt:disabled[disabled],.woocommerce #respond input#submit.alt:disabled[disabled]:hover,.woocommerce a.button.alt.disabled,.woocommerce a.button.alt.disabled:hover,.woocommerce a.button.alt:disabled,.woocommerce a.button.alt:disabled:hover,.woocommerce a.button.alt:disabled[disabled],.woocommerce a.button.alt:disabled[disabled]:hover,.woocommerce button.button.alt.disabled,.woocommerce button.button.alt.disabled:hover,.woocommerce button.button.alt:disabled,.woocommerce button.button.alt:disabled:hover,.woocommerce button.button.alt:disabled[disabled],.woocommerce button.button.alt:disabled[disabled]:hover,.woocommerce input.button.alt.disabled,.woocommerce input.button.alt.disabled:hover,.woocommerce input.button.alt:disabled,.woocommerce input.button.alt:disabled:hover,.woocommerce input.button.alt:disabled[disabled],.woocommerce input.button.alt:disabled[disabled]:hover.woocommerce .widget_price_filter .ui-slider .ui-slider-handle{border-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            $custom_css .= ".woocommerce div.product .woocommerce-tabs ul.tabs{border-bottom-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            $custom_css .= ".woocommerce-error, .woocommerce-info, .woocommerce-message{border-top-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // Preloader Color
            $custom_css .= ".ogma-blog-wave .og-rect,.ogma-blog-three-bounce .og-child,.ogma-blog-folding-cube .og-cube:before,.ogma-blog-ball div{background-color: ". esc_attr( $ogma_blogger_primary_theme_color ) ."}\n";

            // Primary Hover Color
            $custom_css .= "#site-navigation ul li:hover > a, #site-navigation ul li.current-menu-item > a, #site-navigation ul li.current_page_item > a, #site-navigation ul li.current-menu-ancestor > a, #site-navigation ul li.focus > a{color: ". esc_attr( $ogma_blogger_primary_darker_color ) ."}\n";

        // Link Color
        $custom_css .= ".page-content a, .entry-content a, .entry-summary a {color: ". esc_attr( $ogma_blogger_link_color ) ."}\n";

        // Link Hover Color
        $custom_css .= ".page-content a:hover, .entry-content a:hover, .entry-summary a:hover{color: ". esc_attr( $ogma_blogger_link_hover_color ) ."}\n";

        if ( ! empty( $custom_css ) ) {
            $output_css .= $custom_css;
        }

        return $output_css;

    }

endif;

add_filter( 'ogma_blog_head_css', 'ogma_blogger_general_css', 999 );