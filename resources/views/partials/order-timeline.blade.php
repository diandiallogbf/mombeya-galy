@use('App\Enums\OrderStatus')
@php
    $steps = OrderStatus::timeline();
    $currentIndex = array_search($order->status, $steps, true);
    $icons = ['fa-receipt', 'fa-circle-check', 'fa-scissors', 'fa-truck-fast', 'fa-house-circle-check'];
@endphp

@if ($order->status === OrderStatus::Cancelled)
    <div class="rounded-lg bg-danger/10 px-4 py-3 text-center text-danger"><i class="fa-solid fa-ban mr-1"></i> Cette commande a été annulée.</div>
@else
    <ol class="grid grid-cols-5 gap-1 text-center">
        @foreach ($steps as $i => $step)
            @php($done = $currentIndex !== false && $i <= $currentIndex)
            <li class="relative">
                @if (! $loop->first)
                    <span @class(['absolute right-1/2 top-5 h-1 w-full -translate-y-1/2', 'bg-brand' => $done, 'bg-line' => ! $done])></span>
                @endif
                <span @class(['relative mx-auto flex size-10 items-center justify-center rounded-full text-sm', 'bg-brand text-white' => $done, 'bg-soft text-muted' => ! $done])>
                    <i class="fa-solid {{ $icons[$i] }}"></i>
                </span>
                <span @class(['mt-2 block text-[.7rem] sm:text-xs', 'font-medium text-ink' => $done, 'text-muted' => ! $done])>{{ $step->getLabel() }}</span>
            </li>
        @endforeach
    </ol>
@endif
