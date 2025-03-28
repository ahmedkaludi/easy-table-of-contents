window.addEventListener('DOMContentLoaded', () => {
    let lastActive = null;
    let observer;

    const tocContainer = document.querySelector('.ez-toc-widget-sticky nav');


    function isElementFullyVisible(el, container) {
        const containerRect = container.getBoundingClientRect();
        const elRect = el.getBoundingClientRect();
        return elRect.top >= containerRect.top && elRect.bottom <= containerRect.bottom;
    }


    function createObserver() {
        observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                    const id = entry.target.getAttribute('id');
                    if( id ){
                    const link = document.querySelector(`.ez-toc-widget-sticky nav li a[href="#${id}"]`);
                    const all_links = document.querySelectorAll('.ez-toc-widget-sticky nav li.active')
                  
                    if (link) {
                        if (all_links.length > 0) {
                            all_links.forEach(linkk => {
                                linkk.classList.remove('active');
                            });
                        }
                        link.parentElement.classList.add('active');
                        lastActive = link.parentElement;
        
                        if (!isElementFullyVisible(link.parentElement, tocContainer)) {
                            link.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                }
            });
        }, { 
            threshold: [0, 0.5], 
            rootMargin: '0px 0px -40% 0px' // Adjust the bottom margin to extend the "active" zone
        });
    }
    

    // Initialize the observer and start observing all sections.
    createObserver();
    document.querySelectorAll('.ez-toc-section').forEach(section => {
        observer.observe(section);
    });


    tocContainer.addEventListener('click', event => {
        const link = event.target.closest('a');
        if (link) {
            // Disconnect the observer so it won't interfere with the native anchor jump.
            observer.disconnect();

            setTimeout(() => {
                createObserver();
                document.querySelectorAll('.ez-toc-section').forEach(section => {
                    observer.observe(section);
                });
            }, 2000); 
        }
    });
});
