<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Validator;
// use Hash;
use Session;

class LoginController extends Controller
{
    public function loginPage()
    {
        return view('Login.login');
    }

    public function postLogin(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $messages = [
            'email.required'        => 'Please enter your email.',
            'email.email'           => 'Email is not valid.',
            'password.required'     => 'Please enter your password.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
        // $data = [
        //     'email'     => $request->input('email'),
        //     'password'  => $request->input('password'),
        // ];
        // Auth::attempt($data);

        // if (Auth::check()) { // true sekalian session field di users nanti bisa dipanggil via Auth
        //     //Login Success
        //     return redirect()->route('home');
        // } else { // false

        //     //Login Fail
        //     Session::flash('error', 'Email atau password salah');
        //     return redirect()->route('login');
        // }
        // dump($request->all());
        $role = DB::table('users')->where('email', $request->email)->value('role');
        // dd($role);
        // if(Auth::attempt($request->only('email','password'))){
        //     return redirect('/home');
        // }
        // return redirect('/');
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if ($role == 'admin') {
                return redirect()->intended('home');
            } else {
                return redirect()->intended('userHome');
            }
        } else {
            //Login Fail
            Session::flash('error', 'Email or password is incorrect.');
            return redirect()->route('login');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function register()
    {
        return view('Login.register');
    }

    public function saveData(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8',
            'alamat'                => 'required',
            'noTelepon'             => 'required|numeric',
        ];

        $messages = [
            'name.required'         => 'Please enter your full name.',
            'email.required'        => 'Please enter your email.',
            'email.email'           => 'Email is not valid.',
            'email.unique'          => 'Email has already been registered.',
            'password.required'     => 'Please enter your password.',
            'alamat'                => 'Please enter your address.',
            'noTelepon'             => 'Please enter your phone number.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
        // User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'email_verified_at' => now(),
        //     'password' => Hash::make($request->password),
        //     'noTelepon' => $request->noTelepon,
        //     'alamat' => $request->alamat,
        //     'role' => 'user',
        //     'remember_token' => Str::random(60),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
        $user = new User;
        $user->name = ucwords(strtolower($request->name));
        $user->email = strtolower($request->email);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->noTelepon = $request->noTelepon;
        $user->alamat = $request->alamat;
        $user->role = 'user';
        $user->remember_token =  Str::random(60);
        $user->created_at = now();
        $user->updated_at = now();
        $simpan = $user->save();

        if ($simpan) {
            Session::flash('success', 'Register successful! You may login to access your data.');
            return redirect()->route('login');
        } else {
            Session::flash('errors', 'Register failed! Please try again.');
            return redirect()->route('register');
        }

        return view('/welcome');
    }
}

// $rules = [
//     'email' => 'required|email',
//     'password' => 'required|string'
// ];
// $messages = [
//     'email.required'        => 'Email wajib diisi',
//     'email.email'           => 'Email tidak valid',
//     'password.required'     => 'Password wajib diisi',
//     'password.string'       => 'Password harus berupa string'
// ];
// $validator = Validator::make($request->all(), $rules, $messages);

// if ($validator->fails()) {
//     return redirect()->back()->withErrors($validator)->withInput($request->all);
// }
// $data = [
//     'email'     => $request->input('email'),
//     'password'  => $request->input('password'),
// ];
// Auth::attempt($data);

// if (Auth::check()) { // true sekalian session field di users nanti bisa dipanggil via Auth
//     //Login Success
//     return redirect()->route('home');
// } else { // false

//     //Login Fail
//     Session::flash('error', 'Email atau password salah');
//     return redirect()->route('login');
// }

/////

// $rules = [
//     'name'                  => 'required|min:3|max:35',
//     'email'                 => 'required|email|unique:users,email',
//     'password'              => 'required|confirmed'
// ];

// $messages = [
//     'name.required'         => 'Nama Lengkap wajib diisi',
//     'name.min'              => 'Nama lengkap minimal 3 karakter',
//     'name.max'              => 'Nama lengkap maksimal 35 karakter',
//     'email.required'        => 'Email wajib diisi',
//     'email.email'           => 'Email tidak valid',
//     'email.unique'          => 'Email sudah terdaftar',
//     'password.required'     => 'Password wajib diisi',
//     'password.confirmed'    => 'Password tidak sama dengan konfirmasi password'
// ];
// $validator = Validator::make($request->all(), $rules, $messages);

// if ($validator->fails()) {
//     return redirect()->back()->withErrors($validator)->withInput($request->all);
// }

// $user = new User;
// $user->name = ucwords(strtolower($request->name));
// $user->email = strtolower($request->email);
// $user->password = Hash::make($request->password);
// $user->email_verified_at = \Carbon\Carbon::now();
// $simpan = $user->save();

// if ($simpan) {
//     Session::flash('success', 'Register berhasil! Silahkan login untuk mengakses data');
//     return redirect()->route('login');
// } else {
//     Session::flash('errors', 'Register gagal! Silahkan ulangi beberapa saat lagi');
//     return redirect()->route('register');
// }