<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new class extends Component {
    //
};
?>

<div class="min-h-screen bg-[#0b0f19] text-slate-300 font-sans selection:bg-blue-500/30">
    <!-- Navbar -->
    <nav
        class="fixed w-full z-50 top-0 transition-all duration-300 backdrop-blur-md bg-[#0b0f19]/80 border-b border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-5l5 2.5-5 2.5z" />
                </svg>
                <span class="text-2xl font-bold text-white tracking-wide">HoyoDash</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#features"
                    class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Features</a>
                <a href="#games"
                    class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Games</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" wire:navigate
                    class="hidden md:block text-sm font-semibold text-slate-300 hover:text-white transition-colors">Log
                    In</a>
                <a href="{{ route('login') }}" wire:navigate
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 inline-block text-center">
                    Start Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section
        class="relative pt-24 pb-20 lg:pt-48 lg:pb-32 overflow-hidden flex items-center justify-center min-h-[90vh]">
        <!-- Background Glow -->
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/20 rounded-full blur-[120px] pointer-events-none">
        </div>
        <div
            class="absolute top-20 right-20 w-[400px] h-[400px] bg-purple-600/20 rounded-full blur-[100px] pointer-events-none">
        </div>
        <div
            class="absolute bottom-20 left-20 w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[100px] pointer-events-none">
        </div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-8 text-center z-10">
            <span
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-medium mb-8">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Unofficial Hoyolab API Client
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-8 leading-tight">
                Your Ultimate Hub for <br class="hidden md:block" />
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-teal-400">All
                    Things HoYoverse</span>
            </h1>
            <p class="mt-6 text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed mb-10">
                Elevate your gaming experience. Access real-time progression, in-depth character builds, and latest
                event updates across Genshin Impact, Honkai: Star Rail, and more—all in one magnificent dashboard.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" wire:navigate
                    class="w-full sm:w-auto px-8 py-4 bg-white text-[#0b0f19] text-base font-bold rounded-2xl hover:bg-slate-100 transition-all shadow-xl shadow-white/10 hover:shadow-white/20 hover:-translate-y-1 transform text-center inline-block">
                    Enter Dashboard
                </a>
                <a href="#features"
                    class="w-full sm:w-auto px-8 py-4 bg-[#1e293b]/50 text-white text-base font-bold rounded-2xl backdrop-blur-md border border-slate-700/50 hover:bg-[#1e293b]/80 transition-all hover:-translate-y-1 transform flex items-center justify-center gap-2">
                    Explore Features
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-[#111827]/50 border-y border-slate-800/50 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Supercharge Your Account</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">Everything you need to dominate the spiral abyss or the
                    simulated universe, conveniently packaged with actionable insights.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="bg-[#1e293b]/40 backdrop-blur-sm p-8 rounded-3xl border border-slate-700/50 hover:border-blue-500/50 hover:bg-[#1e293b]/60 transition-all group">
                    <div
                        class="w-14 h-14 bg-blue-500/20 text-blue-400 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Real-time Tracking</h3>
                    <p class="text-slate-400 leading-relaxed">Never let your Resin or Trailblaze Power cap again. Track
                        your game resources dynamically alongside daily commission statuses.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="bg-[#1e293b]/40 backdrop-blur-sm p-8 rounded-3xl border border-slate-700/50 hover:border-purple-500/50 hover:bg-[#1e293b]/60 transition-all group">
                    <div
                        class="w-14 h-14 bg-purple-500/20 text-purple-400 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Meta Character Builds</h3>
                    <p class="text-slate-400 leading-relaxed">Access Tier 0 recommendations, best-in-slot relics,
                        weapons, and optimal team compositions straight from community experts.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="bg-[#1e293b]/40 backdrop-blur-sm p-8 rounded-3xl border border-slate-700/50 hover:border-teal-500/50 hover:bg-[#1e293b]/60 transition-all group">
                    <div
                        class="w-14 h-14 bg-teal-500/20 text-teal-400 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Live News & Events</h3>
                    <p class="text-slate-400 leading-relaxed">Stay ahead with the latest version updates, web event
                        reminders, and new character banners so you never miss a primogem.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Supported Games -->
    <section id="games" class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <h2 class="text-3xl lg:text-4xl font-bold text-white text-center mb-16">Supported Titles</h2>

            <div class="flex flex-wrap justify-center gap-6 lg:gap-10">
                <!-- Genshin -->
                <div class="w-40 sm:w-48 lg:w-56 group cursor-pointer">
                    <div
                        class="aspect-square rounded-full flex items-center justify-center p-1 bg-gradient-to-br from-emerald-500 to-teal-900 group-hover:scale-105 transition-transform duration-500 shadow-xl shadow-teal-900/40">
                        <div
                            class="w-full h-full bg-[#111827] rounded-full flex items-center justify-center overflow-hidden border-4 border-[#111827]">
                            <img src="https://upload-os-bbs.mihoyo.com/upload/2021/09/02/101683416/b3d5b22b6bb3b68b75e7a9e224e0f45a_5126300185989710385.png?x-oss-process=image/auto-orient,0/interlace,1/format,png"
                                alt="Genshin Impact"
                                class="w-3/4 object-contain group-hover:scale-110 transition-transform duration-500"
                                loading="lazy">
                        </div>
                    </div>
                    <h4 class="mt-6 text-center font-bold text-white group-hover:text-teal-400 transition-colors">
                        Genshin Impact</h4>
                </div>

                <!-- Star Rail -->
                <div class="w-40 sm:w-48 lg:w-56 group cursor-pointer">
                    <div
                        class="aspect-square rounded-full flex items-center justify-center p-1 bg-gradient-to-br from-indigo-500 to-purple-900 group-hover:scale-105 transition-transform duration-500 shadow-xl shadow-purple-900/40">
                        <div
                            class="w-full h-full bg-[#111827] rounded-full flex items-center justify-center overflow-hidden border-4 border-[#111827]">
                            <img src="https://upload-os-bbs.hoyolab.com/upload/2023/03/24/110967397/a5aaee7b243405d4d39f6fa11f440db7_6324888127017649563.png?x-oss-process=image/auto-orient,0/interlace,1/format,png"
                                alt="Honkai: Star Rail"
                                class="w-3/4 object-contain group-hover:scale-110 transition-transform duration-500"
                                loading="lazy">
                        </div>
                    </div>
                    <h4 class="mt-6 text-center font-bold text-white group-hover:text-purple-400 transition-colors">
                        Honkai: Star Rail</h4>
                </div>

                <!-- ZZZ -->
                <div class="w-40 sm:w-48 lg:w-56 group cursor-pointer">
                    <div
                        class="aspect-square rounded-full flex items-center justify-center p-1 bg-gradient-to-br from-green-400 to-lime-800 group-hover:scale-105 transition-transform duration-500 shadow-xl shadow-lime-900/40">
                        <div
                            class="w-full h-full bg-[#111827] rounded-full flex items-center justify-center overflow-hidden border-4 border-[#111827]">
                            <img src="https://upload-os-bbs.hoyolab.com/upload/2023/11/03/110967397/c25efde4fa22dfc904e571424b9fcdd9_3638202999403444654.png?x-oss-process=image/auto-orient,0/interlace,1/format,png"
                                alt="Zenless Zone Zero"
                                class="w-3/4 object-contain group-hover:scale-110 transition-transform duration-500"
                                loading="lazy">
                        </div>
                    </div>
                    <h4 class="mt-6 text-center font-bold text-white group-hover:text-lime-400 transition-colors">
                        Zenless Zone Zero</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer CTA -->
    <footer class="bg-[#111827] border-t border-slate-800 pt-20 pb-10">
        <div class="max-w-4xl mx-auto px-6 text-center mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-6">Ready to enhance your journey?</h2>
            <p class="text-slate-400 mb-8 max-w-xl mx-auto">Join fellow Trailblazers, Travelers, and Proxies who use
                HoyoDash to optimize their daily gameplay.</p>
            <a href="{{ route('login') }}" wire:navigate
                class="inline-block px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl shadow-xl shadow-blue-900/20 hover:shadow-blue-500/40 hover:-translate-y-1 transform transition-all text-center">
                Connect Your Hoyolab Account
            </a>
        </div>
        <div class="border-t border-slate-800/50 mt-10 pt-10 text-center flex flex-col items-center">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-5l5 2.5-5 2.5z" />
                </svg>
                <span class="text-lg font-bold text-white tracking-wide">HoyoDash</span>
            </div>
            <p class="text-slate-500 text-sm max-w-lg">
                HoyoDash is an unofficial dashboard. Not affiliated with, endorsed by, or in any way officially
                connected with Cognosphere PTE. LTD. or HoYoverse.
            </p>
            <p class="text-slate-600 text-xs mt-4">&copy; {{ date('Y') }} HoyoDash. All rights reserved.</p>
        </div>
    </footer>
</div>
