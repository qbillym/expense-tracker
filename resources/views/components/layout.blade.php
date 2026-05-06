@props(['title' => 'Expense Tracker', 'icon' => 'wallet2', 'header' => null])

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-dark-green">
                <i class="bi bi-{{ $icon }} me-2"></i>
                {{ $title }}
            </h1>
            {!! $header ?? '' !!}
        </div>
    </div>
</div>

{{ $slot }}