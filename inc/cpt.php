<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * تسجيل Custom Post Type: Video
 * وتسجيل Taxonomy: Video Category
 */
function pv_register_cpt() {
    
    // 1. تسجيل تصنيف الفيديوهات (Categories)
    $cat_labels = array(
        'name'              => 'تصنيفات الفيديو',
        'singular_name'     => 'تصنيف',
        'search_items'      => 'بحث في التصنيفات',
        'all_items'         => 'كل التصنيفات',
        'parent_item'       => 'تصنيف أب',
        'parent_item_colon' => 'تصنيف أب:',
        'edit_item'         => 'تعديل التصنيف',
        'update_item'       => 'تحديث التصنيف',
        'add_new_item'      => 'أضف تصنيف جديد',
        'new_item_name'     => 'اسم التصنيف الجديد',
        'menu_name'         => 'تصنيفات الفيديو',
    );

    register_taxonomy( 'video_category', array( 'video' ), array(
        'hierarchical'      => true,
        'labels'            => $cat_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'video-category' ),
    ) );

    // 2. تسجيل نوع المحتوى (Video)
    $labels = array(
        'name'               => 'المكتبة المرئية',
        'singular_name'      => 'فيديو',
        'menu_name'          => 'المكتبة المرئية',
        'name_admin_bar'     => 'فيديو',
        'add_new'            => 'أضف فيديو جديد',
        'add_new_item'       => 'أضف فيديو جديد',
        'new_item'           => 'فيديو جديد',
        'edit_item'          => 'تعديل الفيديو',
        'view_item'          => 'عرض الفيديو',
        'all_items'          => 'كل الفيديوهات',
        'search_items'       => 'بحث عن فيديو',
        'not_found'          => 'لا يوجد فيديوهات',
        'not_found_in_trash' => 'لا يوجد فيديوهات في سلة المهملات',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'videos' ), // الرابط سيكون domain.com/videos
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-video-alt3', // أيقونة الفيديو
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    );

    register_post_type( 'video', $args );
}
add_action( 'init', 'pv_register_cpt' );