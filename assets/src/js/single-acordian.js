
/**
 * Single Product Accordions
 */
function initProductAccordions() {
           const tabsWrapper = document.querySelector('.ht-single-product .woocommerce-tabs');
           if (!tabsWrapper) return;

           const panels = tabsWrapper.querySelectorAll('.woocommerce-Tabs-panel');
           const tabsNav = tabsWrapper.querySelector('ul.tabs');
           const tabLinks = tabsNav ? tabsNav.querySelectorAll('li a') : [];

           panels.forEach((panel, index) => {
                      // Get title from the corresponding tab link if possible
                      const tabId = panel.id.replace('tab-', '');
                      let title = '';

                      if (tabLinks[index]) {
                                 title = tabLinks[index].textContent;
                      } else {
                                 // Fallback to ID-based title
                                 title = tabId.charAt(0).toUpperCase() + tabId.slice(1).replace('_', ' ');
                      }

                      // Create header if not already there
                      if (!panel.querySelector('.ht-accordion-header')) {
                                 const header = document.createElement('h3');
                                 header.className = 'ht-accordion-header';
                                 header.innerHTML = `<span>${title}</span>`;

                                 // Wrap existing content
                                 const content = document.createElement('div');
                                 content.className = 'ht-accordion-content';
                                 while (panel.firstChild) {
                                            content.appendChild(panel.firstChild);
                                 }

                                 panel.appendChild(header);
                                 panel.appendChild(content);

                                 // Open first accordion by default (usually Description)
                                 if (index === 0) {
                                            header.classList.add('active');
                                            content.classList.add('active');
                                 }

                                 header.addEventListener('click', () => {
                                            const isActive = header.classList.contains('active');
                                            if (!isActive) {
                                                       header.classList.add('active');
                                                       content.classList.add('active');
                                            } else {
                                                       header.classList.remove('active');
                                                       content.classList.remove('active');
                                            }
                                 });
                      }
           });
}

document.addEventListener('DOMContentLoaded', () => {
           initProductAccordions();
});

