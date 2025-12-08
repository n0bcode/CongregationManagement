<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-stone-800">Notifications</h1>
            <p class="text-slate-600 mt-1">Stay updated with the latest activities.</p>
        </div>
        <button wire:click="markAllAsRead" class="btn-secondary text-sm">
            Mark all as read
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
            <div class="card space-y-1">
                <button wire:click="setFilter('all')" 
                        class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors {{ $filter === 'all' ? 'bg-amber-50 text-amber-900 font-medium' : 'text-slate-700 hover:bg-stone-50' }}">
                    <span>All Notifications</span>
                    <span class="bg-stone-100 text-slate-600 py-0.5 px-2 rounded-full text-xs">
                        {{ auth()->user()->notifications()->count() }}
                    </span>
                </button>
                <button wire:click="setFilter('unread')" 
                        class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors {{ $filter === 'unread' ? 'bg-amber-50 text-amber-900 font-medium' : 'text-slate-700 hover:bg-stone-50' }}">
                    <span>Unread</span>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="bg-amber-100 text-amber-800 py-0.5 px-2 rounded-full text-xs font-bold">
                            {{ auth()->user()->unreadNotifications()->count() }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Notification List -->
        <div class="lg:col-span-3">
            <div class="space-y-4">
                @forelse($notifications as $notification)
                    <div class="card {{ $notification->read_at ? 'bg-white' : 'bg-blue-50 border-blue-200' }} transition-all duration-200 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 mt-1">
                                    @if($notification->read_at)
                                        <div class="w-8 h-8 rounded-full bg-stone-100 flex items-center justify-center text-stone-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-slate-800">
                                        {{ $notification->title ?? 'Notification' }}
                                    </h4>
                                    <p class="text-slate-600 mt-1">
                                        {{ $notification->message ?? '' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-2">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$notification->read_at)
                                    <button wire:click="markAsRead('{{ $notification->id }}')" 
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition-colors"
                                            title="Mark as read">
                                        Mark Read
                                    </button>
                                @endif
                                <button wire:click="deleteNotification('{{ $notification->id }}')" 
                                        class="text-stone-400 hover:text-rose-600 transition-colors p-1 rounded hover:bg-rose-50"
                                        title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-stone-100 mb-4">
                            <svg class="w-8 h-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900">No notifications</h3>
                        <p class="mt-1 text-slate-500">You're all caught up!</p>
                    </div>
                @endforelse

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
