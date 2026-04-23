{{-- Global toast container — fixed bottom-right, Sonner/shadcn style --}}
<div x-data="{
        toasts: [],
        httpStatus: {
            200: 'OK',
            201: 'Created',
            400: 'Bad Request',
            401: 'Unauthorized',
            403: 'Forbidden',
            404: 'Not Found',
            409: 'Conflict',
            422: 'Unprocessable Entity',
            429: 'Too Many Requests',
            500: 'Internal Server Error'
        },
        typeLabels: {
            error: 'Error',
            success: 'Success',
            warning: 'Warning',
            info: 'Info'
        },
        add(toast) {
            const id = Date.now();
            const code = toast.code;
            const statusText = this.httpStatus[code] || '';
            const type = toast.type || 'error';
            const label = this.typeLabels[type] || 'Error';
            this.toasts.push({
                id,
                code,
                status: statusText,
                label,
                message: toast.message,
                type,
                remaining: 6000,
                total: 6000,
                paused: false,
                show: false,
                lastTick: Date.now()
            });

            $nextTick(() => {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.show = true;
            });

            this.tick(id);
        },
        tick(id) {
            const frame = () => {
                const t = this.toasts.find(t => t.id === id);
                if (!t) return;
                if (!t.paused) {
                    const now = Date.now();
                    t.remaining -= (now - t.lastTick);
                    t.lastTick = now;
                    if (t.remaining <= 0) {
                        t.remaining = 0;
                        t.show = false;
                        setTimeout(() => this.toasts = this.toasts.filter(x => x.id !== id), 300);
                        return;
                    }
                } else {
                    t.lastTick = Date.now();
                }
                requestAnimationFrame(frame);
            };
            requestAnimationFrame(frame);
        },
        getSeconds(t) {
            return Math.ceil(t.remaining / 1000);
        },
        getProgress(t) {
            return (t.remaining / t.total) * 100;
        },
        dotColor(type) {
            return { error: 'bg-red', success: 'bg-green', warning: 'bg-yellow', info: 'bg-blue' }[type] || 'bg-gray';
        }
     }"
     @toast.window="add($event.detail)"
     class="pointer-events-none fixed inset-0 z-[99] flex items-end justify-end p-4 sm:p-6"
     aria-live="assertive">

    <div class="flex w-full flex-col items-end gap-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 @mouseenter="toast.paused = true"
                 @mouseleave="toast.paused = false; toast.lastTick = Date.now()"
                 class="pointer-events-auto w-full max-w-sm cursor-default overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
                <div class="p-3">
                    {{-- Header --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-x-2">
                            <span class="relative flex size-2 shrink-0">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"
                                      :class="toast.type === 'error' ? 'bg-red-400' : toast.type === 'success' ? 'bg-green-400' : 'bg-yellow-400'"></span>
                                <span class="relative inline-flex size-2 rounded-full"
                                      :class="toast.type === 'error' ? 'bg-red-500' : toast.type === 'success' ? 'bg-green-500' : 'bg-yellow-500'"></span>
                            </span>
                            <span class="text-xs font-semibold text-gray-900 dark:text-gray-100"
                                  x-text="toast.label + ' ' + toast.code + ': ' + toast.status"></span>
                        </div>
                        <span class="text-[10px] tabular-nums text-gray-400 dark:text-gray-500" x-text="getSeconds(toast) + 's'"></span>
                    </div>
                    {{-- Message --}}
                    <p class="mt-1 pl-4 text-xs text-gray-500 dark:text-gray-400" x-text="toast.message"></p>
                    {{-- Progress bar --}}
                    <div class="mt-2 h-0.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="h-full rounded-full"
                             :class="toast.type === 'error' ? 'bg-red-400/60' : toast.type === 'success' ? 'bg-green-400/60' : 'bg-yellow-400/60'"
                             :style="'width: ' + getProgress(toast) + '%; transition: none'"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
