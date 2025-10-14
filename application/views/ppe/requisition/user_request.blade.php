@extends('layout/template')
@section('contents')
<style>
    .form-group {
        margin-bottom: 15px;
    }

    .table {
        margin-top: 20px;
    }
</style>


<!-- <form action="" id="formadd">
    <div class="w-full mx-auto bg-white shadow-lg rounded-lg p-6">
        <div class="card bg-base-200 p-2 mb-6 ">
            <div class="card-body p-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Input By:</span>
                        </label>
                        <input type="text" class="input input-bordered" id="key" placeholder="กรุณากรอกรหัสพนักงาน" value="{{$_SESSION['user']->SEMPNO}}" readonly>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Requested By:</span>
                        </label>
                        <input type="text" class="input input-bordered" id="req" placeholder="กรุณากรอกรหัสพนักงาน" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-primary" id="addRow">เพิ่มรายการเบิก</button>
        <div class="overflow-x-auto">

            <table class="table w-full" id="myTable">
                <thead>
                    <tr>
                        <th>รุ่น</th>
                        <th>ไซส์</th>
                        <th>สต็อกคงเหลือ</th>
                        <th>จำนวนที่ต้องการเบิก</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <div class="form-mst" data-NFRMNO="{{$fmst[0]->NNO}}" data-VORGNO="{{$fmst[0]->VORGNO}}" data-CYEAR="{{$fmst[0]->CYEAR}}"></div>
                    <tr>
                        <td style="width: 30%;">
                            <select class="select select-bordered w-full category">

                            </select>
                        </td>
                        <td>
                            <select class="select select-bordered w-full product">

                            </select>
                        </td>
                        <td>
                            <p class="remain"></p>
                        </td>
                        <td>
                            <input type="number" class="input input-bordered w-full qty" min="1" max="10" value="1">
                        </td>
                        <td>
                            <select class="select select-bordered w-full">
                                <option value="">เลือกเหตุผลการเบิก</option>
                                <option value="ชำรุด">ชำรุด</option>
                                <option value="เข้างานใหม่">เข้างานใหม่</option>
                                <option value="ย้ายแผนก">ย้ายแผนก</option>
                                <option value="สูญหาย">สูญหาย</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-center gap-4 mt-6">
            <button class="btn btn-primary" id="submit_form">ส่งคำขอเบิก</button>
            <button class="btn btn-secondary" onclick="clearForm()">ล้างรายการ</button>
        </div>
    </div>


</form> -->
<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button-->
                <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden focus:ring-inset" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <!--
            Icon when menu is closed.

            Menu open: "hidden", Menu closed: "block"
          -->
                    <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!--
            Icon when menu is open.

            Menu open: "block", Menu closed: "hidden"
          -->
                    <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex shrink-0 items-center">
                    <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
                </div>
                <div class="hidden sm:ml-6 sm:block">
                    <div class="flex space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="#" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white" aria-current="page">Dashboard</a>
                        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Team</a>
                        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Projects</a>
                        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Calendar</a>
                    </div>
                </div>
            </div>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                    <span class="absolute -inset-1.5"></span>
                    <span class="sr-only">View notifications</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </button>

                <!-- Profile dropdown -->
                <div class="relative ml-3">
                    <div>
                        <button type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">Open user menu</span>
                            <img class="size-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                        </button>
                    </div>

                    <!--
            Dropdown menu, show/hide based on menu state.

            Entering: "transition ease-out duration-100"
              From: "transform opacity-0 scale-95"
              To: "transform opacity-100 scale-100"
            Leaving: "transition ease-in duration-75"
              From: "transform opacity-100 scale-100"
              To: "transform opacity-0 scale-95"
          -->
                    <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 focus:outline-hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                        <!-- Active: "bg-gray-100 outline-hidden", Not Active: "" -->
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pt-2 pb-3">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            <a href="#" class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white" aria-current="page">Dashboard</a>
            <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Team</a>
            <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Projects</a>
            <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Calendar</a>
        </div>
    </div>
</nav>
<div class="w-full bg-gray-100 shadow-lg rounded-lg p-6 mt-6">
    <div class="form-mst" data-NFRMNO="{{$fmst[0]->NNO}}" data-VORGNO="{{$fmst[0]->VORGNO}}" data-CYEAR="{{$fmst[0]->CYEAR}}"></div>
    <h2 class="text-center text-2xl font-bold mb-4">แบบฟอร์มเบิกอุปกรณ์ Safety</h2>
    <form class="space-y-4">
        <div class="form-control">
            <label class="label" for="employeeName">
                <span class="label-text">Input By:</span>
            </label>
            <input type="text" class="input input-bordered w-40" id="key" placeholder="กรุณากรอกรหัสพนักงาน" value="{{$_SESSION['user']->SEMPNO}}" readonly>
        </div>
        <div class="form-control">
            <label class="label" for="department">
                <span class="label-text">Requested By:</span>
            </label>
            <input type="text" class="input input-bordered w-40" id="req" placeholder="กรุณากรอกรหัสพนักงาน" required>
        </div>
        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="table w-full border-collapse border border-gray-200 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-400/50">
                        <th class="border border-gray-200 p-3 text-left rounded-tl-lg">รุ่น</th>
                        <th class="border border-gray-200 p-3 text-left">ไซส์</th>
                        <th class="border border-gray-200 p-3 text-left">สต็อกคงเหลือ</th>
                        <th class="border border-gray-200 p-3 text-left">จำนวนที่ต้องการเบิก</th>
                        <th class="border border-gray-200 p-3 text-left rounded-tr-lg">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr class="hover:bg-gray-50">
                        <td style="width: 30%;" class="border border-gray-200 p-3">
                            <select class="select select-bordered w-full category bg-white rounded-md">
                                <!-- ตัวเลือกรุ่น -->
                            </select>
                        </td>
                        <td class="border border-gray-200 p-3">
                            <select class="select select-bordered w-full product bg-white rounded-md">
                                <!-- ตัวเลือกไซส์ -->
                            </select>
                        </td>
                        <td class="border border-gray-200 p-3">
                            <p class="remain text-gray-700">0</p>
                        </td>
                        <td class="border border-gray-200 p-3">
                            <input type="number" class="input input-bordered w-full qty bg-white rounded-md" min="1" max="10" value="1">
                        </td>
                        <td class="border border-gray-200 p-3">
                            <select class="select select-bordered w-full bg-white rounded-md">
                                <option value="">เลือกเหตุผลการเบิก</option>
                                <option value="ชำรุด">ชำรุด</option>
                                <option value="เข้างานใหม่">เข้างานใหม่</option>
                                <option value="ย้ายแผนก">ย้ายแผนก</option>
                                <option value="สูญหาย">สูญหาย</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-center gap-4 mt-4">
            <button type="button" class="btn btn-primary w-50" id="addRow">เพิ่มอุปกรณ์</button>
            <button type="submit" class="btn btn-success w-50">ยืนยันการเบิก</button>
        </div>
    </form>
</div>


@endsection

@section('scripts')
<script src="{{ $GLOBALS['script'] }}pperequest.bundle.js?ver={{ date('Ymdhis') }}"></script>
<script>

    function clearForm() {
        // ล้างฟอร์ม
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
        document.querySelectorAll('input[type="number"]').forEach(input => input.value = 1);
        document.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
    }
</script>
<script>
    function addRow() {
        const tableBody = document.getElementById("tableBody");
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
                <td>
                    <select class="select select-bordered w-full" required>
                        <option value="">เลือกอุปกรณ์</option>
                        <option value="helmet">หมวกนิรภัย</option>
                        <option value="gloves">ถุงมือกันบาด</option>
                        <option value="glasses">แว่นตานิรภัย</option>
                        <option value="shoes">รองเท้านิรภัย</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="input input-bordered w-full" min="1" required>
                </td>
                <td>
                    <textarea class="textarea textarea-bordered w-full" rows="2"></textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-error" onclick="removeRow(this)">ลบ</button>
                </td>
            `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button) {
        button.parentElement.parentElement.remove();
    }
</script>
@endsection