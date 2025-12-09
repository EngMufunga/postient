@if ($items->hasPages())
    <div class="ajax-pagination pagination__wrapper">
        {{ $items->links() }}
    </div>
@endif
