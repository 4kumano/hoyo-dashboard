<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\GenshinService;

new #[Layout('layouts.dashboard')] #[Title('Genshin Impact Character Detail')] class extends Component {
    public $uid;
    public $character;
    public $id;

    public $characterData = [];
    public $errorMessage = '';

    public function loadData($cookie)
    {
        $genshinService = app(GenshinService::class);
        $result = $genshinService->getCharacterDetail($cookie, $this->uid, (int) $this->id);
        // dd($result);
        if (isset($result['message']) && $result['message'] === 'OK') {
            $this->characterData = $result['data'] ?? [];
        } else {
            // Redirect back to Genshin dashboard if character fetch fails
            return redirect()->route('dashboard.game.genshin', ['uid' => $this->uid]);
        }
    }

    public function mount($uid, $character, $id)
    {
        $this->uid = $uid;
        $this->character = $character;
        $this->id = $id;
    }
};
?>

<div x-data="{
    loading: true,
    showBackToTop: false,
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
    },
    handleScroll() {
        if (this.$refs.scrollContainer) {
            this.showBackToTop = this.$refs.scrollContainer.scrollTop > 300;
        }
    },
    scrollToTop() {
        if (this.$refs.scrollContainer) {
            this.$refs.scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}" x-ref="scrollContainer" @scroll.passive="handleScroll"
    class="w-full flex-1 h-full bg-[#0b0f19] text-slate-300 relative overflow-y-auto overflow-x-hidden">

    <!-- Loading State -->
    <div x-show="loading"
        class="absolute inset-0 z-50 flex items-center justify-center bg-[#0b0f19]/80 backdrop-blur-sm">
        <div class="flex flex-col items-center">
            <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <p class="text-lg font-medium text-slate-300">Loading character data...</p>
        </div>
    </div>

    <!-- Content (shows when loaded) -->
    <div x-show="!loading" style="display: none;" class="relative z-10">
        @if (!empty($characterData) && !empty($characterData['base']))
            <!-- Hero / Header Section -->
            <div class="relative h-[400px] md:h-[500px] w-full overflow-hidden">
                <!-- Character Background Image -->
                <div class="absolute inset-0 bg-cover bg-top opacity-40 mix-blend-screen"
                    style="background-image: url('{{ $characterData['base']['image'] ?? '' }}');">
                </div>
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#0b0f19] via-[#0b0f19]/80 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#0b0f19] via-[#0b0f19]/50 to-transparent"></div>

                <!-- Navigation Back -->
                <div class="absolute top-6 left-6 md:top-8 md:left-8 z-20">
                    <a livewire:navigate href="{{ route('dashboard.game.genshin', ['uid' => $uid]) }}"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/80 text-slate-300 border border-slate-700 hover:text-white hover:bg-slate-700 hover:border-slate-500 transition-all shadow-lg backdrop-blur-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                </div>

                <!-- Character Header Details -->
                <div
                    class="absolute bottom-0 left-0 w-full p-4 sm:p-6 md:p-12 z-20 flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-6 bg-gradient-to-t from-[#0b0f19] to-transparent pt-20">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                        <div
                            class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-2xl bg-slate-800 border-2 border-slate-700 shadow-2xl overflow-hidden shrink-0 filter drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                            <img src="{{ $characterData['base']['icon'] ?? '' }}"
                                alt="{{ $characterData['base']['name'] ?? '' }}" class="w-full h-full object-cover">
                            <!-- Rarity Background Gradient based on standard Genshin colors -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t @if (($characterData['base']['rarity'] ?? 4) == 5) from-yellow-600/60 @else from-purple-600/60 @endif mix-blend-overlay">
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <h1
                                    class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight drop-shadow-lg">
                                    {{ $characterData['base']['name'] ?? 'Unknown' }}
                                </h1>
                                <span
                                    class="px-3 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wider rounded-full bg-slate-800/80 border border-slate-600 text-slate-300 drop-shadow-md">
                                    Lv. {{ $characterData['base']['level'] ?? 1 }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm font-medium">
                                <span
                                    class="flex items-center gap-1.5 px-2 sm:px-3 py-1 rounded-md bg-slate-900/60 border border-slate-800 text-slate-300">
                                    <span
                                        class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full @if (($characterData['base']['element'] ?? '') == 'Hydro') bg-blue-500 @elseif(($characterData['base']['element'] ?? '') == 'Pyro') bg-red-500 @elseif(($characterData['base']['element'] ?? '') == 'Cryo') bg-cyan-400 @elseif(($characterData['base']['element'] ?? '') == 'Electro') bg-purple-500 @elseif(($characterData['base']['element'] ?? '') == 'Anemo') bg-teal-400 @elseif(($characterData['base']['element'] ?? '') == 'Geo') bg-yellow-500 @elseif(($characterData['base']['element'] ?? '') == 'Dendro') bg-green-500 @else bg-slate-400 @endif shadow-[0_0_8px_currentColor]"></span>
                                    {{ $characterData['base']['element'] ?? 'Unknown' }}
                                </span>
                                <span
                                    class="flex items-center gap-1.5 px-2 sm:px-3 py-1 rounded-md bg-slate-900/60 border border-slate-800 text-slate-300">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-yellow-500 drop-shadow-[0_0_5px_rgba(234,179,8,0.5)]"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                    {{ $characterData['base']['rarity'] ?? 4 }} Star
                                </span>
                                <span
                                    class="flex items-center gap-1.5 px-2 sm:px-3 py-1 rounded-md bg-slate-900/60 border border-slate-800 text-slate-300">
                                    <span class="text-pink-400">♥</span> Fetter
                                    {{ $characterData['base']['fetter'] ?? 1 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Constellation Ribbon Banner -->
                    <div class="hidden md:flex flex-col items-end gap-2 drop-shadow-xl">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Constellation</span>
                        <div
                            class="flex items-center bg-slate-900/80 border border-slate-700 rounded-xl p-1.5 backdrop-blur-md">
                            @for ($i = 0; $i < 6; $i++)
                                <div
                                    class="w-10 h-10 rounded-lg flex items-center justify-center {{ $i < ($characterData['base']['actived_constellation_num'] ?? 0) ? 'bg-cyan-900/40 text-cyan-300' : 'bg-slate-800/50 text-slate-600' }} border border-transparent mx-0.5">
                                    <span class="font-bold text-lg font-serif">C{{ $i + 1 }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Container lg -->
            <div class="max-w-7xl mx-auto px-4 md:px-8 pb-20 relative z-20 mt-4 md:-mt-6">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">

                    <!-- Left Column: Weapon & Relics -->
                    <div class="lg:col-span-2 space-y-6 md:space-y-8">

                        <!-- Weapon Section -->
                        @if (!empty($characterData['weapon']))
                            <div
                                class="bg-slate-900/50 rounded-3xl border border-slate-800 backdrop-blur-xl overflow-hidden hover:border-slate-700 transition-colors shadow-xl">
                                <div class="border-b border-slate-800/50 px-6 py-4 bg-slate-800/20">
                                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                        Equipped Weapon
                                    </h2>
                                </div>
                                <div class="p-4 sm:p-6 md:p-8 flex flex-col sm:flex-row gap-5 sm:gap-6 md:gap-8 items-center sm:items-start">
                                    <div
                                        class="w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-2xl bg-gradient-to-br @if (($characterData['weapon']['rarity'] ?? 4) == 5) from-yellow-900/50 to-amber-600/20 border-amber-700/50 @else from-purple-900/50 to-fuchsia-600/20 border-purple-700/50 @endif border shrink-0 flex items-center justify-center p-2 relative group cursor-pointer shadow-lg">
                                        <img src="{{ $characterData['weapon']['icon'] ?? '' }}"
                                            class="w-full h-full object-contain filter drop-shadow-md group-hover:scale-110 transition-transform duration-300"
                                            loading="lazy">
                                        <div
                                            class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 bg-slate-900 text-[10px] sm:text-xs font-bold px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md border border-slate-700 text-white shadow-xl">
                                            R{{ $characterData['weapon']['affix_level'] ?? 1 }}</div>
                                    </div>
                                    <div class="flex-1 text-center sm:text-left space-y-2 sm:space-y-3 w-full">
                                        <div>
                                            <h3 class="text-xl sm:text-2xl font-bold text-white mb-1">
                                                {{ $characterData['weapon']['name'] ?? 'Unknown Weapon' }}
                                            </h3>
                                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 sm:gap-3">
                                                <span
                                                    class="text-xs sm:text-sm text-slate-400">{{ $characterData['weapon']['type_name'] ?? 'Weapon' }}</span>
                                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-slate-700"></span>
                                                <span
                                                    class="text-xs sm:text-sm font-semibold text-white px-1.5 sm:px-2 py-0.5 rounded bg-slate-800 border border-slate-700">Lv.
                                                    {{ $characterData['weapon']['level'] ?? 1 }}</span>
                                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-slate-700"></span>
                                                <div class="flex text-yellow-500 text-xs sm:text-sm">
                                                    @for ($i = 0; $i < ($characterData['weapon']['rarity'] ?? 4); $i++)
                                                        ★
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>

                                        <p class="text-slate-400 text-xs sm:text-sm italic border-l-2 border-indigo-500/30 pl-3 leading-relaxed">
                                            "{{ $characterData['weapon']['desc'] ?? '' }}"
                                        </p>

                                        <!-- Weapon Stats -->
                                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mt-4 pt-4 border-t border-slate-800/50 w-full">
                                            @if (isset($characterData['weapon']['main_property']))
                                                @php
                                                    $mainType = $characterData['weapon']['main_property']['property_type'] ?? null;
                                                    $mainStatInfo = $mainType ? ($characterData['icon_stats'][$mainType] ?? null) : null;
                                                @endphp
                                                <div class="bg-slate-800/40 rounded-xl p-3 border border-slate-700/50 flex flex-col justify-center transition-colors hover:bg-slate-800/60">
                                                    <div class="flex items-center gap-1.5 mb-1.5">
                                                        @if ($mainStatInfo && !empty($mainStatInfo['icon']))
                                                            <img src="{{ $mainStatInfo['icon'] }}" alt="{{ $mainStatInfo['name'] }}" class="w-4 h-4 filter drop-shadow opacity-90" loading="lazy">
                                                        @endif
                                                        <span class="text-xs font-semibold text-slate-400 truncate">{{ $mainStatInfo['name'] ?? 'Base ATK' }}</span>
                                                    </div>
                                                    <p class="text-lg sm:text-xl font-bold text-white">
                                                        {{ $characterData['weapon']['main_property']['final'] ?? 0 }}
                                                    </p>
                                                </div>
                                            @endif
                                            @if (isset($characterData['weapon']['sub_property']))
                                                @php
                                                    $subType = $characterData['weapon']['sub_property']['property_type'] ?? null;
                                                    $subStatInfo = $subType ? ($characterData['icon_stats'][$subType] ?? null) : null;
                                                @endphp
                                                <div class="bg-slate-800/40 rounded-xl p-3 border border-slate-700/50 flex flex-col justify-center transition-colors hover:bg-slate-800/60">
                                                    <div class="flex items-center gap-1.5 mb-1.5">
                                                        @if ($subStatInfo && !empty($subStatInfo['icon']))
                                                            <img src="{{ $subStatInfo['icon'] }}" alt="{{ $subStatInfo['name'] }}" class="w-4 h-4 filter drop-shadow opacity-90" loading="lazy">
                                                        @endif
                                                        <span class="text-xs font-semibold text-slate-400 truncate">{{ $subStatInfo['name'] ?? 'Sub Stat' }}</span>
                                                    </div>
                                                    <p class="text-lg sm:text-xl font-bold text-emerald-400">
                                                        {{ $characterData['weapon']['sub_property']['final'] ?? 0 }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Artifacts/Relics -->
                        @if (!empty($characterData['relics']))
                            <div
                                class="bg-slate-900/50 rounded-3xl border border-slate-800 backdrop-blur-xl overflow-hidden hover:border-slate-700 transition-colors shadow-xl">
                                <div
                                    class="border-b border-slate-800/50 px-6 py-4 bg-slate-800/20 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                            </path>
                                        </svg>
                                        Artifacts
                                    </h2>
                                </div>
                                <div class="p-4 sm:p-6 md:p-8">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                                        @foreach ($characterData['relics'] as $relic)
                                            <div
                                                class="bg-slate-800/30 rounded-2xl p-3 sm:p-4 border border-slate-700/50 hover:bg-slate-800/50 transition-colors flex gap-3 sm:gap-4 relative overflow-hidden group">
                                                <!-- Artifact Rarity BG Glow -->
                                                <div
                                                    class="absolute -right-10 -top-10 w-32 h-32 rounded-full @if (($relic['rarity'] ?? 4) == 5) bg-yellow-500/5 @else bg-purple-500/5 @endif blur-2xl group-hover:bg-opacity-10 transition-all">
                                                </div>

                                                <div
                                                    class="relative w-16 h-16 sm:w-20 sm:h-20 shrink-0 bg-slate-900/60 rounded-xl border border-slate-700 p-1">
                                                    <img src="{{ $relic['icon'] ?? '' }}" alt="{{ $relic['name'] ?? '' }}"
                                                        class="w-full h-full object-contain" loading="lazy">
                                                    <span
                                                        class="absolute -bottom-2 -left-2 bg-slate-800 text-[10px] sm:text-xs px-1.5 py-0.5 rounded border border-slate-600 text-slate-300 font-bold">+{{ $relic['level'] ?? 0 }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0 flex flex-col pt-0.5">
                                                    <div class="flex justify-between items-start mb-1.5">
                                                        <div class="min-w-0 pr-2">
                                                            <p class="text-[9px] sm:text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-0.5">
                                                                {{ $relic['pos_name'] ?? 'Artifact' }}
                                                            </p>
                                                            <p class="text-xs sm:text-sm font-bold text-white truncate"
                                                                title="{{ $relic['name'] ?? '' }}">
                                                                {{ $relic['set']['name'] ?? ($relic['name'] ?? 'Unknown') }}
                                                            </p>
                                                        </div>
                                                        <!-- Relic Rarity Stars -->
                                                        <div class="flex text-yellow-500 text-[10px] sm:text-xs shrink-0 drop-shadow mt-0.5">
                                                            @for ($i = 0; $i < ($relic['rarity'] ?? 4); $i++)
                                                                ★
                                                            @endfor
                                                        </div>
                                                    </div>

                                                    <!-- Main property -->
                                                    @php
                                                        $mainType = $relic['main_property']['property_type'] ?? null;
                                                        $mainStatInfo = $mainType ? ($characterData['icon_stats'][$mainType] ?? null) : null;
                                                    @endphp
                                                    <div class="flex items-center justify-between mt-auto mb-2.5 bg-slate-900/40 rounded-lg p-1.5 sm:p-2 border border-slate-700/50">
                                                        <div class="flex items-center gap-1.5">
                                                            @if ($mainStatInfo && !empty($mainStatInfo['icon']))
                                                                <img src="{{ $mainStatInfo['icon'] }}" alt="{{ $mainStatInfo['name'] }}" class="w-3.5 h-3.5 sm:w-4 sm:h-4 opacity-80 filter drop-shadow" loading="lazy">
                                                            @endif
                                                            <span class="text-[10px] sm:text-xs text-slate-300 font-medium">{{ $mainStatInfo['name'] ?? 'Main Stat' }}</span>
                                                        </div>
                                                        <span class="text-xs sm:text-sm font-bold text-amber-400">{{ $relic['main_property']['value'] ?? 0 }}</span>
                                                    </div>

                                                    <!-- Sub properties -->
                                                    @if(!empty($relic['sub_property_list']))
                                                        <div class="flex flex-col gap-1">
                                                            @foreach ($relic['sub_property_list'] as $sub)
                                                                @php
                                                                    $subStatInfo = isset($sub['property_type']) ? ($characterData['icon_stats'][$sub['property_type']] ?? null) : null;
                                                                @endphp
                                                                <div class="flex items-center justify-between text-[10px] sm:text-[11px]">
                                                                    <div class="flex items-center gap-1.5 min-w-0 pr-2">
                                                                        @if ($subStatInfo && !empty($subStatInfo['icon']))
                                                                            <img src="{{ $subStatInfo['icon'] }}" alt="{{ $subStatInfo['name'] }}" class="w-3 h-3 opacity-60 shrink-0" loading="lazy">
                                                                        @else
                                                                            <span class="w-3 h-3 shrink-0"></span>
                                                                        @endif
                                                                        <span class="text-slate-400 truncate">{{ $subStatInfo['name'] ?? 'Sub Stat' }}</span>
                                                                    </div>
                                                                    <div class="flex items-center gap-2 shrink-0">
                                                                        <span class="font-medium text-slate-200">{{ $sub['value'] ?? 0 }}</span>
                                                                        <div class="w-7 sm:w-8 flex justify-end">
                                                                            @if(($sub['times'] ?? 0) > 0)
                                                                                <span class="text-[8px] sm:text-[9px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-1 py-0.5 rounded" title="Upgraded {{ $sub['times'] }} times">
                                                                                    +{{ $sub['times'] }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Skills / Talents -->
                        @if (!empty($characterData['skills']))
                            <div
                                class="bg-slate-900/50 rounded-3xl border border-slate-800 backdrop-blur-xl overflow-hidden hover:border-slate-700 transition-colors shadow-xl">
                                <div
                                    class="border-b border-slate-800/50 px-6 py-4 bg-slate-800/20 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Talents & Passives
                                    </h2>
                                </div>
                                <div class="p-4 sm:p-6 md:p-8 space-y-5 sm:space-y-6">
                                    @foreach ($characterData['skills'] as $skill)
                                                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-5 group">
                                                            <div class="flex items-start gap-3 sm:gap-4 sm:w-1/3 shrink-0">
                                                                <div
                                                                    class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 border-slate-700 bg-slate-800 flex items-center justify-center shrink-0 shadow-lg group-hover:border-amber-500/50 transition-colors">
                                                                    <img src="{{ $skill['icon'] ?? '' }}" alt="{{ $skill['name'] ?? 'Skill' }}"
                                                                        class="w-8 h-8 sm:w-10 sm:h-10 object-contain" loading="lazy">
                                                                </div>
                                                                <div>
                                                                    <h3 class="text-xs sm:text-sm font-bold text-white mb-1 leading-tight">
                                                                        {{ $skill['name'] ?? 'Unknown Skill' }}
                                                                    </h3>
                                                                    @if (isset($skill['level']) && $skill['skill_type'] == 1)
                                                                        <span
                                                                            class="inline-block px-1.5 sm:px-2 py-0.5 rounded text-[10px] sm:text-xs font-bold bg-slate-800 border border-slate-600 text-amber-400 shadow-inner">
                                                                            Lv. {{ $skill['level'] }}
                                                                        </span>
                                                                    @elseif($skill['skill_type'] == 2)
                                                                        <span
                                                                            class="inline-block px-1.5 sm:px-2 py-0.5 rounded text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-slate-800/60 border border-slate-700 text-slate-400">
                                                                            Passive
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="sm:w-2/3 bg-slate-800/30 rounded-xl p-3 sm:p-4 border border-slate-700/50">
                                                                <p class="text-xs sm:text-[13px] text-slate-300 leading-relaxed whitespace-pre-line">
                                                                    {!! preg_replace(
                                            '/<color.*?>(.*?)<\/color>/',
                                            '<span class="text-amber-400 font-medium">$1</span>',
                                            strip_tags(str_replace('\n', '<br>', $skill['desc'] ?? ''), '<span><br>'),
                                        ) !!}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="h-px bg-slate-800/50 w-full last:hidden"></div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Stats & Constellations -->
                    <div class="space-y-6 md:space-y-8">

                        <!-- Base Stats Panel -->
                        <div
                            class="bg-slate-900/50 rounded-3xl border border-slate-800 backdrop-blur-xl overflow-hidden shadow-xl">
                            <div class="border-b border-slate-800/50 px-4 sm:px-6 py-3 sm:py-4 bg-slate-800/20">
                                <h2 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    Combat Stats
                                </h2>
                            </div>
                            <div class="p-4 sm:p-5 md:p-6 space-y-0.5 sm:space-y-1">
                                @if (!empty($characterData['selected_properties']))
                                    @foreach ($characterData['selected_properties'] as $prop)
                                        @php
                                            $statInfo = $characterData['icon_stats'][$prop['property_type']] ?? null;
                                        @endphp
                                        <div
                                            class="flex justify-between items-center py-2.5 px-3 rounded-lg hover:bg-slate-800/40 transition-colors">
                                            <div class="flex items-center gap-3">
                                                @if ($statInfo && !empty($statInfo['icon']))
                                                    <img src="{{ $statInfo['icon'] }}" alt="{{ $statInfo['name'] }}"
                                                        class="w-5 h-5 filter drop-shadow opacity-90" loading="lazy">
                                                @else
                                                    <span
                                                        class="w-5 h-5 rounded-full bg-slate-700/30 border border-slate-600 flex items-center justify-center shrink-0"></span>
                                                @endif
                                                <span class="text-xs sm:text-sm font-medium text-slate-300">
                                                    {{ $statInfo['name'] ?? 'Stat' }}
                                                </span>
                                            </div>
                                            <div class="text-right flex flex-col items-end">
                                                <span class="text-xs sm:text-sm font-bold text-white">{{ $prop['final'] ?? 0 }}</span>
                                                @if (!empty($prop['base']) && !empty($prop['add']))
                                                    <span class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                                        {{ $prop['base'] }} <span class="text-emerald-400">+{{ $prop['add'] }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-slate-500 text-sm text-center py-4">No stat data available.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Constellations Detail Panel -->
                        <div
                            class="bg-slate-900/50 rounded-3xl border border-slate-800 backdrop-blur-xl overflow-hidden shadow-xl">
                            <div class="border-b border-slate-800/50 px-4 sm:px-6 py-3 sm:py-4 bg-slate-800/20">
                                <h2 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                        </path>
                                    </svg>
                                    Constellations
                                </h2>
                            </div>
                            <div class="p-4 sm:p-5 md:p-6">
                                @if (!empty($characterData['constellations']))
                                    <div class="space-y-4 sm:space-y-5 relative">
                                        <!-- Connecting Line -->
                                        <div class="absolute left-5 sm:left-6 top-5 sm:top-6 bottom-5 sm:bottom-6 w-0.5 bg-slate-800 -z-10"></div>

                                        @foreach ($characterData['constellations'] as $index => $constellation)
                                                                @php $isActive = $constellation['is_actived'] ?? false; @endphp
                                                                <div class="flex gap-3 sm:gap-4 group">
                                                                    <div
                                                                        class="relative shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 {{ $isActive ? 'border-cyan-500 bg-cyan-900/40 shadow-[0_0_15px_rgba(6,182,212,0.3)]' : 'border-slate-700 bg-slate-800' }} flex items-center justify-center transition-colors">
                                                                        <img src="{{ $constellation['icon'] ?? '' }}"
                                                                            class="w-6 h-6 sm:w-8 sm:h-8 opacity-{{ $isActive ? '100' : '40' }} group-hover:opacity-100 transition-opacity"
                                                                            loading="lazy">
                                                                        @if (!$isActive)
                                                                            <div class="absolute inset-0 bg-slate-900/50 rounded-full">
                                                                            </div>
                                                                            <svg class="absolute w-3 h-3 sm:w-4 sm:h-4 text-slate-500" fill="currentColor"
                                                                                viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                                                    clip-rule="evenodd"></path>
                                                                            </svg>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-1 pb-1">
                                                                        <h4
                                                                            class="text-sm font-bold {{ $isActive ? 'text-cyan-100' : 'text-slate-500' }} mb-1 line-clamp-1">
                                                                            {{ $constellation['name'] ?? 'Unknown' }}
                                                                        </h4>
                                                                        <p
                                                                            class="text-xs {{ $isActive ? 'text-slate-400' : 'text-slate-600' }} line-clamp-2 md:line-clamp-none leading-relaxed transition-colors group-hover:text-slate-400">
                                                                            <!-- Strip any custom color tags from Hoyolab API string -->
                                                                            {!! preg_replace(
                                                '/<color.*?>(.*?)<\/color>/',
                                                '<span class="text-amber-400">$1</span>',
                                                strip_tags(str_replace('\n', '<br>', $constellation['effect'] ?? ''), '<span><br>'),
                                            ) !!}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-slate-500 text-sm text-center py-4">No constellation data available.
                                    </p>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        @else
            <!-- Error State -->
            <div class="flex flex-col items-center justify-center min-h-[500px] text-center px-4">
                <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-bold text-white mb-2">Character Data Unavailable</h2>
                <p class="text-slate-400 mb-6 max-w-md">We couldn't retrieve the details for this character. The
                    character might not exist or there was a problem with the API connection.</p>
                <a livewire:navigate href="{{ route('dashboard.game.genshin', ['uid' => $uid]) }}"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Back to Roster
                </a>
            </div>
        @endif
    </div>

    <!-- Back to Top Button -->
    <button x-cloak x-show="showBackToTop" @click="scrollToTop" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-6 right-6 md:bottom-8 md:right-8 z-[100] p-3 rounded-full bg-blue-600 text-white shadow-xl hover:bg-blue-500 hover:scale-110 active:scale-95 transition-all focus:outline-none ring-2 ring-blue-500/50"
        style="display: none;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18">
            </path>
        </svg>
    </button>
</div>