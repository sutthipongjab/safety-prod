<ul class="menu bg-primary w-80 md:w-64 lg:w-80 h-full text-base-100 text-base">
    <li class="hidden md:flex hover:bg-primary active:bg-primary focus:bg-primary mb-12">
        <a href="{{ base_url() }}home">
            <div class="flex">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle bg-gray-50">
                    <i class="icofont-hand-thunder text-7xl text-amber-500"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Workspace</h1>
                </div>
            </div>
        </a>
    </li>
    @foreach ($_SESSION['menu'] as $menu)
        @if (isset($menu['submenu']))
            <li class="{{ $menu['menu_class'] }}">
                <details open>
                    <summary class="font-semibold text-base"><span class="text-2xl">{!! $menu['menu_icon'] !!}</span>
                        {{ $menu['menu_name'] }}</summary>
                    <ul class="text-sm list-disc">
                        @foreach ($menu['submenu'] as $sub)
                            <li><a href="{{ base_url() . $sub['menu_link'] }}">{{ $sub['menu_name'] }}</a></li>
                        @endforeach
                    </ul>
                </details>
            </li>
        @else
            <li class="{{ $menu['menu_class'] }}">
                <a href="{{ base_url() . $menu['menu_link'] }}">
                    <span class="text-2xl">{!! $menu['menu_icon'] !!}</span>
                    <span class="font-semibold">{!! $menu['menu_name'] !!}</span>
                </a>
            </li>
        @endif
    @endforeach

    {{-- Profile Section --}}
    <li class="mt-auto">
        <div class="flex">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    <img alt="{{ $_SESSION['user']->SNAME }}" src="{{ $_SESSION['profile-img'] }}" />
                </div>
            </div>
            <div class="block">
                @php
                    $fullname = explode(' ', $_SESSION['user']->SNAME);
                    $name = $fullname[0];
                @endphp
                <div class="text-md font-bold">{{ $name }}</div>
                <div class="text-xs">{{ $_SESSION['user']->SSEC }}</div>
            </div>
            <div class="ms-auto flex">
                <a tabindex="1" role="button" class="btn btn-ghost btn-circle"
                    href="{{ 'https://' . $_SERVER['HTTP_HOST'] . '/itadmindoc/' }}" target="_blank">
                    <i class="icofont-book-alt text-2xl"></i>
                </a>
                <a tabindex="1" role="button" class="btn btn-ghost btn-circle"
                    href="{{ base_url() }}welcome/logout/">
                    <i class="icofont-logout text-2xl"></i>
                </a>
            </div>
    </li>
</ul>
