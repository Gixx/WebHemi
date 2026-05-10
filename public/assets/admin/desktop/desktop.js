(() => {
    const shell = document.getElementById('desktopShell');
    if (!shell) {
        return;
    }

    const windowLayer = document.getElementById('desktopWindowLayer');
    const taskButtons = document.getElementById('desktopTaskButtons');
    const startButton = document.getElementById('desktopStartButton');
    const startMenu = document.getElementById('desktopStartMenu');
    const clock = document.getElementById('desktopClock');

    if (!windowLayer || !taskButtons || !startButton || !startMenu || !clock) {
        return;
    }

    let zIndexSeed = 50;
    const windows = new Map();
    let selectedIcon = null;

    // URL-prefix → module window mapping (must match control_panel.html.twig data-window-* attributes)
    const MODULE_WINDOWS = [
        { prefix: '/admin/sites',       windowId: 'module-sites',       title: 'Sites',       width: 760, height: 520 },
        { prefix: '/admin/permissions', windowId: 'module-permissions', title: 'Permissions', width: 760, height: 520 },
        { prefix: '/admin/roles',       windowId: 'module-roles',       title: 'Roles',       width: 760, height: 520 },
        { prefix: '/admin/users',       windowId: 'module-users',       title: 'Users',       width: 760, height: 520 },
    ];

    const findModuleWindowForUrl = (url) => {
        try {
            const pathname = new URL(url, window.location.href).pathname;
            return MODULE_WINDOWS.find((m) => pathname.startsWith(m.prefix)) ?? null;
        } catch {
            return null;
        }
    };

    // --- Hash-based state (deeplink) ---

    const serializeHash = () => {
        const parts = [];

        windows.forEach((state, windowId) => {
            try {
                const currentPath = new URL(state.currentUrl, window.location.href).pathname;
                const title = state.element.querySelector('.desktop-window-title')?.textContent?.trim() || '';
                const width = state.element.offsetWidth || 640;
                const height = state.element.offsetHeight || 440;
                const active = state.element.classList.contains('active') ? '1' : '0';
                // format: windowId[path|title|width|height|active]
                parts.push(`${windowId}[${currentPath}|${encodeURIComponent(title)}|${width}|${height}|${active}]`);
            } catch {
                parts.push(`${windowId}[||640|440|0]`);
            }
        });

        return parts.join('|');
    };

    const updateHash = () => {
        const hash = serializeHash();
        history.replaceState(
            null,
            '',
            hash ? `#${hash}` : `${window.location.pathname}${window.location.search}`,
        );
    };

    const parseHash = (hash) => {
        if (!hash || hash === '#') {
            return [];
        }

        const raw = hash.startsWith('#') ? hash.slice(1) : hash;
        const results = [];
        // format: windowId[path|title|width|height]
        const regex = /([^|[\]]+)\[([^\]]*)\]/g;
        let match;

        while ((match = regex.exec(raw)) !== null) {
            const windowId = match[1].trim();
            const inner = match[2];
            const parts = inner.split('|');
            const path = parts[0] || '';
            const title = parts[1] ? decodeURIComponent(parts[1]) : '';
            const width = Number.parseInt(parts[2], 10) || 640;
            const height = Number.parseInt(parts[3], 10) || 440;
            const active = parts[4] === '1';
            results.push({ windowId, path, title, width, height, active });
        }

        return results;
    };

    const findTriggerByWindowId = (windowId) => {
        const el = shell.querySelector(`[data-window-id="${CSS.escape(windowId)}"]`);

        return el instanceof HTMLElement ? el : null;
    };

    const restoreFromHash = () => {
        const entries = parseHash(window.location.hash);
        let activeWindowId = null;

        for (const { windowId, path, title, width, height, active } of entries) {
            // Try desktop trigger for defaults, but don't require it
            const trigger = findTriggerByWindowId(windowId);
            const url = path || (trigger ? trigger.dataset.windowUrl : '');
            if (!url) {
                continue;
            }

            createWindow({
                windowId,
                title: title || (trigger ? trigger.dataset.windowTitle : '') || 'Window',
                url,
                width: width || Number.parseInt(trigger?.dataset.windowWidth || '640', 10),
                height: height || Number.parseInt(trigger?.dataset.windowHeight || '440', 10),
            });

            if (active) {
                activeWindowId = windowId;
            }
        }

        // Re-focus the window that was active when the page was left
        if (activeWindowId) {
            focusWindow(activeWindowId);
        }
    };

    const clearIconSelection = () => {
        if (!(selectedIcon instanceof HTMLElement)) {
            selectedIcon = null;
            return;
        }

        selectedIcon.classList.remove('selected');
        selectedIcon = null;
    };

    const selectIcon = (icon) => {
        if (!(icon instanceof HTMLElement)) {
            clearIconSelection();
            return;
        }

        if (selectedIcon === icon) {
            return;
        }

        clearIconSelection();
        selectedIcon = icon;
        selectedIcon.classList.add('selected');
    };

    const getDesktopIcons = () => {
        return Array.from(shell.querySelectorAll('.js-desktop-launch'))
            .filter((icon) => icon instanceof HTMLElement);
    };

    const selectAndFocusIcon = (icon) => {
        if (!(icon instanceof HTMLElement)) {
            return;
        }

        selectIcon(icon);
        icon.focus();
    };

    const findIconByDirection = (currentIcon, direction) => {
        const icons = getDesktopIcons();
        if (!(currentIcon instanceof HTMLElement) || icons.length === 0) {
            return null;
        }

        const currentRect = currentIcon.getBoundingClientRect();
        const currentCenterX = currentRect.left + (currentRect.width / 2);
        const currentCenterY = currentRect.top + (currentRect.height / 2);

        let bestMatch = null;
        let bestScore = Number.POSITIVE_INFINITY;

        icons.forEach((candidate) => {
            if (candidate === currentIcon) {
                return;
            }

            const rect = candidate.getBoundingClientRect();
            const centerX = rect.left + (rect.width / 2);
            const centerY = rect.top + (rect.height / 2);
            const deltaX = centerX - currentCenterX;
            const deltaY = centerY - currentCenterY;

            if (direction === 'left' && deltaX >= -2) {
                return;
            }

            if (direction === 'right' && deltaX <= 2) {
                return;
            }

            if (direction === 'up' && deltaY >= -2) {
                return;
            }

            if (direction === 'down' && deltaY <= 2) {
                return;
            }

            // Favor candidates in the requested axis, then shortest distance.
            const primary = (direction === 'left' || direction === 'right') ? Math.abs(deltaX) : Math.abs(deltaY);
            const secondary = (direction === 'left' || direction === 'right') ? Math.abs(deltaY) : Math.abs(deltaX);
            const score = primary + (secondary * 2.25);

            if (score < bestScore) {
                bestScore = score;
                bestMatch = candidate;
            }
        });

        return bestMatch;
    };

    const getIconLabel = (icon) => {
        if (!(icon instanceof HTMLElement)) {
            return '';
        }

        const labelElement = icon.querySelector('span');
        const rawLabel = labelElement?.textContent ?? icon.textContent ?? '';

        return rawLabel.trim();
    };

    const findIconByInitial = (initial, currentIcon) => {
        const icons = getDesktopIcons();
        if (icons.length === 0) {
            return null;
        }

        const normalizedInitial = initial.toLowerCase();
        const matchingIcons = icons.filter((icon) => getIconLabel(icon).toLowerCase().startsWith(normalizedInitial));
        if (matchingIcons.length === 0) {
            return null;
        }

        const currentIndex = matchingIcons.findIndex((icon) => icon === currentIcon);
        if (currentIndex === -1) {
            return matchingIcons[0];
        }

        return matchingIcons[(currentIndex + 1) % matchingIcons.length];
    };

    const updateClock = () => {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        clock.textContent = `${hours}:${minutes}`;
    };

    updateClock();
    window.setInterval(updateClock, 1000);

    startButton.addEventListener('click', (event) => {
        event.stopPropagation();
        startMenu.hidden = !startMenu.hidden;
    });

    document.addEventListener('click', (event) => {
        if (!startMenu.contains(event.target) && event.target !== startButton) {
            startMenu.hidden = true;
        }

        const clickedDesktopIcon = event.target.closest('.desktop-icon');
        if (!clickedDesktopIcon || !shell.contains(clickedDesktopIcon)) {
            clearIconSelection();
        }
    });

    const focusWindow = (windowId) => {
        const state = windows.get(windowId);
        if (!state) {
            return;
        }

        zIndexSeed += 1;
        state.element.style.zIndex = String(zIndexSeed);

        windows.forEach((entry) => {
            entry.taskButton.classList.remove('active');
            entry.element.classList.remove('active');
        });

        state.taskButton.classList.add('active');
        state.element.classList.add('active');
        updateHash();
    };

    const removeWindow = (windowId) => {
        const state = windows.get(windowId);
        if (!state) {
            return;
        }

        state.element.remove();
        state.taskButton.remove();
        windows.delete(windowId);
        updateHash();
    };

    const isInAppWindowUrl = (url) => {
        try {
            const parsed = new URL(url, window.location.href);

            return parsed.origin === window.location.origin && parsed.pathname.startsWith('/admin');
        } catch (error) {
            return false;
        }
    };

    const extractWindowMarkup = (rawHtml) => {
        if (typeof rawHtml !== 'string' || rawHtml.trim() === '') {
            return '<div class="desktop-window-loading">Empty response.</div>';
        }

        if (rawHtml.includes('<html')) {
            const parser = new DOMParser();
            const documentNode = parser.parseFromString(rawHtml, 'text/html');
            const bodyContent = documentNode.querySelector('.admin-body');

            if (bodyContent instanceof HTMLElement) {
                return bodyContent.innerHTML;
            }
        }

        return rawHtml;
    };

    const setWindowContent = async (windowId, url, requestOptions = {}) => {
        const state = windows.get(windowId);
        if (!state) {
            return;
        }

        state.content.innerHTML = '<div class="desktop-window-loading">Loading...</div>';

        try {
            const response = await window.fetch(url, {
                method: requestOptions.method || 'GET',
                body: requestOptions.body,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(requestOptions.headers || {}),
                },
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            state.content.innerHTML = extractWindowMarkup(await response.text());
            state.currentUrl = response.url || url;
            updateHash();
        } catch (error) {
            state.content.innerHTML = '<div class="desktop-window-loading">Failed to load window content.</div>';
        }
    };

    const handleWindowContentClick = (windowId, event) => {
        const state = windows.get(windowId);
        if (!state) {
            return;
        }

        const link = event.target.closest('a');
        if (!(link instanceof HTMLAnchorElement)) {
            return;
        }

        if (link.target && link.target !== '_self') {
            return;
        }

        if (!isInAppWindowUrl(link.href)) {
            return;
        }

        event.preventDefault();

        // If the link belongs to a different module window, open it there instead
        const targetModule = findModuleWindowForUrl(link.href);
        if (targetModule && targetModule.windowId !== windowId) {
            const existingTarget = windows.get(targetModule.windowId);
            if (existingTarget) {
                setWindowContent(targetModule.windowId, link.href);
                focusWindow(targetModule.windowId);
            } else {
                createWindow({
                    windowId: targetModule.windowId,
                    title: targetModule.title,
                    url: link.href,
                    width: targetModule.width,
                    height: targetModule.height,
                });
            }
            return;
        }

        setWindowContent(windowId, link.href);
    };

    const handleWindowFormSubmit = (windowId, event) => {
        const state = windows.get(windowId);
        if (!state) {
            return;
        }

        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const actionUrl = form.action || state.currentUrl;
        if (!isInAppWindowUrl(actionUrl)) {
            return;
        }

        event.preventDefault();
        const method = (form.method || 'GET').toUpperCase();

        if (method === 'GET') {
            const query = new URLSearchParams(new FormData(form)).toString();
            const targetUrl = query ? `${actionUrl}${actionUrl.includes('?') ? '&' : '?'}${query}` : actionUrl;
            setWindowContent(windowId, targetUrl);

            return;
        }

        setWindowContent(windowId, actionUrl, {
            method,
            body: new FormData(form),
        });
    };

    const makeDraggable = (windowElement, handle, windowId) => {
        let dragging = false;
        let dragOffsetX = 0;
        let dragOffsetY = 0;

        handle.addEventListener('mousedown', (event) => {
            if (event.button !== 0) {
                return;
            }

            dragging = true;
            focusWindow(windowId);

            const rect = windowElement.getBoundingClientRect();
            dragOffsetX = event.clientX - rect.left;
            dragOffsetY = event.clientY - rect.top;

            event.preventDefault();
        });

        document.addEventListener('mousemove', (event) => {
            if (!dragging) {
                return;
            }

            const shellRect = shell.getBoundingClientRect();
            const taskbarHeight = 34;
            const maxLeft = shellRect.width - windowElement.offsetWidth;
            const maxTop = shellRect.height - taskbarHeight - windowElement.offsetHeight;

            const nextLeft = Math.min(Math.max(0, event.clientX - shellRect.left - dragOffsetX), Math.max(0, maxLeft));
            const nextTop = Math.min(Math.max(0, event.clientY - shellRect.top - dragOffsetY), Math.max(0, maxTop));

            windowElement.style.left = `${nextLeft}px`;
            windowElement.style.top = `${nextTop}px`;
        });

        document.addEventListener('mouseup', () => {
            dragging = false;
        });
    };

    const makeResizable = (windowElement, resizeHandle, windowId) => {
        let resizing = false;
        let startX = 0;
        let startY = 0;
        let startWidth = 0;
        let startHeight = 0;

        resizeHandle.addEventListener('mousedown', (event) => {
            if (event.button !== 0) {
                return;
            }

            resizing = true;
            focusWindow(windowId);
            startX = event.clientX;
            startY = event.clientY;
            startWidth = windowElement.offsetWidth;
            startHeight = windowElement.offsetHeight;
            event.preventDefault();
        });

        document.addEventListener('mousemove', (event) => {
            if (!resizing) {
                return;
            }

            const nextWidth = Math.max(320, startWidth + (event.clientX - startX));
            const nextHeight = Math.max(220, startHeight + (event.clientY - startY));
            windowElement.style.width = `${nextWidth}px`;
            windowElement.style.height = `${nextHeight}px`;
        });

        document.addEventListener('mouseup', () => {
            resizing = false;
        });
    };

    const createWindow = ({ windowId, title, url, width, height }) => {
        const existing = windows.get(windowId);
        if (existing) {
            existing.element.classList.remove('hidden');
            focusWindow(windowId);
            return;
        }

        const windowElement = document.createElement('section');
        windowElement.className = 'desktop-window';
        windowElement.dataset.windowId = windowId;
        windowElement.style.width = `${width}px`;
        windowElement.style.height = `${height}px`;

        const currentCount = windows.size;
        windowElement.style.left = `${60 + (currentCount * 28)}px`;
        windowElement.style.top = `${52 + (currentCount * 26)}px`;

        windowElement.innerHTML = `
            <header class="desktop-window-header">
                <div class="desktop-window-title"></div>
                <div class="desktop-window-controls">
                    <button type="button" data-action="minimize" aria-label="Minimize">_</button>
                    <button type="button" data-action="close" aria-label="Close">X</button>
                </div>
            </header>
            <div class="desktop-window-content"></div>
            <div class="desktop-window-resize" aria-hidden="true"></div>
        `;

        const titleElement = windowElement.querySelector('.desktop-window-title');
        const contentElement = windowElement.querySelector('.desktop-window-content');
        const resizeHandle = windowElement.querySelector('.desktop-window-resize');
        const headerElement = windowElement.querySelector('.desktop-window-header');

        if (!titleElement || !contentElement || !resizeHandle || !headerElement) {
            return;
        }

        titleElement.textContent = title;

        const taskButton = document.createElement('button');
        taskButton.type = 'button';
        taskButton.className = 'desktop-task-button';
        taskButton.textContent = title;

        taskButton.addEventListener('click', () => {
            windowElement.classList.toggle('hidden');
            if (!windowElement.classList.contains('hidden')) {
                focusWindow(windowId);
            } else {
                taskButton.classList.remove('active');
            }
        });

        windowElement.addEventListener('mousedown', () => {
            focusWindow(windowId);
        });

        windowElement.querySelector('[data-action="close"]')?.addEventListener('click', () => {
            removeWindow(windowId);
        });

        windowElement.querySelector('[data-action="minimize"]')?.addEventListener('click', () => {
            windowElement.classList.add('hidden');
            taskButton.classList.remove('active');
        });

        makeDraggable(windowElement, headerElement, windowId);
        makeResizable(windowElement, resizeHandle, windowId);

        windowLayer.appendChild(windowElement);
        taskButtons.appendChild(taskButton);

        windows.set(windowId, {
            element: windowElement,
            content: contentElement,
            taskButton,
            currentUrl: url,
        });

        contentElement.addEventListener('click', (event) => {
            handleWindowContentClick(windowId, event);
        });

        contentElement.addEventListener('submit', (event) => {
            handleWindowFormSubmit(windowId, event);
        });

        focusWindow(windowId);
        setWindowContent(windowId, url);
    };

    const openFromElement = (trigger) => {
        const windowId = trigger.dataset.windowId;
        const title = trigger.dataset.windowTitle || 'Window';
        const url = trigger.dataset.windowUrl;
        const width = Number.parseInt(trigger.dataset.windowWidth || '640', 10);
        const height = Number.parseInt(trigger.dataset.windowHeight || '440', 10);

        if (!windowId || !url) {
            return;
        }

        createWindow({
            windowId,
            title,
            url,
            width,
            height,
        });
    };

    shell.addEventListener('click', (event) => {
        const trigger = event.target.closest('.js-desktop-launch');
        if (!(trigger instanceof HTMLElement)) {
            return;
        }

        selectIcon(trigger);
    });

    shell.addEventListener('dblclick', (event) => {
        const trigger = event.target.closest('.js-desktop-launch');
        if (!(trigger instanceof HTMLElement)) {
            return;
        }

        event.preventDefault();
        startMenu.hidden = true;
        selectIcon(trigger);
        openFromElement(trigger);
    });

    shell.addEventListener('keydown', (event) => {
        const getCurrentIcon = () => {
            const activeElement = document.activeElement;

            return activeElement instanceof HTMLElement && activeElement.classList.contains('js-desktop-launch')
                ? activeElement
                : selectedIcon;
        };

        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'ArrowUp' || event.key === 'ArrowDown') {
            const currentIcon = getCurrentIcon();

            if (!(currentIcon instanceof HTMLElement)) {
                const icons = getDesktopIcons();
                if (icons.length > 0) {
                    event.preventDefault();
                    selectAndFocusIcon(icons[0]);
                }

                return;
            }

            const directionMap = {
                ArrowLeft: 'left',
                ArrowRight: 'right',
                ArrowUp: 'up',
                ArrowDown: 'down',
            };

            const direction = directionMap[event.key];
            const targetIcon = findIconByDirection(currentIcon, direction);
            if (targetIcon instanceof HTMLElement) {
                event.preventDefault();
                selectAndFocusIcon(targetIcon);
            }

            return;
        }

        if (event.key === 'Home' || event.key === 'End') {
            const icons = getDesktopIcons();
            if (icons.length === 0) {
                return;
            }

            event.preventDefault();
            const targetIcon = event.key === 'Home' ? icons[0] : icons[icons.length - 1];
            selectAndFocusIcon(targetIcon);

            return;
        }

        if (!event.ctrlKey && !event.metaKey && !event.altKey && event.key.length === 1 && /[a-z0-9]/i.test(event.key)) {
            const targetIcon = findIconByInitial(event.key, getCurrentIcon());
            if (targetIcon instanceof HTMLElement) {
                event.preventDefault();
                selectAndFocusIcon(targetIcon);
            }

            return;
        }

        if (event.key === 'Escape') {
            startMenu.hidden = true;
            clearIconSelection();
            return;
        }

        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        const trigger = event.target.closest('.js-desktop-launch');
        if (!(trigger instanceof HTMLElement)) {
            return;
        }

        event.preventDefault();
        startMenu.hidden = true;
        selectIcon(trigger);
        openFromElement(trigger);
    });

    restoreFromHash();
})();
