<div class="header-content position-sticky sticky-top mx-4 mt-4 mb-1">
    <nav class="px-4 py-3">
        <div class="row no-gutters">
            <button class="btn btn-light" type="button">
                <!-- <i data-feather="skip-back"></i> -->
                <i data-feather="menu"></i>
            </button>
        </div>
        <div class="user-profile">
            <div class="btn-group">
                <button class="btn btn-primary btn-dropdown rounded-circle" data-toggle="dropdown" style="background-image: url('{{ (Auth::user()->profile_image) ? asset('images/user_profiles/'.Auth::user()->username.Auth::user()->id.'/'.Auth::user()->profile_image.'') : asset('images/user_profiles/TIMEKEEPING_Robertson_ Morales.jpg') }}');"></button>
              <div class="dropdown-menu dropdown-menu-right mt-2 py-2">

                <div class="dropdown-item-text d-flex align-items-center py-2">
                    <span class="profile-image rounded-circle mr-2" style="background-image: url('{{ (Auth::user()->profile_image) ? asset('images/user_profiles/'.Auth::user()->username.Auth::user()->id.'/'.Auth::user()->profile_image.'') : asset('images/user_profiles/TIMEKEEPING_Robertson_ Morales.jpg') }}');"></span>

                    <span class="profile-info">
                        <span id="user-name">{{ ucfirst(Crypt::decryptString(Auth::user()->first_name)). ' '.ucfirst(Crypt::decryptString(Auth::user()->last_name)) }}</span>
                        <span id="user-email">{{ Crypt::decryptString(Auth::user()->email) }}</span>
                    </span>
                </div>

                <div class="dropdown-divider"></div>

                <!-- <button class="dropdown-item d-flex justify-content-between py-2" type="button">
                    <span class="item-text">Set Status</span>
                    <span class="item-icon"><i data-feather="chevron-right"></i></span>
                </button> -->

                <button onclick="window.location.href='{{ route('account_settings.index') }}'" class="dropdown-item py-2" type="button">Profile & Account</button>

                <div class="dropdown-divider"></div>
                
                <button onclick="document.getElementById('logout-form').submit();" class="dropdown-item py-2" type="button">
                    <span><i data-feather="log-out"></i></span>
                    <span>Logout</span>
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              </div>
            </div>
        </div>
    </nav>
</div>