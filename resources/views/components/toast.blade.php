<div x-data="{
        show: false,
        message: '',
        type: 'success',
        init() {
            @if (session()->has('success'))
                this.showNotification('{{ session('success') }}', 'success');
            @elseif (session()->has('error'))
                this.showNotification('{{ session('error') }}', 'error');
            @elseif (session()->has('warning'))
                this.showNotification('{{ session('warning') }}', 'warning');
            @elseif (session()->has('info'))
                this.showNotification('{{ session('info') }}', 'info');
            @endif

            window.addEventListener('notify', (event) => {
                let detail = event.detail;
                if (Array.isArray(detail) && detail.length > 0) {
                    detail = detail[0];
                }
                this.showNotification(detail.message || detail, detail.type || 'success');
            });
        },
        showNotification(message, type) {
            this.message = message;
            this.type = type;
            this.show = true;
            setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-4 sm:-translate-y-4 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 -translate-y-4 sm:-translate-y-4 sm:scale-95"
    class="fixed top-4 right-4 z-50 flex items-center p-4 mb-4 text-slate-300 bg-[#1e293b] rounded-lg shadow-xl border border-slate-700/50"
    :class="{
        'border-l-4 border-l-emerald-500': type === 'success',
        'border-l-4 border-l-rose-500': type === 'error',
        'border-l-4 border-l-amber-500': type === 'warning',
        'border-l-4 border-l-blue-500': type === 'info'
    }" role="alert" style="display: none;">
    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg" :class="{
             'text-emerald-400 bg-emerald-400/10': type === 'success',
             'text-rose-400 bg-rose-400/10': type === 'error',
             'text-amber-400 bg-amber-400/10': type === 'warning',
             'text-blue-400 bg-blue-400/10': type === 'info'
         }">
        <template x-if="type === 'success'">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
        </template>
        <template x-if="type === 'error'">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
            </svg>
        </template>
        <template x-if="type === 'warning'">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z" />
            </svg>
        </template>
        <template x-if="type === 'info'">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
        </template>
    </div>
    <div class="ms-3 text-sm font-normal font-sans" x-text="message"></div>
    <button type="button" @click="show = false"
        class="ms-auto -mx-1.5 -my-1.5 bg-transparent text-slate-400 hover:text-white rounded-lg p-1.5 hover:bg-slate-700 inline-flex items-center justify-center h-8 w-8 ml-4 transition-colors"
        aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
    </button>
</div>