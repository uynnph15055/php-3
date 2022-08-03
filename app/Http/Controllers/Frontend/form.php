<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Exception;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreLogin;
use App\Http\Requests\StoreRegister;

class form extends Controller
{
    protected $file;
    public function viewLogin(){
        return view('form.login');
    }

    public function viewRegister(){
        return view('form.register');
    }

    // Save Register
    public function register(StoreRegister $request){
        $validated = $request->validated();
       try {
        $user = new User();
        $arrResquest = $request->all();
        $passEncryption = password_hash($request->password ,PASSWORD_DEFAULT); 
        $arrResquest['password'] =  $passEncryption;
        if($request->hasFile('avatar')){
            $file = $request->avatar;
            $fileName =  $file->getClientoriginalName();
            $file->move(public_path('/upload'), $fileName);
        }
        $user->password = $passEncryption;
        $user->avatar = $fileName;
        $user->role = 2;
        $user->fill($request->all());
        $user->save();
        session()->put('success' ,  'Register success !!!');
        return redirect()->route('login.login');
       } catch (\Throwable $th) {
        session()->put('error' ,  'Register error !!!');
       }
    }

    public function login(StoreLogin $request){
        $validated = $request->validated();
        if (Auth::attempt(['email' => $request->email_username, 'password' => $request->password])) {
            return redirect()->route('home');
        }
        
        return redirect()->route('login.login');
    }

    public function logOut(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back();
    }
}