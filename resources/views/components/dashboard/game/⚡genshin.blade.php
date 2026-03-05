<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\GenshinService;

new #[Layout('layouts.dashboard')] class extends Component {
    public $uid;
    public $statsData = [];
    public $profile = [];
    public $errorMessage = '';

    public function loadData($cookie)
    {
        $genshinService = app(GenshinService::class);
        try {
            $data = $genshinService->getGenshinGameRecordCard($cookie);

            if (isset($data['retcode']) && $data['retcode'] !== 0) {
                $this->errorMessage = $data['message'] ?? 'Gagal mengambil data dari server HoYoLAB.';
            } else {
                $this->statsData = $data['stats'] ?? [];
                $this->uid = $data['game_role_id'] ?? '-';
                $this->profile = [
                    'nickname' => $data['nickname'] ?? '',
                    'level' => $data['level'] ?? '',
                    'region_name' => $data['region_name'] ?? '',
                    'bg' => $data['bg'] ?? '',
                ];
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan koneksi saat mengambil data: ' . $e->getMessage();
        }
    }
};
?>

<div class="h-full flex flex-col" x-data="{
    init() {
        let cookie = localStorage.getItem('hoyolab_cookie');
        if (!cookie) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        $wire.loadData(cookie);
    }
}">
    <!-- Header Page -->
    <div class="p-8 lg:p-10 shrink-0 border-b border-slate-800/80 bg-[#111827]/40 relative overflow-hidden">
        @if(!empty($profile['bg']))
            <div class="absolute inset-x-0 top-0 h-full opacity-20 pointer-events-none"
                style="background-image: url('{{ $profile['bg'] }}'); background-size: cover; background-position: center; mask-image: linear-gradient(to bottom, black, transparent);">
            </div>
        @endif

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-5">
                <div
                    class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-700 p-0.5 shadow-lg shadow-teal-900/20">
                    <img src="https://img-os-static.hoyolab.com/communityWeb/upload/1d7dd8f33c5ccdfdeac86e1e86ddd652.png"
                        class="w-full h-full object-cover rounded-[14px]" alt="Genshin">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-wide">
                        Genshin Impact
                        @if(!empty($profile['nickname']))
                            <span class="text-xl text-teal-400 font-medium ml-2"> {{ $profile['nickname'] }} (Lv.
                                {{ $profile['level'] }})</span>
                        @endif
                    </h1>
                    <p class="text-teal-400 font-medium tracking-wider mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                            </path>
                        </svg>
                        UID: {{ $uid }}
                        @if(!empty($profile['region_name']))
                            <span class="text-slate-400 ml-2">| {{ $profile['region_name'] }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <a livewire:navigate href="{{ route('dashboard.home') }}"
                class="px-5 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl transition-all border border-slate-700/50 shadow-sm flex items-center gap-2 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 overflow-y-auto p-8 lg:p-10 hide-scrollbar bg-[#0f172a]">
        @if ($errorMessage)
            <div class="flex flex-col items-center justify-center py-20">
                <div
                    class="bg-red-500/10 border border-red-500/30 rounded-2xl p-8 max-w-xl text-center shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-transparent"></div>
                    <svg class="w-16 h-16 text-red-400 mx-auto mb-4 relative z-10" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-bold text-red-400 mb-2 relative z-10">Gagal Memuat Data</h3>
                    <p class="text-slate-300 relative z-10">{{ $errorMessage }}</p>
                </div>
            </div>
        @else
            <div class="space-y-8">
                <!-- Stat Cards -->
                <div
                    class="bg-gradient-to-r from-teal-900/30 to-[#111827] border border-teal-900/50 rounded-2xl p-8 shadow-xl relative overflow-hidden">
                    <!-- Decorative Element -->
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>

                    <h2 class="text-xl font-bold text-white mb-6 relative z-10 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                            </path>
                        </svg>
                        Battle Chronicle
                    </h2>

                    <!-- Main Stats from Card Array -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 relative z-10">
                        @if(empty($statsData))
                            <p class="text-slate-500 col-span-4 text-center py-4">Data battle chronicle kosong.</p>
                        @endif
                        @foreach($statsData as $stat)
                            <div
                                class="bg-[#1e293b]/60 backdrop-blur-sm border border-slate-700/50 p-5 rounded-xl text-center hover:border-teal-500/30 transition-colors">
                                <p class="text-sm text-slate-400 mb-2">{{ $stat['name'] ?? '' }}</p>
                                <p class="text-3xl font-bold text-emerald-400">{{ $stat['value'] ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>