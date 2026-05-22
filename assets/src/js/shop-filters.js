/**
 * Shop Taxonomy Filter Bar
 *
 * Handles dropdown toggling, checkbox filtering,
 * and AJAX product loading.
 */

const FilterBar = {
    init() {
        this.filterBar = document.querySelector('.ht-filter-bar');
        if (!this.filterBar) return;

        this.productsContainer = document.getElementById('ht-shop-products');
        this.items = this.filterBar.querySelectorAll('.ht-filter-item');

        this.bindEvents();
    },

    bindEvents() {
        // Toggle dropdowns
        this.items.forEach(item => {
            const trigger = item.querySelector('.ht-filter-item__trigger');
            if (trigger) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDropdown(item);
                });
            }
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.ht-filter-item')) {
                this.closeAll();
            }
        });

        // Checkbox change → filter
        this.filterBar.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') {
                this.filterProducts();
            }
        });
    },

    toggleDropdown(item) {
        const isOpen = item.classList.contains('is-open');

        // Close all dropdowns first
        this.closeAll();

        // Toggle current
        if (!isOpen) {
            item.classList.add('is-open');
            item.querySelector('.ht-filter-item__trigger')
                ?.setAttribute('aria-expanded', 'true');
        }
    },

    closeAll() {
        this.items.forEach(item => {
            item.classList.remove('is-open');
            item.querySelector('.ht-filter-item__trigger')
                ?.setAttribute('aria-expanded', 'false');
        });
    },

    getSelectedFilters() {
        const filters = {};

        this.items.forEach(item => {
            const taxonomy = item.dataset.taxonomy;
            const checked = item.querySelectorAll('input[type="checkbox"]:checked');

            if (checked.length > 0) {
                filters[taxonomy] = Array.from(checked).map(cb => cb.value);
                item.classList.add('is-active');
            } else {
                item.classList.remove('is-active');
            }
        });

        return filters;
    },

    async filterProducts() {
        if (!this.productsContainer || typeof heritageFilters === 'undefined') return;

        const filters = this.getSelectedFilters();

        // Show loading state
        this.productsContainer.classList.add('is-loading');

        const formData = new FormData();
        formData.append('action', 'heritage_filter_products');
        formData.append('nonce', heritageFilters.nonce);

        // Send filters as nested object
        for (const [taxonomy, terms] of Object.entries(filters)) {
            terms.forEach(term => {
                formData.append(`filters[${taxonomy}][]`, term);
            });
        }

        // Pass current taxonomy context for archive pages
        const currentTaxonomy = this.filterBar.dataset.currentTaxonomy;
        const currentTerm = this.filterBar.dataset.currentTerm;
        if (currentTaxonomy) {
            formData.append('current_taxonomy', currentTaxonomy);
            formData.append('current_term', currentTerm);
        }

        try {
            const response = await fetch(heritageFilters.ajaxUrl, {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                this.productsContainer.innerHTML = result.data.html;

                // Append pagination if present
                if (result.data.pagination) {
                    this.productsContainer.insertAdjacentHTML('beforeend', result.data.pagination);
                }
            }
        } catch (error) {
            console.error('Filter error:', error);
        } finally {
            this.productsContainer.classList.remove('is-loading');
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    FilterBar.init();
});
