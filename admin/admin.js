/* Logo Preloader — admin JS */
jQuery(function ($) {

    /* ── Media uploader ── */
    var frame;

    $('#lp-upload-btn').on('click', function () {
        if (frame) { frame.open(); return; }

        frame = wp.media({
            title:    'Choose Logo',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });

        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            $('#lp-logo-url').val(att.url).trigger('input');
            $('#lp-img-preview').attr('src', att.url);
            $('#lp-preview-wrap').show();
            $('#lp-remove-btn').show();
            syncPreviewLogo(att.url);
        });

        frame.open();
    });

    $('#lp-remove-btn').on('click', function () {
        $('#lp-logo-url').val('').trigger('input');
        $('#lp-img-preview').attr('src', '');
        $('#lp-preview-wrap').hide();
        $(this).hide();
        syncPreviewLogo('');
    });

    /* ── Color pickers ── */
    // Keep hex text input in sync with native color picker
    $('input[type="color"]').on('input', function () {
        var hex = $(this).val();
        $(this).siblings('.lp-hex-text').val(hex);
        syncColor($(this).attr('id'), hex);
    });

    $('.lp-hex-text').on('input', function () {
        var val = $(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            var target = $(this).data('for');
            $('#' + target).val(val);
            syncColor(target, val);
        }
    });

    function syncColor(id, hex) {
        if (id === 'lp-bg') {
            $('#lp-live-preview').css('background', hex);
        }
        if (id === 'lp-accent') {
            $('#lpv-ring').css('border-top-color', hex);
            $('#lpv-fill').css('background', hex);
        }
    }

    /* ── Logo width slider ── */
    $('#lp-logo-width').on('input', function () {
        var w = parseInt($(this).val(), 10) || 64;
        $('#lpv-img').css('width', w + 'px');
        $('#lpv-wrap').css({
            width:  (w + 28) + 'px',
            height: (w + 28) + 'px',
        });
    });

    /* ── Toggle ring / bar visibility ── */
    $('input[name$="[show_ring]"]').on('change', function () {
        $('#lpv-ring').toggle(this.checked);
    });

    $('input[name$="[show_bar]"]').on('change', function () {
        $('#lpv-bar').toggle(this.checked);
    });

    /* ── Logo URL typed manually ── */
    $('#lp-logo-url').on('input', function () {
        syncPreviewLogo($(this).val());
    });

    function syncPreviewLogo(url) {
        if (url) {
            $('#lpv-img').attr('src', url).show();
            $('#lpv-placeholder').hide();
        } else {
            $('#lpv-img').hide();
            $('#lpv-placeholder').show();
        }
    }
});
