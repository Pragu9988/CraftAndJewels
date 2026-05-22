document.addEventListener("DOMContentLoaded", function () {

           // Mobile Menu Toggle
           const menuToggle = document.getElementById('ht-menu-toggle');
           const menuClose = document.getElementById('ht-menu-close');
           const mobileNav = document.getElementById('ht-mobile-nav');
           const mobileOverlay = document.getElementById('ht-mobile-overlay');

           if (menuToggle && menuClose && mobileNav) {
                      menuToggle.addEventListener('click', function () {
                                 mobileNav.classList.remove('-translate-x-full');
                                 if (mobileOverlay) {
                                            mobileOverlay.classList.remove('hidden');
                                            setTimeout(() => mobileOverlay.classList.remove('opacity-0'), 10);
                                 }
                                 document.body.style.overflow = 'hidden'; // prevent bg scroll
                      });

                      function closeMenu() {
                                 mobileNav.classList.add('-translate-x-full');
                                 if (mobileOverlay) {
                                            mobileOverlay.classList.add('opacity-0');
                                            setTimeout(() => mobileOverlay.classList.add('hidden'), 300);
                                 }
                                 document.body.style.overflow = '';
                      }

                      menuClose.addEventListener('click', closeMenu);
                      if (mobileOverlay) {
                                 mobileOverlay.addEventListener('click', closeMenu);
                      }
           }

           // Mobile Submenu Toggle
           const mobileSubmenuToggles = document.querySelectorAll('.ht-mobile-submenu-toggle');
           mobileSubmenuToggles.forEach(toggle => {
                      toggle.addEventListener('click', function () {
                                 const submenuWrapper = this.nextElementSibling;
                                 const icon = this.querySelector('svg');

                                 // Close all other submenus
                                 mobileSubmenuToggles.forEach(otherToggle => {
                                            if (otherToggle !== this) {
                                                       const otherWrapper = otherToggle.nextElementSibling;
                                                       const otherIcon = otherToggle.querySelector('svg');
                                                       if (otherWrapper && otherWrapper.classList.contains('is-open')) {
                                                                  otherWrapper.classList.remove('is-open');
                                                                  if (otherIcon) {
                                                                             otherIcon.classList.remove('rotate-180', 'text-red-500');
                                                                             otherIcon.classList.add('text-gray-600');
                                                                  }
                                                       }
                                            }
                                 });

                                 if (submenuWrapper) {
                                            const isOpen = submenuWrapper.classList.contains('is-open');
                                            if (isOpen) {
                                                       submenuWrapper.classList.remove('is-open');
                                                       if (icon) {
                                                                  icon.classList.remove('rotate-180', 'text-red-500');
                                                                  icon.classList.add('text-gray-600');
                                                       }
                                            } else {
                                                       submenuWrapper.classList.add('is-open');
                                                       if (icon) {
                                                                  icon.classList.add('rotate-180', 'text-red-500');
                                                                  icon.classList.remove('text-gray-600');
                                                       }
                                            }
                                 }
                      });
           });

           // Highlight Active Menu Items based on current URL
           const currentPath = window.location.pathname;
           const allLinks = document.querySelectorAll('.ht-header-nav a, .ht-mobile-nav a');

           allLinks.forEach(link => {
                      const linkHref = link.getAttribute('href');
                      if (linkHref && linkHref !== '#' && linkHref.startsWith('http')) {
                                 try {
                                            const linkPath = new URL(linkHref).pathname;

                                            // Active condition: current path matches link path or starts with it (except root directory)
                                            if (currentPath === linkPath || (linkPath !== '/' && currentPath.startsWith(linkPath))) {

                                                       // Add active class to the link itself
                                                       link.classList.add('current-menu-item');

                                                       // Handle Tailwind classes for mobile child links
                                                       if (link.classList.contains('text-slate-600') || link.classList.contains('text-gray-800')) {
                                                                  link.classList.remove('text-slate-600', 'text-gray-800');
                                                                  link.classList.add('text-red-600', 'font-bold');
                                                       }

                                                       // Desktop Parent
                                                       const desktopParentLi = link.closest('.ht-has-submenu');
                                                       if (desktopParentLi) {
                                                                  const parentLink = desktopParentLi.querySelector('.menu-item-parent');
                                                                  if (parentLink) {
                                                                             parentLink.classList.add('current-menu-parent');
                                                                  }
                                                       }

                                                       // Mobile Parent
                                                       const mobileParentLi = link.closest('.ht-mobile-has-submenu');
                                                       if (mobileParentLi) {
                                                                  const parentSpan = mobileParentLi.querySelector('.ht-mobile-submenu-toggle span');
                                                                  if (parentSpan) {
                                                                             parentSpan.classList.remove('text-gray-800');
                                                                             parentSpan.classList.add('text-red-600', 'font-bold');
                                                                  }
                                                       }

                                                       // Desktop submenu top title
                                                       const desktopSubmenuCol = link.closest('div.flex-col');
                                                       if (desktopSubmenuCol) {
                                                                  const topTitleLink = desktopSubmenuCol.querySelector('.submenu-top-title__link');
                                                                  if (topTitleLink && topTitleLink !== link) {
                                                                             topTitleLink.classList.add('current-menu-parent');
                                                                  }
                                                       }

                                                       // Mobile submenu top title
                                                       const mobileCategoryDiv = link.closest('div > span.block + ul');
                                                       if (mobileCategoryDiv && mobileCategoryDiv.previousElementSibling) {
                                                                  const spanA = mobileCategoryDiv.previousElementSibling.querySelector('a');
                                                                  if (spanA && spanA !== link) {
                                                                             spanA.parentElement.classList.remove('text-slate-500');
                                                                             spanA.parentElement.classList.add('text-red-600', 'font-bold');
                                                                  }
                                                       }
                                            }
                                 } catch (e) {
                                            // Ignore invalid URLs
                                 }
                      }
           });

});