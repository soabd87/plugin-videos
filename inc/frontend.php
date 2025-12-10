<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * دالة مساعدة لاستخراج ID الفيديو من الرابط (YouTube Only for v1)
 */
function pv_get_video_id( $url ) {
    $video_id = '';
    if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match ) ) {
        $video_id = $match[1];
    }
    return $video_id;
}

/**
 * دالة لجلب الصورة المصغرة (Thumbnail)
 * الأولوية: الصورة البارزة > صورة يوتيوب > صورة افتراضية (placeholder)
 */
function pv_get_video_thumb( $post_id ) {
    // 1. فحص الصورة البارزة (Featured Image)
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, 'medium_large' );
    }

    // 2. فحص رابط يوتيوب واستخراج الصورة
    $video_url = get_post_meta( $post_id, '_pv_video_url', true );
    $video_id  = pv_get_video_id( $video_url );

    if ( $video_id ) {
        // جلب صورة عالية الجودة من سيرفرات يوتيوب
        return "https://img.youtube.com/vi/$video_id/hqdefault.jpg"; 
        // أو maxresdefault.jpg (قد لا تتوفر لبعض الفيديوهات)
    }

    // 3. صورة افتراضية (يفضل وضع صورة في مجلد assets لاحقاً)
    return ''; 
}

/**
 * تسجيل الشورت كود: [latest_videos count="3"]
 */
function pv_shortcode_latest_videos( $atts ) {
    $atts = shortcode_atts( array(
        'count' => 3,
        'cat'   => '', // slug للتصنيف إذا أردت تصفية
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

    ob_start();
    ?>
    <div class="pv-video-grid">
        <?php while ( $query->have_posts() ) : $query->the_post(); 
            $post_id   = get_the_ID();
            $thumb_url = pv_get_video_thumb( $post_id );
            $video_url = get_post_meta( $post_id, '_pv_video_url', true );
            $duration  = get_post_meta( $post_id, '_pv_video_duration', true );
        ?>
            <div class="pv-video-item">
                <a href="<?php echo esc_url( $video_url ); ?>" target="_blank" class="pv-video-link" aria-label="شاهد <?php the_title(); ?>">
                    <div class="pv-thumb-wrapper" style="position:relative; overflow:hidden; border-radius:8px; aspect-ratio:16/9; background:#000;">
                        <?php if ( $thumb_url ) : ?>
                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title(); ?>" style="width:100%; height:100%; object-fit:cover; opacity:0.8; transition:0.3s;">
                        <?php endif; ?>
                        
                        <div class="pv-play-icon" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:50px; height:50px; background:rgba(255,0,0,0.8); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <span style="border-left: 15px solid #fff; border-top: 10px solid transparent; border-bottom: 10px solid transparent; margin-left:5px;"></span>
                        </div>

                        <?php if ( $duration ) : ?>
                            <span class="pv-duration" style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,0.7); color:#fff; padding:2px 6px; font-size:12px; border-radius:4px;">
                                <?php echo esc_html( $duration ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="pv-video-title" style="margin-top:10px; font-size:1.1rem; font-weight:bold;"><?php the_title(); ?></h3>
                </a>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    
    <style>
        .pv-video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .pv-video-item img:hover { opacity: 1 !important; transform: scale(1.05); }
    </style>

    <?php
    return ob_get_clean();
}
add_shortcode( 'latest_videos', 'pv_shortcode_latest_videos' );