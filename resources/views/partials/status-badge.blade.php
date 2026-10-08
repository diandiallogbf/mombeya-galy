@php
    $classes = match ($status->getColor()) {
        'success' => 'bg-success/15 text-[#1f7a52]',
        'danger' => 'bg-danger/15 text-danger',
        'warning' => 'bg-[#fea569]/20 text-[#b45d12]',
        'info' => 'bg-azure/15 text-ocean',
        default => 'bg-brand-light text-brand',
    };
@endphp
<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $classes }}">{{ $status->getLabel() }}</span>
