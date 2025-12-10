<?php
/**
 * Plugin Name: GZG Video Manager
 * Description: A lightweight agency plugin to manage video lectures and interviews with smart YouTube handling.
 * Version: 1.0
 * Author: GZG Agency
 * Text Domain: plugin-videos
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// تعريف ثوابت للمسارات
define( 'PV_PATH', plugin_dir_path( __FILE__ ) );
define( 'PV_URL', plugin_dir_url( __FILE__ ) );

// استدعاء الملفات الفرعية
require_once PV_PATH . 'inc/cpt.php';       // تسجيل الـ Post Type
require_once PV_PATH . 'inc/metaboxes.php'; // حقول البيانات
require_once PV_PATH . 'inc/frontend.php';  // الشورت كود والمنطق الذكي