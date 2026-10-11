@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4 text-[.72rem] tracking-[.16em] uppercase">
        @if ($paginator->onFirstPage())
            <span class="flex items-center gap-2 text-muted/60"><i class="fa-solid fa-arrow-left"></i> Précédent</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center gap-2 text-ink hover:text-brand"><i class="fa-solid fa-arrow-left"></i> Précédent</a>
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
                                    <span aria-current="page" class="flex size-10 items-center justify-center border border-ink font-display text-base text-ink">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="flex size-10 items-center justify-center font-display text-base text-muted transition hover:text-ink">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
        @endisset
        <span class="text-muted sm:hidden">Page {{ $paginator->currentPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center gap-2 text-ink hover:text-brand">Suivant <i class="fa-solid fa-arrow-right"></i></a>
        @else
            <span class="flex items-center gap-2 text-muted/60">Suivant <i class="fa-solid fa-arrow-right"></i></span>
        @endif
    </nav>
@endif
