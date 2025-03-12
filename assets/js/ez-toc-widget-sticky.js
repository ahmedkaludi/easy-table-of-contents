window.addEventListener('DOMContentLoaded', () => {
    let lastActive = null;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            const id = entry.target.getAttribute('id');
            const link = document.querySelector(`.ez-toc-widget-sticky nav li a[href="#${id}"]`);

            if (entry.isIntersecting) {
                if (lastActive) {
                    lastActive.classList.remove('active');
                }
                link.parentElement.classList.add('active');
                lastActive = link.parentElement;

                // Ensure the active link is visible in the TOC
                link.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.ez-toc-section').forEach((section) => {
        observer.observe(section);
    });
});

