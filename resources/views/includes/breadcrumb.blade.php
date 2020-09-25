<div class="header">
    <h2>{{ $header }}</h2>
    <div class="horizontal-divider"></div>
    <ul class="breadcrumb">
        @if(count($breadcrumbs['name']) > 1)
        @for($i=0; $i < count($breadcrumbs['name']); $i++)
        <li class="breadcrumb-items">
            <a href="{{ $breadcrumbs['mode'][$i] }}">{{ $breadcrumbs['name'][$i] }}</a>
        </li>
        <li class="breadcrumb-items">
            <!-- <i class="fas fa-angle-double-right"></i> -->
            <div class="chevrons-right">
                <i data-feather="chevrons-right"></i>
            </div>
        </li>
        @endfor
        @endif
    </ul>
</div>