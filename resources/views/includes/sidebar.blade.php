<nav class="sidebar">
    <div class="logo">
        <a href="/dashboard">
            <img src="{{ asset('images/logo/favicon.png') }}">
        </a>
    </div>
    <ul class="btn-sidebar">
    @if(isset($navigations))
    @foreach($navigations as $nav)

        @if($nav['type'] == "single")

        <li>
            <a href="{{ url('/'.$nav['mode']) }}" id="{{ $nav['mode'] }}" class="{{ $nav['mode'] }}">
                <div>
                    <span class="awesome">
                        <i data-feather="{{ $nav['icon'] }}"></i>
                    </span>
                    <span class="nav-name">{{ $nav['name'] }}</span>
                </div>
            </a>
        </li>

        @elseif($nav['type'] == "main")

        <li>
            <a href="#" id="{{ $nav['mode'] }}" class="nav-list a-{{ $nav['mode'] }}">
                <div>
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

                <li class="sub-nav-list">
                    <a href="{{ url('/'.$sub['mode']) }}" id="{{ $sub['mode'] }}" class="{{ $sub['mode'] }}">
                        <div>
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
</nav>