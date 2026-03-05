<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\HoyolabService;

new class extends Component {
    public $cookieVal = '';
    public $userInfo = [];
    public $errorMessage = '';

    public function saveCookie(HoyolabService $hoyolabService)
    {
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
                'userInfo' => $this->userInfo
            ]);


        } else {
            $this->errorMessage = $response['message'] ?? 'Cookie tidak valid atau sesi telah kedaluwarsa.';
            $this->dispatch('cookie-invalid');
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
        if (savedCookie) {
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
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-blue-500/30 mb-4 shadow-inner">
                    <svg class="w-8 h-8 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-5l5 2.5-5 2.5z" />
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
                    <textarea id="cookie" wire:model="cookieVal" rows="4"
                        placeholder="Paste ltoken, ltuid, dll di sini..."
                        class="w-full bg-[#0b0f19]/50 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all resize-none"></textarea>

                    @if ($errorMessage)
                        <p class="text-red-400 text-xs mt-2 font-medium">{{ $errorMessage }}</p>
                    @endif
                </div>

                <button type="submit"
                    class="w-full relative flex justify-center items-center py-3.5 px-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 hover:shadow-blue-500/40 transition-all transform hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Connect Account</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Validating...
                    </span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="#" class="text-xs text-slate-500 hover:text-blue-400 transition-colors">Cara mendapatkan
                    HoYoLAB Cookie?</a>
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

        // Simpan ke local storage user 
        if (cookieVal) {
            localStorage.setItem('hoyolab_cookie', cookieVal);
            if (userInfo) {
                localStorage.setItem('hoyolab_user_info', JSON.stringify(userInfo));
            }
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