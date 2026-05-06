<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if ($user->is_guest) {
            return back()->withErrors(['email' => 'This account was created as a guest. Please continue as guest.'])->withInput();
        }

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'age' => 'nullable|integer',
            'gender' => 'required|in:male,female,other',
            'target_gender' => 'required|in:male,female,both,any',
        ]);

        $location = $this->getLocationFromIp($request->ip());

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'],
            'target_gender' => $validated['target_gender'],
            'location' => $location['city'] . ', ' . $location['country'],
            'ip_address' => $request->ip(),
            'is_guest' => false,
            'is_online' => true,
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function guest(Request $request)
    {
        $validated = $request->validate([
            'age' => 'nullable|integer',
            'gender' => 'required|in:male,female,other',
            'target_gender' => 'required|in:male,female,both,any',
        ]);

        $location = $this->getLocationFromIp($request->ip());
        $sessionToken = Str::random(64);

        $user = User::create([
            'name' => $request->name ?? null,
            'email' => null,
            'password' => null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'],
            'target_gender' => $validated['target_gender'],
            'location' => $location['city'] . ', ' . $location['country'],
            'ip_address' => $request->ip(),
            'is_guest' => true,
            'is_online' => true,
            'session_token' => $sessionToken,
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function logout()
    {
        $user = auth()->user();
        if ($user) {
            $user->update(['is_online' => false]);
        }
        Auth::logout();
        return redirect('/');
    }

    private function getLocationFromIp(string $ip): array
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return ['city' => 'Local', 'country' => 'Local'];
        }

        try {
            $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=city,country");
            $data = json_decode($response, true);
            return [
                'city' => $data['city'] ?? 'Unknown',
                'country' => $data['country'] ?? 'Unknown',
            ];
        } catch (\Exception $e) {
            return ['city' => 'Unknown', 'country' => 'Unknown'];
        }
    }
}