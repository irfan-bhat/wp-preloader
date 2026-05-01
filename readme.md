=== Logo Preloader ===
Contributors:      Irfan Rafiq
Tags:              preloader, loading screen, logo, animation
Requires at least: 5.5
Tested up to:      6.5
Requires PHP:      7.4
Stable tag:        1.0.0
License:           GPL-2.0+
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

A customisable full-screen preloader with your logo, a spinner ring, and a progress bar.

== Description ==

Logo Preloader displays a sleek full-screen loading overlay while your WordPress site loads. Configure everything from the WordPress admin — no code needed.

**Features**

* Upload any logo via the WordPress media library
* Choose background and accent colours
* Toggle the spinner ring and progress bar on/off
* Set a minimum display time so the preloader never flashes by too quickly
* Optionally disable on mobile devices
* Clean fade-out transition after the page loads
* Zero dependencies — pure CSS + vanilla JS, ~3 KB total

== Installation ==

1. Upload the `preloader-plugin` folder to `/wp-content/plugins/`
   — OR — install directly via **Plugins › Add New › Upload Plugin**
2. Activate the plugin through the **Plugins** menu
3. Go to **Settings › Logo Preloader** to configure

== Frequently Asked Questions ==

= My theme doesn't support wp_body_open — what do I do? =

Add `<?php wp_body_open(); ?>` right after your opening `<body>` tag in `header.php`.
Most modern themes already include this hook.

= The preloader shows on every page refresh. Is that normal? =

Yes — the preloader fires on every page load by design. If you want to skip returning visitors,
you can set a session cookie in `js/preloader.js` and exit early if it exists.

== Changelog ==

= 1.0.0 =
* Initial release
