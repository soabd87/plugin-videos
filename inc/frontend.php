<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. دالة مساعدة لاستخراج ID الفيديو من الرابط (YouTube Only)
 */
function pv_get_video_id( $url ) {
    $video_id = '';
    if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match ) ) {
        $video_id = $match[1];
    }
    return $video_id;
}

/**
 * 2. دالة لجلب الصورة المصغرة (Thumbnail)
 * هذه هي الدالة التي كانت مفقودة وتسببت باختفاء الصور
 */
function pv_get_video_thumb( $post_id ) {
    // أ. فحص الصورة البارزة (Featured Image)
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, 'medium_large' );
    }

    // ب. فحص رابط يوتيوب واستخراج الصورة
    $video_url = get_post_meta( $post_id, '_pv_video_url', true );
    $video_id  = pv_get_video_id( $video_url );

    if ( $video_id ) {
        return "https://img.youtube.com/vi/$video_id/hqdefault.jpg";
    }

    // ج. صورة افتراضية فارغة
    return ''; 
}

/**
 * 3. تسجيل الشورت كود: [latest_videos count="3"]
 * (Logic Only - Design delegated to Theme)
 */
function pv_shortcode_latest_videos( $atts ) {
    $atts = shortcode_atts( array(
        'count' => 3,
        'cat'   => '', 
    ), $atts );

    $args = array(
        'post_type'      => 'video',
        'posts_per_page' => intval( $atts['count'] ),
        'post_status'    => 'publish',
    );

    if ( ! empty( $atts['cat'] ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'video_category',
                'field'    => 'slug',
                'terms'    => $atts['cat'],
            ),
        );
    }

    $query = new WP_Query( $args );
    
    if ( ! $query->have_posts() ) {
        return '';
    }

    // تمرير البيانات للقالب
    set_query_var( 'video_query', $query );

    ob_start();
    // استدعاء ملف التصميم من القالب
    get_template_part( 'template-parts/block', 'videos' );

    return ob_get_clean();
}
add_shortcode( 'latest_videos', 'pv_shortcode_latest_videos' );