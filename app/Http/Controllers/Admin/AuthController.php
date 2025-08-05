<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Owner;
use App\Services\LoginLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $owner = Owner::where('user_id', $user->id)->first();

                if ($owner && $owner->is_super_admin) {
                    $request->session()->regenerate();
                    
                    // Log successful admin login
                    $this->loginLogService->logLogin($request, $user, 'success');
                    
                    return redirect()->intended('/admin/dashboard');
                } else {
                    Auth::logout();
                    
                    // Log failed admin login attempt (unauthorized access)
                    $this->loginLogService->logLogin($request, null, 'failed', 'No super admin privileges');
                    
                    return back()->withErrors([
                        'email' => 'You do not have super admin privileges.',
                    ]);
                }
            }

            // Log failed login attempt
            $this->loginLogService->logLogin($request, null, 'failed', 'Invalid credentials');
            
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        } catch (\Exception $e) {
            // Log failed login attempt
            $this->loginLogService->logLogin($request, null, 'failed', $e->getMessage());
            throw $e;
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        
        if ($user) {
            // Log logout
            $this->loginLogService->logLogout($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
