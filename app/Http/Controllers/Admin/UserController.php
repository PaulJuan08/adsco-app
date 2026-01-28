<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserApprovedMail;

class UserController extends Controller
{
    public function index()
    {
        // Get search query
        $search = request()->input('search');
        $role = request()->input('role');
        
        // Build query
        $query = User::query();
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                ->orWhere('l_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply role filter
        if ($role && in_array($role, [1, 2, 3, 4])) {
            $query->where('role', $role);
        }
        
        // Get paginated results with 10 per page
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }
    
    public function create()
    {
        return view('admin.users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:1,2,3,4',
            'password' => 'required|string|min:8'
        ]);
        
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }
    
    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
    
    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    
    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $user = User::findOrFail($id);
        
        $request->validate([
            'f_name' => 'required|string|max:50',
            'l_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:1,2,3,4'
        ]);
        
        $user->update($request->all());
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    public function approve($id)
    {
        try {
            // First try to decrypt (in case it's encrypted from the user index page)
            try {
                $decryptedId = Crypt::decrypt($id);
                $user = User::findOrFail($decryptedId);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // If decryption fails, treat it as a regular ID
                $user = User::findOrFail($id);
            }
            
            // Check if user is already approved
            if ($user->is_approved) {
                return redirect()->route('admin.users.index')
                    ->with('warning', 'User is already approved.');
            }
            
            // Update user approval status
            $user->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);
            
            // Send approval email (optional - comment out if not needed)
            try {
                if (class_exists(\App\Mail\UserApprovedMail::class)) {
                    Mail::to($user->email)->send(new \App\Mail\UserApprovedMail($user));
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
            
            // Log the approval action
            if (class_exists(\App\Helpers\AuditHelper::class)) {
                \App\Helpers\AuditHelper::log('approve', "Approved user: {$user->email}", $user);
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User approved successfully!');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}