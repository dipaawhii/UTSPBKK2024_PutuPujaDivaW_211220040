<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'level' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'level' => $request->level,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if (Auth::user()->level == 'Administrator') {
            return redirect(route('admin.admin.dashboard'));
        } else if (Auth::user()->level == 'Karyawan') {
            return redirect(route('karyawan.karyawan.dashboard'));
        } else if (Auth::user()->level == 'Supervisi') {
            return redirect(route('supervisi.supervisi.dashboard'));
        } else if (Auth::user()->level == 'Member') {
            return redirect(route('member.member.dashboard'));
        }

        return redirect(route('dashboard', absolute: false));
    }
}
