const mobileMenu = document.querySelector('.mobile-menu');  // Select the mobile menu element (the hamburger icon)
const header = document.querySelector('header');            // Select the header element where we want to toggle a class

// Add an event listener to detect clicks on the mobile menu icon
mobileMenu.addEventListener('click', function() {
    // When clicked, toggle the 'menu-open' class on the header
    // If the class is present, it will be removed, otherwise it will be added
    header.classList.toggle('menu-open');
});

// Intersection observer to reveal hidden elements
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
        } else {
            entry.target.classList.remove('show');
        }
    });
});

const hiddenElements = document.querySelectorAll('.hidden');

hiddenElements.forEach((el) => observer.observe(el));

// Counter section
const counters = document.querySelectorAll('.stats-section__number'); // Fix document spelling
const counterOptions = {
    root: null, // Uses the viewport as the root
    rootMargin: '0px', // No margin around the root
    threshold: 0.5 // Triggers when 50% of the element is visible
};

const updateCounter = (counter) => {
    const target = +counter.getAttribute('data-target');
    const current = +counter.innerText;
    const increment = target / 200; // Divide the value to make the animation smoother

    if (current < target) {
        counter.innerText = Math.ceil(current + increment);
        setTimeout(() => updateCounter(counter), 10); // Update the value every 10ms
    } else {
        counter.innerText = target; // Ensure the final value is exact
    }
};

const handleCounterIntersect = (entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target;
            updateCounter(counter); // Start the animation when the element is visible
            observer.unobserve(counter); // Stop observing once the animation has started
        }
    });
};

// Create a new observer specifically for the counters
const counterObserver = new IntersectionObserver(handleCounterIntersect, counterOptions);

counters.forEach(counter => {
    counterObserver.observe(counter); // Observe each counter
});
