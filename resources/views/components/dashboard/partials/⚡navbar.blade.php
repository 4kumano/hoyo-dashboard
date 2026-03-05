<?php

use Livewire\Component;

new class extends Component {
    // public $user = [
    //     // Using generic data for the overall profile for now.
    //     // We'll update the user name if we find a nickname in the accounts later.
    //     'name' => 'Commander',
    //     'avatar' => 'https://ui-avatars.com/api/?name=Commander&background=2563eb&color=fff',
    //     'hoyolab_id' => 'Active',
    // ];
    public $user = [];
    public function mount()
    {
        $Userinfo = session('hoyolab_user_info', []);
        $this->user['name'] = $Userinfo['nickname'];
        $this->user['avatar'] = $Userinfo['avatar'];
        $this->user['hoyolab_id'] = $Userinfo['uid'];
    }
};
?>

<div>
    <!-- Top Header -->
    <header
        class="h-16 bg-[#111827]/80 backdrop-blur-md border-b border-slate-800 flex items-center justify-between px-8 sticky top-0 z-10">
        <div
            class="flex items-center bg-[#1e293b] rounded-full px-4 py-2 w-96 border border-slate-700/50 hover:border-slate-600 transition-colors">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" placeholder="Search characters, guides, news..."
                class="bg-transparent border-none outline-none text-sm text-white ml-3 w-full placeholder-slate-500">
        </div>
        <div class="flex items-center space-x-5">
            <button class="text-slate-400 hover:text-white transition-colors relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border border-[#111827]"></span>
            </button>
            <div class="flex items-center space-x-3 cursor-pointer group">
                <img src="{{ $user['avatar'] }}" alt="Avatar"
                    class="w-9 h-9 rounded-full ring-2 ring-slate-800 group-hover:ring-blue-500 transition-all">
                <div class="hidden md:block">
                    <p class="text-sm font-semibold text-white leading-tight">{{ $user['name'] }}</p>
                    <p class="text-xs text-slate-400">UID: {{ $user['hoyolab_id'] }}</p>
                </div>
            </div>
        </div>
    </header>

</div>