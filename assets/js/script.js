const mobileMenu = document.querySelector('.mobile-menu');  // Select the mobile menu element (the hamburger icon)
const header = document.querySelector('header');            // Select the header element where we want to toggle a class

// Add an event listener to detect clicks on the mobile menu icon
mobileMenu.addEventListener('click', function() {
    // When clicked, toggle the 'menu-open' class on the header
    // If the class is present, it will be removed, otherwise it will be added
    header.classList.toggle('menu-open');
});
