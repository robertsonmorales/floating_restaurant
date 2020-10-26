<div class="col-3 mr-4 list-options">
    <ul id="list-actions">
        <li>
            <a href="/account_settings">
                <i data-feather="user"></i>
                <span>Basic Information</span>
            </a>
        </li>
        <li>
            <a href="{{ route('account_settings.email') }}">
                <i data-feather="at-sign"></i>
                <span>Email</span>
            </a>
        </li>
        <li>
            <a href="{{ route('account_settings.password') }}">
                <i data-feather="lock"></i>
                <span>Password</span>
            </a>
        </li>
        <li>
            <a href="#preferences">
                <i data-feather="settings"></i>
                <span>Preferences</span>
            </a>
        </li>
        <li>
            <a href="#preferences">
                <i data-feather="monitor"></i>
                <span>Recent Devices</span>
            </a>
        </li>
        <li>
            <a href="{{ route('account_settings.delete_account') }}">
                <i data-feather="trash"></i>
                <span>Delete Account</span>
            </a>
        </li>
    </ul>
</div>