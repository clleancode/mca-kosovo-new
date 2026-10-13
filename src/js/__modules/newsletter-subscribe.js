const newsletterSubscribe = () => {
    document.addEventListener('wpcf7mailsent', event => {
        const section = event.target.closest('[data-newsletter-subscribe]');
        if (!section) return;

        const success = section.querySelector('[data-newsletter-success]');
        if (!success) return;

        success.hidden = false;
        success.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
};

export default newsletterSubscribe;
