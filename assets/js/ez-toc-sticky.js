function ezTOC_hideBar(e) {
    var sidebar = document.querySelector(".ez-toc-sticky-fixed");
    if (typeof(sidebar) !== "undefined" && sidebar !== null) {
        sidebar.classList.remove("show");
        sidebar.classList.add("hide");
        setTimeout(function() {
            document.querySelector(".ez-toc-open-icon").style = "z-index: 9999999";
        }, 200);
        if (e.target.classList.contains('ez-toc-close-icon') || e.target.parentElement.classList.contains('ez-toc-close-icon')) {
            e.preventDefault();
        }
    }
}

function ezTOC_showBar(e) {
    e.preventDefault();
    document.querySelector(".ez-toc-open-icon").style = "z-index: -1;";
    setTimeout(function() {
        var sidebar = document.querySelector(".ez-toc-sticky-fixed");
        sidebar.classList.remove("hide");
        sidebar.classList.add("show");
    }, 200);
}

function ezTOC_getStickyViewportWidth() {
    return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
}

function ezTOC_getStickyDeviceType( mobileBreakpoint, tabletMaxBreakpoint ) {
    var viewportWidth = ezTOC_getStickyViewportWidth();

    if ( viewportWidth <= mobileBreakpoint ) {
        return 'mobile';
    }

    if ( viewportWidth <= tabletMaxBreakpoint ) {
        return 'tablet';
    }

    return 'desktop';
}

function ezTOC_isStickyVisibleForDeviceTarget() {
    if ( typeof eztoc_sticky_local === 'undefined' ) {
        return true;
    }

    var deviceTarget = eztoc_sticky_local.device_target || '';
    var mobileBreakpoint = parseInt( eztoc_sticky_local.mobile_breakpoint, 10 ) || 768;
    var tabletMaxBreakpoint = parseInt( eztoc_sticky_local.tablet_max_breakpoint, 10 ) || 1024;

    if ( ! deviceTarget ) {
        return true;
    }

    var deviceType = ezTOC_getStickyDeviceType( mobileBreakpoint, tabletMaxBreakpoint );

    switch ( deviceTarget ) {
        case 'mobile':
            return 'mobile' === deviceType;
        case 'tablet':
            return 'tablet' === deviceType;
        case 'desktop':
            return 'desktop' === deviceType;
        case 'desktop_tablet':
            return 'desktop' === deviceType || 'tablet' === deviceType;
        case 'desktop_mobile':
            return 'desktop' === deviceType || 'mobile' === deviceType;
        case 'tablet_mobile':
            return 'tablet' === deviceType || 'mobile' === deviceType;
        default:
            return true;
    }
}

function ezTOC_applyStickyDeviceTargetVisibility() {
    var stickyRoot = document.querySelector('.ez-toc-sticky');

    if ( ! stickyRoot ) {
        return;
    }

    if ( ezTOC_isStickyVisibleForDeviceTarget() ) {
        stickyRoot.style.display = '';
    } else {
        stickyRoot.style.display = 'none';
    }
}

(function() {
    let ez_toc_sticky_fixed_container = document.querySelector('div.ez-toc-sticky-fixed');
    if (ez_toc_sticky_fixed_container) {
        ezTOC_applyStickyDeviceTargetVisibility();
        window.addEventListener('resize', ezTOC_applyStickyDeviceTargetVisibility);

        document.body.addEventListener("click", function(evt) {
            ezTOC_hideBar(evt);
        });
        ez_toc_sticky_fixed_container.addEventListener('click', function(event) {
            event.stopPropagation();
        });
        document.querySelector('.ez-toc-open-icon').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
})();

if(typeof eztoc_sticky_local !== 'undefined' && 1 === parseInt(eztoc_sticky_local.close_on_link_click)){
    jQuery(document).ready(function() {
        jQuery("#ez-toc-sticky-container a.ez-toc-link").click(function(e) {
            ezTOC_hideBar(e);
        });
    });
}