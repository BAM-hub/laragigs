<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // show register/crete form
    public function create() {
      return view('users.register');
    }

    // crete new user
    public function store(Request $request) {
      $company_name = $request->company;
      if(!$company_name == null){

        $company = Company::where('company_name', '=', $company_name)
        ->get();
        
        if(count($company) == 0) {
          $company = Company::create([
            'company_name' => $company_name
          ]);
          $company = $company->id;
        } else {
          $company = $company[0]->id;
        }
      }

      $formFields = $request->validate([
        'name' => ['required', 'min:3'],
        'company_id' => ['nullable'],
        'email' => ['required', 'email', Rule::unique('users', 'email')],
        'password' => 'required|confirmed|min:6'
      ]);

      // Hash Password
      $formFields['password'] = bcrypt($formFields['password']);
      $formFields['company_id'] = $company_name != null ? $company : null;
      // crete user
      $user = User::create($formFields);

      // login
      auth()->login($user);
      return redirect('/')->with('message', 'User created and logeged in');
    }

    // logout user
    public function logout(Request $request) {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out!');
    }

    // show login form
    public function login() {
      return view('users.login');
    }

    // log user in
    public function authenicate(Request $request) {
      $formFields = $request->validate([
        'email' => ['required', 'email'],
        'password' => 'required'
      ]);
      if(auth()->attempt($formFields)) {
        $request->session()->regenerate();

        return redirect('/')->with('message', 'You are now logged in');
      }

      return back()->withErrors(['email' => 'Invalid Credentials'])
      ->onlyInput('email');
    }
}