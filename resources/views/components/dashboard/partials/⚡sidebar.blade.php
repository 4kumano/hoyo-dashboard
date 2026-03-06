<?php

use Livewire\Component;

new class extends Component {
    public $games = [];

    public function mount()
    {
        // Ambil data akun dari memori session yang disimpan saat Login
        $savedAccounts = session('hoyolab_accounts', []);
        $hoyolabService = app(\App\Services\HoyolabService::class);

        $mappedGames = [];
        $mappedNews = [];

        foreach ($savedAccounts as $acc) {
            $biz = $acc['game_biz'] ?? '';
            $gameName = 'Unknown Game';
            $color = 'from-slate-700 to-slate-900';
            $iconColor = 'text-slate-400';

            $uid = $acc['game_uid'] ?? '';

            // Map Styling, Nama, dan Route berdasar game_biz
            if (str_contains($biz, 'hk4e')) {
                $gameName = 'Genshin Impact';
                $color = 'from-emerald-900 to-teal-900';
                $iconColor = 'text-teal-400';
                $route = route('dashboard.game.genshin', ['uid' => $uid]);
            } elseif (str_contains($biz, 'hkrpg')) {
                $gameName = 'Honkai: Star Rail';
                $color = 'from-indigo-900 to-purple-900';
                $iconColor = 'text-indigo-400';
                $route = route('dashboard.game.startrail', ['uid' => $uid]);
            } elseif (str_contains($biz, 'bh3')) {
                $gameName = 'Honkai Impact 3rd';
                $color = 'from-blue-900 to-sky-900';
                $iconColor = 'text-blue-400';
                $route = route('dashboard.game.honkai', ['uid' => $uid]);
            } elseif (str_contains($biz, 'nap')) {
                $gameName = 'Zenless Zone Zero';
                $color = 'from-green-900 to-lime-900';
                $iconColor = 'text-lime-400';
                $route = route('dashboard.game.zzz', ['uid' => $uid]);
            } else {
                $route = '#';
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
                'route' => $route,
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
    }
};
?>
<div>
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="sidebarOpen = false"
        x-transition.opacity style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="absolute z-50 w-64 bg-[#111827] border-r border-slate-800 flex flex-col h-full transition-transform duration-300 md:relative md:translate-x-0">
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-5l5 2.5-5 2.5z" />
                </svg>
                <span class="text-xl font-bold text-white tracking-wide">HoyoDash</span>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a livewire:navigate href="{{ route('dashboard.home') }}"
                class="flex items-center space-x-3 px-3 py-2.5 bg-blue-600/10 text-blue-400 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Games</p>
            </div>
            @foreach ($games as $game)
                <a livewire:navigate href="{{ $game['route'] }}"
                    class="flex items-center space-x-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group">
                    <div
                        class="w-5 h-5 rounded-full overflow-hidden bg-slate-800 border border-slate-700 group-hover:border-white transition-colors">
                        {{-- Using a generic placeholder icon if no img, else showing bg color --}}
                        <div class="w-full h-full bg-gradient-to-br {{ $game['color'] }}"></div>
                    </div>
                    <span class="font-medium text-sm">{{ $game['name'] }}</span>
                </a>
            @endforeach
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Discover</p>
            </div>
            <a href="#"
                class="flex items-center space-x-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                    </path>
                </svg>
                <span class="font-medium">News & Events</span>
            </a>
            <a href="#"
                class="flex items-center space-x-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z">
                    </path>
                </svg>
                <span class="font-medium">Character Builds</span>
            </a>
            <a href="#"
                class="flex items-center space-x-3 px-3 py-2.5 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                    </path>
                </svg>
                <span class="font-medium">Interactive Map</span>
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <a href="#" class="flex items-center space-x-3 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium">Settings</span>
            </a>
        </div>
    </aside>

</div>
