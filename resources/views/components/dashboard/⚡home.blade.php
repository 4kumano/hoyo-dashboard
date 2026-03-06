<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.dashboard')] class extends Component {
    public $user = [
        // Using generic data for the overall profile for now.
        // We'll update the user name if we find a nickname in the accounts later.
        'name' => 'Commander',
        'avatar' => 'https://ui-avatars.com/api/?name=Commander&background=2563eb&color=fff',
        'hoyolab_id' => 'Active',
    ];
    public $games = [];
    public $news = [];

    public $builds = [['name' => 'Acheron', 'game' => 'Honkai: Star Rail', 'tier' => 'T0', 'role' => 'Main DPS', 'path' => 'Nihility', 'bg' => 'bg-purple-950/50'], ['name' => 'Arlecchino', 'game' => 'Genshin Impact', 'tier' => 'T0', 'role' => 'Main DPS', 'path' => 'Pyro', 'bg' => 'bg-red-950/50'], ['name' => 'Ellen Joe', 'game' => 'Zenless Zone Zero', 'tier' => 'S', 'role' => 'Attack', 'path' => 'Ice', 'bg' => 'bg-blue-950/50']];

    public function mount()
    {
        $savedAccounts = session('hoyolab_accounts', []);
        $hoyolabService = app(\App\Services\HoyolabService::class);

        $mappedGames = [];
        $mappedNews = [];

        foreach ($savedAccounts as $acc) {
            $biz = $acc['game_biz'] ?? '';
            $gameName = 'Unknown Game';
            $color = 'from-slate-700 to-slate-900';
            $iconColor = 'text-slate-400';

            // Map Styling & Nama berdasar game_biz
            if (str_contains($biz, 'hk4e')) {
                $gameName = 'Genshin Impact';
                $color = 'from-emerald-900 to-teal-900';
                $iconColor = 'text-teal-400';
            } elseif (str_contains($biz, 'hkrpg')) {
                $gameName = 'Honkai: Star Rail';
                $color = 'from-indigo-900 to-purple-900';
                $iconColor = 'text-indigo-400';
            } elseif (str_contains($biz, 'bh3')) {
                $gameName = 'Honkai Impact 3rd';
                $color = 'from-blue-900 to-sky-900';
                $iconColor = 'text-blue-400';
            } elseif (str_contains($biz, 'nap')) {
                $gameName = 'Zenless Zone Zero';
                $color = 'from-green-900 to-lime-900';
                $iconColor = 'text-lime-400';
            }

            $mappedGames[] = [
                'name' => $gameName,
                'uid' => $acc['game_uid'],
                'server' => $acc['region_name'],
                'nickname' => $acc['nickname'],
                'level' => $acc['level'],
                'color' => $color,
                'icon_color' => $iconColor,
                'icon_url' => $hoyolabService->getIcon($biz),
                // Kita belum setup daily task API jadi sementara Resin dibuat null/Loading state
                'resin' => '?',
                'max_resin' => '?',
            ];

            // Setup Dummy News Structure per Game
            // Nantinya di HoyolabService diisikan array berita asli
            $rawRegion = $acc['region'] ?? '';
            $rawUid = $acc['game_uid'] ?? 0;
            $rawLevel = $acc['level'] ?? 1;

            $gameNewsList = $hoyolabService->getNews($biz, $rawUid, $rawRegion, $rawLevel);

            if (!empty($gameNewsList)) {
                // Batasi hanya 3 berita per game
                $mappedNews[$gameName] = array_slice($gameNewsList, 0, 3);
            }
        }

        // Kalau ada session, gunakan itu. Kalau tidak ada, kosongkan.
        $this->games = $mappedGames;
        $this->news = $mappedNews;
        // dd($mappedNews);
    }
};
?>


<!-- Main Body -->
<div class="flex-1 overflow-y-auto p-5 md:p-8 lg:p-10 space-y-10" x-data="{
    init() {
        let cookie = localStorage.getItem('hoyolab_cookie');
        let accounts = localStorage.getItem('hoyolab_accounts');

        if (!cookie) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        // Trigger data load sending local storage purely to Backend Livewire
        {{-- $wire.loadData(cookie, accounts); --}}
    }
}">

    <!-- Hero / Daily Summary Banner -->
    <section class="relative rounded-2xl overflow-hidden shadow-2xl shadow-blue-900/10">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-[#111827] z-0"></div>
        <!-- Decorative background elements -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl z-0"></div>
        <div class="absolute bottom-0 right-10 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl z-0"></div>

        <div
            class="relative z-10 p-8 lg:p-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 px-10">
            <div x-data="{
                userName: '{{ $user['name'] }}',
                init() {
                    // Listener if Livewire updates the data
                    Livewire.on('update-user-info', (data) => {
                        let payload = Array.isArray(data) ? data[0] : data;
                        if (payload && payload.userInfo) {
                            localStorage.setItem('hoyolab_user_info', JSON.stringify(payload.userInfo));
                            if (payload.userInfo.nickname) {
                                this.userName = payload.userInfo.nickname;
                            }
                        }
                    });
            
                    // Format fallback dr LocalStorage jika halaman baru diload cepat
                    let savedInfo = localStorage.getItem('hoyolab_user_info');
                    if (savedInfo) {
                        try {
                            let parsed = JSON.parse(savedInfo);
                            if (parsed.nickname) this.userName = parsed.nickname;
                        } catch (e) {}
                    }
                }
            }">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-2">Welcome Back, <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-300"
                        x-text="userName"></span>
                </h1>
                <p class="text-slate-300 text-lg max-w-xl">Your daily commissions and expeditions await. Check
                    your real-time resins and trailblaze power below.</p>
            </div>
            <div class="flex gap-4">
                <button
                    class="px-6 py-3 bg-white text-blue-900 font-semibold rounded-xl hover:bg-slate-100 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform">
                    Daily Check-In
                </button>
                <button
                    class="px-6 py-3 bg-white/10 text-white font-semibold rounded-xl backdrop-blur-md border border-white/20 hover:bg-white/20 transition-all">
                    View Battle Records
                </button>
            </div>
        </div>
    </section>

    <!-- Real-time Game Data Grid -->
    <section name="games">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Live Game Status</h2>
            <a href="#" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">Manage
                Games
                &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($games as $game)
                <div
                    class="bg-[#1e293b]/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 hover:border-slate-500/50 transition-all shadow-lg hover:shadow-xl group relative overflow-hidden">
                    <!-- BG Gradient decorative -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br {{ $game['color'] }} opacity-0 group-hover:opacity-10 transition-opacity duration-500">
                    </div>

                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $game['color'] }} flex items-center justify-center p-0.5 shadow-md overflow-hidden">
                                <img src="{{ $game['icon_url'] }}" alt="{{ $game['name'] }}"
                                    class="w-full h-full object-cover rounded-[10px]" loading="lazy">
                            </div>
                            <div>
                                <h3 class="font-bold text-white leading-tight">{{ $game['name'] }}</h3>
                                <p class="text-xs text-slate-400">UID: {{ $game['uid'] }} •
                                    {{ $game['server'] }}
                                </p>
                            </div>
                        </div>
                        <span class="flex h-3 w-3 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $game['resin'] >= $game['max_resin'] ? 'bg-red-400' : 'bg-green-400' }} opacity-75"></span>
                            <span
                                class="relative inline-flex rounded-full h-3 w-3 {{ $game['resin'] >= $game['max_resin'] ? 'bg-red-500' : 'bg-green-500' }}"></span>
                        </span>
                    </div>

                    <div class="space-y-4 relative z-10">
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-slate-300 font-medium">Energy / Resin</span>
                                <span
                                    class="font-bold {{ $game['resin'] !== '?' && $game['max_resin'] !== '?' && $game['resin'] >= $game['max_resin'] ? 'text-red-400' : 'text-white' }}">{{ $game['resin'] }}
                                    <span class="text-slate-500 font-normal">/
                                        {{ $game['max_resin'] }}</span></span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                @if ($game['resin'] !== '?' && $game['max_resin'] !== '?')
                                    <div class="{{ $game['resin'] >= $game['max_resin'] ? 'bg-red-500' : 'bg-gradient-to-r from-blue-500 to-teal-400' }} h-2.5 rounded-full"
                                        style="width: {{ ($game['resin'] / $game['max_resin']) * 100 }}%">
                                    </div>
                                @else
                                    <div class="bg-slate-700 h-2.5 rounded-full w-full animate-pulse"></div>
                                @endif
                            </div>
                            @if ($game['resin'] !== '?' && $game['max_resin'] !== '?' && $game['resin'] >= $game['max_resin'])
                                <p class="text-xs text-red-400 mt-2 font-medium">Energy is fully capped!</p>
                            @elseif($game['resin'] === '?')
                                <p class="text-xs text-slate-500 mt-2">Connecting to game server...</p>
                            @else
                                <p class="text-xs text-slate-400 mt-2">Recovers fully in ~4h 20m</p>
                            @endif
                        </div>

                        <div class="flex justify-between items-center bg-slate-800/50 rounded-lg p-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-slate-300">Daily Tasks</span>
                            </div>
                            <span class="text-sm font-bold text-white">4 <span class="text-slate-500">/
                                    4</span> <span class="text-green-400 ml-1">✓</span></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Bottom Sections Grid: Builds & News -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-10">
        <!-- Recommendations / Builds -->
        <section name="builds">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-500"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z">
                        </path>
                    </svg> Trending Builds</h2>
                <a href="#" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">View
                    All
                    &rarr;</a>
            </div>
            <div class="space-y-4">
                @foreach ($builds as $build)
                    <div
                        class="{{ $build['bg'] }} border border-slate-700/50 rounded-xl p-4 flex items-center justify-between hover:scale-[1.02] transition-transform cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-slate-800 rounded-lg shrink-0 border border-slate-600 shadow-inner">
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-bold text-white">{{ $build['name'] }}</h4>
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold bg-yellow-500/20 text-yellow-500 rounded-full border border-yellow-500/30">{{ $build['tier'] }}
                                        Tier</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">{{ $build['game'] }} •
                                    {{ $build['path'] }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                class="bg-blue-500/20 text-blue-400 text-xs px-3 py-1 rounded-lg font-medium">{{ $build['role'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Latest News / Events -->
        <section name="news">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white flex items-center"><svg class="w-5 h-5 mr-2 text-blue-500"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg> Game Announcements</h2>
            </div>
            <div class="space-y-4" x-data="{ expanded: '{{ count($news) > 0 ? array_key_first($news) : '' }}' }">
                @if (count($news) > 0)
                    @foreach ($news as $gameName => $gameNews)
                        <div class="border border-slate-700/50 rounded-xl overflow-hidden bg-[#1e293b]/30">
                            <button
                                @click="expanded = expanded === '{{ $gameName }}' ? null : '{{ $gameName }}'"
                                class="w-full flex items-center justify-between p-4 bg-[#111827]/80 hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <span class="font-bold text-white">{{ $gameName }}</span>
                                    <span
                                        class="text-xs bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded-full">{{ count($gameNews) }}</span>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                    :class="expanded === '{{ $gameName }}' ? 'rotate-180 text-blue-400' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>

                            <div x-show="expanded === '{{ $gameName }}'" style="display: none;"
                                class="p-4 grid grid-cols-1 gap-4 bg-[#0b0f19] border-t border-slate-700/50">
                                @foreach ($gameNews as $n)
                                    <div
                                        class="bg-gradient-to-r {{ $n['bg'] }} to-[#1e293b]/50 border border-slate-700/50 p-4 rounded-xl flex flex-col hover:border-slate-500/80 transition-all cursor-pointer group">

                                        @if (!empty($n['banner']))
                                            <div class="mb-3 rounded-lg overflow-hidden shrink-0 w-full h-32 md:h-40">
                                                <img src="{{ $n['banner'] }}" alt="{{ $n['title'] }}"
                                                    class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300"
                                                    loading="lazy">
                                            </div>
                                        @endif

                                        <div class="flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="flex justify-between items-start mb-2">
                                                    <span
                                                        class="text-[10px] sm:text-xs font-bold {{ $n['tag_color'] }} uppercase tracking-widest">{{ $n['tag'] }}</span>
                                                    <span
                                                        class="text-xs text-slate-500 whitespace-nowrap ml-2">{{ $n['time'] }}</span>
                                                </div>
                                                <h3
                                                    class="text-white font-bold text-base sm:text-lg mb-2 group-hover:text-blue-300 transition-colors leading-tight">
                                                    {{ $n['title'] }}
                                                </h3>
                                                <p
                                                    class="text-xs sm:text-sm text-slate-400 line-clamp-2 leading-relaxed">
                                                    {{ $n['desc'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6 text-slate-500">
                        Tidak ada pengumuman hari ini.
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
