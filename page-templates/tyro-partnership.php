<?php
/**
 * Template Name: Tyro Partnership Page
 */

add_filter( 'body_class', function( $classes ) {
    $classes[] = 'tyro-fullwidth';
    return $classes;
});

get_header();
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap" rel="stylesheet">

<div id="tyro-page" class="tyro-pg">

    <section class="tp-hero">
        <div class="tp-hero__bg-grid"></div>
        <div class="tp-hero__glow tp-hero__glow--left"></div>
        <div class="tp-hero__glow tp-hero__glow--right"></div>

        <div class="tp-container">

            <div class="tp-hero__logos" aria-label="AAAPOS partnering with Tyro">
                <div class="tp-hero__logo-wrap">
                    <img
                    src="https://www.aaapos.com/wp-content/uploads/aaapos-rm.png"
                        alt="AAAPOS Logo"
                        class="tp-hero__logo"
                        onerror="this.outerHTML='<span class=\'tp-logo-fallback\'>AAAPOS</span>'"
                    >
                </div>
                <div class="tp-hero__x" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <line x1="14" y1="2" x2="14" y2="26" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        <line x1="2" y1="14" x2="26" y2="14" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="tp-hero__logo-wrap">
                    <img
                    src="https://www.aaapos.com/wp-content/uploads/tyro-1.png"
                        alt="Tyro Logo"
                        class="tp-hero__logo"
                        onerror="this.outerHTML='<span class=\'tp-logo-fallback\'>Tyro</span>'"
                    >
                </div>
            </div>

            <h1 class="tp-hero__title">
                Payment Changes &amp;
                <span class="tp-hero__title-accent">EFTPOS Integration</span> Update
            </h1>

            <p class="tp-hero__sub">
                Important updates for Australian businesses on credit card surcharging regulations
                and a new EFTPOS partnership that simplifies your payment setup.
            </p>

            <div class="tp-hero__date-pill">
                <span style="display:flex;align-items:center;">
                    <img src="https://www.aaapos.com/wp-content/uploads/bell-1.gif" alt="" width="22" height="22" style="display:block;">
                </span>
                Effective <strong>1 October 2026</strong> Act before the deadline
            </div>

        </div>
    </section>

    <section class="tp-section tp-section--grey">
        <div class="tp-container">

            <div class="tp-partner-grid">

                <div class="tp-section-head">
                    <span class="tp-chip">New Partnership</span>
                    <h2 class="tp-sec-title">AAAPOS Partners with Tyro for Seamless EFTPOS Integration</h2>
                    <p class="tp-lead">AAAPOS is pleased to announce a new partnership with Tyro, a leading EFTPOS provider fully integrated with RetailManager. Unlike traditional setups requiring coordination between your bank, middleware like Linkly, and hardware vendors, Tyro offers a streamlined all-in-one solution.</p>
                </div>

                <div class="tp-feat-grid">

                    <div class="tp-feat-card">
                        <div class="tp-feat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <div class="tp-feat-label">Faster Setup</div>
                            <div class="tp-feat-desc">Quick integration, no multi-vendor coordination needed</div>
                        </div>
                    </div>

                    <div class="tp-feat-card">
                        <div class="tp-feat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <div class="tp-feat-label">Fewer Failure Points</div>
                            <div class="tp-feat-desc">Single provider means less can go wrong</div>
                        </div>
                    </div>

                    <div class="tp-feat-card">
                        <div class="tp-feat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="tp-feat-label">Reduced Downtime</div>
                            <div class="tp-feat-desc">Reliable uptime with a purpose-built EFTPOS system</div>
                        </div>
                    </div>

                    <div class="tp-feat-card">
                        <div class="tp-feat-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <div class="tp-feat-label">Faster Support</div>
                            <div class="tp-feat-desc">One point of contact for all issue resolution</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="tp-section">
        <div class="tp-container">

            <div class="tp-section-head">
                <span class="tp-chip tp-chip--amber">Regulatory Change</span>
                <h2 class="tp-sec-title">Upcoming Change to Credit Card Surcharging</h2>
                <p class="tp-lead">Businesses that currently pass on card surcharges must review and adjust their pricing strategy before this date.</p>
            </div>

            <div class="tp-date-banner">
                <div class="tp-date-pill">
                    <img src="https://www.aaapos.com/wp-content/uploads/bell-1.gif" alt="" width="36" height="36" style="display:block;">
                </div>
                <div class="tp-date-text">
                    <strong>From 1 October 2026, surcharging on credit cards will no longer be permitted</strong>
                    <p>The ban applies to consumer credit card surcharges. Businesses must absorb costs or reprice before this deadline.</p>
                </div>
            </div>

            <div class="tp-opt-row">
                <div class="tp-opt-card">
                    <div class="tp-opt-num">01</div>
                    <div class="tp-opt-title">Absorb the Cost</div>
                    <div class="tp-opt-desc">Cover card processing fees within your existing business margins going forward.</div>
                </div>
                <div class="tp-opt-card">
                    <div class="tp-opt-num">02</div>
                    <div class="tp-opt-title">Adjust Pricing</div>
                    <div class="tp-opt-desc">Build card processing costs into your product or service pricing before October.</div>
                </div>
            </div>

        </div>
    </section>

    <section class="tp-section tp-section--grey">
        <div class="tp-container">

            <div class="tp-section-head">
                <span class="tp-chip">Cost Breakdown</span>
                <h2 class="tp-sec-title">Understanding Payment Processing Fees</h2>
                <p class="tp-lead">Typical processing fees vary by card type. Here's what most Australian businesses can expect to pay.</p>
            </div>

            <div class="tp-cost-grid">

                <div class="tp-table-wrap">
                    <table class="tp-table">
                        <thead>
                            <tr>
                                <th>Card Type</th>
                                <th>Typical Rate</th>
                                <th>Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tp-table__type">EFTPOS</td>
                                <td>Less than 0.5%</td>
                                <td><span class="tp-badge tp-badge--low">Low</span></td>
                            </tr>
                            <tr>
                                <td class="tp-table__type">Visa &amp; Mastercard Debit</td>
                                <td>0.5% to 1.0%</td>
                                <td><span class="tp-badge tp-badge--mid">Moderate</span></td>
                            </tr>
                            <tr>
                                <td class="tp-table__type">Visa &amp; Mastercard Credit</td>
                                <td>1.0% to 1.5%</td>
                                <td><span class="tp-badge tp-badge--mid">Moderate</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tp-flat-stack">
                    <p class="tp-flat-label">Some providers may charge higher flat rates:</p>
                    <div class="tp-flat-card">
                        <span>Standard flat rate per transaction</span>
                        <strong>1.6%</strong>
                    </div>
                    <div class="tp-flat-card">
                        <span>Card-not-present transactions</span>
                        <strong>2.2%</strong>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="tp-section">
        <div class="tp-container">
            <div class="tp-rec-box">
                <div>
                    <span class="tp-rec-chip">Our Recommendation</span>
                    <h2 class="tp-rec-title">Consider a Cost + 30 Basis Points Model</h2>
                    <p class="tp-rec-desc">Based on our work with Tyro, this pricing model ensures you pay closer to the true cost of each transaction rather than a flat rate, which can significantly reduce your overall payment costs.</p>
                </div>
                <div class="tp-rec-highlight">
                    <div class="tp-rec-rate">Cost + 0.30%</div>
                    <div class="tp-rec-rate-desc">Per transaction, transparent and fair</div>
                </div>
            </div>
        </div>
    </section>

    <section class="tp-section tp-section--grey">
        <div class="tp-container">

            <div class="tp-rba-grid">

                <div class="tp-section-head">
                    <span class="tp-chip">RBA Update</span>
                    <h2 class="tp-sec-title">Industry Update</h2>
                    <p class="tp-lead">These changes follow updates from the Reserve Bank of Australia (RBA), designed to improve conditions for Australian businesses and consumers across the board.</p>
                    <a href="https://www.rba.gov.au/payments-and-infrastructure/review-of-retail-payments-regulation/about.html" target="_blank" rel="noopener noreferrer" class="tp-rba-learn-more">
                        Learn More
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <div class="tp-rba-cards">
                    <div class="tp-rba-card">
                        <span class="tp-rba-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="4" y2="4"/></svg>
                        </span>
                        <span>Remove surcharging on certain card types</span>
                    </div>
                    <div class="tp-rba-card">
                        <span class="tp-rba-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        </span>
                        <span>Reduce interchange fees across the industry</span>
                    </div>
                    <div class="tp-rba-card">
                        <span class="tp-rba-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </span>
                        <span>Improve pricing transparency for merchants</span>
                    </div>
                    <div class="tp-rba-card">
                        <span class="tp-rba-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <span>Increase competition between payment providers</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="tp-section tp-cta">
    <div class="tp-container">
        <div class="tp-cta__inner">

            <!-- Left: content -->
            <div class="tp-cta__content">
                <h2 class="tp-cta__title">Need Help Navigating These Changes?</h2>
                <p class="tp-cta__sub">AAAPOS is here to support you. We can review your current setup and help you find the most cost-effective solution for your business.</p>
                <div class="tp-cta__btns">
                    <a href="mailto:support@aaapos.com" class="tp-cta__btn">
                        support@aaapos.com
                    </a>
                    <a href="tel:1300555115" class="tp-cta__btn tp-cta__btn--outline">
                        1300 555 115
                    </a>
                </div>
            </div>

            <!-- Right: mockup -->
            <div class="tp-cta__mockup-wrap">
                <img
                    src="https://www.aaapos.com/wp-content/uploads/aaapos-mockup.png"
                    alt="AAAPOS mockup"
                    class="tp-cta__mockup"
                >
            </div>

        </div>
    </div>
</section>

</div><!-- /#tyro-page -->

<script>
(function(){

    /* ── Word-slice: wraps each word in overflow:hidden so it reveals upward from clip ── */
    function sliceWords(el, baseDelay) {
        if (el.querySelector('.tp-word-wrap')) return;
        var words = el.innerText.trim().split(/\s+/);
        el.innerHTML = words.map(function(word, i){
            return '<span class="tp-word-wrap"><span class="tp-word" style="transition-delay:' + (baseDelay + i * 0.07) + 's">' + word + '</span></span>';
        }).join(' ');
    }

    /* ── 1. Word-slice titles ── */
    document.querySelectorAll('.tp-sec-title, .tp-rec-title, .tp-cta__title').forEach(function(el){
        sliceWords(el, 0.05);
    });

    /* ── 2. Slide-up for body text and supporting elements ── */
    document.querySelectorAll([
        '.tp-lead',
        '.tp-rec-desc',
        '.tp-opt-num',
        '.tp-opt-title',
        '.tp-opt-desc',
        '.tp-cta__sub',
        '.tp-cta__btn',
        '.tp-cta__note',
        '.tp-flat-label',
        '.tp-rba-learn-more',
        '.tp-rec-highlight',
        '.tp-date-text'
    ].join(',')).forEach(function(el){
        el.classList.add('tp-anim-slide-up');
    });

    /* ── 3. Drop-in for chips ── */
    document.querySelectorAll('.tp-chip, .tp-rec-chip').forEach(function(el){
        el.classList.add('tp-anim-drop');
    });

    /* ── 4. Fade-up for table and date banner ── */
    document.querySelectorAll('.tp-date-banner, .tp-table-wrap').forEach(function(el){
        el.classList.add('tp-anim-fade-up');
    });

    /* ── 5. Stagger non-word-slice elements within each section ── */
    document.querySelectorAll('.tp-section, .tp-hero').forEach(function(section){
        var els = section.querySelectorAll('.tp-anim-slide-up, .tp-anim-drop, .tp-anim-fade-up');
        els.forEach(function(el, i){
            el.style.transitionDelay = (0.1 + i * 0.14) + 's';
        });
    });

    /* ── 6. After each card animates in, swap to hover-only transitions ── */
    document.querySelectorAll('.tp-feat-card, .tp-opt-card, .tp-rba-card, .tp-flat-card').forEach(function(card){
        card.addEventListener('transitionend', function onDone(e){
            /* Wait for the opacity transition to finish (last to complete) */
            if (e.propertyName === 'opacity') {
                card.classList.add('tp-animated');
                card.removeEventListener('transitionend', onDone);
            }
        });
    });

    /* ── 7. Observe sections — add tp-in to trigger all animations ── */
    var targets = document.querySelectorAll('.tp-section, .tp-hero');
    if (!targets.length) return;

    var observer = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
            if (entry.isIntersecting) {
                entry.target.classList.add('tp-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(function(el){ observer.observe(el); });

})();
</script>

<?php get_footer(); ?>