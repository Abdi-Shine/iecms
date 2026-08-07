<?php

namespace App\Http\Controllers;

use App\Mail\AccessGrantedMail;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    /**
     * Restrict an Employee query to the current user's institution.
     * Super admins see every employee, unscoped. Uses employees.institution_id
     * directly (not the linked User's institution) so staff who don't have
     * login access yet — exactly who the Access Login page needs to find —
     * still show up for their institution's admin.
     */
    private function scopeToInstitution($query): void
    {
        $user = auth()->user();

        if (!$user || $user->is_super_admin) {
            return;
        }

        if ($user->institution_id === null) {
            $query->whereNull('institution_id');
        } else {
            $query->where('institution_id', $user->institution_id);
        }
    }

    /**
     * Guards direct show/edit/delete access to a single employee by ID —
     * scopeToInstitution() only hides employees from listings, it doesn't
     * stop a URL-guessed request for an out-of-institution employee.
     */
    private function authorizeEmployeeAccess(Employee $employee): void
    {
        $user = auth()->user();

        if (!$user || $user->is_super_admin) {
            return;
        }

        if ($employee->institution_id !== $user->institution_id) {
            abort(403, 'You do not have access to this employee record.');
        }
    }

    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $query = Employee::query();
        $this->scopeToInstitution($query);

        if ($request->filled('search')) {
            $query->where('EmpName', 'like', '%' . $request->search . '%')
                  ->orWhere('EmpID', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('unit')) {
            $query->where('courtID', $request->unit);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stats = [
            'total'  => (clone $query)->count(),
            'judges' => (clone $query)->where('Position', 'like', '%Judge%')->count(),
            'clerks' => (clone $query)->where('Position', 'like', '%Clerk%')->count(),
            'chiefs' => (clone $query)->where('Position', 'like', '%Chief%')->count(),
        ];

        $perPage = $this->resolvePerPage($request);

        $employees = $query->with(['court', 'assignments.case'])->latest('AID')->paginate($perPage)->withQueryString();
        $roles     = \App\Models\Role::all();
        $courts    = \App\Models\Court::all();
        return view('setting.employee_view', compact('employees', 'roles', 'courts', 'stats'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $roles   = \App\Models\Role::all();
        $courts  = \App\Models\Court::all();
        $regions = \App\Models\StateRegion::with(['cities' => function ($q) {
            $q->orderBy('city_name');
        }])->orderBy('state_name')->get();
        $nextEmpId = $this->generateNextEmpId();
        return view('setting.employee_add', compact('roles', 'courts', 'regions', 'nextEmpId'));
    }

    /**
     * Generate the next sequential Employee ID (e.g. EMID0001).
     */
    private function generateNextEmpId(): string
    {
        $prefix = 'EMID';

        $lastNumber = Employee::where('EmpID', 'like', $prefix . '%')
            ->lockForUpdate()
            ->pluck('EmpID')
            ->map(fn ($empId) => (int) substr($empId, strlen($prefix)))
            ->max();

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'EmpName' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'Position' => 'required',
            'courtID' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('EmpID');

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/employees'), $filename);
            $data['photo'] = 'uploads/employees/' . $filename;
        } else {
            $data['photo'] = 'uploads/employees/default.png';
        }

        // Set Default Values for required DB fields
        $data['islogin'] = $data['islogin'] ?? '0';
        $data['addedBy'] = auth()->user()->name ?? 'Admin';
        $data['updatedBy'] = '';
        $data['updatedDate'] = '';
        $data['Dates'] = $data['Dates'] ?? date('Y-m-d');
        // Institution admins' new staff inherit their institution; a super
        // admin using this form is registering court staff historically,
        // so default to the Courts institution rather than leaving it null
        // (which would hide the employee from every institution admin).
        $data['institution_id'] = auth()->user()->institution_id
            ?? \App\Models\Institution::where('type', 'court')->value('id');

        \DB::transaction(function () use ($data) {
            $data['EmpID'] = $this->generateNextEmpId();
            Employee::create($data);
        });

        return redirect()->route('employee.index')->with('success', 'Employee registered successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);
        return view('setting.employee_show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);
        $regions  = \App\Models\StateRegion::with(['cities' => function ($q) {
            $q->orderBy('city_name');
        }])->orderBy('state_name')->get();
        return view('setting.employee_edit', compact('employee', 'regions'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);

        $request->validate([
            'EmpName'  => 'required',
            'Position' => 'required',
            'courtID'  => 'required',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['signature_data']);

        // Handle Photo Update
        if ($request->hasFile('photo')) {
            if ($employee->photo && file_exists(public_path($employee->photo)) && !str_contains($employee->photo, 'default.png')) {
                @unlink(public_path($employee->photo));
            }
            $file     = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/employees'), $filename);
            $data['photo'] = 'uploads/employees/' . $filename;
        }

        // Handle Signature (canvas base64 PNG)
        if ($request->filled('signature_data') && str_starts_with($request->input('signature_data'), 'data:')) {
            $sigRaw   = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('signature_data'));
            $sigBytes = base64_decode($sigRaw);
            $sigDir   = public_path('uploads/employees/signatures');
            if (!is_dir($sigDir)) {
                mkdir($sigDir, 0755, true);
            }
            if ($employee->signature && file_exists(public_path($employee->signature))) {
                @unlink(public_path($employee->signature));
            }
            $sigFilename = 'sig_' . time() . '_' . $id . '.png';
            file_put_contents($sigDir . '/' . $sigFilename, $sigBytes);
            $data['signature'] = 'uploads/employees/signatures/' . $sigFilename;
        }

        $data['updatedBy']   = auth()->user()->name ?? 'Admin';
        $data['updatedDate'] = date('Y-m-d H:i:s');

        if (empty($data['DOB']))   unset($data['DOB']);
        if (empty($data['Dates'])) unset($data['Dates']);

        $employee->update($data);

        return redirect()->back()->with('success', 'Employee details updated.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);
        $employee->delete();

        return redirect()->back()->with('success', 'Employee removed from registry.');
    }

    /**
     * Display the access management view.
     */
    public function accessLogin(Request $request)
    {
        $query = Employee::with(['court', 'user.group']);
        $this->scopeToInstitution($query);

        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(EmpName) LIKE LOWER(?)', [$term])
                  ->orWhereRaw('LOWER(EmpID) LIKE LOWER(?)', [$term])
                  ->orWhereRaw('LOWER(email) LIKE LOWER(?)', [$term])
                  ->orWhereRaw('LOWER(system_username) LIKE LOWER(?)', [$term]);
            });
        }

        if ($request->filled('court')) {
            $query->where('courtID', $request->court);
        }

        if ($request->filled('status')) {
            $query->where('islogin', $request->status);
        }

        $perPage = $this->resolvePerPage($request);

        $employees = $query->orderBy('EmpName')->paginate($perPage)->withQueryString();
        $courts    = \App\Models\Court::orderBy('longName')->get();

        $allEmployeesQuery = Employee::query();
        $this->scopeToInstitution($allEmployeesQuery);
        $allEmployees = $allEmployeesQuery->get();
        $stats = [
            'total'  => $allEmployees->count(),
            'active' => $allEmployees->where('islogin', '1')->count(),
        ];

        return view('setting.access_login_view', compact('employees', 'courts', 'stats'));
    }

    public function accessLoginCreate(string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);
        $roles    = \App\Models\Role::orderBy('display_name')->get();
        $groups   = \App\Models\Group::where('status', 'active')->orderBy('name')->get();
        return view('setting.access_login_add', compact('employee', 'roles', 'groups'));
    }

    public function accessLoginStore(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);

        $request->validate([
            'system_username' => 'required|unique:users,email',
            'password'        => 'required|min:6|confirmed',
            'system_role'     => 'required',
            'group_id'        => 'nullable|exists:groups,id',
        ]);

        $plainPassword = $request->input('password');
        $username      = $request->input('system_username');

        User::create([
            'name'           => $employee->EmpName,
            'email'          => $username,
            'password'       => Hash::make($plainPassword),
            'position'       => $request->input('system_role'),
            'group_id'       => $request->input('group_id') ?: null,
            'institution_id' => $employee->institution_id,
        ]);

        $employee->update([
            'islogin'         => '1',
            'system_username' => $username,
            'system_role'     => $request->input('system_role'),
            'updatedBy'       => auth()->user()->name ?? 'Admin',
            'updatedDate'     => now()->toDateTimeString(),
        ]);

        $mailSent = true;
        try {
            Mail::to($employee->email)->send(new AccessGrantedMail(
                empName:  $employee->EmpName,
                username: $username,
                password: $plainPassword,
                empEmail: $employee->email,
            ));
        } catch (\Exception $e) {
            $mailSent = false;
            \Log::error('Failed to send AccessGrantedMail to ' . $employee->email . ': ' . $e->getMessage());
        }

        $message = 'Access granted successfully for ' . $employee->EmpName . '.';
        $message .= $mailSent
            ? ' Confirmation email sent.'
            : ' However, the confirmation email could not be sent — please share the credentials with them manually.';

        return redirect()->route('employee.access-login')
            ->with($mailSent ? 'success' : 'warning', $message);
    }

    public function export()
    {
        $employees = Employee::with('court')->latest('AID')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees_' . date('Y-m-d') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = ['EmpID', 'EmpName', 'gender', 'phone', 'email', 'DOB', 'POB', 'Position', 'courtID', 'status', 'Dates'];

        $callback = function () use ($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($employees as $emp) {
                fputcsv($file, [
                    $emp->EmpID,
                    $emp->EmpName,
                    $emp->gender,
                    $emp->phone,
                    $emp->email,
                    $emp->DOB ? $emp->DOB->format('Y-m-d') : '',
                    $emp->POB,
                    $emp->Position,
                    $emp->courtID,
                    $emp->status,
                    $emp->Dates ? $emp->Dates->format('Y-m-d') : '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file    = $request->file('csv_file');
        $handle  = fopen($file->getRealPath(), 'r');
        $header  = fgetcsv($handle); // skip header row
        $imported = 0;
        $skipped  = 0;
        $institutionId = auth()->user()->institution_id
            ?? \App\Models\Institution::where('type', 'court')->value('id');

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) { $skipped++; continue; }

            [$empID, $empName, $gender, $phone, $email, $dob, $pob, $position, $courtID, $status, $dates] = array_pad($row, 11, '');

            if (empty($empID) || empty($empName) || empty($email)) { $skipped++; continue; }
            if (Employee::where('EmpID', $empID)->orWhere('email', $email)->exists()) { $skipped++; continue; }

            Employee::create([
                'EmpID'       => $empID,
                'EmpName'     => $empName,
                'gender'      => $gender ?: 'Male',
                'phone'       => $phone ?: '—',
                'email'       => $email,
                'photo'       => 'uploads/employees/default.png',
                'DOB'         => $dob ?: now()->toDateString(),
                'POB'         => $pob ?: 'Mogadishu',
                'Position'    => $position ?: 'Staff',
                'courtID'     => $courtID ?: '',
                'institution_id' => $institutionId,
                'status'      => in_array(strtolower($status), ['active', 'inactive']) ? strtolower($status) : 'active',
                'islogin'     => '0',
                'Dates'       => $dates ?: now()->toDateString(),
                'addedBy'     => auth()->user()->name ?? 'Admin',
                'updatedBy'   => '',
                'updatedDate' => '',
            ]);
            $imported++;
        }
        fclose($handle);

        $msg = "Import complete: {$imported} added" . ($skipped ? ", {$skipped} skipped (duplicate or invalid)." : '.');
        return redirect()->route('employee.index')->with('success', $msg);
    }

    public function accessLoginEdit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);
        $roles    = \App\Models\Role::orderBy('display_name')->get();
        $groups   = \App\Models\Group::where('status', 'active')->orderBy('name')->get();
        $user     = \App\Models\User::where('email', $employee->system_username)->first();
        return view('setting.access_login_edit', compact('employee', 'roles', 'groups', 'user'));
    }

    public function accessLoginUpdate(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorizeEmployeeAccess($employee);

        $request->validate([
            'system_username' => 'required|unique:users,email,' . optional(User::where('email', $employee->system_username)->first())->id,
            'password'        => 'nullable|min:6|confirmed',
            'system_role'     => 'required',
            'group_id'        => 'nullable|exists:groups,id',
        ]);

        $user = User::where('email', $employee->system_username)->first();

        $userData = [
            'name'     => $employee->EmpName,
            'email'    => $request->system_username,
            'position' => $request->system_role,
            'group_id' => $request->input('group_id') ?: null,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($user) {
            $user->update($userData);
        } else {
            if (!$request->filled('password')) {
                return back()->withErrors(['password' => 'A password is required since no existing user account was found.'])->withInput();
            }
            $userData['password'] = Hash::make($request->password);
            User::create($userData);
        }

        $employee->update([
            'islogin'         => $request->islogin ?? '1',
            'system_username' => $request->system_username,
            'system_role'     => $request->system_role,
            'updatedBy'       => auth()->user()->name ?? 'Admin',
            'updatedDate'     => now()->toDateTimeString(),
        ]);

        return redirect()->route('employee.access-login')
            ->with('success', 'Access credentials updated for ' . $employee->EmpName . '.');
    }
}
