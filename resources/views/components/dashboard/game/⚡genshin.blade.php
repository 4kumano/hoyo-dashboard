<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\GenshinService;

new #[Layout('layouts.dashboard')] #[Title('Genshin Impact Stats')] class extends Component {
    public $uid;
    public $statsData = [];
    public $profile = [];
    public $errorMessage = '';

    public $role = [];
    public $avatars = [];
    public $stats = [];
    public $city_explorations = [];
    public $world_explorations = [];
    public $event_calendar = [];
    public $daily_note = [];

    public function loadData($cookie)
    {
        $genshinService = app(GenshinService::class);
        try {
            $data = $genshinService->getGenshinGameRecordCard($cookie);
            // dd($data);

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

                if ($this->uid !== '-') {
                    $indexData = $genshinService->getGameRecordIndex($cookie, $this->uid);
                    if (isset($indexData['message']) && $indexData['message'] === 'OK') {
                        $this->role = $indexData['data']['role'] ?? [];
                        $this->avatars = $indexData['data']['avatars'] ?? [];
                        $this->stats = $indexData['data']['stats'] ?? [];
                        $this->city_explorations = $indexData['data']['city_explorations'] ?? [];
                        $this->world_explorations = $indexData['data']['world_explorations'] ?? [];
                        $this->profile['avatar'] = $indexData['data']['role']['game_head_icon'] ?? '';
                    }

                    $calendarData = $genshinService->getEventCalendar($cookie, $this->uid);
                    if (isset($calendarData['message']) && $calendarData['message'] === 'OK') {
                        $this->event_calendar = $calendarData['data'] ?? [];
                    }

                    $dailyNoteData = $genshinService->getDailyNote($cookie, $this->uid);
                    if (isset($dailyNoteData['retcode']) && $dailyNoteData['retcode'] === 0) {
                        $this->daily_note = $dailyNoteData['data'] ?? [];
                    }
                }

            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan koneksi saat mengambil data: ' . $e->getMessage();
        }
    }
};
?>

<div class="h-full flex flex-col relative" x-data="{
    loading: true,
    init() {
        let cookie = localStorage.getItem('hoyolab_cookie');
        let isLogin = localStorage.getItem('isLogin');
        if (!cookie || !isLogin) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        $wire.loadData(cookie).then(() => {
            this.loading = false;
        });
    }
}">
    <!-- Loading State -->
    <div x-show="loading"
        class="absolute inset-0 z-50 flex items-center justify-center bg-[#0b0f19]/80 backdrop-blur-sm">
        <div class="flex flex-col items-center">
            <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-teal-500 mb-4" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-teal-400 font-medium tracking-widest animate-pulse">MEMUAT DATA...</span>
        </div>
    </div>

    <!-- Header Page -->
    <div class="p-5 md:p-8 lg:p-10 shrink-0 border-b border-slate-800/80 bg-[#111827]/40 relative overflow-hidden">
        @if (!empty($profile['bg']))
            <div class="absolute inset-x-0 top-0 h-full opacity-20 pointer-events-none"
                style="background-image: url('{{ $profile['bg'] }}'); background-size: cover; background-position: center; mask-image: linear-gradient(to bottom, black, transparent);">
            </div>
        @endif

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 relative z-10">
            <div class="flex items-center gap-5">
                <div
                    class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-700 p-0.5 shadow-lg shadow-teal-900/20">
                    <img src="{{ $profile['avatar'] ?? 'https://img-os-static.hoyolab.com/communityWeb/upload/1d7dd8f33c5ccdfdeac86e1e86ddd652.png' }}"
                        class="w-full h-full object-cover rounded-[14px]" alt="Genshin" loading="lazy">
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white tracking-wide">
                        Genshin Impact
                        @if (!empty($profile['nickname']))
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
                        @if (!empty($profile['region_name']))
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
    <div class="flex-1 overflow-y-auto p-5 md:p-8 lg:p-10 hide-scrollbar bg-[#0f172a]">
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
            <div x-data="{ activeTab: 'dailynote' }" class="space-y-6">
                <!-- Navigation Tabs -->
                <div class="flex space-x-2 border-b border-slate-700/50 pb-2 overflow-x-auto hide-scrollbar">
                    <button @click="activeTab = 'dailynote'"
                        :class="activeTab === 'dailynote' ? 'border-teal-400 text-teal-400' :
                                                                                    'border-transparent text-slate-400 hover:text-slate-300'"
                        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        Daily Note
                    </button>
                    <button @click="activeTab = 'mystat'"
                        :class="activeTab === 'mystat' ? 'border-teal-400 text-teal-400' :
                                                                                    'border-transparent text-slate-400 hover:text-slate-300'"
                        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        My Stats
                    </button>
                    <button @click="activeTab = 'mychara'"
                        :class="activeTab === 'mychara' ? 'border-teal-400 text-teal-400' :
                                                                                    'border-transparent text-slate-400 hover:text-slate-300'"
                        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        My Characters
                    </button>
                    <button @click="activeTab = 'worldexploring'"
                        :class="activeTab === 'worldexploring' ? 'border-teal-400 text-teal-400' :
                                                                                    'border-transparent text-slate-400 hover:text-slate-300'"
                        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        World Exploration
                    </button>
                    <button @click="activeTab = 'eventcalendar'"
                        :class="activeTab === 'eventcalendar' ? 'border-teal-400 text-teal-400' :
                                                                                    'border-transparent text-slate-400 hover:text-slate-300'"
                        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        Event Calendar
                    </button>
                </div>

                <!-- Tab: Daily Note -->
                <div x-show="activeTab === 'dailynote'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if (empty($daily_note))
                            <p class="text-slate-500 col-span-full">Belum ada data Daily Note.</p>
                        @else
                            <!-- Resin Card -->
                            <div
                                class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 shadow-xl">
                                <h3 class="text-lg font-bold text-teal-400 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Original Resin
                                </h3>
                                <div class="flex items-end gap-2 mb-2">
                                    <span
                                        class="text-3xl font-bold {{ ($daily_note['current_resin'] ?? 0) >= ($daily_note['max_resin'] ?? 200) ? 'text-red-400' : 'text-white' }}">{{ $daily_note['current_resin'] ?? 0 }}</span>
                                    <span class="text-slate-400 font-medium mb-1">/ {{ $daily_note['max_resin'] ?? 200 }}</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden mb-3">
                                    <div class="h-2.5 rounded-full transition-all {{ ($daily_note['current_resin'] ?? 0) >= ($daily_note['max_resin'] ?? 200) ? 'bg-red-500' : 'bg-gradient-to-r from-teal-500 to-emerald-400' }}"
                                        style="width: {{ min(100, (($daily_note['current_resin'] ?? 0) / ($daily_note['max_resin'] ?? 200)) * 100) }}%">
                                    </div>
                                </div>
                                @if(isset($daily_note['resin_recovery_time']) && $daily_note['resin_recovery_time'] > 0)
                                    @php
                                        $h = floor($daily_note['resin_recovery_time'] / 3600);
                                        $m = floor(($daily_note['resin_recovery_time'] % 3600) / 60);
                                    @endphp
                                    <p class="text-xs text-slate-400">Recovers in {{ $h }}h {{ $m }}m</p>
                                @elseif(($daily_note['current_resin'] ?? 0) >= ($daily_note['max_resin'] ?? 200))
                                    <p class="text-xs text-red-400">Fully Capped</p>
                                @endif
                            </div>

                            <!-- Daily Tasks Card -->
                            <div
                                class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 shadow-xl">
                                <h3 class="text-lg font-bold text-purple-400 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                    Daily Commissions
                                </h3>
                                @php
                                    $finishedNum = $daily_note['finished_task_num'] ?? 0;
                                    $totalNum = $daily_note['total_task_num'] ?? 4;
                                    $extraReward = $daily_note['is_extra_task_reward_received'] ?? false;
                                @endphp
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-slate-300">Completed</span>
                                    <span class="text-lg font-bold text-white">{{ $finishedNum }} <span
                                            class="text-slate-500 text-sm">/ {{ $totalNum }}</span></span>
                                </div>
                                <div class="grid grid-cols-4 gap-2 mb-4">
                                    @for($i = 0; $i < $totalNum; $i++)
                                        <div class="h-2 rounded-full {{ $i < $finishedNum ? 'bg-purple-500' : 'bg-slate-700' }}">
                                        </div>
                                    @endfor
                                </div>
                                <div
                                    class="flex items-center gap-2 text-[11px] sm:text-xs p-2 rounded-lg {{ $extraReward ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($finishedNum >= $totalNum ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse' : 'bg-slate-800 text-slate-400') }}">
                                    @if($extraReward)
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Extra rewards claimed
                                    @elseif($finishedNum >= $totalNum)
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Go to Katheryne to claim rewards!
                                    @else
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Complete commissions to claim extra rewards.
                                    @endif
                                </div>
                            </div>

                            <!-- Expeditions Card -->
                            <div
                                class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 shadow-xl">
                                <h3 class="text-lg font-bold text-amber-400 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Expeditions
                                </h3>
                                @php
                                    $currentExp = $daily_note['current_expedition_num'] ?? 0;
                                    $maxExp = $daily_note['max_expedition_num'] ?? 5;
                                    $expeditions = $daily_note['expeditions'] ?? [];
                                @endphp
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-slate-300">Dispatched</span>
                                    <span class="text-lg font-bold text-white">{{ $currentExp }} <span
                                            class="text-slate-500 text-sm">/ {{ $maxExp }}</span></span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    @forelse($expeditions as $exp)
                                        <div
                                            class="flex items-center gap-3 bg-slate-800/60 rounded-lg p-2 border {{ $exp['status'] === 'Finished' ? 'border-emerald-500/30' : 'border-slate-700' }}">
                                            <div class="w-8 h-8 rounded-full bg-slate-700 overflow-hidden shrink-0">
                                                <img src="{{ $exp['avatar_side_icon'] ?? '' }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @if($exp['status'] === 'Finished')
                                                    <span class="text-xs font-bold text-emerald-400">Finished</span>
                                                @else
                                                    @php
                                                        $rT = $exp['remained_time'] ?? 0;
                                                        $rTh = floor($rT / 3600);
                                                        $rTm = floor(($rT % 3600) / 60);
                                                    @endphp
                                                    <span class="text-xs font-medium text-amber-400">{{ $rTh }}h {{ $rTm }}m</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-500">No ongoing expeditions.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Realm Currency Card -->
                            <div
                                class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 shadow-xl">
                                <h3 class="text-lg font-bold text-orange-400 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Realm Currency
                                </h3>
                                <div class="flex items-end gap-2 mb-2">
                                    <span
                                        class="text-3xl font-bold {{ ($daily_note['current_home_coin'] ?? 0) >= ($daily_note['max_home_coin'] ?? 2400) ? 'text-orange-400' : 'text-white' }}">{{ $daily_note['current_home_coin'] ?? 0 }}</span>
                                    <span class="text-slate-400 font-medium mb-1">/
                                        {{ $daily_note['max_home_coin'] ?? 2400 }}</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden mb-3">
                                    <div class="h-2.5 rounded-full transition-all {{ ($daily_note['current_home_coin'] ?? 0) >= ($daily_note['max_home_coin'] ?? 2400) ? 'bg-orange-500' : 'bg-gradient-to-r from-orange-400 to-amber-300' }}"
                                        style="width: {{ min(100, (($daily_note['current_home_coin'] ?? 0) / ($daily_note['max_home_coin'] ?? 2400)) * 100) }}%">
                                    </div>
                                </div>
                                @if(isset($daily_note['home_coin_recovery_time']) && $daily_note['home_coin_recovery_time'] > 0)
                                    @php
                                        $hCoin = floor($daily_note['home_coin_recovery_time'] / 3600);
                                        $mCoin = floor(($daily_note['home_coin_recovery_time'] % 3600) / 60);
                                    @endphp
                                    <p class="text-xs text-slate-400">Recovers in {{ floor($hCoin / 24) }}d {{ $hCoin % 24 }}h
                                        {{ $mCoin }}m
                                    </p>
                                @elseif(($daily_note['current_home_coin'] ?? 0) >= ($daily_note['max_home_coin'] ?? 2400))
                                    <p class="text-xs text-orange-400">Fully Capped</p>
                                @endif
                            </div>

                            <!-- Transformer Card -->
                            @if(isset($daily_note['transformer']))
                                @php
                                    $trans = $daily_note['transformer'];
                                @endphp
                                <div
                                    class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 shadow-xl">
                                    <h3 class="text-lg font-bold text-sky-400 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                            </path>
                                        </svg>
                                        Parametric Transformer
                                    </h3>
                                    @if($trans['obtained'])
                                        @if($trans['recovery_time']['reached'])
                                            <div
                                                class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-3">
                                                <div class="w-10 h-10 rounded bg-emerald-500/20 flex items-center justify-center shrink-0">
                                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-emerald-400">Ready to use!</p>
                                                    <p class="text-[11px] text-slate-400 mt-0.5">Time to obtain materials.</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 bg-slate-800/60 border border-slate-700 rounded-lg p-3">
                                                <div
                                                    class="w-10 h-10 rounded bg-slate-900 border border-slate-700 flex items-center justify-center shrink-0 font-bold text-slate-300">
                                                    {{ $trans['recovery_time']['Day'] ?? 0 }}d
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-300">In Cooldown</p>
                                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $trans['recovery_time']['Day'] ?? 0 }}
                                                        days {{ $trans['recovery_time']['Hour'] ?? 0 }} hours remaining.</p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-sm text-slate-500">Not obtained yet.</p>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Tab: MyStat -->
                <div x-show="activeTab === 'mystat'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;">
                    <div
                        class="bg-gradient-to-r from-teal-900/30 to-[#111827] border border-teal-900/50 rounded-2xl p-5 md:p-8 shadow-xl relative overflow-hidden">
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
                            @if (empty($statsData) && empty($stats))
                                <p class="text-slate-500 col-span-4 text-center py-4">Loading...</p>
                            @endif
                            @foreach ($statsData as $stat)
                                <div
                                    class="bg-[#1e293b]/60 backdrop-blur-sm border border-slate-700/50 p-5 rounded-xl text-center hover:border-teal-500/30 transition-colors">
                                    <p class="text-sm text-slate-400 mb-2">{{ $stat['name'] ?? '' }}</p>
                                    <p class="text-3xl font-bold text-emerald-400">{{ $stat['value'] ?? '-' }}</p>
                                </div>
                            @endforeach

                            @if (!empty($stats))
                                @php
                                    $excludeKeys = [
                                        'field_ext_map',
                                        'role_combat',
                                        'hard_challenge',
                                        'full_fetter_avatar_num',
                                        'active_day_number',
                                        'achievement_number',
                                        'spiral_abyss',
                                        'avatar_number',
                                    ];
                                @endphp
                                @foreach ($stats as $key => $value)
                                    @if (!in_array($key, $excludeKeys) && !is_array($value) && !is_object($value))
                                        @php
                                            // Format label
                                            $label = str_replace(['_number', 'num'], '', $key);
                                            $label = ucwords(str_replace('_', ' ', trim($label)));
                                            if ($key === 'spiral_abyss') {
                                                $label = 'Spiral Abyss';
                                            }
                                        @endphp
                                        <div
                                            class="bg-[#1e293b]/60 backdrop-blur-sm border border-slate-700/50 p-5 rounded-xl text-center hover:border-teal-500/30 transition-colors">
                                            <p class="text-sm text-slate-400 mb-2">{{ $label }}</p>
                                            <p class="text-3xl font-bold text-emerald-400">{{ $value ?: '-' }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab: MyChara -->
                <div x-show="activeTab === 'mychara'" style="display: none;"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div
                        class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 md:p-8 shadow-xl relative overflow-hidden">
                        <h2 class="text-xl font-bold text-white mb-6">Characters</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                            @if (empty($avatars))
                                <p class="text-slate-500 col-span-full">Belum ada data karakter.</p>
                            @else
                                @foreach ($avatars as $avatar)
                                    <a wire:navigate
                                        href="{{ route('dashboard.game.genshin.characters.detail', ['uid' => $uid, 'character' => str_replace(' ', '_', $avatar['name']), 'id' => $avatar['id']]) }}"
                                        class="block relative group cursor-pointer aspect-square rounded-xl overflow-hidden border border-slate-700/50 hover:border-{{ strtolower($avatar['element'] ?? 'slate') }}-500 transition-colors bg-slate-800">
                                        <!-- Rarity background -->
                                        @php
                                            $bgClass = 'from-slate-700 to-slate-900';
                                            if (($avatar['rarity'] ?? 0) == 5) {
                                                $bgClass = 'from-orange-400/20 to-orange-600/50';
                                            } elseif (($avatar['rarity'] ?? 0) == 4) {
                                                $bgClass = 'from-purple-400/20 to-purple-600/50';
                                            }
                                        @endphp
                                        <div class="absolute inset-0 bg-gradient-to-b {{ $bgClass }}"></div>

                                        <img src="{{ $avatar['image'] }}" alt="{{ $avatar['name'] }}"
                                            class="w-full h-full object-cover relative z-10 transition-transform duration-300 group-hover:scale-110"
                                            loading="lazy">

                                        <!-- Level badge -->
                                        <div
                                            class="absolute bottom-1 right-1 bg-black/70 backdrop-blur-sm text-xs font-bold px-1.5 py-0.5 rounded text-white z-20 shadow">
                                            Lv.{{ $avatar['level'] ?? 0 }}
                                        </div>

                                        <!-- Extra Tooltip Info -->
                                        <div
                                            class="absolute inset-x-0 top-0 p-1.5 bg-gradient-to-b from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity z-20 flex justify-between items-start pointer-events-none">
                                            <span
                                                class="text-[10px] text-white font-medium bg-black/50 px-1 rounded truncate flex-1">{{ $avatar['name'] }}</span>
                                            @if (($avatar['actived_constellation_num'] ?? 0) > 0)
                                                <span
                                                    class="text-[10px] text-yellow-300 ml-1 font-bold">C{{ $avatar['actived_constellation_num'] }}</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab: World Exploration -->
                <div x-show="activeTab === 'worldexploring'" style="display: none;"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div
                        class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 md:p-8 shadow-xl">
                        <h2 class="text-xl font-bold text-white mb-6">World Exploration</h2>

                        <div class="space-y-6">
                            @if (empty($world_explorations))
                                <p class="text-slate-500">Belum ada eksplorasi.</p>
                            @endif
                            @foreach ($world_explorations as $world)
                                <div
                                    class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-5 hover:border-slate-600 transition-colors">
                                    <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-center">
                                        <!-- Area icon -->
                                        <div
                                            class="w-16 h-16 rounded-full overflow-hidden shrink-0 border-2 border-slate-600 relative">
                                            @if (!empty($world['icon']))
                                                <img src="{{ $world['icon'] }}" alt="{{ $world['name'] }}"
                                                    class="w-full h-full object-cover" loading="lazy">
                                            @else
                                                <div class="w-full h-full bg-slate-700 flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif

                                            <!-- Reputation Type badge -->
                                            @if (!empty($world['type']) && $world['type'] !== 'TypeUnknow')
                                                <div
                                                    class="absolute -bottom-1 -right-1 bg-slate-900 text-[10px] px-1.5 rounded-sm border border-slate-600 text-slate-300">
                                                    {{ $world['type'] === 'Reputation' ? 'Rep.' : 'Off.' }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Area info -->
                                        <div class="flex-1 min-w-0 w-full">
                                            <div class="flex justify-between items-baseline mb-2">
                                                <h3 class="text-lg font-semibold text-slate-200 truncate pr-2">
                                                    {{ $world['name'] }}
                                                </h3>
                                                <span
                                                    class="text-sm font-bold {{ ($world['exploration_percentage'] ?? 0) >= 1000 ? 'text-emerald-400' : 'text-slate-400' }}">
                                                    {{ ($world['exploration_percentage'] ?? 0) / 10 }}%
                                                </span>
                                            </div>

                                            <!-- Progress bar -->
                                            <div class="h-2 w-full bg-slate-700 rounded-full overflow-hidden mb-3">
                                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000"
                                                    style="width: {{ min(100, ($world['exploration_percentage'] ?? 0) / 10) }}%">
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                                                @if (isset($world['level']) && $world['level'] > 0)
                                                    <span class="text-slate-400 flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                                            </path>
                                                        </svg>
                                                        Level: <span class="text-white">{{ $world['level'] }}</span>
                                                    </span>
                                                @endif

                                                @if (isset($world['seven_statue_level']) && $world['seven_statue_level'] > 0)
                                                    <span class="text-slate-400 flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                            </path>
                                                        </svg>
                                                        Statue: <span class="text-white">Lv
                                                            {{ $world['seven_statue_level'] }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Offerings section if exists -->
                                    @if (!empty($world['offerings']) && is_array($world['offerings']))
                                        <div class="mt-4 pt-4 border-t border-slate-700/50 flex flex-wrap gap-2">
                                            @foreach ($world['offerings'] as $offering)
                                                <div
                                                    class="flex items-center gap-2 bg-slate-900/50 rounded-lg px-3 py-1.5 border border-slate-700 text-xs">
                                                    @if (!empty($offering['icon']))
                                                        <img src="{{ $offering['icon'] }}" class="w-4 h-4 object-contain" loading="lazy">
                                                    @endif
                                                    <span
                                                        class="text-slate-300 truncate max-w-[120px]">{{ $offering['name'] ?? '' }}</span>
                                                    <span class="text-amber-400 font-bold ml-1">Lv.{{ $offering['level'] ?? 0 }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tab: Event Calendar -->
                <div x-show="activeTab === 'eventcalendar'" style="display: none;"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div
                        class="bg-gradient-to-r from-slate-900/50 to-[#111827] border border-slate-700/50 rounded-2xl p-5 md:p-8 shadow-xl">
                        <h2 class="text-xl font-bold text-white mb-6">Event Calendar</h2>

                        @if (empty($event_calendar))
                            <div class="flex items-center justify-center min-h-[200px] text-center">
                                <div>
                                    <svg class="w-16 h-16 text-slate-500 mx-auto mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-slate-500">Event tidak tersedia saat ini.</p>
                                </div>
                            </div>
                        @else
                            @php
                                $hoyolabService = app(App\Services\HoyolabService::class);
                            @endphp

                            <!-- Groups: Event Wishes -->
                            @if (
                                    !empty($event_calendar['avatar_card_pool_list']) ||
                                    !empty($event_calendar['weapon_card_pool_list']) ||
                                    !empty($event_calendar['mixed_card_pool_list'])
                                )
                                <div class="mb-10">
                                    <h3 class="text-lg font-bold text-teal-400 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                            </path>
                                        </svg>
                                        Event Wishes
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @php
                                            $allWishes = array_merge(
                                                $event_calendar['avatar_card_pool_list'] ?? [],
                                                $event_calendar['weapon_card_pool_list'] ?? [],
                                                $event_calendar['mixed_card_pool_list'] ?? [],
                                            );
                                        @endphp

                                        @foreach ($allWishes as $wish)
                                            <div
                                                class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-5 relative overflow-hidden group">
                                                @if (($wish['pool_type'] ?? '') == 1)
                                                    <div
                                                        class="absolute top-0 right-0 px-3 py-1 bg-purple-500/20 text-purple-400 text-[10px] font-bold rounded-bl-lg">
                                                        Character</div>
                                                @elseif(($wish['pool_type'] ?? '') == 2)
                                                    <div
                                                        class="absolute top-0 right-0 px-3 py-1 bg-orange-500/20 text-orange-400 text-[10px] font-bold rounded-bl-lg">
                                                        Weapon</div>
                                                @elseif(($wish['pool_type'] ?? '') == 3)
                                                    <div
                                                        class="absolute top-0 right-0 px-3 py-1 bg-blue-500/20 text-blue-400 text-[10px] font-bold rounded-bl-lg">
                                                        Chronicled</div>
                                                @endif

                                                <h4 class="font-bold text-slate-200 mb-1 pr-16 truncate"
                                                    title="{{ $wish['pool_name'] }}">{{ $wish['pool_name'] }}</h4>
                                                <p
                                                    class="text-[11px] text-slate-400 mb-4 whitespace-nowrap overflow-hidden text-ellipsis">
                                                    {{ $hoyolabService->epochconverter($wish['start_timestamp'] ?? 0) }}
                                                    -
                                                    {{ $hoyolabService->epochconverter($wish['end_timestamp'] ?? 0) }}
                                                </p>

                                                <div class="flex flex-wrap gap-2">
                                                    @if (!empty($wish['avatars']))
                                                        @foreach (array_slice($wish['avatars'], 0, 5) as $avatar)
                                                            @php
                                                                $bgClass = 'from-slate-700 to-slate-900';
                                                                if (($avatar['rarity'] ?? 0) == 5) {
                                                                    $bgClass = 'from-orange-400/20 to-orange-600/50';
                                                                } elseif (($avatar['rarity'] ?? 0) == 4) {
                                                                    $bgClass = 'from-purple-400/20 to-purple-600/50';
                                                                }
                                                            @endphp
                                                            <div class="w-10 h-10 rounded-md overflow-hidden bg-gradient-to-b {{ $bgClass }} border border-slate-700 relative shrink-0"
                                                                title="{{ $avatar['name'] }}">
                                                                <img src="{{ $avatar['icon'] }}" alt="{{ $avatar['name'] }}"
                                                                    class="w-full h-full object-cover" loading="lazy">
                                                            </div>
                                                        @endforeach
                                                        @if (count($wish['avatars']) > 5)
                                                            <div
                                                                class="w-10 h-10 rounded-md bg-slate-800 border border-slate-700 flex items-center justify-center text-xs text-slate-400 font-bold shrink-0">
                                                                +{{ count($wish['avatars']) - 5 }}
                                                            </div>
                                                        @endif
                                                    @endif

                                                    @if (!empty($wish['weapon']))
                                                        @foreach (array_slice($wish['weapon'], 0, 5) as $weapon)
                                                            @php
                                                                $bgClass = 'from-slate-700 to-slate-900';
                                                                if (($weapon['rarity'] ?? 0) == 5) {
                                                                    $bgClass = 'from-orange-400/20 to-orange-600/50';
                                                                } elseif (($weapon['rarity'] ?? 0) == 4) {
                                                                    $bgClass = 'from-purple-400/20 to-purple-600/50';
                                                                }
                                                            @endphp
                                                            <div class="w-10 h-10 rounded-md overflow-hidden bg-gradient-to-b {{ $bgClass }} border border-slate-700 relative shrink-0"
                                                                title="{{ $weapon['name'] }}">
                                                                <img src="{{ $weapon['icon'] }}" alt="{{ $weapon['name'] }}"
                                                                    class="w-full h-full object-cover" loading="lazy">
                                                            </div>
                                                        @endforeach
                                                        @if (count($wish['weapon']) > 5)
                                                            <div
                                                                class="w-10 h-10 rounded-md bg-slate-800 border border-slate-700 flex items-center justify-center text-xs text-slate-400 font-bold shrink-0">
                                                                +{{ count($wish['weapon']) - 5 }}
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Groups: Events Overview -->
                            @if (!empty($event_calendar['act_list']))
                                <div>
                                    <h3 class="text-lg font-bold text-teal-400 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        Events Overview
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($event_calendar['act_list'] as $event)
                                            <div
                                                class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-5 hover:border-slate-600 transition-colors flex flex-col h-full">
                                                <div class="flex flex-col gap-1 mb-2">
                                                    <h4 class="font-bold text-slate-200 text-base leading-tight">
                                                        {{ $event['name'] }}
                                                        @if (($event['status'] ?? 0) == 2)
                                                            <span
                                                                class="inline-block flex-shrink-0 ml-1 px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded uppercase tracking-wider align-middle">Active</span>
                                                        @endif
                                                    </h4>
                                                    @if (!empty($event['start_timestamp']) && !empty($event['end_timestamp']))
                                                        <div class="text-[11px] text-slate-400">
                                                            {{ $hoyolabService->epochconverter($event['start_timestamp']) }}
                                                            –
                                                            {{ $hoyolabService->epochconverter($event['end_timestamp']) }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <p class="text-xs text-slate-400 mb-4 flex-1">
                                                    {{ !empty($event['desc']) ? $event['desc'] : 'Tidak ada keterangan event.' }}
                                                </p>

                                                @if (!empty($event['reward_list']))
                                                    <div class="flex flex-wrap gap-1.5 mt-auto pt-3 border-t border-slate-700/50">
                                                        @foreach (array_slice($event['reward_list'], 0, 10) as $reward)
                                                            <div class="flex items-center gap-1 bg-slate-900/60 border border-slate-700 rounded px-1.5 py-0.5 text-xs text-slate-300 w-fit"
                                                                title="{{ $reward['name'] ?? '' }}">
                                                                @if (!empty($reward['icon']))
                                                                    <img src="{{ $reward['icon'] }}" class="w-3.5 h-3.5 object-contain"
                                                                        loading="lazy">
                                                                @endif
                                                                @if (!empty($reward['num']))
                                                                    <span
                                                                        class="text-[10px] font-medium">x{{ number_format($reward['num']) }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                        @if (count($event['reward_list']) > 10)
                                                            <div
                                                                class="flex items-center justify-center bg-slate-900/60 border border-slate-700 rounded px-1.5 py-0.5 text-[10px] text-slate-400 font-bold shrink-0">
                                                                +{{ count($event['reward_list']) - 10 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>