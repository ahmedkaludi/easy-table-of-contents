// Prevent multiple initializations
if (window.ezTocWidgetStickyInitialized) {
} else {
    window.ezTocWidgetStickyInitialized = true;

    // Wait for DOM to be ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

ready(() => {
    
    let lastActive = null;
    let observer;
    let stickyInitialized = false;
    let mobileToggleInitialized = false;
    let observerDisabled = false;

    const tocContainer = document.querySelector('.ez-toc-widget-sticky nav');
    const stickyContainer = document.querySelector('.ez-toc-widget-sticky-container');



    // Check if elements exist
    if (!tocContainer || !stickyContainer) {
        return;
    }

    // Function to find the article or post-content container
    function findArticleContainer() {
        
        // Look for common article containers
        const selectors = [
            'article',
            '.post-content',
            '.entry-content',
            '.content-area',
            '.main-content',
            '.post-body',
            '.article-content',
            '.single-post',
            '.single-page',
            '.post',
            '.page',
            '[role="main"]',
            'main'
        ];
        
        for (const selector of selectors) {
            const container = document.querySelector(selector);
            if (container) {
                // Check if the container is visible and has content
                const rect = container.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) {
                    return container;
                } else {
                }
            } else {
                console.log('EZ TOC Widget Sticky: No element found for selector:', selector);
            }
        }
        
        // Fallback to body if no suitable container found
        return document.body;
    }

    // Check if device is mobile
    function isMobileDevice() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
               window.innerWidth <= 768;
        return isMobile;
    }
    
    // Function to initialize mobile toggle functionality
    function initializeMobileToggle() {
        if (mobileToggleInitialized || !isMobileDevice()) {
            return;
        }

        // Create mobile toggle button
        const mobileToggleBtn = document.createElement('div');
        mobileToggleBtn.className = 'ez-toc-mobile-toggle-btn';
        mobileToggleBtn.innerHTML = `
            <span class="ez-toc-mobile-toggle-icon">☰</span>
            <span class="ez-toc-mobile-toggle-text">TOC</span>
        `;
        
        // Add styles for mobile toggle button
        const mobileToggleStyles = document.createElement('style');
        mobileToggleStyles.textContent = `
            .ez-toc-mobile-toggle-btn {
                position: fixed;
                top: 50%;
                right: 0;
                transform: translateY(-50%);
                background: #007cba;
                color: white;
                padding: 12px 8px;
                border-radius: 8px 0 0 8px;
                cursor: pointer;
                z-index: 10000;
                box-shadow: -2px 2px 8px rgba(0,0,0,0.2);
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4px;
                font-size: 12px;
                font-weight: bold;
            }
            
            .ez-toc-mobile-toggle-btn:hover {
                background: #005a87;
                transform: translateY(-50%) translateX(-2px);
            }
            
            .ez-toc-mobile-toggle-icon {
                font-size: 16px;
                line-height: 1;
            }
            
            .ez-toc-mobile-toggle-text {
                font-size: 10px;
                line-height: 1;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                height: 100vh !important;
                z-index: 10001 !important;
                background: rgba(0,0,0,0.8) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 20px !important;
                box-sizing: border-box !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay .ez-toc-widget-sticky-content {
                background: white;
                border-radius: 12px;
                max-width: 90%;
                max-height: 80vh;
                width: 100%;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay .ez-toc-widget-sticky-title {
                padding: 15px 20px !important;
                border-bottom: 1px solid #eee !important;
                margin: 0 !important;
                background: #f9f9f9 !important;
                border-radius: 12px 12px 0 0 !important;
                font-size: 18px !important;
                font-weight: bold !important;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay nav {
                padding: 20px !important;
                max-height: 60vh !important;
                overflow-y: auto !important;
                flex: 1;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay .ez-toc-mobile-close-btn {
                position: absolute;
                top: 15px;
                right: 15px;
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #666;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: background-color 0.2s ease;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay .ez-toc-mobile-close-btn:hover {
                background-color: #f0f0f0;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay {
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            
            .ez-toc-widget-sticky-container.mobile-overlay.show {
                opacity: 1;
                visibility: visible;
            }
            
            @media (max-width: 768px) {
                .ez-toc-widget-sticky-container:not(.mobile-overlay) {
                    display: none !important;
                }
            }
        `;
        
        document.head.appendChild(mobileToggleStyles);
        document.body.appendChild(mobileToggleBtn);
        
        // Wrap the sticky container content for mobile overlay
        const originalContent = stickyContainer.innerHTML;
        stickyContainer.innerHTML = `
            <div class="ez-toc-widget-sticky-content">
                <button class="ez-toc-mobile-close-btn" aria-label="Close Table of Contents">×</button>
                ${originalContent}
            </div>
        `;
        
        // Add event listeners
        mobileToggleBtn.addEventListener('click', function() {
            stickyContainer.classList.add('mobile-overlay', 'show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
        
        const closeBtn = stickyContainer.querySelector('.ez-toc-mobile-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                closeMobileOverlay();
            });
        }
        
        // Close overlay when clicking outside the content
        stickyContainer.addEventListener('click', function(e) {
            if (e.target === stickyContainer) {
                closeMobileOverlay();
            }
        });
        
        // Close overlay when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && stickyContainer.classList.contains('mobile-overlay')) {
                closeMobileOverlay();
            }
        });
        
        // Close overlay when clicking on any link within the mobile overlay
        stickyContainer.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && isMobileDevice()) {
                // Close the overlay immediately when any link is clicked
                stickyContainer.classList.remove('show');
                stickyContainer.classList.remove('mobile-overlay');
                document.body.style.overflow = '';
            }
        });
        
        function closeMobileOverlay() {
            stickyContainer.classList.remove('show');
            setTimeout(() => {
                stickyContainer.classList.remove('mobile-overlay');
                document.body.style.overflow = '';
            }, 200); // Reduced delay for better responsiveness
        }
        
        mobileToggleInitialized = true;

    }
    
    // Function to initialize sticky functionality
    function initializeSticky() {
        if (stickyInitialized || !stickyContainer) {
            return;
        }

        // Don't initialize sticky on mobile devices
        if (isMobileDevice()) {
            return;
        }

        // Find the article container
        const articleContainer = findArticleContainer();
        
        // Check if sticky-kit is available
        if (typeof jQuery !== 'undefined' && jQuery.fn.stick_in_parent) {
            const $ = jQuery;
            
            try {
                // Get the offset top setting
                const offsetTop = (typeof ezTocWidgetSticky !== 'undefined' && ezTocWidgetSticky.fixed_top_position) 
                    ? parseInt(ezTocWidgetSticky.fixed_top_position) 
                    : 30;
                
                // Initialize sticky with body as parent but respect article boundaries
                $(stickyContainer).stick_in_parent({
                    inner_scrolling: false,
                    offset_top: offsetTop,
                    sticky_class: 'is_stuck',
                    parent: 'body' // Use body as parent
                });
                
                stickyInitialized = true;
                
                // Add custom logic to respect article boundaries
                let isSticky = false;
                const originalTop = stickyContainer.offsetTop;
                const originalPosition = stickyContainer.style.position;
                
                function handleArticleBoundaries() {
                    if (!isSticky) return;
                    
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const containerTop = articleContainer.offsetTop;
                    const containerBottom = containerTop + articleContainer.offsetHeight;
                    const stickyHeight = stickyContainer.offsetHeight;
                    const stickyTop = stickyContainer.offsetTop;
                    
                    // Check if we've scrolled past the article container
                    if (scrollTop + stickyHeight + offsetTop > containerBottom) {
                        // Position the sticky element at the bottom of the article
                        const maxTop = containerBottom - stickyHeight - offsetTop;
                        stickyContainer.style.top = Math.max(offsetTop, maxTop) + 'px';
                    } else {
                        // Normal sticky behavior
                        stickyContainer.style.top = offsetTop + 'px';
                    }
                }
                
                // Handle window resize
                $(window).on('resize', function() {
                    // Don't recalculate on mobile
                    if (isMobileDevice()) {
                        if ($(stickyContainer).hasClass('is_stuck')) {
                            $(stickyContainer).trigger('sticky_kit:recalc');
                        }
                        return;
                    }
                    
                    if ($(stickyContainer).hasClass('is_stuck')) {
                        $(stickyContainer).trigger('sticky_kit:recalc');
                        handleArticleBoundaries();
                    }
                });
                
                // Add scroll listener for article boundary handling
                $(window).on('scroll', function() {
                    if ($(stickyContainer).hasClass('is_stuck')) {
                        handleArticleBoundaries();
                    }
                });
                
            } catch (error) {
                console.error('EZ TOC Widget Sticky: Error initializing sticky functionality:', error);
            }
        } else {
            console.warn('EZ TOC Widget Sticky: jQuery or sticky-kit not available');
        }
    }

    // Initialize based on device type
    if (isMobileDevice()) {
        initializeMobileToggle();
    } else {
        initializeSticky();
    }

    // Handle device type changes on resize
    let currentDeviceType = isMobileDevice();
    
    window.addEventListener('resize', function() {
        const newDeviceType = isMobileDevice();
        if (newDeviceType !== currentDeviceType) {
            currentDeviceType = newDeviceType;
            
            if (newDeviceType) {
                // Switch to mobile mode
                    if (stickyInitialized) {
                    // Detach sticky functionality
                    if (typeof jQuery !== 'undefined' && jQuery.fn.stick_in_parent) {
                        const $ = jQuery;
                        $(stickyContainer).trigger('sticky_kit:detach');
                    }
                    stickyInitialized = false;
                }
                initializeMobileToggle();
            } else {
                // Switch to desktop mode
                if (mobileToggleInitialized) {
                    // Remove mobile toggle button
                    const mobileToggleBtn = document.querySelector('.ez-toc-mobile-toggle-btn');
                    if (mobileToggleBtn) {
                        mobileToggleBtn.remove();
                    }
                    mobileToggleInitialized = false;
                }
                initializeSticky();
            }
        }
    });

    // If not initialized, try again with a timeout
    if (!stickyInitialized && !isMobileDevice()) {
        setTimeout(() => {
            initializeSticky();
        }, 1000); // Wait 1 second
    }

    // Try again after a longer timeout as fallback
    setTimeout(() => {
        if (!stickyInitialized && !isMobileDevice()) {
            initializeSticky();
        }
    }, 3000); // Wait 3 seconds

    // Final attempt after page is fully loaded
    window.addEventListener('load', () => {
        if (!stickyInitialized && !isMobileDevice()) {
            initializeSticky();
        }
        if (!mobileToggleInitialized && isMobileDevice()) {
            initializeMobileToggle();
        }
    });

    // Fallback sticky functionality without sticky-kit
    function initializeFallbackSticky() {
        if (stickyInitialized || !stickyContainer) {
            return;
        }

        // Don't initialize fallback sticky on mobile devices
        if (isMobileDevice()) {
            return;
        }

        try {
            const articleContainer = findArticleContainer();
            const offsetTop = (typeof ezTocWidgetSticky !== 'undefined' && ezTocWidgetSticky.fixed_top_position) 
                ? parseInt(ezTocWidgetSticky.fixed_top_position) 
                : 30;

            let isSticky = false;
            const originalTop = stickyContainer.offsetTop;
            const originalPosition = stickyContainer.style.position;
            const containerRect = articleContainer.getBoundingClientRect();

            function handleScroll() {
                // Don't handle sticky on mobile
                if (isMobileDevice()) {
                    return;
                }
                
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const containerTop = articleContainer.offsetTop;
                const containerBottom = containerTop + articleContainer.offsetHeight;
                const stickyHeight = stickyContainer.offsetHeight;

                // Check if we should make it sticky
                if (scrollTop > originalTop - offsetTop && !isSticky) {
                    stickyContainer.style.position = 'fixed';
                    stickyContainer.style.top = offsetTop + 'px';
                    stickyContainer.style.zIndex = '9999';
                    stickyContainer.classList.add('is_stuck');
                    isSticky = true;
                } 
                // Check if we should unstick it (when reaching container bottom)
                else if ((scrollTop + stickyHeight + offsetTop > containerBottom) && isSticky) {
                    stickyContainer.style.position = 'absolute';
                    stickyContainer.style.top = (containerBottom - stickyHeight - offsetTop) + 'px';
                    stickyContainer.style.zIndex = '9999';
                    stickyContainer.classList.add('is_stuck');
                }
                // Check if we should return to normal position
                else if (scrollTop <= originalTop - offsetTop && isSticky) {
                    stickyContainer.style.position = originalPosition;
                    stickyContainer.style.top = '';
                    stickyContainer.style.zIndex = '';
                    stickyContainer.classList.remove('is_stuck');
                    isSticky = false;
                }
            }

            window.addEventListener('scroll', handleScroll);
            window.addEventListener('resize', handleScroll);
            
            stickyInitialized = true;
        } catch (error) {
            console.error('EZ TOC Widget Sticky: Error initializing fallback sticky:', error);
        }
    }

    // Try fallback if sticky-kit fails after 5 seconds
    setTimeout(() => {
        if (!stickyInitialized && !isMobileDevice()) {
            initializeFallbackSticky();
        }
    }, 5000);

    function isElementFullyVisible(el, container) {
        const containerRect = container.getBoundingClientRect();
        const elRect = el.getBoundingClientRect();
        return elRect.top >= containerRect.top && elRect.bottom <= containerRect.bottom;
    }

    function highlightHeading(headingId) {
        const allTocLinks = document.querySelectorAll('.ez-toc-widget-sticky nav li a');
        const all_active_items = document.querySelectorAll('.ez-toc-widget-sticky nav li.active');
        
        // Remove active class from all previously active list items
        if (all_active_items.length > 0) {
            all_active_items.forEach(item => {
                item.classList.remove('active');
            });
        }
        
        // Find the link that corresponds to the clicked heading
        let targetLink = null;
        allTocLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const id = href.substring(1);
                if (id === headingId) {
                    targetLink = link;
                }
            }
        });
        
        // Add active class to the specific list item containing the link
        if (targetLink) {
            const listItem = targetLink.closest('li');
            if (listItem) {
                listItem.classList.add('active');
                lastActive = listItem;
                
                // Scroll the active item into view if needed
                if (!isElementFullyVisible(listItem, tocContainer)) {
                    listItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    }

    // Track scroll direction and last scroll position
    let lastScrollTop = 0;
    let scrollDirection = 'down';
    
    function updateScrollDirection() {
        const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
        scrollDirection = currentScrollTop > lastScrollTop ? 'down' : 'up';
        lastScrollTop = currentScrollTop;
    }
    
    function getHeadingAtTop() {
        const topOffset = 50; // Small offset from the very top
        const allTocLinks = document.querySelectorAll('.ez-toc-widget-sticky nav li a');
        const tocLinkMap = new Map();
        const headingPositions = [];
        
        // Build map of all headings and their positions
        allTocLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const id = href.substring(1);
                const element = document.getElementById(id);
                if (element) {
                    const rect = element.getBoundingClientRect();
                    const top = rect.top + window.pageYOffset;
                    const bottom = rect.bottom + window.pageYOffset;
                    const center = top + (rect.height / 2);
                    
                    tocLinkMap.set(id, link);
                    headingPositions.push({
                        id: id,
                        link: link,
                        top: top,
                        bottom: bottom,
                        center: center,
                        element: element
                    });
                }
            }
        });
        
        // Sort headings by their position (top to bottom)
        headingPositions.sort((a, b) => a.top - b.top);
        
        const viewportTop = window.pageYOffset + topOffset;
        let bestHeading = null;
        
        // When scrolling down, be more conservative - only advance when we've scrolled past the current heading's content
        if (scrollDirection === 'down') {
            // Find the current active heading first
            const activeListItem = document.querySelector('.ez-toc-widget-sticky nav li.active');
            if (activeListItem) {
                const activeLink = activeListItem.querySelector('a');
                if (activeLink) {
                    const activeHref = activeLink.getAttribute('href');
                    if (activeHref && activeHref.startsWith('#')) {
                        const activeId = activeHref.substring(1);
                        const activeHeading = headingPositions.find(h => h.id === activeId);
                        
                        if (activeHeading) {
                            // Only advance to next heading if we've scrolled significantly past the current heading's bottom
                            const advanceThreshold = activeHeading.bottom + 100; // 100px buffer
                            
                            if (viewportTop >= advanceThreshold) {
                                // Find the next heading
                                const currentIndex = headingPositions.findIndex(h => h.id === activeId);
                                if (currentIndex < headingPositions.length - 1) {
                                    bestHeading = headingPositions[currentIndex + 1];
                                } else {
                                    bestHeading = activeHeading; // Stay on last heading
                                }
                            } else {
                                bestHeading = activeHeading; // Keep current heading
                            }
                        }
                    }
                }
            }
        }
        
        // If no heading found for scroll down logic, or if scrolling up, use the original logic
        if (!bestHeading) {
            // Find the first heading that is at or below the top of the viewport
            for (let i = 0; i < headingPositions.length; i++) {
                const heading = headingPositions[i];
                if (heading.top >= viewportTop) {
                    bestHeading = heading;
                    break;
                }
            }
            
            // If no heading found at or below viewport top, use the last heading
            if (!bestHeading && headingPositions.length > 0) {
                bestHeading = headingPositions[headingPositions.length - 1];
            }
            
            // If scrolling up, prefer the previous heading when current is below top
            if (scrollDirection === 'up' && bestHeading) {
                const currentIndex = headingPositions.findIndex(h => h.id === bestHeading.id);
                if (currentIndex > 0) {
                    const currentHeading = headingPositions[currentIndex];
                    const previousHeading = headingPositions[currentIndex - 1];
                    
                    // If current heading is below top, use previous heading
                    if (currentHeading.top > viewportTop) {
                        bestHeading = previousHeading;
                    }
                }
            }
        }
        
        return bestHeading;
    }
    
    function createObserver() {
        observer = new IntersectionObserver(entries => {

            // Skip processing if observer is disabled
            if (observerDisabled) {
                return;
            }

            updateScrollDirection();

            const topHeading = getHeadingAtTop();
            
            if (!topHeading) {
                return;
            }
            
            // Build map of all TOC links
            const allTocLinks = document.querySelectorAll('.ez-toc-widget-sticky nav li a');
            const tocLinkMap = new Map();
            
            allTocLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href.startsWith('#')) {
                    const id = href.substring(1);
                    tocLinkMap.set(id, link);
                }
            });
            
            // Find the best heading to highlight by checking hierarchy
            let bestHeadingId = topHeading.id;
            let bestHeadingLink = tocLinkMap.get(topHeading.id);
            
            if (bestHeadingLink) {
                const bestListItem = bestHeadingLink.closest('li');
                
                // Check if this heading has child headings that are also visible
                if (bestListItem) {
                    const childLinks = bestListItem.querySelectorAll('ul li a');
                    const visibleChildIds = [];
                    
                    childLinks.forEach(childLink => {
                        const childHref = childLink.getAttribute('href');
                        if (childHref && childHref.startsWith('#')) {
                            const childId = childHref.substring(1);
                            const childElement = document.getElementById(childId);
                            if (childElement) {
                                const rect = childElement.getBoundingClientRect();
                                const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                                if (isVisible) {
                                    visibleChildIds.push(childId);
                                }
                            }
                        }
                    });
                    
                    // If child headings are visible, check if any are at the top of viewport
                    if (visibleChildIds.length > 0) {
                        let topChildId = null;
                        const viewportTop = window.pageYOffset + 50;
                        
                        // Find the first child heading that is at or below the top of the viewport
                        for (let i = 0; i < visibleChildIds.length; i++) {
                            const childId = visibleChildIds[i];
                            const childElement = document.getElementById(childId);
                            if (childElement) {
                                const rect = childElement.getBoundingClientRect();
                                const childTop = rect.top + window.pageYOffset;
                                
                                if (childTop >= viewportTop) {
                                    topChildId = childId;
                                    break;
                                }
                            }
                        }
                        
                        // Use child if it's at the top of viewport and parent is not
                        if (topChildId && topHeading.top < viewportTop) {
                            bestHeadingId = topChildId;
                            bestHeadingLink = tocLinkMap.get(topChildId);
                        }
                    }
                }
            }
            
            const all_active_items = document.querySelectorAll('.ez-toc-widget-sticky nav li.active');
            
            if (bestHeadingLink) {
                // Remove active class from all previously active list items
                if (all_active_items.length > 0) {
                    all_active_items.forEach(item => {
                        item.classList.remove('active');
                    });
                }
                
                // Add active class only to the specific list item containing the link
                const listItem = bestHeadingLink.closest('li');
                if (listItem) {
                    listItem.classList.add('active');
                    lastActive = listItem;
                    
                    // Scroll the active item into view if needed
                    if (!isElementFullyVisible(listItem, tocContainer)) {
                        listItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
        }, { 
            threshold: [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0], 
            rootMargin: '0px 0px -80% 0px' // Adjust to focus more on top of viewport
        });
    }
    

    // Initialize the observer and start observing all sections.
    createObserver();
    
    // Try multiple selectors to find heading elements
    const headingSelectors = [
        '.ez-toc-section',
        '[id] h1, [id] h2, [id] h3, [id] h4, [id] h5, [id] h6',
        'h1[id], h2[id], h3[id], h4[id], h5[id], h6[id]',
        '.ez-toc-heading',
        '[id]'
    ];
    
    let sectionsFound = false;
    
    for (const selector of headingSelectors) {
        const sections = document.querySelectorAll(selector);
        
        if (sections.length > 0) {
            sections.forEach(section => {
                const id = section.getAttribute('id');
                if (id) {
                    observer.observe(section);
                }
            });
            sectionsFound = true;
            break;
        }
    }

    if (!sectionsFound) {
        console.warn('EZ TOC Widget Sticky: No heading sections found to observe');
    }


    tocContainer.addEventListener('click', event => {
        const link = event.target.closest('a');
        if (link) {
            // Get the heading ID from the clicked link
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const headingId = href.substring(1);
                
                // Immediately highlight the clicked heading
                highlightHeading(headingId);
            }
            
            // Close mobile overlay immediately if it's open
            if (isMobileDevice() && stickyContainer.classList.contains('mobile-overlay')) {
                // Close the overlay immediately
                stickyContainer.classList.remove('show');
                stickyContainer.classList.remove('mobile-overlay');
                document.body.style.overflow = '';
            }
            
            // Disable observer for 3 seconds to prevent conflicts
            observerDisabled = true;
            
            // Disconnect the observer so it won't interfere with the native anchor jump.
            observer.disconnect();

            setTimeout(() => {
                // Re-enable observer after 3 seconds
                observerDisabled = false;
                createObserver();
                
                // Use the same improved element selection
                const headingSelectors = [
                    '.ez-toc-section',
                    '[id] h1, [id] h2, [id] h3, [id] h4, [id] h5, [id] h6',
                    'h1[id], h2[id], h3[id], h4[id], h5[id], h6[id]',
                    '.ez-toc-heading',
                    '[id]'
                ];
                
                for (const selector of headingSelectors) {
                    const sections = document.querySelectorAll(selector);
                    if (sections.length > 0) {
                        sections.forEach(section => {
                            const id = section.getAttribute('id');
                            if (id) {
                                observer.observe(section);
                            }
                        });
                        break;
                    }
                }
            }, 3000); // Changed from 2000 to 3000 milliseconds
        }
    });
    
    // Global event listener to catch ALL link clicks within the mobile overlay
    document.addEventListener('click', event => {
        const link = event.target.closest('a');
        if (link && isMobileDevice() && stickyContainer.classList.contains('mobile-overlay')) {
            // Check if the link is within the TOC container
            const tocContainer = link.closest('.ez-toc-widget-sticky-container');
            if (tocContainer) {
                // Close the overlay immediately for any link click
                stickyContainer.classList.remove('show');
                stickyContainer.classList.remove('mobile-overlay');
                document.body.style.overflow = '';
            }
        }
    });
    
    // Additional event listener specifically for the mobile overlay content
    stickyContainer.addEventListener('click', event => {
        const link = event.target.closest('a');
        if (link && isMobileDevice() && stickyContainer.classList.contains('mobile-overlay')) {
            // Close the overlay immediately
            stickyContainer.classList.remove('show');
            stickyContainer.classList.remove('mobile-overlay');
            document.body.style.overflow = '';
        }
    });
    
});
}

document.addEventListener("DOMContentLoaded", function () {
    const tocContainer = document.querySelector(".ez-toc-widget-sticky-container");
    let postContent = document.querySelector("article");
      
      if (document.querySelector("article")) {
        postContent = document.querySelector("article");
      } else if (document.querySelector(".post-content")) {
        postContent = document.querySelector(".post-content");
      } else if (document.querySelector(".entry-content")) {
        postContent = document.querySelector(".entry-content");
      } else if (document.querySelector(".single-post-content")) {
        postContent = document.querySelector(".single-post-content");
      } else if (document.querySelector(".content-area")) {
        postContent = document.querySelector(".content-area");
      }
  
    function checkTOCInsideContent() {
      if (!tocContainer || !postContent) return;
  
      const tocRect = tocContainer.getBoundingClientRect();
      const contentRect = postContent.getBoundingClientRect();
  
      const inside =
        tocRect.top >= contentRect.top &&
        tocRect.bottom <= contentRect.bottom;
  
      if (inside) {
        tocContainer.style.opacity = "1";
        tocContainer.style.pointerEvents = "auto";
      } else {
        tocContainer.style.opacity = "0";
        tocContainer.style.pointerEvents = "none";
      }
    }
  
    // Run on load + scroll + resize
    checkTOCInsideContent();
    document.addEventListener("scroll", checkTOCInsideContent);
    window.addEventListener("resize", checkTOCInsideContent);
  });
  