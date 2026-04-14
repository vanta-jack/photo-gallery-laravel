@props([
    'paginator' => null,
    'showSummary' => true,
    'emptyTitle' => 'No results found.',
    'emptyDescription' => null,
])

@php
$isLengthAwarePaginator = $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
$isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator;
$hasPaginator = $isLengthAwarePaginator || $isPaginator;
$hasResults = $hasPaginator ? $paginator->count() > 0 : false;

$from = $hasPaginator && method_exists($paginator, 'firstItem') ? $paginator->firstItem() : null;
$to = $hasPaginator && method_exists($paginator, 'lastItem') ? $paginator->lastItem() : null;
$total = $isLengthAwarePaginator ? $paginator->total() : null;
@endphp

<div {{ $attributes->class(['space-y-4']) }}>
    {{ $slot }}

    @if($hasPaginator && $paginator->hasPages())
        <div class="flex flex-col gap-3 border-t border-border pt-4 sm:flex-row sm:items-center sm:justify-between">
            @if($showSummary && ! is_null($from) && ! is_null($to))
                <p class="text-sm text-muted-foreground">
                    Showing {{ $from }} to {{ $to }}
                    @if(! is_null($total))
                        of {{ $total }}
                    @endif
                </p>
            @endif

            <div class="max-w-full overflow-x-auto">
                @isset($links)
                    {{ $links }}
                @else
                    {{ $paginator->links() }}
                @endisset
            </div>
        </div>
    @elseif($hasPaginator && ! $hasResults)
        <x-ui.empty-state :title="$emptyTitle" :description="$emptyDescription" />
    @endif
</div>
