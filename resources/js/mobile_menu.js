/**
 * Toggle mobile menu (show/hide).
 */
const toggleMobileMenu = () => {
    const mobileMenuElement = document.getElementById('mobile-menu');

    if (mobileMenuElement.classList.contains('hidden')) {
        document.body.classList.add('overflow-hidden');
        mobileMenuElement.classList.remove('hidden');
    } else {
        mobileMenuElement.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};
