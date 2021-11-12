<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ResetPassword;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller{
//     public function __construct()
//     {
//         $this->middleware('guest:admin')->except('logout');
//     }

//     public function authenticated(Request $request, $user)
//     {

//         if ($user->hasRole('admin')) {
//             return redirect()->route('bank.master-bank.index');
//         } else if ($user->hasRole('finance')) {
//             return redirect()->route('bank.master-bank.index');
//         } else if ($user->hasRole('supervisor')) {
//             return redirect()->route('bank.master-bank.index');
//         }

//         return redirect('login');
//     }

//     public function login(Request $request)
//     {
//         $this->validate($request, [
//             'email' => 'required|email',
//             'password' => 'required'
//         ]);

//         if (auth()->guard('admins')->attempt($request->only('email', 'password'))) {
//             $request->session()->regenerate();
//             $this->clearLoginAttempts($request);
//             return redirect()->intended('/bank/master-bank');
//         } else {
//             $this->incrementLoginAttempts($request);

//             return redirect()
//                 ->back()
//                 ->withInput()
//                 ->withErrors(["Incorrect user login details!"]);
//         }
// }

}
?>




