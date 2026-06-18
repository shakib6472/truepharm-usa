# TruePharm USA — Theme Documentation

> Developer & client reference for the **TruePharm USA** custom WordPress / WooCommerce theme.
> Generated from the actual theme source — every hook, function, constant, and template below exists in the code.

**Version:** 1.0.0 | **WordPress:** 7.0 (requires ≥ 6.5) | **WooCommerce:** 10.8.1 (requires ≥ 8.0) | **PHP:** 8.0+

---

## 1. Theme Overview

| | |
|---|---|
| **Name** | TruePharm USA |
| **Version** | 1.0.0 |
| **Author** | TruePharm USA *(placeholder — edit in `style.css` header)* |
| **Text Domain** | `truepharm` |
| **Requires WP** | 6.5+ (tested to 7.0) |
| **Requires PHP** | 8.0+ |
| **WooCommerce** | 8.0+ (tested to 10.8.1) |

A pure custom theme built **without** page builders, CSS frameworks, or ACF. Custom meta is registered natively via `register_post_type()`, `register_post_meta()`, and `add_meta_box()`.

### Installation

1. In WP Admin go to **Appearance → Themes → Add New → Upload Theme**.
2. Upload `truepharm-usa.zip` and click **Install Now**, then **Activate**.
3. Go to **Settings → Permalinks** and click **Save Changes** (flushes rewrite rules so the COA archive and the rewards endpoint resolve). *Activation also flushes automatically, but a manual save is the safe fallback.*
4. Install & activate **WooCommerce** if not already active.
5. (Recommended) Visit **Appearance → Menus**, create a menu, and assign it to the **Primary Menu (slide-in panel)** location.

### What activation auto-creates

The `after_switch_theme` hook (`truepharm_activate()` in `inc/theme-activation.php`) runs once on activation and:

- **Flushes rewrite rules** (CPT archive + `rewards` account endpoint).
- **Creates DB tables** (via `dbDelta`): `{prefix}_tp_newsletter_emails`, `{prefix}_tp_contact_submissions`.
- **Sets WooCommerce options:** `woocommerce_coming_soon=no`, `woocommerce_enable_myaccount_registration=yes`, `woocommerce_registration_generate_password=no`, `woocommerce_registration_generate_username=yes`.
- **Schedules** the daily birthday cron (`tp_birthday_check`).
- **Creates & template-assigns pages** (idempotent): `why-us`, `faq`, `contact-us`, `rewards-program`, `legal`, `terms-of-use`, `privacy-policy`, `shipping-returns`, `compliance-statement`.

> Note: the `pa_vial_size` global product attribute is created on activation and self-heals on `admin_init` (`inc/woo-product-fields.php`). The newsletter/contact tables also self-heal on `after_setup_theme` if their version option is missing.

---

## 2. File Structure

```
truepharm-usa/
├── style.css                         Theme header + complete design system (CSS variables, all components)
├── functions.php                     Bootstrap: setup, includes loader, cart badge, WC wrappers, rewards endpoint, order-status helper
├── index.php                         Fallback template (blog/archive loop)
├── page.php                          Generic page template (renders WC pages bare, others in .wrap)
├── front-page.php                    Homepage (hero, credentials, categories, carousel, goals, rewards, newsletter)
├── header.php                        Top bar, sticky nav, utility icons, cart badge, slide-in menu, entrance-gate hook
├── footer.php                        4-column footer, legal disclaimer box, copyright, SSL badge
├── 404.php                           Styled 404 (sky bg, navy heading, search box)
├── search.php                        Search results grid (products + pages)
├── searchform.php                    Styled search form (matches .search-box)
├── archive-product.php               Shop ("Clinical Formulations"): sidebar filters + sort + product grid
├── single-product.php                Product page: gallery, vial-size pills, bundle box, tabs, related
├── archive-coa_library.php           COA Library archive: education panel, live search, data table
├── single-coa_library.php            Single COA: data summary, PDF iframe viewer, download bar
├── page-why-us.php                   Why Us: mission, standards, pipeline timeline, CTA banner
├── page-faq.php                      FAQ: sticky sidebar + 4 accordion groups
├── page-contact.php                  Contact: info block + AJAX form + Turnstile
├── page-rewards.php                  Rewards Program (marketing): how-it-works, ways-to-earn, referral
├── page-legal.php                    Legal parent + 4 policy pages (sidebar nav + the_content)
├── screenshot.png                    Theme preview image
├── assets/js/
│   ├── main.js                       Global: reveal, menu, cart, carousel, newsletter, FAQ, contact, copy buttons
│   ├── product.js                    Single product: variant pills, gallery swap, qty, tabs, bundle, add-to-cart
│   ├── rewards.js                    Account rewards tab: redeem AJAX
│   ├── coa-filter.js                 COA archive: live table filter
│   └── admin-coa.js                  Admin: COA PDF media uploader (wp.media)
├── inc/
│   ├── enqueue.php                   All script/style enqueues + localized JS data objects
│   ├── customizer.php                Customizer sections/settings (top bar, homepage, why-us, contact, footer)
│   ├── cpt-coa.php                   COA Library CPT, meta, meta box, save, admin columns
│   ├── woo-product-fields.php        Form field, Chemical Data tab, Storage tab, pa_vial_size, product card
│   ├── bundle-pricing.php            Quantity-based per-product bundle discounts
│   ├── rewards.php                   Full rewards points engine (constants, functions, earn/redeem, cron)
│   ├── newsletter.php                Newsletter table + AJAX subscribe
│   ├── contact-form.php              Contact table + AJAX submit + wp_mail
│   ├── turnstile.php                 Cloudflare Turnstile placeholder/live widget + verification + Customizer keys
│   ├── entrance-gate.php             Age/compliance modal (cookie-gated) + AJAX
│   └── theme-activation.php          One-time activation setup (pages, tables, options, cron)
└── woocommerce/
    ├── checkout/
    │   └── form-checkout.php          Restyled checkout (all WC hooks intact, Turnstile before Place Order)
    └── myaccount/
        ├── my-account.php             Dashboard layout wrapper (page-header + grid)
        ├── navigation.php             Themed .dash-nav sidebar with icons
        ├── form-login.php             Two-column login / register portal
        ├── dashboard.php              Overview: stat cards + recent orders
        ├── orders.php                 Order history (10/page, status tags, actions)
        ├── my-address.php             Billing/shipping address cards
        └── dashboard-rewards.php      Rewards tab: wallet, redeem, referral, ledger
```

Plus this file (`DOCUMENTATION.md`) and `documentation.html`.

---

## 3. Design System

### CSS Variables (`:root` in `style.css`)

| Variable | Value | Used for |
|---|---|---|
| `--bg` | `#ffffff` | Page background |
| `--bg-card` | `#ffffff` | Card / panel background |
| `--blue` | `#7fb8e8` | Light blue accents, metallic blue gradient stop |
| `--blue-deep` | `#2b7cc0` | Primary buttons (`.btn`), links, focus rings |
| `--navy` | `#15527e` | Headings, nav, navy buttons, prices |
| `--sky` | `#d6e8f9` | Sky section backgrounds, batch tags, page headers |
| `--sky-edge` | `#bcd9f3` | Sky borders / hover edges |
| `--warm` | `#d99a86` | Rose gold accents, active states, eyebrow text |
| `--warm-soft` | `#f7e4dc` | Pale rose-gold section backgrounds |
| `--slate` | `#1e293b` | Body text, top bar, footer background |
| `--slate-soft` | `#475569` | Secondary text |
| `--line` | `#e2e8f0` | Borders, dividers |
| `--radius` | `12px` | Standard border-radius |
| `--maxw` | `1200px` | Max content width (`.wrap`) |

### Typography

- **Montserrat** (weights 500–900) — headings (`h1`–`h5`), buttons, eyebrows, badges, stat values. Loaded from Google Fonts CDN.
- **Inter** (weights 400–600) — body copy, form fields, table cells. Loaded from Google Fonts CDN.

Both are enqueued in `inc/enqueue.php` (handle `truepharm-fonts`) with preconnect resource hints to `fonts.googleapis.com` / `fonts.gstatic.com`.

### Button Classes

| Class | Description |
|---|---|
| `.btn` | Base blue-deep button (uppercase Montserrat) |
| `.btn-cart` | Navy variant of `.btn` (hero CTA, "Get Referral Link") |
| `.btn-ghost` | Transparent bordered button |
| `.btn-white` | White button on dark backgrounds (CTA banner) |
| `.btn-navy` | Full-width navy submit ("Access Account" login) |
| `.btn-add` | Product-card add-to-cart / "View Formula" link |
| `.btn-view` | Small sky pill (dashboard / order actions) |
| `.btn-edit` | Address-card edit button |
| `.btn-pdf` | Rose-gold metallic COA button (archive "View COA") |
| `.btn-pdf.btn-pdf-lg` | Larger rose-gold COA download (single COA) |
| `.filter-btn` | Shop price-filter apply button |

### Metallic Button Classes

| Class | Visual | Used on |
|---|---|---|
| `.btn-buy` + `.metallic-text` | **Blue metallic** 3D plate, gradient navy→sky→navy, blue gradient text | Single product "Add to Cart" |
| `.btn-bundle` + `.metallic-text-warm` | **Rose gold / copper** 3D plate, white→copper text | Single product "Add Bundle to Cart" |
| `.btn-agree` + `.metallic-text-warm` | Rose gold plate | Entrance gate "I Agree" |
| `.btn-rosegold` | Rose gold metallic (copper sweep; peachy/white variant inside `.auth-card` & `.wallet-action`) | COA tab, register, redeem, contact submit |
| `.btn-exit` | Ghost/navy outline | Entrance gate "I Disagree" |
| `.best-value-badge` | Metallic rose-gold pill | Bundle "Best Value" (10-pack) |

### Animation Classes

| Class | Behavior |
|---|---|
| `.reveal` | Hidden + translated 24px down (initial state) |
| `.reveal.in` | Revealed (opacity 1, no transform) — added by IntersectionObserver in `main.js` |
| `.ring` / `.ring.r2–r4` | Hero pulsating rings, `@keyframes ringPulse` 6.5s loop, staggered delays |
| `.base-ring.b1–b3` | Static hero halo rings |
| `.glow` | Blurred radial hero glow |
| `@media (prefers-reduced-motion: reduce)` | Disables reveal + ring animations |

### Layout / Utility Classes

| Class | Purpose |
|---|---|
| `.wrap` | Centered max-width (1200px) container with 28px side padding |
| `.ph-img` | Diagonal-stripe placeholder image box |
| `.tag` | Small dashed "dev note" tag |
| `.eyebrow` | Uppercase rose-gold gradient label above headings |
| `.compliance-badge` | "Research Only" badge (absolute on cards; inline in product details) |
| `.breadcrumbs` | Breadcrumb trail |
| `.section-head` | Section heading row (title + "view all" link) |
| `.top-bar` | Slate compliance bar above nav |

---

## 4. Template Reference

| Template | Handles | Key functions | Customizer keys | Theme helpers |
|---|---|---|---|---|
| `front-page.php` | Site front page | `wc_get_products`, `get_custom_logo`, `wp_get_attachment_image`, `get_term_by`, `get_term_meta` | `truepharm_hero_mission`, `truepharm_goals_title`, `truepharm_goals_p1/p2`, `truepharm_goals_image` | `tp_get_molecular_class`, `tp_get_product_form`, `tp_rewards_signup_bonus`, `tp_rewards_points_per_dollar`, `tp_rewards_redeem_points`, `tp_rewards_redeem_value_display` |
| `header.php` | Global header | `has_custom_logo`, `the_custom_logo`, `wc_get_account_endpoint_url`, `wc_get_cart_url`, `wp_nav_menu` | `truepharm_topbar_left/center/right` | `truepharm_cart_badge_html`, `truepharm_primary_menu_fallback` |
| `footer.php` | Global footer | `has_custom_logo`, `wp_nav_menu`, `wp_date` | `truepharm_footer_tagline`, `truepharm_legal_disclaimer` | — |
| `archive-product.php` | Shop + product category archives | main loop, `wc_get_product`, `woocommerce_pagination`, `get_terms`, `woocommerce_output_all_notices` | — | `tp_product_card` |
| `single-product.php` | Single product (`/product/...`) | `wc_get_product`, `get_available_variations`, `wc_get_related_products`, `WP_Query` (COA match) | — | `tp_get_product_form`, `tp_product_card`, `TP_PF_*` consts |
| `archive-coa_library.php` | `/coa-library/` | main loop, `get_post_meta`, `the_posts_pagination` | — | `TP_COA_*` consts |
| `single-coa_library.php` | Single COA | `get_post_meta`, `wp_get_attachment_url`, `get_post_type_archive_link` | — | `TP_COA_*` consts |
| `page-why-us.php` | `/why-us/` | `wc_get_page_permalink`, `get_post_type_archive_link`, `wp_get_attachment_image` | `truepharm_why_heading`, `truepharm_why_p1/p2/p3`, `truepharm_why_step1–4`, `truepharm_why_image` | — |
| `page-faq.php` | `/faq/` | `get_post_type_archive_link` | — | — |
| `page-contact.php` | `/contact-us/` | `do_action('tp_turnstile_widget')` | `truepharm_contact_email/phone/address` | — |
| `page-rewards.php` | `/rewards-program/` | `wc_get_account_endpoint_url`, `wc_price`, `add_query_arg` | — | `tp_rewards_*` accessors, `tp_rewards_get_referral_code`, `tp_rewards_points_to_value`, `TP_REWARDS_*` consts |
| `page-legal.php` | `legal` + 4 policy pages | `get_page_by_path`, `the_content`, `get_the_modified_date` | — | — |
| `woocommerce/myaccount/my-account.php` | My Account wrapper | `wp_get_current_user`, `wc_logout_url`, `do_action('woocommerce_account_navigation'/'_content')` | — | — |
| `woocommerce/myaccount/navigation.php` | Account sidebar | `wc_get_account_menu_items`, `wc_get_account_menu_item_classes`, `wc_get_account_endpoint_url` | — | `tp_account_nav_icon` |
| `woocommerce/myaccount/form-login.php` | Login/Register | WC form hooks, `wp_nonce_field`, `do_action('tp_turnstile_widget')` | — | — |
| `woocommerce/myaccount/dashboard.php` | Overview tab | `wc_get_orders`, `wc_format_datetime`, `get_post_type_archive_link` | — | `tp_rewards_get_balance`, `truepharm_order_status_tag` |
| `woocommerce/myaccount/orders.php` | Orders tab | `wc_get_endpoint_url`, `wc_get_cart_url`, `wp_nonce_url` | — | `truepharm_order_status_tag` |
| `woocommerce/myaccount/my-address.php` | Addresses tab | `WC_Customer`, `wc_get_account_formatted_address`, `wc_get_endpoint_url` | — | — |
| `woocommerce/myaccount/dashboard-rewards.php` | Rewards tab (`rewards` endpoint) | — | — | `tp_rewards_get_balance/get_ledger/points_to_value/get_referral_code/redeem_value_display`, `TP_REWARDS_REDEEM_STEP/REFERRAL_*` |
| `woocommerce/checkout/form-checkout.php` | `/checkout/` | all `woocommerce_checkout_*` hooks | — | — |
| `404.php` | Not-found | `get_search_form`, `home_url` | — | — |
| `search.php` | `/?s=` | main loop, `wc_get_product`, `the_posts_pagination` | — | `tp_product_card` |

---

## 5. Custom Post Type: COA Library

Registered in `inc/cpt-coa.php` on `init` (`tp_register_coa_cpt`).

```php
register_post_type( 'coa_library', array(
    'public'        => true,
    'show_in_rest'  => false,
    'has_archive'   => true,
    'menu_icon'     => 'dashicons-clipboard',
    'menu_position' => 26,
    'rewrite'       => array( 'slug' => 'coa-library', 'with_front' => false ),
    'supports'      => array( 'title' ),   // Compound name = post title
) );
```

- **Archive URL:** `/coa-library/`
- **Single URL:** `/coa-library/{post-slug}/`

### Meta fields (`register_post_meta`, all `single`, `show_in_rest:false`)

| Constant | Meta key | Type | Sanitize |
|---|---|---|---|
| `TP_COA_BATCH` | `_coa_batch_number` | string | `sanitize_text_field` |
| `TP_COA_DATE` | `_coa_testing_date` | string (date `YYYY-MM-DD`) | `sanitize_text_field` |
| `TP_COA_PURITY` | `_coa_verified_purity` | string | `sanitize_text_field` |
| `TP_COA_PDF` | `_coa_pdf_file` | integer (attachment ID) | `absint` |

Meta box **"COA Details"** (`tp_render_coa_meta_box`) renders the four fields; the PDF field uses the WP media uploader (`assets/js/admin-coa.js` + `wp_enqueue_media`). Save handler `tp_save_coa_meta` (on `save_post_coa_library`) verifies nonce `tp_save_coa_meta` / field `tp_coa_nonce`, checks autosave + `edit_post` cap, and sanitizes.

### Admin columns (`tp_coa_columns`)

`Batch Number` · `Testing Date` · `Verified Purity` · `PDF` (shows **View PDF** link or **No PDF**). The **Testing Date** column is sortable (`tp_coa_sortable_columns` + `tp_coa_orderby` sets `meta_key=_coa_testing_date`, `orderby=meta_value`).

### Adding a COA (client steps)

1. **COA Library → Add New**.
2. **Title** = the compound name (e.g. *GHK-Cu*).
3. In the **COA Details** box, fill Batch Number, Testing Date (date picker), Verified Purity (e.g. `99.2%`).
4. Click **Select / Upload PDF** and choose/upload the lab report PDF.
5. **Publish**. It appears in the `/coa-library/` table and its single page shows the PDF viewer + download button.

---

## 6. WooCommerce Custom Fields

Defined in `inc/woo-product-fields.php` (native `register_post_meta` on `init`, saved on `woocommerce_process_product_meta` after verifying `woocommerce_meta_nonce`).

| Constant | Meta key | Label | Tab | Sanitize |
|---|---|---|---|---|
| `TP_PF_FORM` | `_tp_product_form` | Form (e.g. Lyophilized) | **General** | `sanitize_text_field` |
| `TP_PF_CAS` | `_tp_cas_number` | CAS Number | **Chemical Data** | `sanitize_text_field` |
| `TP_PF_FORMULA` | `_tp_molecular_formula` | Molecular Formula | **Chemical Data** | `sanitize_text_field` |
| `TP_PF_WEIGHT` | `_tp_molecular_weight` | Molecular Weight | **Chemical Data** | `sanitize_text_field` |
| `TP_PF_SEQUENCE` | `_tp_sequence` | Sequence | **Chemical Data** | `sanitize_textarea_field` |
| `TP_PF_PURITY` | `_tp_purity` | Purity (HPLC) | **Chemical Data** | `sanitize_text_field` |
| `TP_PF_STORAGE` | `_tp_storage_info` | Storage Information | **Storage & Handling** | `sanitize_textarea_field` |

- **Molecular Class** is **not** a custom field — it is the product's first `product_cat` term (`tp_get_molecular_class()`).
- The **Chemical Data** and **Storage & Handling** tabs are added via `woocommerce_product_data_tabs` and rendered via `woocommerce_product_data_panels`.

### Vial Size variants (`pa_vial_size`)

A **global attribute** `Vial Size` (taxonomy `pa_vial_size`) is auto-created (`tp_register_vial_size_attribute`). To sell per-size pricing:

1. Edit the product → **Product data → Variable product**.
2. **Attributes** tab → add **Vial Size**, enter values (e.g. `5mg | 10mg | 15mg`), check **Used for variations**.
3. **Variations** tab → generate variations, set a **price** per variation.

The single-product template renders variations as `.variant-btn` pills (`product.js`), updating the displayed price + the variation ID used for add-to-cart.

### Bundle pricing (`inc/bundle-pricing.php`)

Quantity-based, per product (variations grouped under their parent), applied on `woocommerce_before_calculate_totals` (`tp_apply_bundle_pricing`, priority 20). Price is recalculated from the stored `_price` meta to avoid compounding.

| Quantity of same product | Discount |
|---|---|
| 3–4 | **5% off** (`tp_bundle_discount_rate` returns `0.05`) |
| 5–9 | **10% off** (`0.10`) |
| 10+ | **20% off** (`0.20`) |

A cart-page notice (`tp_bundle_cart_notice` on `woocommerce_before_cart`) summarizes active discounts.

---

## 7. Rewards Engine (`inc/rewards.php`)

### Constants

| Constant | Value | Meaning |
|---|---|---|
| `TP_REWARDS_SIGNUP_POINTS` | `50` | Points awarded on account creation |
| `TP_REWARDS_POINTS_PER_DOLLAR` | `1` | Points per $1 of completed-order subtotal |
| `TP_REWARDS_REVIEW_POINTS` | `100` | Points for an approved product review (once per product) |
| `TP_REWARDS_BIRTHDAY_POINTS` | `200` | Annual birthday bonus |
| `TP_REWARDS_REFERRAL_POINTS` | `200` | Points to referrer when a referee registers |
| `TP_REWARDS_REFERRAL_DISCOUNT` | `20` | Marketing "$ off" figure shown for referrals |
| `TP_REWARDS_POINTS_VALUE` | `0.10` | Dollar value per point (100 pts = $10) |
| `TP_REWARDS_REDEEM_STEP` | `100` | Redemption increment / minimum |

### User meta keys

| Constant | Key | Type | Description |
|---|---|---|---|
| `TP_REWARDS_POINTS_KEY` | `tp_rewards_points` | int | Current balance |
| `TP_REWARDS_LEDGER_KEY` | `tp_rewards_ledger` | array | Transactions `[date, reason, points, balance]` |
| `TP_REWARDS_REFCODE_KEY` | `tp_rewards_referral_code` | string | Unique referral code |
| `TP_REWARDS_REFERRED_KEY` | `tp_rewards_referred_by` | int | Referrer user ID |
| `TP_REWARDS_BIRTHDAY_KEY` | `tp_rewards_birthday` | string `MM-DD` | Birthday |
| `TP_REWARDS_REVIEWED_KEY` | `tp_rewards_reviewed_products` | array | Product IDs already rewarded for review |
| `TP_REWARDS_BDAY_YEAR_KEY` | `tp_rewards_birthday_year` | int | Last year birthday points were granted |
| `TP_REWARDS_COOKIE` | `tp_referral_code` *(cookie)* | string | Referral code captured from `?ref=` |

### Functions

| Signature | Returns | Description |
|---|---|---|
| `tp_rewards_get_balance( int $user_id = 0 )` | `int` | Current balance (current user if 0) |
| `tp_rewards_add_points( int $user_id, int $points, string $reason )` | `bool` | Adds points, appends a ledger row, fires `tp_rewards_points_added` |
| `tp_rewards_deduct_points( int $user_id, int $points, string $reason )` | `bool` | Deducts if balance ≥ points; fires `tp_rewards_points_deducted`; `false` if insufficient |
| `tp_rewards_get_ledger( int $user_id = 0 )` | `array` | Full transaction ledger |
| `tp_rewards_points_to_value( int $points )` | `float` | `$points × 0.10`, rounded to 2 dp |
| `tp_rewards_generate_referral_code( int $user_id )` | `string` | `TPUSA-{UPPER first 5 of user_login}{ID}` |
| `tp_rewards_get_referral_code( int $user_id = 0 )` | `string` | Stored code (generates + saves on first call) |
| `tp_rewards_log()` *(internal)* | `void` | Appends a ledger entry |
| `tp_rewards_signup_bonus() / _points_per_dollar() / _redeem_points() / _redeem_value() / _redeem_value_display()` | mixed | Filterable accessors used by templates |

### Auto-earn triggers

| Hook | Function | When |
|---|---|---|
| `user_register` | `tp_rewards_on_register` | Awards signup points; assigns referral code; resolves `?ref` cookie → awards referrer `TP_REWARDS_REFERRAL_POINTS`, sets `tp_rewards_referred_by`, clears cookie |
| `woocommerce_order_status_completed` | `tp_rewards_on_order_completed` | Awards `floor(subtotal) × 1` (guarded once via `_tp_rewards_awarded` order meta) |
| `comment_post` (approved) | `tp_rewards_review_on_post` | Awards review points if product review & not already rewarded |
| `transition_comment_status` (→ approved) | `tp_rewards_review_on_status` | Same, for later-approved reviews |
| `tp_birthday_check` (daily cron) | `tp_rewards_birthday_check` | Awards birthday points if `MM-DD` matches & not yet awarded this year |

### Referral cookie

`tp_rewards_capture_referral` (on `init`) reads `$_GET['ref']`, sets the `tp_referral_code` cookie for 30 days. On registration the referrer is found via the `tp_rewards_referral_code` user meta.

### Redemption flow

`tp_rewards_redeem` (AJAX `tp_redeem_points`, **logged-in only**):
1. Verify nonce `tp_ajax`; require login.
2. Validate `points_to_redeem` is a multiple of `TP_REWARDS_REDEEM_STEP` (100), balance ≥ requested.
3. Create a `WC_Coupon`: code `TP-REDEEM-{USER_ID}-{timestamp}`, type `fixed_cart`, amount `tp_rewards_points_to_value()`, `individual_use`, `usage_limit=1`, `usage_limit_per_user=1`, expires +30 days, restricted to the user's email.
4. Deduct points (rolls back the coupon if deduction fails).
5. Return JSON `{ code, amount, balance, message }`.

### Birthday cron

Scheduled daily as `tp_birthday_check` (`tp_rewards_schedule_birthday_cron` on `init`). Checks all users with `tp_rewards_birthday`; compares to today's `m-d`; guards repeats with `tp_rewards_birthday_year`.

---

## 8. Action Hooks (`do_action`) — theme-defined

| Hook | File | When it fires | Purpose |
|---|---|---|---|
| `truepharm_entrance_gate` | header.php | After `wp_body_open` | Placeholder hook (the gate itself renders on `wp_footer`) |
| `tp_turnstile_widget` | functions.php (checkout), form-login.php, page-contact.php | Where a CAPTCHA should appear | Outputs the Turnstile widget or placeholder |
| `tp_rewards_points_added` | inc/rewards.php | After points are added | Extensibility (`$user_id, $points, $reason, $balance`) |
| `tp_rewards_points_deducted` | inc/rewards.php | After points are deducted | Extensibility |
| `tp_newsletter_subscribed` | inc/newsletter.php | After a subscriber is stored | Sync to an external email tool (`$email`) |
| `tp_contact_submitted` | inc/contact-form.php | After a contact message is saved + emailed | Extensibility (`$name, $email, $subject, $message`) |
| `tp_birthday_check` | scheduled (WP-Cron) | Daily | Runs the birthday award routine |

> Core WooCommerce hooks (`woocommerce_checkout_*`, `woocommerce_account_navigation`, `woocommerce_login_form`, etc.) are re-fired inside the overridden templates to preserve native behavior.

## 9. Filter Hooks (`add_filter`) — theme registrations

| Hook | File | Priority | Purpose |
|---|---|---|---|
| `get_custom_logo` | functions.php | 10 | Adds `.logo-img` class to the custom logo |
| `woocommerce_add_to_cart_fragments` | functions.php | 10 | Live cart-count badge fragment |
| `woocommerce_account_menu_items` | functions.php | 10 | Inserts the **TruePharm Rewards** menu item |
| `woocommerce_my_account_my_orders_query` | functions.php | 10 | 10 orders per page |
| `woocommerce_registration_errors` | functions.php | 10 | Requires the compliance checkbox |
| `woocommerce_registration_errors` | inc/turnstile.php | 10 | Verifies Turnstile on register |
| `wp_authenticate_user` | inc/turnstile.php | 10 | Verifies Turnstile on WC login |
| `tp_contact_form_validate` | inc/turnstile.php | 10 | Verifies Turnstile on contact |
| `woocommerce_product_data_tabs` | inc/woo-product-fields.php | 10 | Adds Chemical Data + Storage tabs |
| `wp_resource_hints` | inc/enqueue.php | 10 | Preconnect to Google Fonts |

**Theme-provided filters (for developers):** `tp_gate_should_show`, `tp_gate_exit_url` (entrance gate); `tp_rewards_signup_bonus`, `tp_rewards_points_per_dollar`, `tp_rewards_redeem_points` (rewards economics); `tp_contact_form_validate` (contact validation — receives/returns a `WP_Error`).

---

## 10. AJAX Endpoints

| Action | Handler | Auth | Request params | Response |
|---|---|---|---|---|
| `tp_newsletter` | `tp_newsletter_subscribe` | Public (priv + nopriv) | `nonce` (tp_ajax), `email` | JSON `{success, data.message}` |
| `tp_submit_contact` | `tp_submit_contact` | Public | `nonce`, `first_name`, `last_name`, `email`, `order_number`, `subject`, `message`, `cf-turnstile-response` | JSON `{success, data.message}` |
| `tp_redeem_points` | `tp_rewards_redeem` | **Logged-in only** | `nonce`, `points_to_redeem` | JSON `{success, data:{code, amount, balance, message}}` |
| `tp_set_gate_cookie` | `tp_set_gate_cookie` | Public | `nonce` (tp_gate) | JSON `{success}` (sets 30-day cookie) |

All public AJAX uses the `tp_ajax` nonce (from the localized `tp_ajax` JS object) except the gate, which uses its own `tp_gate` nonce embedded inline.

---

## 11. Customizer Settings

Registered in `inc/customizer.php` (`customize_register`), plus Turnstile keys in `inc/turnstile.php`.

| Section | Setting key | Type | Default |
|---|---|---|---|
| **TruePharm: Top Bar** | `truepharm_topbar_left` | text | `Secure Checkout` |
| | `truepharm_topbar_center` | text | `Authorized Clinical Research Formulas` |
| | `truepharm_topbar_right` | text | `Free Priority Logistics on Orders $200+` |
| **TruePharm: Homepage** | `truepharm_hero_mission` | textarea | *(brief hero mission text)* |
| | `truepharm_goals_title` | text | `Pioneering Cellular Integrity` |
| | `truepharm_goals_p1` | textarea | *(brief paragraph 1)* |
| | `truepharm_goals_p2` | textarea | *(brief paragraph 2)* |
| | `truepharm_goals_image` | media (image) | *(none)* |
| **TruePharm: Why Us** | `truepharm_why_heading` | text | `Research demands precision. The market lacked transparency.` |
| | `truepharm_why_p1/p2/p3` | textarea | *(brief mission paragraphs)* |
| | `truepharm_why_step1–4` | textarea | *(brief pipeline step descriptions)* |
| | `truepharm_why_image` | media (image) | *(none)* |
| **TruePharm: Contact** | `truepharm_contact_email` | text (sanitize_email) | `info@truepharmusa.com` |
| | `truepharm_contact_phone` | text | *(empty → "Available upon request")* |
| | `truepharm_contact_address` | textarea | *(empty → "Available upon request")* |
| **TruePharm: Footer** | `truepharm_footer_tagline` | textarea | `USA-synthesized, clinical-grade cellular research solutions backed by empirical batch verification.` |
| | `truepharm_legal_disclaimer` | textarea | *(full FDA research-use disclaimer)* |
| **Cloudflare Turnstile** | `tp_turnstile_site_key` | text | *(empty)* |
| | `tp_turnstile_secret_key` | text | *(empty)* |

---

## 12. Turnstile Integration (`inc/turnstile.php`)

- **Placeholder system:** `do_action('tp_turnstile_widget')` → `tp_turnstile_render_widget()`. If both keys are set it outputs `<div class="cf-turnstile" data-sitekey="…">` and enqueues `https://challenges.cloudflare.com/turnstile/v0/api.js`; otherwise it renders `<div class="tp-turnstile-placeholder">[Cloudflare Turnstile widget will appear here]</div>`.
- **Activate:** **Appearance → Customize → Cloudflare Turnstile** → paste **Site Key** + **Secret Key** → Publish.
- **Verification:** `tp_turnstile_verify( string $token = '' ): bool` — returns `true` (pass-through) when not configured; otherwise POSTs the token + secret to `https://challenges.cloudflare.com/turnstile/v0/siteverify`.
- **Protected forms / hooks:**

| Form | Hook | Function |
|---|---|---|
| Register | `woocommerce_registration_errors` | `tp_turnstile_validate_registration` |
| Login (WC front-end only) | `wp_authenticate_user` | `tp_turnstile_validate_login` |
| Checkout | `woocommerce_checkout_process` | `tp_turnstile_validate_checkout` |
| Contact | `tp_contact_form_validate` (filter) | `tp_turnstile_validate_contact` |

---

## 13. Newsletter System (`inc/newsletter.php`)

- **Table:** `{prefix}_tp_newsletter_emails` — `id` (PK), `email` (unique), `ip_address`, `source`, `created_at`.
- **AJAX:** `tp_newsletter` (public) → `tp_newsletter_subscribe`. Validates email, blocks duplicates gracefully, fires `tp_newsletter_subscribed`.
- **Export subscribers:** in **phpMyAdmin** run `SELECT email, created_at FROM wp_tp_newsletter_emails ORDER BY created_at DESC;` and use *Export* (replace `wp_` with your table prefix).

## 14. Contact Form (`inc/contact-form.php`)

- **Table:** `{prefix}_tp_contact_submissions` — `id`, `name`, `email`, `order_number`, `subject`, `message`, `submitted_at`.
- **AJAX:** `tp_submit_contact` (public) → `tp_submit_contact`. Runs `apply_filters('tp_contact_form_validate', new WP_Error())` (Turnstile), validates required fields, inserts the row, and emails the admin.
- **Email destination:** `get_option('admin_email')` with `Reply-To` set to the sender.
- **View submissions:** phpMyAdmin → `SELECT * FROM wp_tp_contact_submissions ORDER BY submitted_at DESC;`

## 15. Entrance Gate (`inc/entrance-gate.php`)

- **Cookie:** `tp_compliance_agreed` (constant `TP_GATE_COOKIE`), **30 days** (`TP_GATE_COOKIE_DAYS`).
- **Render:** `tp_render_entrance_gate` on `wp_footer` — shows only when the cookie is absent and not in admin.
- **Agree:** AJAX `tp_set_gate_cookie` (`tp_set_gate_cookie`, nonce `tp_gate`) sets the cookie server-side; JS also sets it client-side and hides the modal.
- **Disagree:** redirects to the URL from `apply_filters('tp_gate_exit_url', 'https://www.google.com')`.
- **Disable the gate:** `add_filter( 'tp_gate_should_show', '__return_false' );` (in a child theme or snippet).

---

## 16. JavaScript Files

All vanilla JS, no jQuery, loaded with `defer` in the footer.

| File | Purpose | Key functions | Global objects read |
|---|---|---|---|
| `main.js` | Site-wide behavior | `initReveal`, `initMenu`, `initCart` (Store-API refresh), `initCarousel`, `initNewsletter`, `initFaq` (accordion + smooth scroll), `initContact`, `initCopyButtons` | `TruePharmData`, `tp_ajax` |
| `product.js` | Single product | variant pills (price + variation_id), gallery thumbnail swap, qty ±, tab switching, bundle select, add-to-cart via `wc-ajax=add_to_cart` + fragment apply | `tp_product` |
| `rewards.js` | Account rewards tab | `initRedeem` (AJAX `tp_redeem_points`) | `tp_ajax` |
| `coa-filter.js` | COA archive | live filter of `.coa-table` rows by batch/compound | — |
| `admin-coa.js` | Admin (COA edit) | `wp.media` PDF picker | `tpCoaAdmin`, `wp.media` |

**Localized data objects** (`inc/enqueue.php`): `TruePharmData` = `{ ajaxUrl, restUrl, storeCartUrl, nonce }`; `tp_ajax` = `{ ajax_url, nonce }`; `tp_product` = `{ wc_ajax_url, cart_url, added_text, select_text }`; `tpCoaAdmin` = `{ title, button }` (admin).

---

## 17. Customizer Quick Reference (all `get_theme_mod` keys)

| Key | Default |
|---|---|
| `truepharm_topbar_left` | `Secure Checkout` |
| `truepharm_topbar_center` | `Authorized Clinical Research Formulas` |
| `truepharm_topbar_right` | `Free Priority Logistics on Orders $200+` |
| `truepharm_hero_mission` | *(brief hero mission)* |
| `truepharm_goals_title` | `Pioneering Cellular Integrity` |
| `truepharm_goals_p1` | *(brief paragraph 1)* |
| `truepharm_goals_p2` | *(brief paragraph 2)* |
| `truepharm_goals_image` | *(none)* |
| `truepharm_why_heading` | `Research demands precision. The market lacked transparency.` |
| `truepharm_why_p1` / `_p2` / `_p3` | *(brief mission paragraphs)* |
| `truepharm_why_step1` … `truepharm_why_step4` | *(brief pipeline steps)* |
| `truepharm_why_image` | *(none)* |
| `truepharm_contact_email` | `info@truepharmusa.com` |
| `truepharm_contact_phone` | *(empty)* |
| `truepharm_contact_address` | *(empty)* |
| `truepharm_footer_tagline` | `USA-synthesized, clinical-grade cellular research solutions backed by empirical batch verification.` |
| `truepharm_legal_disclaimer` | *(full FDA disclaimer)* |
| `tp_turnstile_site_key` | *(empty)* |
| `tp_turnstile_secret_key` | *(empty)* |

---

## 18. Client Content Guide

**Add a new product** — *Products → Add New*. Set title, description, price, image, and a **Product category** (this becomes the "Molecular Class" shown on the card). Fill the **Form** field (General tab) and the **Chemical Data** / **Storage & Handling** tabs. For multiple vial sizes, set the product to **Variable**, add the **Vial Size** attribute with a price per size.

**Add a COA entry** — *COA Library → Add New*. Title = compound name; fill Batch/Date/Purity; upload the PDF; Publish. (See §5.)

**Change the top bar text** — *Appearance → Customize → TruePharm: Top Bar* → edit the three fields.

**Set up Cloudflare Turnstile** — get a Site Key + Secret Key from your Cloudflare dashboard, then *Appearance → Customize → Cloudflare Turnstile* → paste both → Publish. The contact, login, register, and checkout forms become protected automatically.

**View newsletter subscribers** — phpMyAdmin (XAMPP: `http://localhost/phpmyadmin`) → your site DB → `wp_tp_newsletter_emails`. (See §13.)

**View contact submissions** — phpMyAdmin → `wp_tp_contact_submissions`. Each submission is also emailed to the admin email. (See §14.)

**Change footer text / legal disclaimer** — *Appearance → Customize → TruePharm: Footer* → edit **Footer tagline** and **Footer legal disclaimer**.

**Edit legal pages** — *Pages* → edit *Terms of Use, Privacy Policy, Shipping & Returns, Compliance Statement* with the normal WordPress editor (placeholder text is marked `<!-- CLIENT TO FILL -->`).
