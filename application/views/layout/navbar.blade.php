<nav class="navbar bg-base-100 shadow-xl fixed top-0 z-50 h-16 W-lvw">
    <div class="navbar">
        {{-- <label for="my-drawer-2" class="btn btn-ghost btn-circle drawer-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </label> --}}
        <button id="togglebar" data-drawer-target="sidebar" data-drawer-toggle="sidebar" aria-controls="sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden mr-2 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
               <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
         </button>
         
        <a href="{{ base_url() }}" class="flex items-center hover:bg-gray-200 rounded p-1">
            <div tabindex="0" role="button" class="btn  btn-circle bg-black">
                <img src="{{ base_url() }}assets/images/safety_icon.png" alt="">
            </div>
            <div class="ms-2">
                <h1 class="text-2xl font-bold">Safety system</h1>
            </div>
        </a>
    </div>
    <div class="navbar-start max-sm:hidden">
    </div>
    <div class="navbar-end max-sm:hidden">
        <div class="flex items-center gap-3">
            
            {{-- <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="badge badge-sm indicator-item">8</span>
                    </div>
                </div>
                <div tabindex="0" class="card card-compact dropdown-content bg-base-100 z-[1] mt-3 w-52 shadow">
                    <div class="card-body">
                        <span class="text-lg font-bold">8 Items</span>
                        <span class="text-info">Subtotal: $999</span>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-block">View cart</button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <p class="text-ellipsis overflow-hidden">{{substr($_SESSION['user']->SNAME,0,strpos($_SESSION['user']->SNAME,' '))}}</p>
            <div  class="dropdown dropdown-end">
                <div  tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full shadow-lg border-solid border-2 border-cOrange">
                        <img  src="{{ $_SESSION['profile-img'] }}" />
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-20 mt-3 w-52 p-2 shadow">
                    {{-- <li>
                        <a class="justify-between">
                            Profile
                            <span class="badge">New</span>
                        </a>
                    </li>--}}
                    <li><a href="{{ base_url() }}authen/logout/">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
