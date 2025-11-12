@php
    // Comprehensive FontAwesome free icon list categorized by type
    $icons = [
        // Navigation & UI
        'fa-home', 'fa-house', 'fa-dashboard', 'fa-bars', 'fa-menu', 'fa-list', 'fa-grip', 'fa-grip-horizontal', 'fa-grip-vertical',

        // Users & People
        'fa-user', 'fa-users', 'fa-user-group', 'fa-user-circle', 'fa-user-cog', 'fa-user-gear', 'fa-user-plus', 'fa-user-minus',
        'fa-user-check', 'fa-user-times', 'fa-user-lock', 'fa-user-shield', 'fa-address-card', 'fa-id-card',

        // Content & Media
        'fa-newspaper', 'fa-file', 'fa-file-text', 'fa-file-pdf', 'fa-file-word', 'fa-file-excel', 'fa-file-image',
        'fa-folder', 'fa-folder-open', 'fa-image', 'fa-images', 'fa-photo', 'fa-video', 'fa-film', 'fa-music',

        // Business & Commerce
        'fa-briefcase', 'fa-building', 'fa-store', 'fa-shopping-cart', 'fa-cart-shopping', 'fa-credit-card',
        'fa-money-bill', 'fa-coins', 'fa-chart-line', 'fa-chart-pie', 'fa-chart-bar', 'fa-analytics',

        // Communication
        'fa-envelope', 'fa-mail', 'fa-phone', 'fa-mobile', 'fa-comment', 'fa-comments', 'fa-message',
        'fa-bell', 'fa-notification', 'fa-megaphone', 'fa-bullhorn',

        // Technology
        'fa-computer', 'fa-laptop', 'fa-mobile-phone', 'fa-tablet', 'fa-wifi', 'fa-signal', 'fa-bluetooth',
        'fa-usb', 'fa-hard-drive', 'fa-database', 'fa-server', 'fa-cloud', 'fa-download', 'fa-upload',

        // Actions & Controls
        'fa-plus', 'fa-minus', 'fa-times', 'fa-xmark', 'fa-check', 'fa-checkmark', 'fa-edit', 'fa-pen',
        'fa-trash', 'fa-delete', 'fa-save', 'fa-copy', 'fa-clone', 'fa-cut', 'fa-paste', 'fa-search',
        'fa-filter', 'fa-sort', 'fa-refresh', 'fa-rotate', 'fa-sync', 'fa-undo', 'fa-redo',

        // System & Settings
        'fa-cog', 'fa-gear', 'fa-settings', 'fa-tools', 'fa-wrench', 'fa-screwdriver', 'fa-hammer',
        'fa-key', 'fa-lock', 'fa-unlock', 'fa-shield', 'fa-security', 'fa-eye', 'fa-eye-slash',

        // Navigation Arrows
        'fa-arrow-up', 'fa-arrow-down', 'fa-arrow-left', 'fa-arrow-right', 'fa-chevron-up', 'fa-chevron-down',
        'fa-chevron-left', 'fa-chevron-right', 'fa-angle-up', 'fa-angle-down', 'fa-angle-left', 'fa-angle-right',

        // Status & Indicators
        'fa-circle', 'fa-circle-check', 'fa-circle-xmark', 'fa-circle-info', 'fa-circle-exclamation',
        'fa-triangle-exclamation', 'fa-warning', 'fa-star', 'fa-heart', 'fa-bookmark', 'fa-flag',

        // Time & Calendar
        'fa-calendar', 'fa-calendar-days', 'fa-clock', 'fa-stopwatch', 'fa-timer', 'fa-hourglass',

        // Transportation
        'fa-car', 'fa-truck', 'fa-plane', 'fa-train', 'fa-ship', 'fa-bicycle', 'fa-motorcycle',

        // Social & Sharing
        'fa-share', 'fa-share-alt', 'fa-link', 'fa-thumbs-up', 'fa-thumbs-down', 'fa-like', 'fa-dislike',

        // Education & Learning
        'fa-book', 'fa-graduation-cap', 'fa-school', 'fa-chalkboard', 'fa-pencil', 'fa-pen-to-square',

        // Healthcare
        'fa-heart-pulse', 'fa-stethoscope', 'fa-pills', 'fa-syringe', 'fa-first-aid',

        // Weather & Nature
        'fa-sun', 'fa-moon', 'fa-cloud', 'fa-rain', 'fa-snow', 'fa-bolt', 'fa-tree', 'fa-leaf',

        // Entertainment
        'fa-gamepad', 'fa-puzzle-piece', 'fa-dice', 'fa-chess', 'fa-trophy', 'fa-medal',

        // Location & Maps
        'fa-map', 'fa-location-dot', 'fa-map-marker', 'fa-compass', 'fa-globe', 'fa-route',

        // Food & Dining
        'fa-utensils', 'fa-coffee', 'fa-wine-glass', 'fa-beer', 'fa-pizza-slice', 'fa-hamburger',

        // Printing & Output
        'fa-print', 'fa-fax', 'fa-scanner'
    ];
@endphp

<!-- Icon Picker Modal -->
<div id="icon-picker-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-[900px] max-w-full rounded-xl bg-white p-6 shadow-lg dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Choose an Icon</h3>
                <input id="icon-picker-search" type="search" placeholder="Search icons..." class="w-80 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400" />
            </div>
            <button id="icon-picker-close" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <div class="mt-4">
            <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">
                Click on an icon to select it. All icons are from FontAwesome free collection.
            </p>

            <div class="grid max-h-[500px] grid-cols-8 gap-3 overflow-auto rounded-lg border border-slate-200 p-4 dark:border-slate-700">
                @foreach($icons as $icon)
                    <button type="button" tabindex="0"
                            class="icon-item group flex h-16 w-16 flex-col items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-slate-600 transition-all hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-400 dark:hover:bg-slate-700 dark:hover:text-indigo-300"
                            data-icon="{{ $icon }}"
                            title="{{ $icon }}">
                        <i class="fa-solid {{ $icon }} text-lg group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-medium opacity-75 group-hover:opacity-100">{{ str_replace('fa-', '', $icon) }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">
                <span id="icon-count">{{ count($icons) }}</span> icons available
            </div>
            <div class="flex gap-3">
                <button id="icon-picker-clear" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-400 dark:hover:bg-slate-800">
                    Clear Selection
                </button>
                <button id="icon-picker-close-alt" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Close
                </button>
            </div>
        </div>

        @push('scripts')
        <script>
            (function(){
                // Helper: update preview element for a given input and icon class
                function updatePreviewForInput(inputEl, icon){
                    if(!inputEl) return;
                    const previewSelector = inputEl.getAttribute('data-preview');
                    if(previewSelector){
                        const preview = document.querySelector(previewSelector);
                        if(preview) {
                            preview.innerHTML = '<i class="fa-solid ' + icon + ' text-xl"></i>';
                        }
                    }
                }

                // Enhanced icon picker initialization for FontAwesome IconPicker plugin
                function initializeIconPicker() {
                    // If jQuery + plugin available, initialize with better options
                    if(window.jQuery && typeof window.jQuery.fn !== 'undefined' && typeof window.jQuery.fn.iconpicker !== 'undefined'){
                        window.jQuery(function($){
                            $('.fa-icon-input, [data-is-iconpicker="true"]').each(function() {
                                const $input = $(this);

                                // Enhanced iconpicker options
                                $input.iconpicker({
                                    placement: 'bottom',
                                    hideOnSelect: true,
                                    animation: true,
                                    searchText: 'Search icons...',
                                    selectedClass: 'btn-primary',
                                    unselectedClass: 'btn-light',
                                    cols: 8,
                                    rows: 6,
                                    iconset: 'fontawesome6',
                                    labelHeader: '{0} of {1} pages',
                                    labelFooter: '{0} - {1} of {2} icons',
                                    templates: {
                                        popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' +
                                                '<div class="popover-title"></div><div class="popover-content"></div></div>',
                                        iconpickerItem: '<a role="button" href="#" class="iconpicker-item"><i></i></a>'
                                    }
                                })
                                .on('change', function(e){
                                    const $el = $(this);
                                    const val = e.icon ? e.icon.split(' ').pop() : $el.val();
                                    $el.val(val).trigger('input');
                                    updatePreviewForInput(this, val);
                                });
                            });
                        });
                        return;
                    }
                }

                // Fallback: Custom modal implementation
                function setupCustomIconPicker() {
                    // Custom modal handlers when plugin/jQuery is unavailable
                    function openCustomModalForInput(targetSelector){
                        const modal = document.getElementById('icon-picker-modal');
                        if(!modal) return;
                        modal.dataset.targetInput = targetSelector;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        const search = modal.querySelector('#icon-picker-search');
                        if(search) {
                            search.focus();
                            search.value = '';
                            filterIcons(''); // Reset filter
                        }
                    }

                    function closeCustomModal() {
                        const modal = document.getElementById('icon-picker-modal');
                        if(modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    }

                    function filterIcons(query) {
                        const iconItems = document.querySelectorAll('#icon-picker-modal .icon-item');
                        let visibleCount = 0;

                        iconItems.forEach(function(btn){
                            const iconName = btn.dataset.icon.toLowerCase();
                            const isVisible = !query || iconName.includes(query);
                            btn.style.display = isVisible ? '' : 'none';
                            if(isVisible) visibleCount++;
                        });

                        // Update count display
                        const countEl = document.getElementById('icon-count');
                        if(countEl) {
                            countEl.textContent = query ? visibleCount : {{ count($icons) }};
                        }
                    }

                    // Attach click handler to inputs with class fa-icon-input or data-is-iconpicker
                    document.addEventListener('click', function(e){
                        const input = e.target.closest('.fa-icon-input, [data-is-iconpicker="true"]');
                        if(input){
                            e.preventDefault();
                            // prefer id selector, fall back to name
                            const id = input.id ? ('#' + input.id) : (input.getAttribute('name') ? '[name="' + input.getAttribute('name') + '"]' : null);
                            if(id) openCustomModalForInput(id);
                            return;
                        }

                        // Icon selection in custom modal
                        const iconBtn = e.target.closest('.icon-item');
                        const modal = document.getElementById('icon-picker-modal');
                        if(iconBtn && modal && !modal.classList.contains('hidden')){
                            e.preventDefault();
                            const icon = iconBtn.dataset.icon;
                            const targetSelector = modal.dataset.targetInput;
                            const target = targetSelector ? document.querySelector(targetSelector) : null;
                            if(target){
                                target.value = icon;
                                target.dispatchEvent(new Event('input', { bubbles: true }));
                                updatePreviewForInput(target, icon);
                            }
                            closeCustomModal();
                            return;
                        }
                    });

                    // Close button handlers
                    document.getElementById('icon-picker-close')?.addEventListener('click', closeCustomModal);
                    document.getElementById('icon-picker-close-alt')?.addEventListener('click', closeCustomModal);

                    // Clear selection handler
                    document.getElementById('icon-picker-clear')?.addEventListener('click', function(){
                        const modal = document.getElementById('icon-picker-modal');
                        if(!modal || modal.classList.contains('hidden')) return;

                        const targetSelector = modal.dataset.targetInput;
                        const target = targetSelector ? document.querySelector(targetSelector) : null;
                        if(target){
                            target.value = '';
                            target.dispatchEvent(new Event('input', { bubbles: true }));
                            updatePreviewForInput(target, '');
                        }
                    });

                    // Search filter for custom modal
                    const searchEl = document.getElementById('icon-picker-search');
                    if(searchEl){
                        searchEl.addEventListener('input', function(){
                            const q = this.value.trim().toLowerCase();
                            filterIcons(q);
                        });
                    }

                    // Keyboard navigation: Enter on focused icon selects it; Escape closes modal
                    document.addEventListener('keydown', function(ev){
                        const modal = document.getElementById('icon-picker-modal');
                        if(!modal || modal.classList.contains('hidden')) return;

                        if(ev.key === 'Escape'){
                            closeCustomModal();
                            return;
                        }

                        const active = document.activeElement;
                        if(ev.key === 'Enter' && active && active.classList && active.classList.contains('icon-item')){
                            ev.preventDefault();
                            const icon = active.dataset.icon;
                            const targetSelector = modal.dataset.targetInput;
                            const target = targetSelector ? document.querySelector(targetSelector) : null;
                            if(target){
                                target.value = icon;
                                target.dispatchEvent(new Event('input', { bubbles: true }));
                                updatePreviewForInput(target, icon);
                            }
                            closeCustomModal();
                        }
                    });
                }

                // Initialize on DOM ready
                document.addEventListener('DOMContentLoaded', function() {
                    // Try plugin first, fallback to custom implementation
                    setTimeout(function() {
                        initializeIconPicker();
                        setupCustomIconPicker();
                    }, 100);
                });

            })();
        </script>
        @endpush

    </div>
</div>
