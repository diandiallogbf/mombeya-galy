@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        @if ($paginator->onFirstPage())
            <span class="flex items-center gap-2 text-sm text-muted/60"><i class="fa-solid fa-chevron-left text-xs"></i> Précédent</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center gap-2 text-sm text-body hover:text-brand"><i class="fa-solid fa-chevron-left text-xs"></i> Précédent</a>
        @endif

        @isset($elements)
            <ul class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="px-2 text-muted">{{ $element }}</li>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li>
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="flex size-9 items-center justify-center rounded-full bg-brand text-sm text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="flex size-9 items-center justify-center rounded-full text-sm text-body transition hover:bg-soft hover:text-brand">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
        @endisset
        <span class="text-sm text-muted sm:hidden">Page {{ $paginator->currentPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center gap-2 text-sm text-body hover:text-brand">Suivant <i class="fa-solid fa-chevron-right text-xs"></i></a>
        @else
            <span class="flex items-center gap-2 text-sm text-muted/60">Suivant <i class="fa-solid fa-chevron-right text-xs"></i></span>
        @endif
    </nav>
@endif
