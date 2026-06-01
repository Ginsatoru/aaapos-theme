<?php
/**
 * Serial Number Required Field
 *
 * @package AAAPOS_Prime
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// ADMIN SETTINGS
// ============================================================================

add_action( 'admin_menu', function() {
    if ( class_exists( 'WooCommerce' ) ) {
        add_submenu_page( 'woocommerce', __( 'Serial Number Settings', 'aaapos-prime' ), __( 'Serial Number', 'aaapos-prime' ), 'manage_woocommerce', 'aaapos-serial-number', 'aaapos_sn_render_settings_page', 99 );
    }
}, 99 );

add_action( 'admin_init', function() {
    $fields = [ 'aaapos_sn_field_label', 'aaapos_sn_placeholder', 'aaapos_sn_error_message', 'aaapos_sn_description' ];
    foreach ( $fields as $field ) {
        register_setting( 'aaapos_sn_settings', $field, [ 'sanitize_callback' => 'sanitize_text_field', 'default' => $field === 'aaapos_sn_field_label' ? __( 'Serial Number', 'aaapos-prime' ) : ( $field === 'aaapos_sn_placeholder' ? __( 'Enter serial number', 'aaapos-prime' ) : ( $field === 'aaapos_sn_error_message' ? __( 'Please enter a serial number before adding this product to your cart.', 'aaapos-prime' ) : '' ) ) ] );
    }
    register_setting( 'aaapos_sn_settings', 'aaapos_sn_min_length', [ 'sanitize_callback' => 'absint', 'default' => 0 ] );
    register_setting( 'aaapos_sn_settings', 'aaapos_sn_max_length', [ 'sanitize_callback' => 'absint', 'default' => 0 ] );
});

function aaapos_sn_render_settings_page() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) return;
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Serial Number Settings', 'aaapos-prime' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'aaapos_sn_settings' ); ?>
            <table class="form-table">
                <tr><th><label><?php esc_html_e( 'Field label', 'aaapos-prime' ); ?></label></th><td><input type="text" name="aaapos_sn_field_label" value="<?php echo esc_attr( get_option( 'aaapos_sn_field_label', __( 'Serial Number', 'aaapos-prime' ) ) ); ?>" class="regular-text" /></td></tr>
                <tr><th><label><?php esc_html_e( 'Placeholder', 'aaapos-prime' ); ?></label></th><td><input type="text" name="aaapos_sn_placeholder" value="<?php echo esc_attr( get_option( 'aaapos_sn_placeholder', __( 'Enter serial number', 'aaapos-prime' ) ) ); ?>" class="regular-text" /></td></tr>
                <tr><th><label><?php esc_html_e( 'Description', 'aaapos-prime' ); ?></label></th><td><input type="text" name="aaapos_sn_description" value="<?php echo esc_attr( get_option( 'aaapos_sn_description', '' ) ); ?>" class="regular-text" /></td></tr>
                <tr><th><label><?php esc_html_e( 'Error message', 'aaapos-prime' ); ?></label></th><td><input type="text" name="aaapos_sn_error_message" value="<?php echo esc_attr( get_option( 'aaapos_sn_error_message', __( 'Please enter a serial number before adding this product to your cart.', 'aaapos-prime' ) ) ); ?>" class="large-text" /></td></tr>
                <tr><th><label><?php esc_html_e( 'Min length', 'aaapos-prime' ); ?></label></th><td><input type="number" name="aaapos_sn_min_length" value="<?php echo esc_attr( get_option( 'aaapos_sn_min_length', 0 ) ); ?>" min="0" class="small-text" /></td></tr>
                <tr><th><label><?php esc_html_e( 'Max length', 'aaapos-prime' ); ?></label></th><td><input type="number" name="aaapos_sn_max_length" value="<?php echo esc_attr( get_option( 'aaapos_sn_max_length', 0 ) ); ?>" min="0" class="small-text" /></td></tr>
            </table>
            <?php submit_button( __( 'Save Settings', 'aaapos-prime' ) ); ?>
        </form>
        <?php aaapos_sn_render_product_picker(); ?>
    </div>
    <?php
}

function aaapos_sn_render_product_picker() {
    $products = get_posts( [ 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC', 'fields' => 'ids' ] );
    if ( empty( $products ) ) return;
    
    $product_data = [];
    $required_ids = [];
    
    foreach ( $products as $pid ) {
        $p = wc_get_product( $pid );
        if ( ! $p ) continue;
        $is_required = get_post_meta( $pid, '_aaapos_require_serial_number', true ) === 'yes';
        if ( $is_required ) {
            $required_ids[] = $pid;
        }
        $product_data[] = [ 
            'id' => $pid, 
            'title' => $p->get_name(), 
            'sku' => $p->get_sku() ?: '—',
            'type' => $p->get_type(),
            'enabled' => $is_required
        ];
    }
    ?>
    <div class="aaapos-sn-picker-wrapper">
        <style>
            .aaapos-sn-picker-wrapper {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 8px;
                padding: 24px;
                margin-top: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .aaapos-sn-picker-header {
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 2px solid #f0f0f1;
            }
            .aaapos-sn-picker-header h2 {
                margin: 0 0 8px 0;
                font-size: 18px;
                font-weight: 600;
            }
            .aaapos-sn-picker-header p {
                margin: 0;
                color: #646970;
            }
            .aaapos-sn-search-section {
                margin-bottom: 30px;
            }
            .aaapos-sn-search-box {
                position: relative;
                margin-bottom: 16px;
            }
            .aaapos-sn-search-box input {
                width: 100%;
                padding: 12px 16px 12px 40px;
                border: 1px solid #ccd0d4;
                border-radius: 6px;
                font-size: 14px;
                transition: all 0.2s;
            }
            .aaapos-sn-search-box input:focus {
                border-color: #2271b1;
                box-shadow: 0 0 0 1px #2271b1;
                outline: none;
            }
            .aaapos-sn-search-box .dashicons {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #8c8f94;
            }
            .aaapos-sn-products-list {
                max-height: 300px;
                overflow-y: auto;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                background: #f9fafb;
            }
            .aaapos-sn-product-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 16px;
                border-bottom: 1px solid #e5e7eb;
                transition: background 0.15s;
                cursor: pointer;
            }
            .aaapos-sn-product-item:hover {
                background: #f0f0f1;
            }
            .aaapos-sn-product-item:last-child {
                border-bottom: none;
            }
            .aaapos-sn-product-info {
                flex: 1;
            }
            .aaapos-sn-product-title {
                font-weight: 600;
                margin-bottom: 4px;
                color: #1e1e1e;
            }
            .aaapos-sn-product-meta {
                font-size: 12px;
                color: #6c757d;
            }
            .aaapos-sn-add-btn {
                background: #2271b1;
                color: white;
                border: none;
                padding: 6px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
                font-weight: 500;
                transition: background 0.15s;
            }
            .aaapos-sn-add-btn:hover {
                background: #135e96;
            }
            .aaapos-sn-add-btn:disabled {
                background: #a7aaad;
                cursor: not-allowed;
            }
            .aaapos-sn-picked-section {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid #f0f0f1;
            }
            .aaapos-sn-picked-header {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                margin-bottom: 16px;
            }
            .aaapos-sn-picked-header h3 {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
            }
            .aaapos-sn-picked-count {
                font-size: 13px;
                color: #6c757d;
            }
            .aaapos-sn-picked-list {
                background: #f8f9fa;
                border-radius: 6px;
                min-height: 100px;
            }
            .aaapos-sn-picked-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 16px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                margin-bottom: 8px;
                transition: all 0.15s;
            }
            .aaapos-sn-picked-item:hover {
                border-color: #2271b1;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .aaapos-sn-picked-item-info {
                flex: 1;
            }
            .aaapos-sn-picked-item-title {
                font-weight: 600;
                margin-bottom: 4px;
                color: #1e1e1e;
            }
            .aaapos-sn-picked-item-meta {
                font-size: 12px;
                color: #6c757d;
            }
            .aaapos-sn-remove-btn {
                background: #dc3545;
                color: white;
                border: none;
                padding: 6px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
                font-weight: 500;
                transition: background 0.15s;
            }
            .aaapos-sn-remove-btn:hover {
                background: #c82333;
            }
            .aaapos-sn-empty-message {
                padding: 40px;
                text-align: center;
                color: #8c8f94;
                background: white;
                border-radius: 6px;
                border: 1px dashed #ccd0d4;
            }
            .aaapos-sn-loading {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #2271b1;
                border-radius: 50%;
                animation: spin 0.5s linear infinite;
                margin-left: 8px;
                vertical-align: middle;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .aaapos-sn-toast {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #32373c;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 9999;
                display: none;
                animation: slideIn 0.3s ease;
            }
            .aaapos-sn-toast.success {
                background: #00a32a;
            }
            .aaapos-sn-toast.error {
                background: #dc3545;
            }
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>

        <div class="aaapos-sn-picker-header">
            <h2><?php esc_html_e( 'Assign Products', 'aaapos-prime' ); ?></h2>
            <p><?php esc_html_e( 'Search for products and add them to the list. Products requiring a serial number will be saved instantly.', 'aaapos-prime' ); ?></p>
        </div>

        <div class="aaapos-sn-search-section">
            <div class="aaapos-sn-search-box">
                <span class="dashicons dashicons-search"></span>
                <input type="text" id="aaapos-sn-search-input" placeholder="<?php esc_attr_e( 'Search by product name or SKU...', 'aaapos-prime' ); ?>" autocomplete="off">
            </div>
            <div class="aaapos-sn-products-list" id="aaapos-sn-products-list">
                <?php foreach ( $product_data as $row ) : 
                    $is_picked = in_array( $row['id'], $required_ids );
                    if ( $is_picked ) continue;
                ?>
                    <div class="aaapos-sn-product-item" data-id="<?php echo esc_attr( $row['id'] ); ?>" data-title="<?php echo esc_attr( $row['title'] ); ?>" data-sku="<?php echo esc_attr( $row['sku'] ); ?>" data-type="<?php echo esc_attr( $row['type'] ); ?>">
                        <div class="aaapos-sn-product-info">
                            <div class="aaapos-sn-product-title"><?php echo esc_html( $row['title'] ); ?></div>
                            <div class="aaapos-sn-product-meta">SKU: <?php echo esc_html( $row['sku'] ); ?> · <?php echo esc_html( ucfirst( $row['type'] ) ); ?></div>
                        </div>
                        <button type="button" class="aaapos-sn-add-btn"><?php esc_html_e( 'Add', 'aaapos-prime' ); ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="aaapos-sn-picked-section">
            <div class="aaapos-sn-picked-header">
                <h3><?php esc_html_e( 'Products Requiring Serial Number', 'aaapos-prime' ); ?></h3>
                <span class="aaapos-sn-picked-count" id="aaapos-sn-picked-count"><?php echo count( $required_ids ); ?> <?php esc_html_e( 'products', 'aaapos-prime' ); ?></span>
            </div>
            <div class="aaapos-sn-picked-list" id="aaapos-sn-picked-list">
                <?php if ( empty( $required_ids ) ) : ?>
                    <div class="aaapos-sn-empty-message">
                        <?php esc_html_e( 'No products selected. Search and add products above.', 'aaapos-prime' ); ?>
                    </div>
                <?php else : ?>
                    <?php foreach ( $product_data as $row ) : 
                        if ( ! in_array( $row['id'], $required_ids ) ) continue;
                    ?>
                        <div class="aaapos-sn-picked-item" data-id="<?php echo esc_attr( $row['id'] ); ?>">
                            <div class="aaapos-sn-picked-item-info">
                                <div class="aaapos-sn-picked-item-title"><?php echo esc_html( $row['title'] ); ?></div>
                                <div class="aaapos-sn-picked-item-meta">SKU: <?php echo esc_html( $row['sku'] ); ?> · <?php echo esc_html( ucfirst( $row['type'] ) ); ?></div>
                            </div>
                            <button type="button" class="aaapos-sn-remove-btn"><?php esc_html_e( 'Remove', 'aaapos-prime' ); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="aaapos-sn-toast" class="aaapos-sn-toast"></div>

    <script>
    jQuery(function($) {
        var allProducts = <?php echo json_encode( $product_data ); ?>;
        var requiredIds = <?php echo json_encode( $required_ids ); ?>;
        var $searchInput = $('#aaapos-sn-search-input');
        var $productsList = $('#aaapos-sn-products-list');
        var $pickedList = $('#aaapos-sn-picked-list');
        var $pickedCount = $('#aaapos-sn-picked-count');
        var $toast = $('#aaapos-sn-toast');
        var toastTimer;
        
        function showToast(message, type) {
            clearTimeout(toastTimer);
            $toast.removeClass('success error').addClass(type).text(message).fadeIn();
            toastTimer = setTimeout(function() { $toast.fadeOut(); }, 3000);
        }
        
        function renderProductsList() {
            var searchTerm = $searchInput.val().toLowerCase().trim();
            var filtered = allProducts.filter(function(p) {
                return !requiredIds.includes(p.id) && 
                       (searchTerm === '' || p.title.toLowerCase().includes(searchTerm) || p.sku.toLowerCase().includes(searchTerm));
            });
            
            if (filtered.length === 0) {
                $productsList.html('<div class="aaapos-sn-empty-message" style="margin:0;"><?php esc_html_e( 'No products found', 'aaapos-prime' ); ?></div>');
                return;
            }
            
            var html = '';
            filtered.forEach(function(p) {
                html += '<div class="aaapos-sn-product-item" data-id="' + p.id + '" data-title="' + escapeHtml(p.title) + '" data-sku="' + escapeHtml(p.sku) + '" data-type="' + escapeHtml(p.type) + '">' +
                            '<div class="aaapos-sn-product-info">' +
                                '<div class="aaapos-sn-product-title">' + escapeHtml(p.title) + '</div>' +
                                '<div class="aaapos-sn-product-meta">SKU: ' + escapeHtml(p.sku) + ' · ' + escapeHtml(p.type.charAt(0).toUpperCase() + p.type.slice(1)) + '</div>' +
                            '</div>' +
                            '<button type="button" class="aaapos-sn-add-btn"><?php esc_html_e( 'Add', 'aaapos-prime' ); ?></button>' +
                        '</div>';
            });
            $productsList.html(html);
        }
        
        function renderPickedList() {
            var picked = allProducts.filter(function(p) { return requiredIds.includes(p.id); });
            $pickedCount.text(picked.length + ' <?php esc_html_e( 'products', 'aaapos-prime' ); ?>');
            
            if (picked.length === 0) {
                $pickedList.html('<div class="aaapos-sn-empty-message"><?php esc_html_e( 'No products selected. Search and add products above.', 'aaapos-prime' ); ?></div>');
                return;
            }
            
            var html = '';
            picked.forEach(function(p) {
                html += '<div class="aaapos-sn-picked-item" data-id="' + p.id + '">' +
                            '<div class="aaapos-sn-picked-item-info">' +
                                '<div class="aaapos-sn-picked-item-title">' + escapeHtml(p.title) + '</div>' +
                                '<div class="aaapos-sn-picked-item-meta">SKU: ' + escapeHtml(p.sku) + ' · ' + escapeHtml(p.type.charAt(0).toUpperCase() + p.type.slice(1)) + '</div>' +
                            '</div>' +
                            '<button type="button" class="aaapos-sn-remove-btn"><?php esc_html_e( 'Remove', 'aaapos-prime' ); ?></button>' +
                        '</div>';
            });
            $pickedList.html(html);
        }
        
        function escapeHtml(str) {
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        function saveProduct(productId, enabled) {
            return $.post(ajaxurl, {
                action: 'aaapos_sn_toggle_product',
                product_id: productId,
                enabled: enabled ? 1 : 0,
                nonce: '<?php echo wp_create_nonce( 'aaapos_sn_toggle' ); ?>'
            });
        }
        
        $productsList.on('click', '.aaapos-sn-add-btn', function() {
            var $btn = $(this);
            var $item = $btn.closest('.aaapos-sn-product-item');
            var productId = parseInt($item.data('id'));
            var productTitle = $item.data('title');
            
            $btn.prop('disabled', true).html('<?php esc_html_e( 'Adding...', 'aaapos-prime' ); ?>');
            
            saveProduct(productId, true).done(function() {
                requiredIds.push(productId);
                renderProductsList();
                renderPickedList();
                showToast('✓ ' + productTitle + ' added - serial number now required', 'success');
            }).fail(function() {
                showToast('✗ Failed to add ' + productTitle, 'error');
            }).always(function() {
                $btn.prop('disabled', false).html('<?php esc_html_e( 'Add', 'aaapos-prime' ); ?>');
            });
        });
        
        $pickedList.on('click', '.aaapos-sn-remove-btn', function() {
            var $btn = $(this);
            var $item = $btn.closest('.aaapos-sn-picked-item');
            var productId = parseInt($item.data('id'));
            var product = allProducts.find(function(p) { return p.id === productId; });
            var productTitle = product ? product.title : '';
            
            $btn.prop('disabled', true).html('<?php esc_html_e( 'Removing...', 'aaapos-prime' ); ?>');
            
            saveProduct(productId, false).done(function() {
                requiredIds = requiredIds.filter(function(id) { return id !== productId; });
                renderProductsList();
                renderPickedList();
                showToast('✓ ' + productTitle + ' removed - serial number no longer required', 'success');
            }).fail(function() {
                showToast('✗ Failed to remove ' + productTitle, 'error');
            }).always(function() {
                $btn.prop('disabled', false).html('<?php esc_html_e( 'Remove', 'aaapos-prime' ); ?>');
            });
        });
        
        $searchInput.on('input', function() {
            renderProductsList();
        });
        
        renderProductsList();
    });
    </script>
    <?php
}

add_action( 'wp_ajax_aaapos_sn_toggle_product', function() {
    check_ajax_referer( 'aaapos_sn_toggle', 'nonce' );
    if ( ! current_user_can( 'manage_woocommerce' ) ) wp_send_json_error();
    $product_id = absint( $_POST['product_id'] ?? 0 );
    $enabled = ! empty( $_POST['enabled'] ) && $_POST['enabled'] == '1';
    if ( $product_id && get_post( $product_id ) ) {
        update_post_meta( $product_id, '_aaapos_require_serial_number', $enabled ? 'yes' : 'no' );
        wp_send_json_success();
    }
    wp_send_json_error();
});

// ============================================================================
// PRODUCT DATA TAB
// ============================================================================

add_filter( 'woocommerce_product_data_tabs', function( $tabs ) {
    $tabs['aaapos_serial_number'] = [ 'label' => __( 'Serial Number', 'aaapos-prime' ), 'target' => 'aaapos_sn_product_data', 'priority' => 80 ];
    return $tabs;
});

add_action( 'woocommerce_product_data_panels', function() {
    global $post;
    woocommerce_wp_checkbox( [ 'id' => '_aaapos_require_serial_number', 'label' => __( 'Require serial number', 'aaapos-prime' ), 'value' => get_post_meta( $post->ID, '_aaapos_require_serial_number', true ), 'cbvalue' => 'yes' ] );
});

add_action( 'woocommerce_process_product_meta', function( $post_id ) {
    update_post_meta( $post_id, '_aaapos_require_serial_number', isset( $_POST['_aaapos_require_serial_number'] ) ? 'yes' : 'no' );
});

// ============================================================================
// FRONTEND
// ============================================================================

add_action( 'woocommerce_before_add_to_cart_button', function() {
    global $product;
    if ( ! $product || get_post_meta( $product->get_id(), '_aaapos_require_serial_number', true ) !== 'yes' ) return;
    
    $label = get_option( 'aaapos_sn_field_label', __( 'Serial Number', 'aaapos-prime' ) );
    $placeholder = get_option( 'aaapos_sn_placeholder', __( 'Enter serial number', 'aaapos-prime' ) );
    $description = get_option( 'aaapos_sn_description', '' );
    $min = (int) get_option( 'aaapos_sn_min_length', 0 );
    $max = (int) get_option( 'aaapos_sn_max_length', 0 );
    ?>
    <div class="aaapos-serial-number-field">
        <label for="aaapos_serial_number"><?php echo esc_html( $label ); ?> <span class="required">*</span></label>
        <input type="text" id="aaapos_serial_number" name="aaapos_serial_number" placeholder="<?php echo esc_attr( $placeholder ); ?>" <?php echo $min ? 'minlength="' . $min . '"' : ''; ?> <?php echo $max ? 'maxlength="' . $max . '"' : ''; ?> />
        <?php if ( $description ) echo '<p class="description">' . wp_kses_post( $description ) . '</p>'; ?>
    </div>
    <?php
});

add_filter( 'woocommerce_add_to_cart_validation', function( $passed, $product_id ) {
    if ( get_post_meta( $product_id, '_aaapos_require_serial_number', true ) !== 'yes' ) return $passed;
    
    $serial = trim( sanitize_text_field( $_POST['aaapos_serial_number'] ?? '' ) );
    $error = get_option( 'aaapos_sn_error_message', __( 'Please enter a serial number before adding this product to your cart.', 'aaapos-prime' ) );
    $min = (int) get_option( 'aaapos_sn_min_length', 0 );
    $max = (int) get_option( 'aaapos_sn_max_length', 0 );
    
    if ( empty( $serial ) ) {
        wc_add_notice( $error, 'error' );
        return false;
    }
    if ( $min && mb_strlen( $serial ) < $min ) {
        wc_add_notice( sprintf( __( 'Serial number must be at least %d characters.', 'aaapos-prime' ), $min ), 'error' );
        return false;
    }
    if ( $max && mb_strlen( $serial ) > $max ) {
        wc_add_notice( sprintf( __( 'Serial number must be no more than %d characters.', 'aaapos-prime' ), $max ), 'error' );
        return false;
    }
    return $passed;
}, 10, 2 );

add_filter( 'woocommerce_add_cart_item_data', function( $cart_item_data, $product_id ) {
    if ( get_post_meta( $product_id, '_aaapos_require_serial_number', true ) === 'yes' && ! empty( $_POST['aaapos_serial_number'] ) ) {
        $cart_item_data['aaapos_serial_number'] = sanitize_text_field( $_POST['aaapos_serial_number'] );
        $cart_item_data['unique_key'] = md5( $product_id . $_POST['aaapos_serial_number'] . microtime() );
    }
    return $cart_item_data;
}, 10, 2 );

add_filter( 'woocommerce_get_item_data', function( $item_data, $cart_item ) {
    if ( ! empty( $cart_item['aaapos_serial_number'] ) ) {
        $item_data[] = [ 'key' => get_option( 'aaapos_sn_field_label', __( 'Serial Number', 'aaapos-prime' ) ), 'value' => wc_clean( $cart_item['aaapos_serial_number'] ) ];
    }
    return $item_data;
}, 10, 2 );

add_action( 'woocommerce_checkout_create_order_line_item', function( $item, $cart_item_key, $values ) {
    if ( ! empty( $values['aaapos_serial_number'] ) ) {
        $item->add_meta_data( get_option( 'aaapos_sn_field_label', __( 'Serial Number', 'aaapos-prime' ) ), sanitize_text_field( $values['aaapos_serial_number'] ), true );
    }
}, 10, 3 );

// ============================================================================
// SHOP/ARCHIVE - Replace Add to Cart with Select Options
// ============================================================================

add_filter( 'woocommerce_loop_add_to_cart_link', function( $html, $product ) {
    if ( get_post_meta( $product->get_id(), '_aaapos_require_serial_number', true ) === 'yes' ) {
        return sprintf( '<a href="%s" class="button">%s</a>', esc_url( $product->get_permalink() ), esc_html__( 'Select Options', 'aaapos-prime' ) );
    }
    return $html;
}, 10, 2 );