const BondTabs = {
    // Drag threshold in px — below this it's treated as a click, not a drag
    DRAG_THRESHOLD: 5,

    init() {
        this.section = document.querySelector('.ht-shop-by-bond');
        if (!this.section) return;

        this.tabs    = this.section.querySelectorAll('.ht-shop-by-bond__tab');
        this.panels  = this.section.querySelectorAll('.ht-shop-by-bond__tab-panel');
        this.tabsList = this.section.querySelector('.ht-shop-by-bond__tabs');

        // Drag / touch state
        this.isDown    = false;
        this.startX    = 0;
        this.scrollLeft = 0;
        this.dragDelta  = 0;
        this.dragged    = false;

        this._activateFirstTab();
        this.bindEvents();
        this.initDragScroll();
        this.initTouchScroll();
    },

    // ── Activate first tab & panel on load ───────────────────────────────────
    _activateFirstTab() {
        if (!this.tabs.length) return;

        // Ensure the list starts from the left (not centered)
        if (this.tabsList) {
            this.tabsList.scrollLeft = 0;
        }

        // Guarantee first tab & panel carry is-active (PHP already does this,
        // but this is a safety net in case of dynamic injection)
        const firstTab = this.tabs[0];
        this.tabs.forEach(t => t.classList.remove('is-active'));
        firstTab.classList.add('is-active');

        this.panels.forEach((panel, i) => {
            panel.classList.toggle('is-active', i === 0);
        });
    },

    // ── Click binding ────────────────────────────────────────────────────────
    bindEvents() {
        this.tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                // Ignore click if cursor/finger actually dragged
                if (this.dragged) {
                    this.dragged = false;
                    return;
                }

                e.preventDefault();
                const targetId = tab.dataset.target;
                if (!targetId) return;

                this.switchTab(tab, targetId);
            });
        });
    },

    // ── Mouse drag-to-scroll (desktop) ───────────────────────────────────────
    initDragScroll() {
        if (!this.tabsList) return;

        this.tabsList.addEventListener('mousedown', (e) => {
            this.isDown    = true;
            this.startX    = e.pageX - this.tabsList.offsetLeft;
            this.scrollLeft = this.tabsList.scrollLeft;
            this.dragDelta  = 0;
            this.dragged    = false;
            this.tabsList.classList.add('is-dragging');
        });

        this.tabsList.addEventListener('mouseleave', () => {
            this.isDown = false;
            this.tabsList.classList.remove('is-dragging');
        });

        this.tabsList.addEventListener('mouseup', () => {
            this.isDown = false;
            this.tabsList.classList.remove('is-dragging');
        });

        this.tabsList.addEventListener('mousemove', (e) => {
            if (!this.isDown) return;
            e.preventDefault();
            const x    = e.pageX - this.tabsList.offsetLeft;
            const walk = (x - this.startX) * 1.5;
            this.dragDelta = Math.abs(x - this.startX);
            if (this.dragDelta > this.DRAG_THRESHOLD) {
                this.dragged = true;
            }
            this.tabsList.scrollLeft = this.scrollLeft - walk;
        });
    },

    // ── Touch swipe-to-scroll (mobile) ───────────────────────────────────────
    initTouchScroll() {
        if (!this.tabsList) return;

        let touchStartX   = 0;
        let touchScrollLeft = 0;

        this.tabsList.addEventListener('touchstart', (e) => {
            touchStartX    = e.touches[0].pageX;
            touchScrollLeft = this.tabsList.scrollLeft;
            this.dragDelta  = 0;
            this.dragged    = false;
        }, { passive: true });

        this.tabsList.addEventListener('touchmove', (e) => {
            const delta = touchStartX - e.touches[0].pageX;
            this.dragDelta = Math.abs(delta);
            if (this.dragDelta > this.DRAG_THRESHOLD) {
                this.dragged = true;
            }
            this.tabsList.scrollLeft = touchScrollLeft + delta;
        }, { passive: true });

        this.tabsList.addEventListener('touchend', () => {
            // Reset dragged flag after a short delay so the click handler
            // (which fires after touchend) can read it.
            setTimeout(() => {
                this.dragged = false;
            }, 100);
        }, { passive: true });
    },

    // ── Tab switch ───────────────────────────────────────────────────────────
    switchTab(selectedTab, targetId) {
        // 1. Update tab active states
        this.tabs.forEach(t => t.classList.remove('is-active'));
        selectedTab.classList.add('is-active');

        // 2. Update panel active states
        this.panels.forEach(panel => {
            panel.classList.toggle('is-active', panel.id === targetId);
        });

        // 3. Scroll & center the selected tab within the strip
        this.centerActiveTab(selectedTab);

        // 4. Refresh AOS animations if present
        if (typeof AOS !== 'undefined') {
            AOS.refreshHard();
        }
    },

    // ── Center active tab in the scroll container ────────────────────────────
    centerActiveTab(tab) {
        if (!this.tabsList) return;

        const containerWidth = this.tabsList.offsetWidth;
        const tabOffsetLeft  = tab.offsetLeft;
        const tabWidth       = tab.offsetWidth;

        // Position that puts the tab in the horizontal centre of the strip
        const scrollPos = tabOffsetLeft - (containerWidth / 2) + (tabWidth / 2);

        this.tabsList.scrollTo({
            left: Math.max(0, scrollPos),
            behavior: 'smooth',
        });
    },
};

document.addEventListener('DOMContentLoaded', () => {
    BondTabs.init();
});
