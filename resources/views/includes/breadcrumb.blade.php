<div class="header">
    <h4>{{ $header }}</h4>
    <div class="horizontal-divider"></div>

    <ul class="breadcrumb-section">
        @if(count($breadcrumbs['name']) > 1)
            @for($i = 0; $i < count($breadcrumbs['name']); $i++)

            <li class="breadcrumb-items">
                <a href="{{ $breadcrumbs['mode'][$i] }}">{{ $breadcrumbs['name'][$i] }}</a>
            </li>

            <li class="breadcrumb-items">
                <div class="chevrons-right">
                    <i data-feather="chevrons-right"></i>
                </div>
            </li>

            @endfor
        @endif
    </ul>
</div>