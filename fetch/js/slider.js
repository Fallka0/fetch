const slider = document.getElementById('slider');
const navDots = document.querySelectorAll('.slider_nav div');

navDots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        const slideWidth = slider.clientWidth; // Get the width of the slider
        slider.scrollTo({
            left: slideWidth * index, // Move to the corresponding image
            behavior: 'smooth'
        });
    });
});
