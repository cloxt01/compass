<nav class="bg-gray-100 p-4">
    <ul class="flex space-x-4">
        <li>
            <a href="{{ route('dashboard') }}"
               class="{{ $currentRoute == 'dashboard' ? 'font-bold text-blue-600' : '' }}">
               Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('apply') }}"
               class="{{ $currentRoute == 'apply' ? 'font-bold text-blue-600' : '' }}">
               Apply Jobs
            </a>
        </li>
        <li>
            <a href="{{ route('profile') }}"
               class="{{ $currentRoute == 'profile' ? 'font-bold text-blue-600' : '' }}">
               Profile
            </a>
        </li>
    </ul>
</nav>
