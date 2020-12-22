<div class="branding-logo w-100 bg-white position-sticky fixed-top py-3">
    <img src="{{ asset('images/logo/favicon.png') }}" width="130">
    
    <button type="button" class="btn btn-light d-block d-lg-none" id="btn-close">
        <i data-feather="x"></i>
    </button>
</div>

<div class="list-group w-100">
@if(isset($navigations))
@foreach($navigations as $nav)

    @if($nav['type'] == "single")

    <a href="{{ url('/'.$nav['mode']) }}" id="{{ $nav['mode'] }}" class="list-group-item list-group-item-action {{ $nav['mode'] }}">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-3">
                    <i data-feather="{{ $nav['icon'] }}"></i>
                </span>
                <span class="ellipsis">{{ $nav['name'] }}</span>
            </div>
            <div class="text-right dr-{{ $nav['mode'] }}">
                @if($nav['badge'])
                <span class="badge badge-pill badge-danger">1</span>
                @endif
            </div>
        </div>  
    </a>

    @elseif($nav['type'] == "main")

    <button class="list-group-item list-group-item-action"
    data-toggle="collapse"
    data-target="#{{ __('collapse-').$nav['mode'] }}"
    aria-expanded="false"
    aria-controls="{{ __('collapse-').$nav['mode'] }}">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-3">
                    <i data-feather="{{ $nav['icon'] }}"></i>
                </span>
                <span class="ellipsis">{{ $nav['name'] }}</span>
            </div>
            <span>
                <i data-feather="chevron-right"></i>
            </span>
        </div>                            
    </button>
        
    <div class="collapse list-group w-100 bg-light"
    id="{{ __('collapse-').$nav['mode'] }}">
        @foreach($nav['sub'] as $sub)
        <a href="{{ url('/'.$sub['mode']) }}" id="{{ $sub['mode'] }}" class="list-group-item list-group-item-action {{ $sub['mode'] }}">
            <div class="row">
                <div class="col">
                    <span class="font-text mr-2">
                        {{-- <i class="{{ $sub['icon'] }}"></i> --}}
                        <i data-feather="circle"></i>
                    </span>
                    <span class="ellipsis">{{ $sub['name'] }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

@endforeach
@endif
</div>