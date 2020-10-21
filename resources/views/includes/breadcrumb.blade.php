<div class="header mt-4 mb-2">
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
<div class="header mb-4">
    <h4>{{ $header }}</h4>
    <div class="mx-2"></div>
</div>