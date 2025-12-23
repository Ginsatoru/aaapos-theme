<?php
/**
 * The header for our theme (header.php) - MODERN VERSION WITH ANIMATIONS
 * 
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package Macedon_Ranges
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo("charset"); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <!-- Skip to content link for accessibility -->
    <a class="skip-link sr-only" href="#main">
        <?php esc_html_e("Skip to content", "macedon-ranges"); ?>
    </a>

    <?php
    if (get_theme_mod("show_top_bar", true)) {
        get_template_part("template-parts/header/topbar");
    }
    ?>

    <!-- Main Header -->
    <header id="masthead" class="site-header" role="banner">
        <div class="container">
            <div class="header-inner">
                
                <!-- Site Branding / Logo - ANIMATED -->
                <div class="site-branding" data-animate="fade-right" data-animate-duration="normal">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <a href="<?php echo esc_url(home_url("/")); ?>" class="site-logo" rel="home">
                            <h1><?php bloginfo("name"); ?></h1>
                        </a>
                        <?php
                    }
                    ?>
                </div>

                <!-- Primary Navigation -->
                <nav id="site-navigation" class="main-navigation" role="navigation">
                    
                    <?php if (get_theme_mod("show_search_bar", true)): ?>
                    <div class="mobile-search-wrapper">
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url("/")); ?>">
                            <label for="mobile-search-input" class="sr-only">
                                <?php esc_html_e("Search for:", "macedon-ranges"); ?>
                            </label>
                            <input 
                                type="search" 
                                id="mobile-search-input"
                                class="search-field" 
                                placeholder="<?php esc_attr_e("Search products...", "macedon-ranges"); ?>" 
                                value="<?php echo get_search_query(); ?>" 
                                name="s"
                            />
                            <button type="submit" class="search-submit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (class_exists("WooCommerce")): ?>
                        <div class="mobile-account-section">
                            <?php if (is_user_logged_in()):
                                $current_user = wp_get_current_user();
                                $display_name = $current_user->display_name;
                                $first_name = $current_user->user_firstname;
                                $username = !empty($first_name) ? $first_name : $display_name;
                                ?>
                                <a href="<?php echo esc_url(get_permalink(get_option("woocommerce_myaccount_page_id"))); ?>" class="mobile-account-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <div class="mobile-account-info">
                                        <span class="mobile-account-name"><?php echo esc_html($username); ?></span>
                                        <span class="mobile-account-status"><?php esc_html_e("View Account", "macedon-ranges"); ?></span>
                                    </div>
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                                
                                <ul class="mobile-account-menu">
                                    <li>
                                        <a href="<?php echo esc_url(wc_get_account_endpoint_url("orders")); ?>">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                            <?php esc_html_e("My Orders", "macedon-ranges"); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url(wc_get_account_endpoint_url("edit-address")); ?>">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            <?php esc_html_e("Addresses", "macedon-ranges"); ?>
                                        </a>
                                    </li>
                                    <li class="menu-divider"></li>
                                    <li>
                                        <a href="<?php echo esc_url(wc_get_account_endpoint_url("customer-logout")); ?>">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                                <polyline points="16 17 21 12 16 7"/>
                                                <line x1="21" y1="12" x2="9" y2="12"/>
                                            </svg>
                                            <?php esc_html_e("Logout", "macedon-ranges"); ?>
                                        </a>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <a href="<?php echo esc_url(get_permalink(get_option("woocommerce_myaccount_page_id"))); ?>" class="mobile-account-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <div class="mobile-account-info">
                                        <span class="mobile-account-name"><?php esc_html_e("Sign In", "macedon-ranges"); ?></span>
                                        <span class="mobile-account-status"><?php esc_html_e("Login or Register", "macedon-ranges"); ?></span>
                                    </div>
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    if (has_nav_menu("primary")) {
                        wp_nav_menu([
                            "theme_location" => "primary",
                            "menu_id" => "primary-menu",
                            "menu_class" => "nav-menu",
                            "container" => "div",
                            "container_id" => "primary-menu-container",
                            "container_class" => "menu-container",
                            "fallback_cb" => false,
                            "depth" => 3,
                        ]);
                    } else {
                        echo '<div id="primary-menu-container" class="menu-container">';
                        echo '<ul id="primary-menu" class="nav-menu">';
                        echo '<li><a href="' . esc_url(home_url("/")) . '">' . esc_html__("Home", "macedon-ranges") . "</a></li>";
                        if (class_exists("WooCommerce")) {
                            echo '<li><a href="' . esc_url(get_permalink(wc_get_page_id("shop"))) . '">' . esc_html__("Shop", "macedon-ranges") . "</a></li>";
                        }
                        echo '<li><a href="' . esc_url(home_url("/about")) . '">' . esc_html__("About", "macedon-ranges") . "</a></li>";
                        echo '<li><a href="' . esc_url(home_url("/contact")) . '">' . esc_html__("Contact", "macedon-ranges") . "</a></li>";
                        echo "</ul>";
                        echo "</div>";
                    }
                    ?>
                </nav>

                <!-- Header Actions -->
                <div class="header-actions">
                    
                    <?php if (get_theme_mod("show_search_bar", true)): ?>
                    <div class="header-search-bar" data-animate="zoom-in" data-animate-duration="normal" data-animate-delay="200">
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url("/")); ?>">
                            <label for="header-search-input" class="sr-only">
                                <?php esc_html_e("Search for:", "macedon-ranges"); ?>
                            </label>
                            <input 
                                type="search" 
                                id="header-search-input"
                                class="search-field" 
                                placeholder="<?php esc_attr_e("Search...", "macedon-ranges"); ?>" 
                                value="<?php echo get_search_query(); ?>" 
                                name="s"
                            />
                            <button type="submit" class="search-submit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <?php if (class_exists("WooCommerce")): ?>
                        
                        <?php if (is_user_logged_in()): ?>
                            <!-- LOGGED IN: Show Account with Dropdown -->
                            <div class="header-account-wrapper" data-animate="fade-left" data-animate-duration="normal" data-animate-delay="300">
                                <a href="<?php echo esc_url(get_permalink(get_option("woocommerce_myaccount_page_id"))); ?>" 
                                   class="header-action account-link">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <?php
                                    $current_user = wp_get_current_user();
                                    $display_name = $current_user->display_name;
                                    $first_name = $current_user->user_firstname;
                                    $username = !empty($first_name) ? $first_name : $display_name;
                                    ?>
                                    <span class="account-username"><?php echo esc_html($username); ?></span>
                                </a>
                                
                                <!-- Account Dropdown Menu -->
                                <div class="account-dropdown">
                                    <div class="account-dropdown-header">
                                        <span class="account-name"><?php echo esc_html($current_user->display_name); ?></span>
                                        <span class="account-email"><?php echo esc_html($current_user->user_email); ?></span>
                                    </div>
                                    <ul class="account-dropdown-menu">
                                        <li>
                                            <a href="<?php echo esc_url(wc_get_account_endpoint_url("dashboard")); ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                                </svg>
                                                <?php esc_html_e("Dashboard", "macedon-ranges"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(wc_get_account_endpoint_url("orders")); ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                                <?php esc_html_e("Orders", "macedon-ranges"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(wc_get_account_endpoint_url("edit-address")); ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                    <circle cx="12" cy="10" r="3"/>
                                                </svg>
                                                <?php esc_html_e("Addresses", "macedon-ranges"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url(wc_get_account_endpoint_url("edit-account")); ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                <?php esc_html_e("Account Details", "macedon-ranges"); ?>
                                            </a>
                                        </li>
                                        <li class="account-dropdown-divider"></li>
                                        <li>
                                            <a href="<?php echo esc_url(wc_get_account_endpoint_url("customer-logout")); ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                                    <polyline points="16 17 21 12 16 7"/>
                                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                                </svg>
                                                <?php esc_html_e("Logout", "macedon-ranges"); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- NOT LOGGED IN: Show Login Button with Icon -->
                            <div class="header-auth-buttons" data-animate="fade-left" data-animate-duration="normal" data-animate-delay="300">
                                <a href="<?php echo esc_url(get_permalink(get_option("woocommerce_myaccount_page_id"))); ?>" class="btn-auth btn-login">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span><?php esc_html_e("Login", "macedon-ranges"); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Shopping Cart with Dropdown -->
                        <div class="header-cart-wrapper" data-animate="fade-left" data-animate-duration="normal" data-animate-delay="400">
                            <?php
                            $cart_count = WC()->cart->get_cart_contents_count();
                            $cart_style = get_theme_mod("cart_icon_style", "icon-count");
                            ?>
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" 
                               class="header-action cart-link cart-style-<?php echo esc_attr($cart_style); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="8" cy="21" r="1"/>
                                    <circle cx="19" cy="21" r="1"/>
                                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                                </svg>
                                
                                <?php if ($cart_style === "icon-count" || $cart_style === "icon-total"): ?>
                                    <span class="cart-count"<?php echo $cart_count === 0 ? ' style="display:none;"' : ""; ?>>
                                        <?php echo esc_html($cart_count); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($cart_style === "icon-total"): ?>
                                    <span class="cart-total">
                                        <?php echo WC()->cart->get_cart_subtotal(); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            
                            <!-- Cart Dropdown -->
                            <div class="cart-dropdown">
                                <div class="cart-dropdown-header">
                                    <h3><?php esc_html_e("Shopping Cart", "macedon-ranges"); ?></h3>
                                    <span class="cart-item-count"><?php echo esc_html($cart_count); ?> <?php echo $cart_count === 1 ? esc_html__("item", "macedon-ranges") : esc_html__("items", "macedon-ranges"); ?></span>
                                </div>
                                <ul class="cart-dropdown-items">
                                    <?php if ($cart_count > 0):
                                        $cart_items = WC()->cart->get_cart();
                                        foreach ($cart_items as $cart_item_key => $cart_item):
                                            $_product = apply_filters("woocommerce_cart_item_product", $cart_item["data"], $cart_item, $cart_item_key);
                                            if ($_product && $_product->exists() && $cart_item["quantity"] > 0): ?>
                                        <li class="cart-dropdown-item">
                                            <a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>" class="cart-item-image">
                                                <?php echo wp_kses_post($_product->get_image("thumbnail")); ?>
                                            </a>
                                            <div class="cart-item-details">
                                                <a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>" class="cart-item-name">
                                                    <?php echo wp_kses_post($_product->get_name()); ?>
                                                </a>
                                                <div class="cart-item-meta">
                                                    <span class="cart-item-quantity"><?php echo esc_html($cart_item["quantity"]); ?> × </span>
                                                    <span class="cart-item-price"><?php echo WC()->cart->get_product_price($_product); ?></span>
                                                </div>
                                            </div>
                                            <button type="button" class="cart-item-remove" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </li>
                                    <?php endif;
                                        endforeach;
                                    else: ?>
                                        <li class="cart-dropdown-empty">
                                            <p><?php esc_html_e("Your cart is empty.", "macedon-ranges"); ?></p>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                                <div class="cart-dropdown-footer">
                                    <div class="cart-subtotal">
                                        <span><?php esc_html_e("Subtotal:", "macedon-ranges"); ?></span>
                                        <strong class="cart-subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
                                    </div>
                                    <div class="cart-dropdown-actions">
                                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn btn-secondary btn-block">
                                            <?php esc_html_e("View Cart", "macedon-ranges"); ?>
                                        </a>
                                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-primary btn-block">
                                            <?php esc_html_e("Checkout", "macedon-ranges"); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php endif; ?>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" 
                            aria-expanded="false"
                            aria-controls="primary-menu"
                            data-animate="fade-left" 
                            data-animate-duration="slow" 
                            data-animate-delay="300">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    
                </div>
                
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay"></div>

    <!-- Main Content Area Starts Here -->
    <main id="main" class="site-main">