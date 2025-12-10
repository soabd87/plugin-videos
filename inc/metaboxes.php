<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. إضافة الميتا بوكس
function pv_add_meta_boxes() {
    add_meta_box(
        'pv_video_details',      // ID
        'بيانات الفيديو',        // Title
        'pv_render_meta_box',    // Callback function
        'video',                 // Screen (Post Type)
        'normal',                // Context
        'high'                   // Priority
    );
}
add_action( 'add_meta_boxes', 'pv_add_meta_boxes' );

// 2. عرض الحقول في لوحة التحكم (HTML)
function pv_render_meta_box( $post ) {
    // استرجاع القيم المحفوظة مسبقاً
    $video_url = get_post_meta( $post->ID, '_pv_video_url', true );
    $duration  = get_post_meta( $post->ID, '_pv_video_duration', true );
    $featured  = get_post_meta( $post->ID, '_pv_is_featured', true );

    // Nonce للحماية
    wp_nonce_field( 'pv_save_video_data', 'pv_video_nonce' );
    ?>
    <p>
        <label for="pv_video_url" style="font-weight:bold; display:block; margin-bottom:5px;">رابط الفيديو (YouTube / Vimeo):</label>
        <input type="url" id="pv_video_url" name="pv_video_url" value="<?php echo esc_attr( $video_url ); ?>" style="width:100%;" placeholder="https://www.youtube.com/watch?v=..." />
        <span class="description">سيتم جلب الصورة المصغرة تلقائياً إذا لم تضع "الصورة البارزة".</span>
    </p>

    <div style="display:flex; gap:20px; margin-top:15px;">
        <div style="flex:1;">
            <label for="pv_video_duration" style="font-weight:bold; display:block; margin-bottom:5px;">مدة الفيديو (اختياري):</label>
            <input type="text" id="pv_video_duration" name="pv_video_duration" value="<?php echo esc_attr( $duration ); ?>" placeholder="مثلاً: 15:30" />
        </div>
        
        <div style="flex:1; padding-top:25px;">
            <label>
                <input type="checkbox" name="pv_is_featured" value="1" <?php checked( $featured, 1 ); ?> />
                تثبيت في القسم المميز (Featured)
            </label>
        </div>
    </div>
    <?php
}

// 3. حفظ البيانات
function pv_save_meta_box_data( $post_id ) {
    // التحقق من Nonce
    if ( ! isset( $_POST['pv_video_nonce'] ) || ! wp_verify_nonce( $_POST['pv_video_nonce'], 'pv_save_video_data' ) ) {
        return;
    }
    // التحقق من الحفظ التلقائي
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // التحقق من الصلاحيات
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // حفظ الرابط
    if ( isset( $_POST['pv_video_url'] ) ) {
        update_post_meta( $post_id, '_pv_video_url', sanitize_url( $_POST['pv_video_url'] ) );
    }
    // حفظ المدة
    if ( isset( $_POST['pv_video_duration'] ) ) {
        update_post_meta( $post_id, '_pv_video_duration', sanitize_text_field( $_POST['pv_video_duration'] ) );
    }
    // حفظ حالة التميز (Checkbox)
    $featured = isset( $_POST['pv_is_featured'] ) ? 1 : 0;
    update_post_meta( $post_id, '_pv_is_featured', $featured );
}
add_action( 'save_post', 'pv_save_meta_box_data' );