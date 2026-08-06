@extends('admin.admin_master')
@section('page_title', 'Edit System Access')
@section('admin_main_content')


<div class="p-4 sm:p-6 w-full bg-[#F8F9FA] min-h-screen">

    {{-- Breadcrumbs --}}
    <div class="mb-4 flex items-center gap-2 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
        <a href="{{ route('employee.access-login') }}" class="hover:text-[#F0B43C] transition-colors">Access Management</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-[#F0B43C]">Edit Access</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-semibold">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 flex items-center gap-3" style="background:#F0B43C">
            <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center text-white">
                <i class="bi bi-shield-shaded text-lg"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-white leading-tight">Edit System Access</h2>
                <p class="text-[11px] text-white/80">For: <span class="font-bold">{{ $employee->EmpName }}</span></p>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6">

            {{-- Staff Info --}}
            <div class="mb-5 px-4 py-3 rounded-xl border border-neutral-100 bg-neutral-50">
                <div class="grid grid-cols-2 gap-x-6 gap-y-0.5 text-[11px]">
                    <div><span class="text-neutral-400 font-bold uppercase">ID</span> <span class="font-semibold text-neutral-700 ml-1">{{ $employee->EmpID }}</span></div>
                    <div><span class="text-neutral-400 font-bold uppercase">Court</span> <span class="font-semibold text-neutral-700 ml-1">{{ $employee->court->longName ?? 'N/A' }}</span></div>
                    <div><span class="text-neutral-400 font-bold uppercase">Name</span> <span class="font-semibold text-neutral-700 ml-1">{{ $employee->EmpName }}</span></div>
                    <div><span class="text-neutral-400 font-bold uppercase">Position</span> <span class="font-semibold text-neutral-700 ml-1">{{ $employee->Position ?? 'N/A' }}</span></div>
                </div>
            </div>

            <form action="{{ route('employee.access-login.update', $employee->AID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    {{-- Username --}}
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">System Username (Email) <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <input type="email" name="system_username"
                                   value="{{ old('system_username', $employee->system_username) }}"
                                   required placeholder="username@iecms.gov.so"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-envelope" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>

                    {{-- Role + Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">System Role <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <select name="system_role" required
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="">— Select Role —</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('system_role', $employee->system_role) == $role->name ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Access Status <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <select name="islogin" required
                                        style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                        onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="1" {{ old('islogin',$employee->islogin)=='1'?'selected':'' }}>Authorized</option>
                                    <option value="0" {{ old('islogin',$employee->islogin)=='0'?'selected':'' }}>Revoked</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Permission Group --}}
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Permission Group
                            <span style="color:#6b7280;font-weight:600;text-transform:none;letter-spacing:0"> — controls what menus &amp; pages this user can access</span>
                        </label>
                        <div style="position:relative">
                            <select name="group_id"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="">— No Group (Super Admin) —</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id', $user?->group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="flex items-center gap-3 py-1">
                        <div class="flex-1 border-t border-dashed border-neutral-200"></div>
                        <span class="text-[10px] font-black text-neutral-400 uppercase tracking-widest whitespace-nowrap">Password (optional)</span>
                        <div class="flex-1 border-t border-dashed border-neutral-200"></div>
                    </div>

                    {{-- Passwords --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">New Password</label>
                            <div style="position:relative">
                                <input type="password" name="password" id="pwd" placeholder="Leave blank to keep"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <button type="button" onclick="togglePwd('pwd','e1')" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;line-height:1">
                                    <i id="e1" class="bi bi-eye" style="font-size:.8rem"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Confirm Password</label>
                            <div style="position:relative">
                                <input type="password" name="password_confirmation" id="pwd2" placeholder="Re-enter"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <button type="button" onclick="togglePwd('pwd2','e2')" style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;line-height:1">
                                    <i id="e2" class="bi bi-eye" style="font-size:.8rem"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="pwdHint" class="hidden px-3 py-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> <span id="pwdHintText"></span>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('employee.access-login') }}"
                       class="px-5 py-2 border border-gray-200 text-gray-400 font-bold rounded-xl hover:bg-gray-50 transition text-xs">
                        Discard
                    </a>
                    <button type="submit"
                            class="flex items-center gap-2 px-6 py-2 text-white font-black rounded-xl text-xs shadow-md transition"
                            style="background:#F0B43C; box-shadow:0 6px 16px rgba(240,180,60,0.3)">
                        <i class="bi bi-cloud-check-fill"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePwd(inputId, iconId) {
    const i = document.getElementById(inputId);
    const e = document.getElementById(iconId);
    i.type = i.type === 'password' ? 'text' : 'password';
    e.className = i.type === 'text' ? 'bi bi-eye-slash text-xs' : 'bi bi-eye text-xs';
}
const pwd = document.getElementById('pwd'), pwd2 = document.getElementById('pwd2');
const hint = document.getElementById('pwdHint'), hintText = document.getElementById('pwdHintText');
function checkMatch() {
    if (!pwd2.value && !pwd.value) { hint.classList.add('hidden'); return; }
    pwd.value !== pwd2.value ? (hint.classList.remove('hidden'), hintText.textContent='Passwords do not match.') : hint.classList.add('hidden');
}
pwd.addEventListener('input', checkMatch);
pwd2.addEventListener('input', checkMatch);
</script>

@endsection
