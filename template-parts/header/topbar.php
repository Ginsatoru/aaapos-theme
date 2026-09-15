<?php
/**
 * Top bar component
 * Displays contact info, a rotating live-update slider, and social links (topbar.php)
 * 
 * @package AAAPOS
 */

if (!get_theme_mod('show_top_bar', true)) {
    return; // Don't render if disabled
}

$phone = get_theme_mod('topbar_phone', '+61 3 8400 3083');
$email = get_theme_mod('topbar_email', 'support@aaapos.com');

// Live update announcements (rotating slider), managed via Customizer drag & drop.
// NOTE: If the user removes all announcements in the Customizer, this stays an
// empty array on purpose — we do NOT fall back to defaults, so the slider
// correctly disappears when intentionally cleared.
$announcements_json = get_theme_mod('topbar_announcements', wp_json_encode(mr_default_topbar_announcements()));
$announcements = json_decode($announcements_json, true);

if (!is_array($announcements)) {
    $announcements = [];
}

// Static promo text (right side, not part of the slider) — fully customizable.
$promo_text = get_theme_mod('topbar_promo_text', 'New Home of MYOB RetailManager');

// Check if there's anything at all to show, so we can skip rendering an empty bar.
$has_contact = $phone || $email;
$has_announcements = !empty($announcements);
$has_promo = !empty($promo_text);

$social_links = array(
    'facebook' => get_theme_mod('facebook_url', ''),
    'instagram' => get_theme_mod('instagram_url', ''),
    'twitter' => get_theme_mod('twitter_url', ''),
);
$has_social = !empty(array_filter($social_links));

// If literally everything is empty, don't render the top bar at all.
if (!$has_contact && !$has_announcements && !$has_promo && !$has_social) {
    return;
}
?>

<div class="top-bar">
    <div class="container">
        <div class="top-bar-inner">
            
            <!-- Left Side: Contact Info -->
            <?php if ($has_contact) : ?>
                <div class="top-bar-left">
                    <?php if ($phone) : ?>
                        <a href="tel:<?php echo esc_attr(str_replace(' ', '', $phone)); ?>" class="top-bar-item">
                            <svg width="16" height="16" viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M94.811,21.696c-35.18,22.816-42.091,94.135-28.809,152.262c10.344,45.266,32.336,105.987,69.42,163.165
                                    c34.886,53.79,83.557,102.022,120.669,129.928c47.657,35.832,115.594,58.608,150.774,35.792
                                    c17.789-11.537,44.218-43.058,45.424-48.714c0,0-15.498-23.896-18.899-29.14l-51.972-80.135
                                    c-3.862-5.955-28.082-0.512-40.386,6.457c-16.597,9.404-31.882,34.636-31.882,34.636c-11.38,6.575-20.912,0.024-40.828-9.142
                                    c-24.477-11.262-51.997-46.254-73.9-77.947c-20.005-32.923-40.732-72.322-41.032-99.264c-0.247-21.922-2.341-33.296,8.304-41.006
                                    c0,0,29.272-3.666,44.627-14.984c11.381-8.392,26.228-28.286,22.366-34.242l-51.972-80.134c-3.401-5.244-18.899-29.14-18.899-29.14
                                    C152.159-1.117,112.6,10.159,94.811,21.696z"/>
                            </svg>
                            <span><?php echo esc_html($phone); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($email) : ?>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="top-bar-item top-bar-item--email">
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            <span><?php echo esc_html($email); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Center: Live Update Slider (only if announcements actually exist) -->
            <?php if ($has_announcements) : ?>
                <div class="top-bar-announce" data-items='<?php echo esc_attr(wp_json_encode($announcements)); ?>' data-interval="5000">
                    <svg class="announce-icon" width="15" height="15" viewBox="0 0 207.238 207.238" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M197.032,167.624c-0.461,2.344-2.517,4.035-4.906,4.035H15.112c-2.39,0-4.445-1.691-4.906-4.035
                            c-0.461-2.345,0.802-4.688,3.013-5.593c0.114-0.047,12.24-5.128,22.93-16c9.53-9.692,14.163-31.721,14.163-67.348
                            c0-30.658,12.725-45.569,23.398-52.679c3.934-2.62,7.961-4.449,11.742-5.738c-0.071-0.653-0.113-1.315-0.113-1.987
                            c0-10.08,8.2-18.28,18.28-18.28s18.28,8.201,18.28,18.28c0,0.672-0.042,1.333-0.113,1.987c3.781,1.288,7.809,3.118,11.743,5.739
                            c10.674,7.11,23.396,22.021,23.396,52.677c0,35.625,4.633,57.655,14.163,67.348c10.767,10.949,22.81,15.951,22.931,16
                            C196.23,162.936,197.493,165.279,197.032,167.624z M121.826,179.03H85.412c-2.762,0-5,2.239-5,5
                            c0,12.797,10.41,23.208,23.207,23.208s23.207-10.411,23.207-23.208C126.826,181.269,124.588,179.03,121.826,179.03z"/>
                    </svg>
                    <div class="announce-viewport" aria-live="polite">
                        <a href="<?php echo esc_url($announcements[0]['url']); ?>" class="announce-link">
                            <span class="announce-text"><?php echo esc_html($announcements[0]['text']); ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Right Side: Static Promo Text & Social Links -->
            <?php if ($has_promo || $has_social) : ?>
                <div class="top-bar-right">
                    <?php if ($has_promo) : ?>
                        <div class="promo-text">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12,0C5.373,0,0,5.373,0,12s5.373,12,12,12s12-5.373,12-12S18.627,0,12,0z M12,19.66
                                    c-0.938,0-1.58-0.723-1.58-1.66c0-0.964,0.669-1.66,1.58-1.66c0.963,0,1.58,0.696,1.58,1.66C13.58,18.938,12.963,19.66,12,19.66z
                                    M12.622,13.321c-0.239,0.815-0.992,0.829-1.243,0c-0.289-0.956-1.316-4.585-1.316-6.942c0-3.11,3.891-3.125,3.891,0
                                    C13.953,8.75,12.871,12.473,12.622,13.321z"/>
                            </svg>
                            <span><?php echo esc_html($promo_text); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_social) : ?>
                        <div class="social-links">
                            <?php
                            foreach ($social_links as $platform => $url) :
                                if (!$url) continue;

                                $icons = array(
                                    'facebook' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
                                    'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>',
                                    'twitter' => '<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>',
                                );
                                ?>
                                <a href="<?php echo esc_url($url); ?>" 
                                   class="social-link" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <?php echo $icons[$platform]; ?>
                                    </svg>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php if ($has_announcements) : ?>
<script>
(function () {
    var el = document.querySelector('.top-bar-announce');
    if (!el) return;

    var items;
    try {
        items = JSON.parse(el.getAttribute('data-items'));
    } catch (e) {
        return;
    }
    if (!items || items.length <= 1) return;

    var holdTime = parseInt(el.getAttribute('data-interval'), 10) || 5000;
    var typeSpeed = 28;
    var eraseSpeed = 16;
    var link = el.querySelector('.announce-link');
    var text = el.querySelector('.announce-text');
    var index = 0;
    var timer = null;
    var running = true;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function setLink(i) {
        link.href = items[i].url;
    }

    function typeText(str, cb) {
        var i = 0;
        (function step() {
            if (!running) return;
            if (i <= str.length) {
                text.textContent = str.slice(0, i);
                i++;
                timer = setTimeout(step, typeSpeed);
            } else if (cb) {
                cb();
            }
        })();
    }

    function eraseText(str, cb) {
        var i = str.length;
        (function step() {
            if (!running) return;
            if (i >= 0) {
                text.textContent = str.slice(0, i);
                i--;
                timer = setTimeout(step, eraseSpeed);
            } else if (cb) {
                cb();
            }
        })();
    }

    function scheduleNext() {
        timer = setTimeout(function () {
            eraseText(items[index].text, function () {
                index = (index + 1) % items.length;
                setLink(index);
                typeText(items[index].text, scheduleNext);
            });
        }, holdTime);
    }

    if (reduceMotion) {
        var plainTimer = setInterval(function () {
            index = (index + 1) % items.length;
            setLink(index);
            text.textContent = items[index].text;
        }, holdTime);

        el.addEventListener('mouseenter', function () { clearInterval(plainTimer); });
        el.addEventListener('mouseleave', function () {
            plainTimer = setInterval(function () {
                index = (index + 1) % items.length;
                setLink(index);
                text.textContent = items[index].text;
            }, holdTime);
        });
        return;
    }

    setLink(index);
    scheduleNext();

    el.addEventListener('mouseenter', function () {
        running = false;
        clearTimeout(timer);
    });
    el.addEventListener('mouseleave', function () {
        running = true;
        scheduleNext();
    });
})();
</script>
<?php endif; ?>