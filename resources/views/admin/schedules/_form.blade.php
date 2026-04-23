<x-form-card :action="$action" :method="$method" :button-label="$buttonLabel" :cancel-route="route('admin.schedules.index')">
    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="classroom_id">Kelas <span class="text-red-500">*</span></label>
            <x-searchable-select
                name="classroom_id"
                :options="$classrooms"
                :selected="old('classroom_id', $schedule->classroom_id ?? '')"
                placeholder="Pilih Kelas"
                :required="true"
            />
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="teacher_id">Guru <span class="text-red-500">*</span></label>
            <x-searchable-select
                name="teacher_id"
                :options="$teachers"
                :selected="old('teacher_id', $schedule->teacher_id ?? '')"
                placeholder="Pilih Guru"
                :required="true"
            />
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="subject_id">Mata Pelajaran <span class="text-red-500">*</span></label>
            <x-searchable-select
                name="subject_id"
                :options="$subjects"
                :selected="old('subject_id', $schedule->subject_id ?? '')"
                placeholder="Pilih Mata Pelajaran"
                :required="true"
            />
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="room_id">Ruangan</label>
            <x-searchable-select
                name="room_id"
                :options="$rooms"
                :selected="old('room_id', $schedule->room_id ?? '')"
                placeholder="Pilih Ruangan"
                empty-label="-- Tanpa Ruangan --"
            />
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="day">Hari <span class="text-red-500">*</span></label>
            <select id="day" name="day" required
                    class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                <option value="">Pilih Hari</option>
                @foreach($days as $day)
                    <option value="{{ $day }}" @selected(old('day', $schedule->day ?? '') === $day)>{{ $day }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="start_time">Jam Mulai <span class="text-red-500">*</span></label>
                <input id="start_time" name="start_time" type="time" required
                       value="{{ old('start_time', $schedule->start_time ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="end_time">Jam Selesai <span class="text-red-500">*</span></label>
                <input id="end_time" name="end_time" type="time" required
                       value="{{ old('end_time', $schedule->end_time ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
            </div>
        </div>
    </div>

    {{-- Conflict Warning --}}
    <div id="conflictWarning" class="hidden rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 dark:border-yellow-500/30 dark:bg-yellow-500/10">
        <div class="flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Konflik Jadwal Terdeteksi</p>
                <ul id="conflictList" class="mt-1 text-xs text-yellow-700 dark:text-yellow-300 list-disc list-inside space-y-1"></ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let conflictCheckTimeout = null;
        function checkConflict() {
            const form = document.querySelector('form');
            const teacherId = form.querySelector('[name="teacher_id"]').value;
            const day = form.querySelector('[name="day"]').value;
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const roomId = form.querySelector('[name="room_id"]').value;
            const scheduleId = '{{ $schedule->id ?? '' }}';

            if (!teacherId || !day || !startTime || !endTime) return;

            clearTimeout(conflictCheckTimeout);
            conflictCheckTimeout = setTimeout(async () => {
                try {
                    const response = await fetch('{{ route("admin.schedules.check-conflict") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ teacher_id: teacherId, day, start_time: startTime, end_time: endTime, room_id: roomId || null, exclude_id: scheduleId || null }),
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const warning = document.getElementById('conflictWarning');
                        const list = document.getElementById('conflictList');

                        if (data.hasConflict) {
                            list.innerHTML = data.conflicts.map(c => `<li>${c.message}</li>`).join('');
                            warning.classList.remove('hidden');
                        } else {
                            warning.classList.add('hidden');
                        }
                    }
                } catch (e) {
                    console.error('Conflict check error:', e);
                }
            }, 500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['day'].forEach(name => {
                const el = document.querySelector(`[name="${name}"]`);
                if (el) el.addEventListener('change', checkConflict);
            });
            document.getElementById('start_time')?.addEventListener('change', checkConflict);
            document.getElementById('end_time')?.addEventListener('change', checkConflict);

            // Listen for hidden input changes from searchable-select
            const observer = new MutationObserver(checkConflict);
            ['teacher_id', 'room_id'].forEach(name => {
                const el = document.querySelector(`input[name="${name}"][type="hidden"]`);
                if (el) observer.observe(el, { attributes: true, attributeFilter: ['value'] });
            });

            // Also listen via Alpine
            ['teacher_id', 'room_id'].forEach(name => {
                const el = document.querySelector(`input[name="${name}"][type="hidden"]`);
                if (el) {
                    let lastVal = el.value;
                    setInterval(() => {
                        if (el.value !== lastVal) {
                            lastVal = el.value;
                            checkConflict();
                        }
                    }, 300);
                }
            });
        });
    </script>
    @endpush
</x-form-card>
