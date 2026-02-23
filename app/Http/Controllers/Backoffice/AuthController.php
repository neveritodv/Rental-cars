<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Auth\LoginRequest;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('backoffice.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        $request->session()->forget('url.intended');

        if (! Auth::guard('backoffice')->attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'Adresse email ou mot de passe incorrect.',
            ])->onlyInput('email');
        }

        /** @var User $user */
        $user = Auth::guard('backoffice')->user();

        if ($user->status !== 'active') {
            Auth::guard('backoffice')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Votre compte est actuellement ' . $user->status . '.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        // Send login notification to the user
        Notification::create([
            'user_id' => $user->id,
            'type' => 'login',
            'title' => 'Connexion réussie',
            'message' => "Vous vous êtes connecté à " . now()->format('H:i'),
            'icon' => 'ti ti-login',
            'is_read' => false,
            'is_archived' => false,
        ]);

        // Notify admins about this login
        $this->notifyAdminsOfLogin($user);

        return redirect()->route('backoffice.dashboard');
    }

    public function demoLogin(Request $request)
    {
        $request->session()->forget('url.intended');

        $user = User::where('email', 'admin@agency1.com')->first();

        if (! $user) {
            return redirect()->route('backoffice.login')
                ->withErrors(['email' => 'Utilisateur de démonstration introuvable. Lancez le seeder.']);
        }

        if ($user->status !== 'active') {
            return redirect()->route('backoffice.login')
                ->withErrors(['email' => 'Le compte de démonstration n’est pas actif.']);
        }

        Auth::guard('backoffice')->login($user, true);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        // Send demo login notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'login',
            'title' => 'Connexion démo',
            'message' => "Connexion de démonstration à " . now()->format('H:i'),
            'icon' => 'ti ti-login',
            'is_read' => false,
            'is_archived' => false,
        ]);

        // Notify admins about this demo login
        $this->notifyAdminsOfLogin($user);

        return redirect()->route('backoffice.dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('backoffice')->user();
        
        if ($user) {
            // Send logout notification to the user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'logout',
                'title' => 'Déconnexion',
                'message' => "Vous vous êtes déconnecté à " . now()->format('H:i'),
                'icon' => 'ti ti-logout',
                'is_read' => false,
                'is_archived' => false,
            ]);
            
            // Notify admins about logout
            $this->notifyAdminsOfLogout($user);
        }

        Auth::guard('backoffice')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('backoffice.login');
    }

    /**
     * Notify all admins about a user login
     */
    private function notifyAdminsOfLogin($user)
    {
        // Get all super-admin users
        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'super-admin');
        })->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'info',
                'title' => 'Connexion utilisateur',
                'message' => "{$user->name} ({$user->email}) s'est connecté à " . now()->format('H:i'),
                'icon' => 'ti ti-user',
                'is_read' => false,
                'is_archived' => false,
            ]);
        }
        
        // Also notify agency admins if user belongs to an agency
        if ($user->agency_id) {
            $agencyAdmins = User::where('agency_id', $user->agency_id)
                ->where('id', '!=', $user->id)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['admin', 'manager']);
                })
                ->get();
                
            foreach ($agencyAdmins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'info',
                    'title' => 'Connexion dans votre agence',
                    'message' => "{$user->name} ({$user->email}) s'est connecté à " . now()->format('H:i'),
                    'icon' => 'ti ti-building',
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            }
        }
    }

    /**
     * Notify admins about a user logout
     */
    private function notifyAdminsOfLogout($user)
    {
        // Get all super-admin users
        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'super-admin');
        })->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'info',
                'title' => 'Déconnexion utilisateur',
                'message' => "{$user->name} ({$user->email}) s'est déconnecté à " . now()->format('H:i'),
                'icon' => 'ti ti-user',
                'is_read' => false,
                'is_archived' => false,
            ]);
        }
    }
}