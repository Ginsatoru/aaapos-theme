<?php
/**
 * Custom Nav Walker - Mega Menu Style Sub-Items
 *
 * Renders sub-menu items with an optional icon + description (set via
 * Customizer > Navigation Menu Items, NOT WordPress's native menu editor
 * fields), matching a card-grid dropdown design. Falls back to a plain
 * title-only link when no icon/description is set for that item, so
 * unconfigured menus still render correctly with no visual regression.
 *
 * Top-level menu items (depth 0) are untouched - this only affects
 * items INSIDE a dropdown (depth 1+).
 *
 * Column count: each top-level dropdown's number of grid columns is
 * configurable via Customizer > Menu Options > [Parent] > Columns
 * (set in inc/customizer/nav-menu-items.php as nav_menu_columns_{ID}).
 * Applied here as a --sub-menu-columns CSS custom property on the
 * <ul class="sub-menu"> itself, consumed by assets/css/components/header/
 * _dropdowns.css.
 *
 * Icons: either a curated SVG (aaapos_nav_icon_choices()) or a custom
 * uploaded image/gif via the media library (nav_item_icon_custom_{ID},
 * used when nav_item_icon_{ID} === 'custom'). Picker UI lives in
 * inc/customizer/nav-menu-items.php (AAAPOS_Icon_Picker_Control).
 *
 * Dropdown style: each top-level dropdown also has a Style switch
 * (Customizer > Menu Options > [Parent] > Style, nav_menu_style_{ID},
 * 'icon' default or 'image') via aaapos_get_nav_menu_style(). 'image'
 * swaps the icon picker above for a single larger image upload per
 * item (nav_item_image_{ID}), rendered via the .menu-item-rich--image-style
 * modifier and assets/css/components/header/_dropdowns-image-style.css.
 *
 * @package AAAPOS_Prime
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('aaapos_nav_icon_choices')) {
    /**
     * Curated icon set for the menu item icon picker.
     * Key => [label, svg markup]
     */
    function aaapos_nav_icon_choices() {
        return array(
            'calendar'     => array('label' => __('Calendar', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>'),
            'document'     => array('label' => __('Document', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>'),
            'video'        => array('label' => __('Video', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>'),
            'book'         => array('label' => __('Book', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>'),
            'cart'         => array('label' => __('Cart', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>'),
            'gear'         => array('label' => __('Settings / Gear', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'),
            'chart'        => array('label' => __('Chart / Reports', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg>'),
            'help'         => array('label' => __('Help / Support', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'),
            'blog'         => array('label' => __('Blog / News', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="12" y2="17"/></svg>'),
            'folder'       => array('label' => __('Folder', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>'),
            'edit'         => array('label' => __('Edit / Customize', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>'),
            'tag'          => array('label' => __('Tag / Pricing', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41L13.42 20.58a2 2 0 0 1-2.83 0L2.5 12.5V2h10.5l8.59 8.59a2 2 0 0 1 0 2.83z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>'),
            'store'        => array('label' => __('Store / Location', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"/><path d="M4 9v11a1 1 0 0 0 1 1h3v-6h8v6h3a1 1 0 0 0 1-1V9"/><path d="M3 9h18"/></svg>'),
            'home'         => array('label' => __('Home', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>'),
            'users'        => array('label' => __('Users / Team', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'),
            'phone'        => array('label' => __('Phone', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>'),
            'mail'         => array('label' => __('Mail', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>'),
            'map-pin'      => array('label' => __('Map Pin', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'),
            'truck'        => array('label' => __('Delivery / Truck', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>'),
            'wrench'       => array('label' => __('Service / Wrench', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.6 5.6l-6 6a2 2 0 1 0 2.8 2.8l6-6a4 4 0 0 0 5.6-5.6l-2.5 2.5-2-2z"/></svg>'),
            'shield'       => array('label' => __('Shield / Warranty', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/></svg>'),
            'star'         => array('label' => __('Star / Featured', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'),
            'heart'        => array('label' => __('Heart / Favorites', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>'),
            'gift'         => array('label' => __('Gift / Offers', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4"/><rect x="4" y="12" width="16" height="9"/><path d="M12 8v13M12 8C10.5 5.5 7 4 5.5 5.5S6.5 8 12 8zM12 8c1.5-2.5 5-4 6.5-2.5S17.5 8 12 8z"/></svg>'),
            'clock'        => array('label' => __('Clock / Hours', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>'),
            'download'     => array('label' => __('Download', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>'),
            'upload'       => array('label' => __('Upload', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>'),
            'link'         => array('label' => __('Link', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>'),
            'image'        => array('label' => __('Image / Gallery', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>'),
            'camera'       => array('label' => __('Camera', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>'),
            'credit-card'  => array('label' => __('Payments / Card', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'),
            'dollar-sign'  => array('label' => __('Pricing / Dollar', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5.5a4 4 0 0 0-3.5-2H10a3.5 3.5 0 0 0 0 7h4a3.5 3.5 0 0 1 0 7H9.5a4 4 0 0 1-3.5-2"/></svg>'),
            'package'      => array('label' => __('Package / Inventory', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8L12 3 3 8v8l9 5 9-5z"/><path d="M3 8l9 5 9-5"/><path d="M12 13v8"/></svg>'),
            'refresh'      => array('label' => __('Refresh / Sync', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>'),
            'filter'       => array('label' => __('Filter', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>'),
            'list'         => array('label' => __('List', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>'),
            'grid'         => array('label' => __('Grid / Categories', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'),
            'bell'         => array('label' => __('Notifications / Bell', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>'),
            'lock'         => array('label' => __('Lock / Secure', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>'),
            'thumbs-up'    => array('label' => __('Thumbs Up', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>'),
            'award'        => array('label' => __('Award / Certified', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>'),
            'zap'          => array('label' => __('Zap / Fast', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>'),
            'monitor'      => array('label' => __('Monitor / POS', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>'),
            'printer'      => array('label' => __('Printer / Receipt', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>'),
            'database'     => array('label' => __('Database', 'aaapos-prime'), 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>'),
        );
    }
}

if (!function_exists('aaapos_get_nav_icon_svg')) {
    function aaapos_get_nav_icon_svg($key) {
        $icons = aaapos_nav_icon_choices();
        return isset($icons[$key]) ? $icons[$key]['svg'] : '';
    }
}

if (!function_exists('aaapos_get_nav_icon_custom_url')) {
    /**
     * nav_item_icon_custom_{ID} stores an attachment ID (set via
     * WP_Customize_Media_Control in inc/customizer/nav-menu-items.php),
     * not a raw URL - resolve it to a displayable URL here.
     */
    function aaapos_get_nav_icon_custom_url($item_id) {
        $attachment_id = (int) get_theme_mod("nav_item_icon_custom_{$item_id}", 0);

        if (!$attachment_id) {
            return '';
        }

        $url = wp_get_attachment_image_url($attachment_id, 'thumbnail');

        return $url ? $url : (string) wp_get_attachment_url($attachment_id);
    }
}

if (!function_exists('aaapos_get_nav_menu_columns')) {
    /**
     * Reads the configured column count for a top-level dropdown,
     * clamped to a sane range. Default matches the original hardcoded
     * 3-column grid.
     */
    function aaapos_get_nav_menu_columns($top_level_item_id) {
        $columns = (int) get_theme_mod("nav_menu_columns_{$top_level_item_id}", 3);
        return max(1, min(5, $columns));
    }
}

if (!function_exists('aaapos_get_nav_menu_style')) {
    /**
     * Reads the configured dropdown style ('icon' or 'image') for a
     * top-level item, set via Customizer > Menu Options > [Parent] >
     * Style. 'icon' is the original behavior (curated SVG or a small
     * custom-uploaded icon, via nav_item_icon_{ID}). 'image' swaps the
     * per-item icon picker for a larger image upload
     * (nav_item_image_{ID}), rendered bigger/differently via
     * assets/css/components/header/_dropdowns-image-style.css.
     */
    function aaapos_get_nav_menu_style($top_level_item_id) {
        $style = get_theme_mod("nav_menu_style_{$top_level_item_id}", 'icon');
        return ('image' === $style) ? 'image' : 'icon';
    }
}

if (!function_exists('aaapos_get_nav_item_image_url')) {
    /**
     * nav_item_image_{ID} stores an attachment ID (Image Style only,
     * set via WP_Customize_Media_Control in
     * inc/customizer/nav-menu-items.php) - kept separate from
     * nav_item_icon_custom_{ID} so switching a dropdown between Icon
     * Style and Image Style never clobbers the other style's upload.
     */
    function aaapos_get_nav_item_image_url($item_id) {
        $attachment_id = (int) get_theme_mod("nav_item_image_{$item_id}", 0);

        if (!$attachment_id) {
            return '';
        }

        $url = wp_get_attachment_image_url($attachment_id, 'medium');

        return $url ? $url : (string) wp_get_attachment_url($attachment_id);
    }
}

if (!class_exists('AAAPOS_Nav_Walker')) {
    class AAAPOS_Nav_Walker extends Walker_Nav_Menu {

        /**
         * ID of the top-level (depth 0) item currently being descended
         * into. Set in start_el(), read back in start_lvl() - which WP
         * core calls for a parent's children immediately after that
         * parent's start_el(), so this is always correct for the
         * dropdown about to open (see class-wp-walker.php
         * Walker::display_element()).
         */
        protected $current_top_level_id = 0;

        /**
         * Renders each menu item.
         * Depth 0 = top-level bar items (untouched, default behavior).
         * Depth 1+ = items inside a dropdown - can show icon + description.
         */
        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            if (0 === $depth) {
                $this->current_top_level_id = $item->ID;
            }

            $classes   = empty($item->classes) ? array() : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;

            $menu_style     = ($depth > 0) ? aaapos_get_nav_menu_style($this->current_top_level_id) : 'icon';
            $is_image_style = ($depth > 0 && 'image' === $menu_style);

            $icon       = ($depth > 0 && !$is_image_style) ? get_theme_mod("nav_item_icon_{$item->ID}", '') : '';
            $desc       = $depth > 0 ? get_theme_mod("nav_item_desc_{$item->ID}", '') : '';
            $custom_url = ($depth > 0 && !$is_image_style && 'custom' === $icon) ? aaapos_get_nav_icon_custom_url($item->ID) : '';
            $image_url  = $is_image_style ? aaapos_get_nav_item_image_url($item->ID) : '';

            // "Custom"/Image Style with nothing uploaded yet renders nothing
            // visually, so don't flag the item as rich for that
            // half-configured state.
            $icon_renders = $is_image_style
                ? !empty($image_url)
                : (!empty($icon) && ('custom' !== $icon || !empty($custom_url)));

            if ($depth > 0 && ($icon_renders || !empty($desc))) {
                $classes[] = 'menu-item-rich';
                if ($is_image_style) {
                    $classes[] = 'menu-item-rich--image-style';
                }
            }

            $class_names = implode(' ', array_filter(array_map('sanitize_html_class', $classes)));

            $output .= '<li id="menu-item-' . esc_attr($item->ID) . '" class="' . esc_attr($class_names) . '">';

            $attributes  = ' href="' . esc_url($item->url) . '"';
            $attributes .= $item->target ? ' target="' . esc_attr($item->target) . '"' : '';
            $attributes .= $item->attr_title ? ' title="' . esc_attr($item->attr_title) . '"' : '';

            $item_output = '<a' . $attributes . '>';

            if ($depth > 0 && ($icon_renders || !empty($desc))) {
                // Rich mega-menu style: icon + title + description
                $item_output .= '<span class="menu-item-rich__inner">';

                if ($icon_renders) {
                    if ($is_image_style) {
                        $item_output .= '<span class="menu-item-rich__icon menu-item-rich__icon--image-style"><img src="' . esc_url($image_url) . '" alt="" loading="lazy"></span>';
                    } elseif ('custom' === $icon) {
                        $item_output .= '<span class="menu-item-rich__icon menu-item-rich__icon--custom"><img src="' . esc_url($custom_url) . '" alt="" loading="lazy"></span>';
                    } else {
                        $svg = aaapos_get_nav_icon_svg($icon);
                        if ($svg) {
                            $item_output .= '<span class="menu-item-rich__icon">' . $svg . '</span>';
                        }
                    }
                }

                $item_output .= '<span class="menu-item-rich__text">';
                $item_output .= '<span class="menu-item-rich__title">' . esc_html($item->title) . '</span>';

                if (!empty($desc)) {
                    $item_output .= '<span class="menu-item-rich__desc">' . esc_html($desc) . '</span>';
                }

                $item_output .= '</span>'; // .menu-item-rich__text
                $item_output .= '</span>'; // .menu-item-rich__inner
            } else {
                // Plain link (top-level items, or sub-items with no icon/desc set)
                $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            }

            $item_output .= '</a>';

            $output .= $item_output;
        }

        /**
         * Opens a sub-menu <ul>. Only first-level dropdowns (depth 0,
         * i.e. children of a top-level bar item) get the configurable
         * --sub-menu-columns custom property; third-level flyouts
         * (depth 1+) fall back to the walker default so they're
         * unaffected by column settings.
         */
        public function start_lvl(&$output, $depth = 0, $args = null) {
            $style_attr = '';

            if (0 === $depth && $this->current_top_level_id) {
                $columns = aaapos_get_nav_menu_columns($this->current_top_level_id);
                $style_attr = ' style="--sub-menu-columns: ' . (int) $columns . ';"';
            }

            $output .= '<ul class="sub-menu"' . $style_attr . '>';
        }
    }
}