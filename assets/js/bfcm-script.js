(function ($) {
    'use strict';

    if (typeof bfcmData === 'undefined') {
        return;
    }

    var $banner = $('#eztoc-bfcm-banner');

    if (!$banner.length) {
        return;
    }

    var bannerEl = $banner.get(0);
    var innerEl = bannerEl.querySelector('.eztoc-bfcm-inner');
    var fittedWidth = 0;

    function fitBanner() {
        if (!innerEl) {
            return;
        }

        var width = bannerEl.clientWidth;

        if (!width || width === fittedWidth) {
            return;
        }

        fittedWidth = width;

        innerEl.style.zoom = '1';
        innerEl.style.transform = 'none';
        innerEl.style.width = 'max-content';
        bannerEl.style.height = 'auto';
        bannerEl.classList.remove('is-stacked');

        var styles = window.getComputedStyle(bannerEl);
        var padX = parseFloat(styles.paddingLeft) + parseFloat(styles.paddingRight);
        var padY = parseFloat(styles.paddingTop) + parseFloat(styles.paddingBottom);
        var available = width - padX;

        if (available < 760) {
            innerEl.style.width = '100%';
            bannerEl.classList.add('is-stacked');
            return;
        }

        var needed = innerEl.offsetWidth;
        var scale = needed > available && needed > 0 ? available / needed : 1;

        if (scale < 1) {
            innerEl.style.width = needed + 'px';
            innerEl.style.transformOrigin = 'left top';
            innerEl.style.transform = 'scale(' + scale + ')';
            bannerEl.style.height = Math.ceil((innerEl.offsetHeight * scale) + padY) + 'px';
            return;
        }

        innerEl.style.width = '100%';
        innerEl.style.transform = 'none';
        bannerEl.style.height = 'auto';
    }

    fitBanner();

    if (window.ResizeObserver) {
        new ResizeObserver(fitBanner).observe(bannerEl);
    } else {
        $(window).on('resize.eztocBfcm', fitBanner);
    }

    var endMs = parseInt(bfcmData.targetTimestamp, 10) * 1000;
    var timerId = null;

    function pad(value) {
        var number = parseInt(value, 10);

        if (isNaN(number) || number < 0) {
            number = 0;
        }

        return number < 10 ? '0' + number : String(number);
    }

    function renderCountdown() {
        if (isNaN(endMs)) {
            return;
        }

        var diff = endMs - Date.now();

        if (diff <= 0) {
            if (timerId) {
                window.clearInterval(timerId);
            }
            $banner.remove();
            return;
        }

        var total = Math.floor(diff / 1000);
        var days = Math.floor(total / 86400);
        var hours = Math.floor((total % 86400) / 3600);
        var mins = Math.floor((total % 3600) / 60);
        var secs = total % 60;

        $banner.find('[data-bfcm="days"]').text(pad(days));
        $banner.find('[data-bfcm="hours"]').text(pad(hours));
        $banner.find('[data-bfcm="mins"]').text(pad(mins));
        $banner.find('[data-bfcm="secs"]').text(pad(secs));
    }

    renderCountdown();
    timerId = window.setInterval(renderCountdown, 1000);

    $banner.on('click', '.eztoc-bfcm-code', function () {
        var $button = $(this);
        var code = bfcmData.coupon || '';
        var $label = $button.find('.eztoc-bfcm-code-label');
        var original = $label.text();

        function markCopied() {
            $button.addClass('is-copied');
            $label.text(bfcmData.copiedText || 'Copied');
            $banner.find('.eztoc-bfcm-sr').text(bfcmData.copiedText || 'Copied');
            window.setTimeout(function () {
                $button.removeClass('is-copied');
                $label.text(original);
            }, 1600);
        }

        if (window.navigator.clipboard && window.navigator.clipboard.writeText) {
            window.navigator.clipboard.writeText(code).then(markCopied).catch(function () {
                if (copyWithFallback(code)) {
                    markCopied();
                }
            });
            return;
        }

        if (copyWithFallback(code)) {
            markCopied();
        }
    });

    function copyWithFallback(code) {
        var textarea = document.createElement('textarea');
        textarea.value = code;
        textarea.setAttribute('readonly', 'readonly');
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();

        var copied = false;

        try {
            copied = document.execCommand('copy');
        } catch (error) {
            copied = false;
        }

        document.body.removeChild(textarea);
        return copied;
    }

    $banner.on('click', '.eztoc-bfcm-dismiss', function (event) {
        event.preventDefault();

        if ($banner.hasClass('is-dismissing')) {
            return;
        }

        $banner.addClass('is-dismissing');

        $.post(bfcmData.ajaxUrl, {
            action: 'eztoc_dismiss_bfcm',
            nonce: bfcmData.nonce
        }).done(function (response) {
            if (response && response.success) {
                $banner.slideUp(160, function () {
                    $banner.remove();
                });
                return;
            }

            $banner.removeClass('is-dismissing');
        }).fail(function () {
            $banner.removeClass('is-dismissing');
        });
    });
}(jQuery));
