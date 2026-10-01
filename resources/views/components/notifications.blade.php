@props(['notifications', 'showAll' => true])
<section class="notification-inbox" aria-label="Notifications">
    <header class="notification-header">
        <div class="notification-heading">
            <span class="notification-bell" aria-hidden="true"><i class="far fa-bell"></i></span>
            <div><h2>{{ $showAll ? 'Notifications' : 'Your activity' }}</h2><p>Job updates, all in one place.</p></div>
        </div>
        @if($showAll)
        <a href="{{ route('notifications.index') }}" class="notification-view-all" aria-label="View all notifications">View all <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        @else
        <span class="notification-total">{{ $notifications->total() }} total</span>
        @endif
    </header>
    <div class="notification-list">
    @forelse($notifications as $notification)
    <article class="notification-item {{ $notification->is_read ? 'is-read' : 'is-unread' }}">
        <span class="notification-type-icon" aria-hidden="true"><i class="{{ $notification->title === 'New Application Received' ? 'far fa-file-alt' : 'far fa-bell' }}"></i></span>
        <div class="notification-content">
            <div class="notification-title"><h3>{{ $notification->title }}</h3>@if(!$notification->is_read)<span class="notification-dot" aria-label="Unread"></span>@endif</div>
            <p class="notification-message">{{ $notification->message }}</p>
            <div class="notification-meta"><time datetime="{{ $notification->created_at->toIso8601String() }}" title="{{ $notification->created_at->format('d M Y, h:i A') }}">{{ $notification->created_at->diffForHumans() }}</time><span aria-hidden="true">&middot;</span><span>{{ $notification->is_read ? 'Read' : 'Unread' }}</span></div>
        </div>
        @if(!$notification->is_read)
        <form class="notification-action" method="POST" action="{{ route('notifications.read', $notification->id) }}">
            @csrf @method('PATCH')
            <button type="submit" class="notification-read-button" aria-label="Mark {{ $notification->title }} as read" title="Mark as read"><i class="fas fa-check" aria-hidden="true"></i></button>
        </form>
        @endif
    </article>
    @empty
    <div class="notification-empty">
        <span class="notification-empty-icon" aria-hidden="true"><i class="far fa-bell"></i></span>
        <h3>No notifications yet.</h3>
        <p>New applications and status updates will appear here.</p>
    </div>
    @endforelse
    </div>
    @if(!$showAll && $notifications->hasPages())
    <nav class="notification-pagination" aria-label="Notification pages">
        <span>Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}</span>
        <div>
            @if($notifications->onFirstPage())
            <span class="notification-page-button is-disabled" aria-disabled="true">Previous</span>
            @else
            <a class="notification-page-button" href="{{ $notifications->previousPageUrl() }}" rel="prev">Previous</a>
            @endif
            @if($notifications->hasMorePages())
            <a class="notification-page-button" href="{{ $notifications->nextPageUrl() }}" rel="next">Next <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            @else
            <span class="notification-page-button is-disabled" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
    @endif
</section>
