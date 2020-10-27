<div class="d-flex flex-column px-4 py-3">
    @if(count($breadcrumbs['name']) > 1)
    <div class="header mb-1">
        <ul class="breadcrumb-section">
            
                @for($i = 0; $i < count($breadcrumbs['name']); $i++)

                <li class="breadcrumb-items">
                    <a href="{{ $breadcrumbs['mode'][$i] }}">{{ $breadcrumbs['name'][$i] }}</a>
                </li>

                <li class="breadcrumb-items">
                    <div class="chevrons-right">
                        <i data-feather="chevron-right"></i>
                    </div>
                </li>

                @endfor
        </ul>
    </div>
    @endif
    <div class="header">
        <h3 class="mb-0">{{ $header }}</h3>
    </div>
</div>