@php
$Cmenu = (isset($_SESSION['menu']) && is_array($_SESSION['menu']))
? count($_SESSION['menu'])
: 0;

@endphp
<div class="relative">
    <div class="cmenu" count="{{$Cmenu}}"></div>
    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-gray-700 bg-opacity-50 z-40 hidden "></div>

    {{-- Side bar --}}
    <aside id="sidebar"
        class="fixed top-0 left-0 z-40 sm:w-64 lg:w-80 h-screen pt-20 transition-all -translate-x-full bg-cOrange border-r border-gray-200 md:translate-x-0 max-sm:w-lvw"
        aria-label="Sidebar">
        <div class="flex flex-col h-full pb-4 overflow-y-auto  ">
            <ul class="menu" id="mainMenu">
                @if ($Cmenu != 0)
                @foreach ($_SESSION['menu'] as $menu)
                {{-- {{$menu['menu_tname']}} --}}
                @if (isset($menu['submenu']))
                <li class="mainmenu {{ $menu['menu_class'] }} text-white text-base">
                    <details class="collapse collapse-arrow">
                        <summary class="collapse-title font-semibold text-base">
                            <span class="text-2xl">{!! $menu['menu_icon'] !!}</span>
                            <span>{{ $menu['menu_tname'] }}</span>
                        </summary>
                        <ul class="text-sm list-disc">
                            @foreach ($menu['submenu'] as $sub)
                            <li class="{{ $sub['menu_class'] }}"><a href="{{ base_url() . $sub['menu_link'] }}"
                                    class="menu-name">{{ $sub['menu_tname'] }}</a></li>
                            @endforeach
                        </ul>
                    </details>
                </li>
                @else
                <li class="{{ $menu['menu_class'] }} menu-name text-white text-base">
                    <a href="{{ base_url() . $menu['menu_link'] }}">
                        <span class="text-3xl">{!! $menu['menu_icon'] !!}</span>
                        <span class="font-semibold menu-name">{{$menu['menu_tname']}}</span>
                    </a>
                </li>
                @endif
                @endforeach
                @endif
            </ul>
            {{-- Logout --}}
            <div class="mt-auto m-5 sm:hidden">
                <hr class="border-gray-300 my-4">
                <div class="flex gap-3 items-center">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full ">
                            <img src="{{ $_SESSION['profile-img'] }}" />
                        </div>
                    </div>
                    <p class="text-ellipsis overflow-hidden text-white">{{
                        substr($_SESSION['user']->SNAME,0,strpos($_SESSION['user']->SNAME,' '))}}</p>
                    <a href="{{ base_url() }}authen/logout/" class="ml-auto text-2xl text-white"><i
                            class="icofont-logout "></i></a>
                </div>
            </div>
        </div>
    </aside>
</div>