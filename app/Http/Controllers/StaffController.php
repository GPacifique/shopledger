<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->isShopAdmin()) {
            abort(403);
        }

        $query = User::where('shop_id', $user->shop_id)
            ->where('id', '!=', $user->id);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('account_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staff = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:seller,accountant',
        ]);

        $admin = $request->user();
        if (!$admin->isShopAdmin()) {
            abort(403);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'shop_id' => $admin->shop_id,
            'account_status' => 'active',
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function show(Request $request, User $staff)
    {
        $this->authorizeStaff($request, $staff);

        return view('staff.show', compact('staff'));
    }

    public function edit(Request $request, User $staff)
    {
        $this->authorizeStaff($request, $staff);

        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        $this->authorizeStaff($request, $staff);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'role' => 'required|in:seller,accountant',
            'account_status' => 'required|in:active,suspended',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->role = $validated['role'];
        $staff->account_status = $validated['account_status'];

        if ($request->filled('password')) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Request $request, User $staff)
    {
        $this->authorizeStaff($request, $staff);

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    protected function authorizeStaff(Request $request, User $staff): void
    {
        $user = $request->user();
        if (!$user->isShopAdmin() || $staff->shop_id !== $user->shop_id) {
            abort(403);
        }
    }
}
