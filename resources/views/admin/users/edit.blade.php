<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @foreach($breadcrumb as $key => $item)
                    @if($loop->last)
                        <span class="text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                    @else
                        @if($item['url'])
                            <a href="{{ $item['url'] }}" class="hover:text-slate-700 dark:hover:text-slate-300">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                        <span class="mx-2">/</span>
                    @endif
                @endforeach
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $pageTitle }}</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Informasi Dasar</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password <span class="text-slate-400">(kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" name="password" id="password"
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="user_group_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">User Group</label>
                        <select name="user_group_id" id="user_group_id" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">Pilih User Group</option>
                            @foreach($userGroups as $group)
                                <option value="{{ $group->id }}" {{ old('user_group_id', $user->user_group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_group_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Permissions</h3>

                    <!-- Global Check All -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="check-all-permissions"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                        <label for="check-all-permissions" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-check-double text-xs text-indigo-600"></i> Check All Permissions
                        </label>
                    </div>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Atur permission untuk user group ini. Permission ini akan berlaku untuk semua user dalam group yang sama.</p>

                <div class="space-y-6">
                    @foreach($modules as $module)
                        @php
                            $accesses = $moduleAccesses->get($module->id, collect());
                            $hasAnyPermission = $accesses->some(fn($access) => isset($currentPermissions[$access->id]) && $currentPermissions[$access->id]);
                        @endphp

                        @if($accesses->count() > 0)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                                            <i class="{{ $module->icon ?? 'fa-cube' }} text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-slate-900 dark:text-white">{{ $module->name }}</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $module->menu?->name }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <!-- Check All for this module -->
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" id="check-module-{{ $module->id }}"
                                                   class="module-check-all h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                                                   data-module="{{ $module->id }}">
                                            <label for="check-module-{{ $module->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                <i class="fa-solid fa-check text-xs text-green-600"></i> Check All
                                            </label>
                                        </div>

                                        <!-- Access Toggle -->
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" id="access-{{ $module->id }}"
                                                   class="module-access-toggle h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                                                   data-module="{{ $module->id }}" {{ $hasAnyPermission ? 'checked' : '' }}>
                                            <label for="access-{{ $module->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                <i class="fa-solid fa-eye text-xs text-blue-600"></i> Access
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="permissions-grid-{{ $module->id }} ml-13 {{ $hasAnyPermission ? '' : 'hidden' }}">
                                    <div class="grid grid-cols-5 gap-3">
                                    @foreach($accesses as $access)
                                        <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:hover:bg-slate-600 cursor-pointer">
                                            <input type="checkbox" name="permissions[{{ $access->id }}]" value="1"
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded permission-checkbox"
                                                   data-module="{{ $module->id }}"
                                                   {{ (isset($currentPermissions[$access->id]) && $currentPermissions[$access->id]) ? 'checked' : '' }}>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                {{ ucfirst(str_replace($module->identifiers . '-', '', $access->identifiers)) }}
                                            </span>
                                        </label>
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Kembali
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-save text-xs"></i>
                    Update Pengguna
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global check all permissions
            const globalCheckAll = document.getElementById('check-all-permissions');
            const allPermissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const allAccessToggles = document.querySelectorAll('.module-access-toggle');
            const allModuleCheckAlls = document.querySelectorAll('.module-check-all');

            // Handle global check all
            globalCheckAll.addEventListener('change', function() {
                const isChecked = this.checked;

                // Toggle all access toggles
                allAccessToggles.forEach(toggle => {
                    toggle.checked = isChecked;
                    const moduleId = toggle.dataset.module;
                    const permissionsGrid = document.querySelector(`.permissions-grid-${moduleId}`);

                    if (isChecked) {
                        permissionsGrid.classList.remove('hidden');
                    } else {
                        permissionsGrid.classList.add('hidden');
                    }
                });

                // Toggle all permission checkboxes
                allPermissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });

                // Toggle all module check-alls
                allModuleCheckAlls.forEach(moduleCheckAll => {
                    moduleCheckAll.checked = isChecked;
                });
            });

            // Handle module access toggle
            allAccessToggles.forEach(toggle => {
                const moduleId = toggle.dataset.module;
                const permissionsGrid = document.querySelector(`.permissions-grid-${moduleId}`);
                const moduleCheckboxes = permissionsGrid.querySelectorAll('.permission-checkbox');
                const moduleCheckAll = document.querySelector(`#check-module-${moduleId}`);

                toggle.addEventListener('change', function() {
                    if (this.checked) {
                        permissionsGrid.classList.remove('hidden');
                    } else {
                        permissionsGrid.classList.add('hidden');
                        moduleCheckboxes.forEach(checkbox => checkbox.checked = false);
                        moduleCheckAll.checked = false;
                        updateGlobalCheckAllState();
                    }
                });
            });

            // Handle module check all
            allModuleCheckAlls.forEach(moduleCheckAll => {
                const moduleId = moduleCheckAll.dataset.module;
                const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`);
                const accessToggle = document.querySelector(`#access-${moduleId}`);

                moduleCheckAll.addEventListener('change', function() {
                    const isChecked = this.checked;

                    // Check access toggle if checking all
                    if (isChecked && !accessToggle.checked) {
                        accessToggle.checked = true;
                        accessToggle.dispatchEvent(new Event('change'));
                    }

                    // Toggle all checkboxes for this module
                    moduleCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });

                    updateGlobalCheckAllState();
                });
            });

            // Handle individual permission checkboxes
            allPermissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const moduleId = this.dataset.module;
                    const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`);
                    const moduleCheckAll = document.querySelector(`#check-module-${moduleId}`);

                    // Check if all checkboxes in this module are checked
                    const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                    const noneChecked = Array.from(moduleCheckboxes).every(cb => !cb.checked);

                    // Update module check all state
                    moduleCheckAll.checked = allChecked;
                    moduleCheckAll.indeterminate = !allChecked && !noneChecked;

                    updateGlobalCheckAllState();
                });
            });

            function updateGlobalCheckAllState() {
                const allChecked = Array.from(allPermissionCheckboxes).every(cb => cb.checked);
                const noneChecked = Array.from(allPermissionCheckboxes).every(cb => !cb.checked);

                globalCheckAll.checked = allChecked;
                globalCheckAll.indeterminate = !allChecked && !noneChecked;
            }

            // Initialize states based on current permissions
            allModuleCheckAlls.forEach(moduleCheckAll => {
                const moduleId = moduleCheckAll.dataset.module;
                const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`);
                const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                const noneChecked = Array.from(moduleCheckboxes).every(cb => !cb.checked);

                moduleCheckAll.checked = allChecked;
                moduleCheckAll.indeterminate = !allChecked && !noneChecked;
            });

            updateGlobalCheckAllState();
        });
    </script>
    @endpush
</x-app-layout>
