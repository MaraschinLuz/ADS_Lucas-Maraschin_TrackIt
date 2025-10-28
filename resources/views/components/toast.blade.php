@php
    $flash = [
        'success' => session('success'),
        'error'   => session('error'),
        'warning' => session('warning'),
        'info'    => session('info'),
        'status'  => session('status'),
    ];
    $map = [
        'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'text' => 'text-emerald-800 dark:text-emerald-200', 'icon' => '#22c55e'],
        'error'   => ['bg' => 'bg-rose-50 dark:bg-rose-900/30',       'text' => 'text-rose-800 dark:text-rose-200',       'icon' => '#e11d48'],
        'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-900/30',     'text' => 'text-amber-800 dark:text-amber-200',     'icon' => '#f59e0b'],
        'info'    => ['bg' => 'bg-blue-50 dark:bg-blue-900/30',       'text' => 'text-blue-800 dark:text-blue-200',       'icon' => '#3b82f6'],
        'status'  => ['bg' => 'bg-blue-50 dark:bg-blue-900/30',       'text' => 'text-blue-800 dark:text-blue-200',       'icon' => '#3b82f6'],
    ];
@endphp

<div class="fixed z-50 top-4 right-4 space-y-2">
@foreach($flash as $type => $msg)
    @if($msg)
    @php($sty = $map[$type])
    <div class="rounded-md shadow px-4 py-3 {{ $sty['bg'] }} {{ $sty['text'] }} flex items-start gap-3" x-data="{ show: true }" x-init="setTimeout(()=>show=false, 3500)" x-show="show" x-transition>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ $sty['icon'] }}" class="w-5 h-5 mt-0.5">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.75 6a.75.75 0 1 0-1.5 0v6a.75.75 0 0 0 1.5 0V8.25ZM12 16.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z" clip-rule="evenodd" />
        </svg>
        <div class="text-sm leading-relaxed">{!! is_array($msg) ? implode('<br>', $msg) : e($msg) !!}</div>
    </div>
    @endif
@endforeach
</div>

