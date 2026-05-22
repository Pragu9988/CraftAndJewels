/**
 * FAQ Accordion — Heritage Craft & Jewels
 *
 * Handles accessible accordion expand/collapse on the FAQ page.
 * Uses the `hidden` attribute + CSS grid-template-rows for smooth animation.
 */

(function () {
    'use strict';

    /**
     * Initialise all FAQ accordion items on the page.
     */
    function initFaqAccordion() {
        const faqItems = document.querySelectorAll('.ht-faq__item');

        if (!faqItems.length) return;

        faqItems.forEach(function (item) {
            const trigger = item.querySelector('.ht-faq__question');
            const panel   = item.querySelector('.ht-faq__answer');

            if (!trigger || !panel) return;

            trigger.addEventListener('click', function () {
                const isOpen = item.classList.contains('is-open');

                // Close all open items (single-open accordion behaviour)
                faqItems.forEach(function (otherItem) {
                    if (otherItem !== item && otherItem.classList.contains('is-open')) {
                        closeItem(otherItem);
                    }
                });

                // Toggle the clicked item
                if (isOpen) {
                    closeItem(item);
                } else {
                    openItem(item);
                }
            });
        });
    }

    /**
     * Open a specific FAQ item.
     * @param {HTMLElement} item - The .ht-faq__item element.
     */
    function openItem(item) {
        const trigger = item.querySelector('.ht-faq__question');
        const panel   = item.querySelector('.ht-faq__answer');

        item.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        panel.removeAttribute('hidden');
    }

    /**
     * Close a specific FAQ item.
     * @param {HTMLElement} item - The .ht-faq__item element.
     */
    function closeItem(item) {
        const trigger = item.querySelector('.ht-faq__question');
        const panel   = item.querySelector('.ht-faq__answer');

        item.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.setAttribute('hidden', '');
    }

    // Initialise on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFaqAccordion);
    } else {
        initFaqAccordion();
    }

})();
