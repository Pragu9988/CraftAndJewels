const accordions = document.querySelectorAll('.accordion');
console.log(accordions);
accordions.forEach((accordion) => {
    const icon = accordion.querySelector('.icon');
    const vacancyDetails = accordion.querySelector('.vacancy-details');
    const vacancyWrapper = accordion.querySelector('.vacancy-wrapper');

    accordion.addEventListener('click', () => {
        if (!icon.classList.contains('active')) {
            // Close all other accordions
            accordions.forEach(item => {
                if (item !== accordion) {
                    const itemIcon = item.querySelector('.icon');
                    const vacancy = item.querySelector('.vacancy-wrapper');
                    const itemDetails = item.querySelector('.vacancy-details');
                    itemIcon.classList.remove('active');
                    vacancy.classList.remove('active');
                    itemDetails.style.maxHeight = null;
                }
            });

            // Open the clicked accordion
            icon.classList.add('active');
            vacancyWrapper.classList.add('active');
            // vacancyDetails.classList.add('active');
            vacancyDetails.style.maxHeight = vacancyDetails.scrollHeight + 'px';
        } else {
            // Close the clicked accordion
            icon.classList.remove('active');
            vacancyWrapper.classList.remove('active');
            // vacancyDetails.classList.add('active');
            vacancyDetails.style.maxHeight = null;
        }
    });
});