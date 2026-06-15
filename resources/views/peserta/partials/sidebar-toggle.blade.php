<style>
    body.sidebar-collapsed {
        min-width: 0 !important;
        overflow-x: hidden !important;
    }

    body.sidebar-collapsed .sidebar {
        transform: translateX(-100%);
        pointer-events: none;
    }

    body.sidebar-collapsed .main-content {
        margin-left: 0 !important;
    }

    .sidebar,
    .main-content {
        transition: transform .25s ease, margin-left .25s ease;
    }

    @media (max-width: 991px) {
        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0 !important;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.sidebar');
        const topHeader = document.querySelector('.top-header');

        if (!sidebar || !topHeader) {
            return;
        }

        const storageKey = 'pesertaSidebarCollapsed';
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center';
        toggleButton.style.width = '42px';
        toggleButton.style.height = '42px';
        toggleButton.innerHTML = '<i class="bi bi-list fs-4"></i>';
        toggleButton.setAttribute('aria-label', 'Tutup sidebar');
        toggleButton.setAttribute('aria-expanded', 'true');

        const headerRow = topHeader.querySelector('.d-flex');
        if (headerRow) {
            headerRow.prepend(toggleButton);
        } else {
            topHeader.prepend(toggleButton);
        }

        const applyState = (collapsed) => {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            toggleButton.setAttribute('aria-expanded', String(!collapsed));
            toggleButton.setAttribute('aria-label', collapsed ? 'Buka sidebar' : 'Tutup sidebar');

            toggleButton.setAttribute('title', collapsed ? 'Buka Sidebar' : 'Tutup Sidebar');
        };

        const storedValue = localStorage.getItem(storageKey);
        applyState(storedValue === '1');

        toggleButton.addEventListener('click', () => {
            const collapsed = !document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem(storageKey, collapsed ? '1' : '0');
            applyState(collapsed);
        });
    });
</script>
