window.addEventListener('load', function () {
    const scrollContainer = document.getElementById('navbarScrollContainer');
    const btnLeft = document.getElementById('navbarScrollLeft');
    const btnRight = document.getElementById('navbarScrollRight');

    function updateScrollButtons() {
        btnLeft.classList.toggle('hidden', scrollContainer.scrollLeft <= 0);
        btnRight.classList.toggle('hidden', scrollContainer.scrollLeft + scrollContainer.clientWidth >= scrollContainer.scrollWidth - 1);
    }

    function scrollNavbar(amount) {
        scrollContainer.scrollBy({ left: amount, behavior: 'smooth' });
    }

    btnLeft.addEventListener('click', () => scrollNavbar(-150));
    btnRight.addEventListener('click', () => scrollNavbar(150));
    scrollContainer.addEventListener('scroll', updateScrollButtons);
    window.addEventListener('resize', updateScrollButtons);

    updateScrollButtons();
});