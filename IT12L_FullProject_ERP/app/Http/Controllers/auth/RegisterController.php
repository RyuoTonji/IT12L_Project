<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Optional: Auto-login after register
        // Auth::login($user);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'given_name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:500'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string', 'size:11', 'unique:users', 'regex:/^09[0-9]{9}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'contact_number.regex' => 'Contact number must be 11 digits and start with 09.',
            'contact_number.size' => 'Contact number must be exactly 11 digits.',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'given_name' => $data['given_name'],
            'surname' => $data['surname'],
            'middle_initial' => $data['middle_initial'] ?? null,
            'suffix' => $data['suffix'] ?? null,
            'name' => trim("{$data['given_name']} {$data['surname']}"),
            'email' => $data['email'],
            'contact_number' => $data['contact_number'],
            'address' => $data['address'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
        ]);
    }
}