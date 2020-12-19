<div class="logo">
    <img src="{{ asset('images/logo/favicon.png') }}">
    <button type="button" class="btn btn-sm d-block d-lg-none" id="btn-close">
        <i data-feather="x"></i>
    </button>
</div>
<ul class="btn-sidebar">
@if(isset($navigations))
@foreach($navigations as $nav)

    @if($nav['type'] == "single")

    <li>
        <a href="{{ url('/'.$nav['mode']) }}" id="{{ $nav['mode'] }}" class="btn {{ $nav['mode'] }}">
            <div class="nav-name">
                <span class="awesome">
                    <i data-feather="{{ $nav['icon'] }}"></i>
                </span>
                <span class="nav-name">{{ $nav['name'] }}</span>
                @if($nav['badge'])
                <span class="badge badge-pill badge-danger">1</span>
                @endif
            </div>
        </a>
    </li>

    @elseif($nav['type'] == "main")

    <li>
        <a href="#" id="{{ $nav['mode'] }}" class="btn nav-list a-{{ $nav['mode'] }}">
            <div class="nav-name">
                <span class="awesome">
                    <i data-feather="{{ $nav['icon'] }}"></i>
                </span>
                <span class="nav-name">{{ $nav['name'] }}</span>
            </div>
            <div class="dropdown dr-{{ $nav['mode'] }}">
                <i data-feather="chevron-down"></i>
            </div>
        </a>
        
        <ul class="sub-nav {{ $nav['mode'] }}">
            @foreach($nav['sub'] as $sub)

            <li class="sub-nav-list ml-3">
                <a href="{{ url('/'.$sub['mode']) }}" id="{{ $sub['mode'] }}" class="btn {{ $sub['mode'] }}">
                    <div class="nav-name">
                        <span class="awesome2">
                            <i class="{{ $sub['icon'] }}"></i>
                        </span>
                        <span>{{ $sub['name'] }}</span>
                    </div>
                </a>
            </li>

            @endforeach
        </ul>
    </li>

    @endif

@endforeach
@endif
</ul>