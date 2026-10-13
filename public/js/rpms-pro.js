/**
 * RPMS Pro — Command Palette & Theme Toggle
 *
 * Features:
 *   - Ctrl+K  : Open command palette (fuzzy search projects, nav links)
 *   - Theme   : Dark/Light toggle persisted in localStorage
 *   - Notification Polling : Badge update every 30 seconds
 *   - FAB     : Floating Action Button toggle
 *
 * No external dependencies — pure vanilla JS.
 */
(function () {
    'use strict';

    /* =========================================================================
       THEME TOGGLE
       ========================================================================= */
    const THEME_KEY = 'rpms-theme';

    function getPreferredTheme() {
        const stored = localStorage.getItem(THEME_KEY);
        if (stored) return stored;
        return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
        // Update toggle button icon
        const toggleBtn = document.getElementById('rpms-theme-btn');
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('.bi');
            if (icon) {
                icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
        }
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || 'dark';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    // Apply on load
    applyTheme(getPreferredTheme());

    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('rpms-theme-btn');
        if (btn) btn.addEventListener('click', toggleTheme);
    });

    /* =========================================================================
       COMMAND PALETTE  (Ctrl+K)
       ========================================================================= */
    const NAV_ITEMS = [
        { label: 'Dashboard',           icon: 'bi-speedometer2',      url: '/dashboard',          roles: 'all' },
        { label: 'My Projects',         icon: 'bi-folder2-open',      url: '/projects',           roles: 'all' },
        { label: 'Create Project',      icon: 'bi-plus-circle',       url: '/projects/create',    roles: 'pi' },
        { label: 'Progress Reports',    icon: 'bi-bar-chart-line',    url: '/progress',           roles: 'all' },
        { label: 'Notifications',       icon: 'bi-bell',              url: '/notifications',      roles: 'all' },
        { label: 'Research Calls',      icon: 'bi-megaphone',         url: '/calls',              roles: 'all' },
        { label: 'Publications',        icon: 'bi-journal-richtext',  url: '/papers',             roles: 'all' },
        { label: 'Certificates',        icon: 'bi-award',             url: '/certificates',       roles: 'coordinator,vparttcs,rcsc,admin' },
        { label: 'Analytics',           icon: 'bi-graph-up-arrow',    url: '/analytics',          roles: 'rcsc,vparttcs,admin' },
        { label: 'Reports Hub',         icon: 'bi-file-earmark-pdf',  url: '/reports',            roles: 'coordinator,dean,rcsc,vparttcs,admin' },
        { label: 'Finance Disbursement',icon: 'bi-cash-coin',         url: '/finance/disbursement',roles: 'finance' },
        { label: 'DH Screening',        icon: 'bi-funnel',            url: '/dh/screening',       roles: 'dh' },
        { label: 'Coordinator Hub',      icon: 'bi-diagram-3',         url: '/coordinator/hub',    roles: 'coordinator' },
        { label: 'Dean Approvals',       icon: 'bi-check2-square',     url: '/dean/approvals',     roles: 'dean' },
        { label: 'IRERC Panel',          icon: 'bi-shield-check',      url: '/irerc/panel',        roles: 'irerc' },
        { label: 'RCSC Portal',          icon: 'bi-bank',              url: '/rcsc/portal',        roles: 'rcsc,vparttcs' },
        { label: 'User Management',      icon: 'bi-people',            url: '/admin/users',        roles: 'admin' },
        { label: 'Audit Logs',           icon: 'bi-clock-history',     url: '/admin/audit-logs',   roles: 'admin' },
        { label: 'Backups',              icon: 'bi-hdd',               url: '/admin/backups',      roles: 'admin' },
        { label: 'Thematic Areas',       icon: 'bi-tags',              url: '/admin/thematic-areas',roles: 'admin' },
        { label: 'Departments',          icon: 'bi-building',          url: '/admin/departments',  roles: 'admin' },
        { label: 'Profile Settings',     icon: 'bi-person-gear',       url: '/profile',            roles: 'all' },
    ];

    let commandOverlay, commandInput, commandResults;
    let activeIndex = 0;

    function getUserRole() {
        const meta = document.querySelector('meta[name="user-role"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function filterNavItems(query) {
        const role = getUserRole();
        const q = query.toLowerCase().trim();
        return NAV_ITEMS.filter(function (item) {
            // Role check
            if (item.roles !== 'all' && !item.roles.split(',').includes(role)) return false;
            // Query check
            if (!q) return true;
            return item.label.toLowerCase().includes(q);
        }).slice(0, 10);
    }

    function renderResults(items) {
        if (!commandResults) return;
        commandResults.innerHTML = '';
        if (items.length === 0) {
            commandResults.innerHTML = '<div style="padding:1rem;color:var(--rpms-text-muted);text-align:center;font-size:0.85rem;">No results found</div>';
            return;
        }
        items.forEach(function (item, idx) {
            var div = document.createElement('a');
            div.href = item.url;
            div.className = 'rpms-command-item' + (idx === activeIndex ? ' active' : '');
            div.innerHTML =
                '<i class="bi ' + item.icon + '"></i>' +
                '<span class="rpms-command-item-label">' + item.label + '</span>' +
                '<span class="rpms-command-item-hint"><kbd class="rpms-command-kbd">↵</kbd></span>';
            commandResults.appendChild(div);
        });
    }

    function openCommandPalette() {
        if (!commandOverlay) return;
        commandOverlay.classList.add('active');
        commandInput.value = '';
        activeIndex = 0;
        renderResults(filterNavItems(''));
        setTimeout(function () { commandInput.focus(); }, 50);
    }

    function closeCommandPalette() {
        if (!commandOverlay) return;
        commandOverlay.classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function () {
        commandOverlay = document.getElementById('rpms-command-overlay');
        commandInput = document.getElementById('rpms-command-input');
        commandResults = document.getElementById('rpms-command-results');

        if (!commandOverlay) return;

        // Listen for input
        if (commandInput) {
            commandInput.addEventListener('input', function () {
                activeIndex = 0;
                renderResults(filterNavItems(this.value));
            });
            commandInput.addEventListener('keydown', function (e) {
                var items = commandResults ? commandResults.querySelectorAll('.rpms-command-item') : [];
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, items.length - 1);
                    renderResults(filterNavItems(this.value));
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, 0);
                    renderResults(filterNavItems(this.value));
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (items[activeIndex]) {
                        window.location.href = items[activeIndex].href;
                    }
                } else if (e.key === 'Escape') {
                    closeCommandPalette();
                }
            });
        }

        // Click backdrop to close
        commandOverlay.addEventListener('click', function (e) {
            if (e.target === commandOverlay) closeCommandPalette();
        });

        // Search button trigger
        var searchBtn = document.getElementById('rpms-search-trigger');
        if (searchBtn) searchBtn.addEventListener('click', openCommandPalette);
    });

    // Global Ctrl+K shortcut
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (commandOverlay && commandOverlay.classList.contains('active')) {
                closeCommandPalette();
            } else {
                openCommandPalette();
            }
        }
        if (e.key === 'Escape' && commandOverlay && commandOverlay.classList.contains('active')) {
            closeCommandPalette();
        }
    });

    /* =========================================================================
       NOTIFICATION POLLING (every 30 seconds)
       ========================================================================= */
    function pollNotifications() {
        var badge = document.getElementById('rpms-notif-badge');
        var mobileBadge = document.getElementById('rpms-notif-badge-mobile');
        if (!badge) return;

        fetch('/api/v1/notifications/poll', {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.ok ? res.json() : null; })
        .then(function (data) {
            if (!data || !data.data) return;
            var count = data.data.unread_count || 0;
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = count > 0 ? 'flex' : 'none';
            if (mobileBadge) {
                mobileBadge.textContent = count > 99 ? '99+' : count;
                mobileBadge.style.display = count > 0 ? 'flex' : 'none';
            }
        })
        .catch(function () { /* silently ignore */ });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initial poll
        setTimeout(pollNotifications, 2000);
        // Recurring poll every 30 seconds
        setInterval(pollNotifications, 30000);
    });

    /* =========================================================================
       FAB (Floating Action Button) Toggle
       ========================================================================= */
    document.addEventListener('DOMContentLoaded', function () {
        var fab = document.getElementById('rpms-fab');
        var fabMenu = document.getElementById('rpms-fab-menu');
        if (fab && fabMenu) {
            fab.addEventListener('click', function () {
                fabMenu.classList.toggle('active');
                fab.querySelector('.bi').className = fabMenu.classList.contains('active')
                    ? 'bi bi-x-lg'
                    : 'bi bi-lightning-charge-fill';
            });
            // Close FAB menu when clicking outside
            document.addEventListener('click', function (e) {
                if (!fab.contains(e.target) && !fabMenu.contains(e.target)) {
                    fabMenu.classList.remove('active');
                    fab.querySelector('.bi').className = 'bi bi-lightning-charge-fill';
                }
            });
        }
    });

    /* =========================================================================
       DASHBOARD WIDGET DRAG & DROP (HTML5 Drag API)
       ========================================================================= */
    const WIDGET_ORDER_KEY = 'rpms-widget-order';

    document.addEventListener('DOMContentLoaded', function () {
        var grid = document.getElementById('rpms-dashboard-grid');
        if (!grid) return;

        // Restore saved order
        var saved = localStorage.getItem(WIDGET_ORDER_KEY);
        if (saved) {
            try {
                var order = JSON.parse(saved);
                var children = Array.from(grid.children);
                var sorted = order.map(function (id) {
                    return children.find(function (c) { return c.id === id; });
                }).filter(Boolean);
                sorted.forEach(function (el) { grid.appendChild(el); });
            } catch (e) { /* ignore */ }
        }

        // Enable drag
        Array.from(grid.children).forEach(function (child) {
            child.setAttribute('draggable', 'true');
            child.addEventListener('dragstart', function (e) {
                e.dataTransfer.setData('text/plain', child.id);
                child.style.opacity = '0.5';
            });
            child.addEventListener('dragend', function () {
                child.style.opacity = '1';
            });
            child.addEventListener('dragover', function (e) {
                e.preventDefault();
            });
            child.addEventListener('drop', function (e) {
                e.preventDefault();
                var draggedId = e.dataTransfer.getData('text/plain');
                var dragged = document.getElementById(draggedId);
                if (dragged && dragged !== child) {
                    var rect = child.getBoundingClientRect();
                    var midY = rect.top + rect.height / 2;
                    if (e.clientY < midY) {
                        grid.insertBefore(dragged, child);
                    } else {
                        grid.insertBefore(dragged, child.nextSibling);
                    }
                    // Save order
                    var newOrder = Array.from(grid.children).map(function (c) { return c.id; }).filter(Boolean);
                    localStorage.setItem(WIDGET_ORDER_KEY, JSON.stringify(newOrder));
                }
            });
        });
    });

    // Expose toggle globally
    window.rpmsToggleTheme = toggleTheme;
    window.rpmsOpenCommandPalette = openCommandPalette;
})();
