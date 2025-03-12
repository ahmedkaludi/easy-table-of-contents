<?php
/**
 * Add sitemap for pages, post and categories through shortocde
 * @class   EZTOC_Sitemap
 * @since   2.0.73
 * */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
class ezTOC_Sitemap {

    /**
     * Constructor function
     * @since   2.0.73
     * */
    public function __construct() {
        
        add_shortcode( 'ez-toc-sitemap', [ $this, 'ez_toc_shortcode_sitemap' ] );
        add_shortcode( 'ez-toc-sitemap-pages', [ $this, 'ez_toc_shortcode_sitemap_pages' ] );
        add_shortcode( 'ez-toc-sitemap-categories', [ $this, 'ez_toc_shortcode_sitemap_categories' ] );
        add_shortcode( 'ez-toc-sitemap-posts', [ $this, 'ez_toc_shortcode_sitemap_posts' ] );

        // Migrate TOC plug plugin shortocde
        add_shortcode( 'sitemap', [ $this, 'ez_toc_shortcode_sitemap' ] );
        add_shortcode( 'sitemap_pages', [ $this, 'ez_toc_shortcode_sitemap_pages' ] );
        add_shortcode( 'sitemap_categories', [ $this, 'ez_toc_shortcode_sitemap_categories' ] );
        add_shortcode( 'sitemap_posts', [ $this, 'ez_toc_shortcode_sitemap_posts' ] );

    }

    /**
     * This shortcode renders pages and categpries
     * @param   $attr   array
     * @return  $html   html string
     * @since   2.0.73
     * */
    public function ez_toc_shortcode_sitemap( $attributes ) {
        
        $atts = shortcode_atts(
            [
                'page_heading'          => 'Pages',
                'category_heading'      => 'Categories',
                'heading'               => 3,
                'no_label'              => false,
            ],
            $attributes
        );

        if ( $atts['heading'] < 1 && $atts['heading'] > 6 ) {
            $atts['heading']   =   3;    
        }

        // Render Pages
        $html           =  '<div class="ez-toc-sitemap">';
        if ( ! $atts['no_label'] ) {
            $html       .=  '<h' . intval( $atts['heading'] ) . ' class="ez-toc-sitemap-pages">' . htmlentities( $atts['page_heading'], ENT_COMPAT, 'UTF-8' ) . '</h' . intval( $atts['heading'] ) .'>';  
        }
        $html           .=  '<ul class="ez-toc-sitemap-pages-list">';
        $html           .=  wp_list_pages(
                                [
                                    'title_li' => '',
                                    'echo'     => false,
                                ]
                            );
        $html           .=  '</ul>';

        // Render Categories
        if ( ! $atts['no_label'] ) {
            $html       .=  '<h' . intval( $atts['heading'] ) . ' class="ez-toc-sitemap-pages">' . htmlentities( $atts['category_heading'], ENT_COMPAT, 'UTF-8' ) . '</h' . intval( $atts['heading'] ) .'>';  
        }
        $html           .=  '<ul class="ez-toc-sitemap-categories-list">';
        $html           .=  wp_list_categories(
                                [
                                    'title_li' => '',
                                    'echo'     => false,
                                ]
                            );
        $html           .=  '</ul>';
        $html           .=  '</div>';

        return $html;

    }

    /**
     * This shortcode renders pages
     * @param   $attr   array
     * @return  $html   html string
     * @since   2.0.73
     * */
    public function ez_toc_shortcode_sitemap_pages( $attributes ) {
        
        $atts = shortcode_atts(
            [
                'heading'       => 3,
                'label'         => 'Pages',
                'no_label'      => false,
                'exclude'       => '',
                'exclude_tree'  => '',
                'child_of'      => 0,
            ],
            $attributes
        );

        if ( $atts['heading'] < 1 && $atts['heading'] > 6 ) {
            $atts['heading']    =   3;    
        }

        if ( 'current' === strtolower( $atts['child_of'] ) ) {
            $atts['child_of'] = get_the_ID();
        } elseif ( is_numeric( $atts['child_of'] ) ) {
            $atts['child_of'] = $atts['child_of'];
        } else {
            $atts['child_of'] = 0;
        }

        // Render Pages
        $html           =  '<div class="ez-toc-sitemap">';
        if ( ! $atts['no_label'] ) {
            $html       .=  '<h' . intval( $atts['heading'] ) . ' class="ez-toc-sitemap-pages">' . htmlentities( $atts['label'], ENT_COMPAT, 'UTF-8' ) . '</h' . intval( $atts['heading'] ) .'>';  
        }
        $html           .=  '<ul class="ez-toc-sitemap-pages-list">';
        $html           .=  wp_list_pages(
                                [
                                    'title_li'     => '',
                                    'echo'         => false,
                                    'exclude'      => esc_attr( $atts['exclude'] ),
                                    'exclude_tree' => esc_attr( $atts['exclude_tree'] ),
                                    'hierarchical' => true,
                                    'child_of'     => intval( $atts['child_of'] ),
                                ]
                            );
        $html           .=  '</ul>';
        $html           .=  '</div>';

        return $html;

    }

    /**
     * This shortcode renders categories
     * @param   $attr   array
     * @return  $html   html string
     * @since   2.0.73
     * */
    public function ez_toc_shortcode_sitemap_categories( $attributes ) {
        
        $atts = shortcode_atts(
            [
                'heading'      => 3,
                'label'        => 'Categories',
                'no_label'     => false,
                'exclude'      => '',
                'exclude_tree' => '',
            ],
            $attributes
        );

        if ( $atts['heading'] < 1 && $atts['heading'] > 6 ) {
            $atts['heading']    =   3;    
        }

        $html = '<div class="toc_sitemap">';
        // Render Categories
        if ( ! $atts['no_label'] ) {
            $html       .=  '<h' . intval( $atts['heading'] ) . ' class="ez-toc-sitemap-pages">' . htmlentities( $atts['label'], ENT_COMPAT, 'UTF-8' ) . '</h' . intval( $atts['heading'] ) .'>';  
        }
        $html           .=  '<ul class="ez-toc-sitemap-categories-list">';
        $html           .=  wp_list_categories(
                                [
                                    'title_li'     => '',
                                    'echo'         => false,
                                    'exclude'      => esc_attr( $atts['exclude'] ),
                                    'exclude_tree' => esc_attr( $atts['exclude_tree'] ),
                                ]
                            );
        $html           .=  '</ul>';
        $html           .= '</div>';

        return $html;

    }

    /**
     * This shortcode renders posts
     * @param   $attr   array
     * @return  $html   html string
     * @since   2.0.73
     * */
    public function ez_toc_shortcode_sitemap_posts( $attributes ) {
        
        $atts = shortcode_atts(
            [
                'order'    => 'ASC',
                'orderby'  => 'title',
                'separate' => true,
            ],
            $attributes
        );

        $order  =   strtoupper( sanitize_text_field( $atts['order'] ) );
        if ( $order !== 'ASC' || $order !== 'DESC' ) {
            $atts['order']  =   'ASC';   
        }

        $articles = new WP_Query(
            [
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'order'          => esc_attr( $atts['order'] ),
                'orderby'        => esc_attr( $atts['orderby'] ),
                'posts_per_page' => -1,
            ]
        );

        $html   = '';
        $letter = '';

        $atts['separate'] = strtolower( $atts['separate'] );
        if ( 'false' === $atts['separate'] || 'no' === $atts['separate'] ) {
            $atts['separate'] = false;
        }


        while ( $articles->have_posts() ) {

            $articles->the_post();
            $title = wp_strip_all_tags( get_the_title() );

            if ( $atts['separate'] ) {
                if ( strtolower( $title[0] ) !== $letter ) {
                    if ( $letter ) {
                        $html .= '</ul></div>';
                    }

                    $html  .= '<div class="ez-toc-toc-sitemap-posts-section"><p class="ez-toc-sitemap-posts-letter">' . strtolower( $title[0] ) . '</p><ul class="ez-toc-sitemap-posts-list">';
                    $letter = strtolower( $title[0] );
                }
            }

            $html .= '<li><a href="' . get_permalink( $articles->post->ID ) . '">' . esc_html( $title ) . '</a></li>';
        }

        if ( $html ) {
            if ( $atts['separate'] ) {
                $html .= '</div>';
            } else {
                $html = '<div class="ez-toc-sitemap-posts-section"><ul class="ez-toc-sitemap-posts-list">' . $html . '</ul></div>';
            }
        }

        wp_reset_postdata();

        return $html;
    }

}

new ezTOC_Sitemap();