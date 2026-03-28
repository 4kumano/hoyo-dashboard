<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\HoyolabService;

new #[Layout('layouts.app')] #[Title('Login - Hoyo Dashboard')] class extends Component {
    public $cookieVal = '';
    public $userInfo = [];
    public $errorMessage = '';
    public $isLogin = false;

    public function updatedCookieVal($value)
    {
        $this->cookieVal = preg_replace('/\s+/', '', $value);
    }

    public function saveCookie(HoyolabService $hoyolabService)
    {
        // Format cookie menjadi 1 baris dengan menghapus spasi dan newline
        $this->cookieVal = preg_replace('/\s+/', '', $this->cookieVal);

        if (trim($this->cookieVal) === '') {
            $this->errorMessage = 'Cookie tidak boleh kosong.';
            return;
        }

        $this->errorMessage = '';

        // Validate cookie
        $response = $hoyolabService->loginWithCookie($this->cookieVal);

        if (isset($response['message']) && $response['message'] === 'OK') {
            // Extract and map the requested account fields
            $accounts = [];
            if (isset($response['data']['list']) && is_array($response['data']['list'])) {
                foreach ($response['data']['list'] as $acc) {
                    $accounts[] = [
                        'game_biz' => $acc['game_biz'] ?? null,
                        'region' => $acc['region'] ?? null,
                        'game_uid' => $acc['game_uid'] ?? null,
                        'nickname' => $acc['nickname'] ?? null,
                        'level' => $acc['level'] ?? null,
                        'region_name' => $acc['region_name'] ?? null,
                    ];
                }
            }

            $this->isLogin = true;

            session(['isLogin' => $this->isLogin]);
            session(['hoyolab_accounts' => $accounts]);

            $hoyolabService = app(\App\Services\HoyolabService::class);
            // Ambil info detail menggunakan cookie string langsung
            $this->userInfo = $hoyolabService->getUserFullInfo($this->cookieVal);

            // Simpan cookie ke dalam Session PHP backend agar HoyolabService dkk bisa ikut mengaksesnya via session('hoyolab_cookie')
            session(['hoyolab_cookie' => $this->cookieVal]);
            session(['hoyolab_user_info' => $this->userInfo]);

            // Dispatch browser event to save the cookie and user info to localStorage
            $this->dispatch('cookie-validated', [
                'cookie' => $this->cookieVal,
                'userInfo' => $this->userInfo,
                'isLogin' => $this->isLogin,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Cookie berhasil di validasi.',
            ]);
        } else {
            $this->errorMessage = $response['message'] ?? 'Cookie tidak valid atau sesi telah kedaluwarsa.';
            $this->dispatch('cookie-invalid');
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cookie tidak valid atau sesi telah kedaluwarsa.',
            ]);
        }
    }

    // Method baru untuk auto-login saat komponen diload pertama kali
    public function autoLogin($savedCookie)
    {
        $this->cookieVal = $savedCookie;
        $this->saveCookie(app(HoyolabService::class));
    }
};
?>

<div x-data="{
    init() {
        let savedCookie = localStorage.getItem('hoyolab_cookie');
        let isLogin = localStorage.getItem('isLogin');
        if (isLogin) {
            // Jangan panggil berkali-kali jika sudah ada error
            $wire.autoLogin(savedCookie);
        }
    }
}"
    class="min-h-screen bg-[#0b0f19] flex items-center justify-center p-6 text-slate-300 font-sans selection:bg-blue-500/30">
    <div class="w-full max-w-md relative z-10">
        <!-- Decorative Glow -->
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-blue-600/20 rounded-full blur-[80px] pointer-events-none">
        </div>
        <div
            class="absolute -bottom-20 -right-20 w-64 h-64 bg-purple-600/20 rounded-full blur-[80px] pointer-events-none">
        </div>

        <div
            class="bg-[#1e293b]/50 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500/20 to-blue-500/20 border border-teal-500/30 mb-4 shadow-inner relative overflow-hidden">
                    <div class="absolute inset-0 bg-teal-400/10 blur-xl"></div>
                    <svg class="w-8 h-8 drop-shadow-[0_0_8px_rgba(45,212,191,0.5)] relative z-10 shrink-0"
                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z"
                            class="fill-teal-500/20 stroke-teal-500" stroke-width="1.5" stroke-linejoin="round" />
                        <path d="M12 6L13.5 10.5L18 12L13.5 13.5L12 18L10.5 13.5L6 12L10.5 10.5L12 6Z"
                            class="fill-teal-400" />
                        <circle cx="12" cy="12" r="1.5" class="fill-white" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Login to HoyoDash</h1>
                <p class="text-sm text-slate-400">Masuk menggunakan HoYoLAB Cookie Anda.</p>
            </div>

            <!-- Disclaimer -->
            <div class="bg-blue-950/40 border border-blue-900/50 rounded-xl p-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs text-blue-200 leading-relaxed">
                    <strong>Privasi & Keamanan:</strong> Kami <span class="text-white font-semibold">TIDAK</span>
                    menyimpan cookie Anda di database server. Data cookie sepenuhnya aman dan hanya disimpan sementara
                    di penyimpanan lokal (Local Storage) browser Anda sendiri.
                </div>
            </div>

            <form wire:submit="saveCookie">
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-300 mb-2" for="cookie">HoYoLAB Account
                        Cookie</label>
                    <textarea id="cookie" wire:model="cookieVal" rows="4" placeholder="Paste ltoken, ltuid, dll di sini..."
                        class="w-full bg-[#0b0f19]/50 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all resize-none"></textarea>

                    @if ($errorMessage)
                        <p class="text-red-400 text-xs mt-2 font-medium">{{ $errorMessage }}</p>
                    @endif
                </div>

                <button type="submit"
                    class="w-full relative flex justify-center items-center py-3.5 px-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 hover:shadow-blue-500/40 transition-all transform hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Connect Account</span>
                    <span wire:loading class="flex justify-items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Validating...
                    </span>
                </button>
            </form>

            <!-- Tutorial Accordion -->
            <div class="mt-6 pt-5 border-t border-slate-700/50" x-data="{ expanded: false }">
                <button @click="expanded = !expanded" type="button"
                    class="w-full flex items-center justify-between text-sm text-slate-400 hover:text-blue-400 transition-colors group">
                    <span class="flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-400 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Cara mendapatkan HoYoLAB Cookie?
                    </span>
                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-300"
                        :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Expanded Content -->
                <div x-show="expanded" x-collapse style="display: none;"
                    class="mt-4 text-left bg-[#0b0f19]/70 p-4 rounded-xl border border-slate-700/50 text-xs text-slate-300 space-y-3 shadow-inner">

                    <p class="font-medium text-white mb-1">Ikuti panduan berikut ini di laptop/PC Anda:</p>

                    <ol class="list-decimal pl-4 space-y-2 marker:text-blue-500 marker:font-bold">
                        <li class="leading-relaxed">
                            Buka situs resmi <a href="https://www.hoyolab.com/" target="_blank"
                                class="text-blue-400 hover:underline font-medium">HoYoLAB</a> dan selesaikan <strong>Log
                                In</strong> menggunakan akun game Anda terlebih dahulu.
                        </li>
                        <li class="leading-relaxed">
                            Setelah berhasil masuk (halaman beranda terbuka), <strong>Klik Kanan</strong> di mana saja
                            pada layar dan pilih <strong class="text-white">Inspect</strong> (atau tekan tombol <kbd
                                class="px-1.5 py-0.5 bg-slate-800 border border-slate-600 rounded text-slate-300">F12</kbd>
                            / <kbd
                                class="px-1.5 py-0.5 bg-slate-800 border border-slate-600 rounded text-slate-300">Ctrl+Shift+I</kbd>).
                        </li>
                        <li class="leading-relaxed">
                            Pada bar navigasi di bagian paling atas kolom <em>Developer Tools</em>, cari dan klik tab
                            bernama <strong class="text-teal-400">Application</strong> (jika tidak terlihat, klik tanda
                            panah <code class="text-slate-400">&gt;&gt;</code> di ujung kanan).
                        </li>
                        <li class="leading-relaxed">
                            Pada menu sebelah kiri, gulir ke arah bagian <strong>Storage</strong>, perbesar folder
                            <strong class="text-white">Cookies</strong>, dan klik tautan <code
                                class="text-slate-400">https://www.hoyolab.com</code>.
                        </li>
                        <li class="leading-relaxed">
                            Cari teks-teks berikut ini di dalam kolom <strong>Name</strong>, klik dua-kali pada kolom
                            <strong>Value</strong>-nya, dan salin nilainya lalu tempelkan seluruh nilainya secara
                            berderet <em>(dipisah dengan titik koma <code>;</code>)</em> ke dalam isian <em>form</em> di
                            atas:
                            <div
                                class="mt-2 flex flex-wrap gap-1.5 px-2 py-1.5 bg-slate-800/80 rounded-lg border border-slate-700 font-mono text-[10px] text-blue-300">
                                <span>account_id_v2=XXXX;</span>
                                <span>account_mid_v2=XXXX;</span>
                                <span>cookie_token_v2=XXXX;</span>
                                <span>ltmid_v2=XXXX;</span>
                                <span>ltoken_v2=XXXX;</span>
                                <span>ltuid_v2=XXXX;</span>
                                <span>_HYVUUID=XXXX;</span>
                                <span>_MHYUUID=XXXX;</span>
                            </div>
                        </li>
                    </ol>

                    <div class="mt-3 pt-3 border-t border-slate-700/50 text-[10px] text-slate-400 flex gap-2">
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>
                            Contoh gabungan akhir: <code class="text-slate-500">ltoken_v2=v2...; ltuid_v2=80...;
                                cookie_token_v2=v2...;</code>
                            (harus berisi parameter tersebut secara lengkap agar bisa mengambil data yang valid).
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        $wire.on('cookie-validated', (data) => {
            // Pada Livewire 3 beda dengan v2, data params dibungkus dalam array
            let payload = Array.isArray(data) ? data[0] : data;

            let cookieVal = payload.cookie || '';
            let userInfo = payload.userInfo || null;
            let isLogin = payload.isLogin || false;

            // Simpan ke local storage user 
            if (cookieVal) {
                localStorage.setItem('hoyolab_cookie', cookieVal);
                if (userInfo) {
                    localStorage.setItem('hoyolab_user_info', JSON.stringify(userInfo));
                }
            }

            if (isLogin) {
                localStorage.setItem('isLogin', isLogin);
            }

            // Redirect ke dashboard
            window.location.href = '{{ route('dashboard.home') }}';
        });

        // Hapus cookie bila kedaluwarsa atau invalid
        $wire.on('cookie-invalid', () => {
            localStorage.removeItem('hoyolab_cookie');
        });
    </script>
@endscript
