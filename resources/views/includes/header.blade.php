<div class="header-content position-sticky sticky-top">
    <nav>
        <div class="date-time">
            <h5 id="date-time"></h5>
        </div>
        <div class="user-profile">
            <ul class="notifications">
                <li>
                    <a href="#notification" title="notifications">
                        <span>
                            <i data-feather="bell"></i>
                        </span>
                        <span class="new-notifications">
                            <i class="fas fa-circle"></i>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#message" title="messages">
                        <span>
                            <i data-feather="message-square"></i>
                        </span>
                        <!-- <span class="new-notifications">
                            <i class="fas fa-circle"></i>
                        </span> -->
                    </a>
                </li>
            </ul>

            <a href="#" class="btn-dropdown" id="btn-dropdown">
                <div class="username">
                    <span class="image-wrapper" style="background-image: url('{{ (Auth::user()->profile_image) ? asset('images/user_profiles/'.Auth::user()->username.Auth::user()->id.'/'.Auth::user()->profile_image.'') : asset('images/user_profiles/avatar.svg') }}');">
                    </span>
                    
                    <div class="user-detail">
                        <span class="user">{{ ucfirst(Crypt::decryptString(Auth::user()->first_name)). ' '.ucfirst(Crypt::decryptString(Auth::user()->last_name)) }}</span>
                        <span class="email">{{ Crypt::decryptString(Auth::user()->email) }}</span>
                    </div>                    
                </div>
            </a>

            <div class="admin-dropdown" id="admin-dropdown">
                <ul>
                    <li>
                        <a href="{{ route('my_account.index') }}" id="my_account">
                            <i data-feather="user"></i>
                            <span>My Account</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="btn-logout">
                            <i data-feather="log-out"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>