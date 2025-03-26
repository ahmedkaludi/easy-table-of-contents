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
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    const link = document.querySelector(`.ez-toc-widget-sticky nav li a[href="#${id}"]`);

                    if (lastActive && lastActive !== link.parentElement) {
                        lastActive.classList.remove('active');
                    }
                    link.parentElement.classList.add('active');
                    lastActive = link.parentElement;


                    if (!isElementFullyVisible(link.parentElement, tocContainer)) {
                        link.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }, { threshold: 0.5 });
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
