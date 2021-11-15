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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            //'nim' => 'required',
            'email' => 'required|email:dns',
            'password' => 'required|min:8',
            //'number' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => true,
                'message' => 'Berhasil Login',
                'data' => $user,
                'token' => $this->auth($user)
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Number atau Password tidak sesuai',
            'data' => '',
        ], 404);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'nim' => 'required|unique:users',
            'name' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email:dns|unique:users',
            'number' => 'required'
        ]);

        $user = User::create([
        //     'nim' => Crypt::encrypt($request->nim),
        //     'name' => Crypt::encrypt($request->name),
        //     'password' => Hash::make($request->password),
        //     'email' => Crypt::encrypt($request -> email),
        //     'role' => Crypt::encrypt($request->role)

            'nim' => $request->nim,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request -> email,
            'role' => $request->role,
            'number' => $request->number
        ]);
        $user->assignRole($request->role);
        $decrypted = array(
            'nim' => $user->nim,
            'name' => $user->name,
            'password' => $user->password,
            'email' => $user->email,
            'role' => $user->role,
            'number' => $request->number
        );

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User created !',
                'data' => $decrypted
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error',
            'data' => ''
        ], 402);
    }

    public function me()
    {
        return response()->json([
            'status' => true,
            'message' => 'Data',
            'data' => User::all()
        ], 200);
    }

    public function logout()
    {
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out' 
        ]);
    }

    protected function auth(User $user)
    {
        $payload = [
            'iss' => "jwt-firebase", 
            'sub' => $user->id, 
            'iat' => time(), 
            'exp' => time() + 60 * 60
        ];
        return JWT::encode($payload, env('JWT_KEY'));
    }

    public function updateuser(Request $request, $id)
    {
        $this->validate($request, [
            'nim' => $request->nim ? 'required' : '',
            'name' => $request->name ? 'required' : '',
            'password' => $request->password ? 'required|min:8' : '',
            'number' => $request->number ? 'required' : '',
        ]);

        $user = User::find($id);
        $user->nim = $request->nim ? $request->nim : $user->nim;
        $user->name = $request->name ? $request->name : $user->name;
        $user->password = $request->password ? Hash::make($request->password) : Hash::make($user->password);
        $user->email = $request->email ? $request->email : $user->email;
        $user->number = $request->number ? $request->number : $user->number;
        $user->save();
        return response()->json([
            'status' => true,
            'Message' => 'Profile Updated',
            'data' => $user
        ], 200);
    }

    public function acces()
    {
        return 'Memiliki Akses';
    }

    public function sendmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns'
        ]);

        $user = User::where('email', $request->email)->first();
       
        if ($user) {
            $kode = Str::random(32);
            $data = ["code" => $kode];
            $cek = ResetPassword::where('email', $user->email)->first();
            if ($cek) {
                $cek->token = $kode;
                $cek->save();
                Mail::send('mail', $data, function ($message) use ($user) {
                    $message->to($user->email, 'Sherina Eria')->subject('Reset Password');
                    $message->from('hastalinas@gmail.com', 'Sherina Eria');
                });
                return response()->json([
                    'status' => true,
                    'message' => 'Cek email untuk mendapatkan kode'
                ], 200);
            }
            ResetPassword::create([
                'email' => $user->email,
                'token' => $kode
            ]);
            Mail::send('mail', $data, function ($message) use ($user) {
                $message->to($user->email, 'Sherina Eria')->subject('Kode Verifikasi Reset Password');
                $message->from('hastalinas@gmail.com', 'Sherina Eria');
            });
            return response()->json([
                'status' => true,
                'message' => 'Cek email untuk mendapatkan kode'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Email tidak terdaftar'
        ], 404);
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $reset = ResetPassword::where('email', $user->email)->first();
            if ($user->email == $reset->email && $reset->token == $request->header('token')) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Password sudah direset, Silahkan login dengan password baru'
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Email atau token tidak valid'
            ], 404);
        }
        return response()->json([
            'status' => false,
            'message' => 'Email tidak terdaftar'
        ], 404);
    }

    public function sendsms(Request $request)
    {
        $this->validate($request, [
            'number' => 'required'
        ]);

        $user = User::where('number', $request->number)->first();

        if ($user) {
            $otp = Str::random(6);
            $kode = Str::random(32);
            $data = ["code" => $otp];
            $basic  = new \Vonage\Client\Credentials\Basic("74ddb9f3", "o0h3oyn8H2dTUV5l");
            $client = new \Vonage\Client($basic);
            $cek = ResetPassword::where('number', $user->number)->first();
            if ($cek) {
                $cek->otp = $otp;
                $cek->save();
                $response = $client->sms($data)->send(
                    new \Vonage\SMS\Message\SMS("6285713493551", 'SMS GATEWAY', 'KODE OTP',$data)
                );
                $message = $response->current();

                if ($message->getStatus() == 0) {
                    return response()->json([
                        'status' => true,
                        'message' => 'The message was sent successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "The message failed with status: " . $message->getStatus() . "\n"
                    ], 400);
                }
            }

            ResetPassword::create([
                'number' => $user->number,
                'otp' => $otp,
                'email' => $user->email,
                'token' => $kode
            ]);

            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS("6285713493551", 'SMS GATEWAY', 'KODE OTP', $data)
            );
            return response()->json([
                'status' => true,
                'message' => 'Cek SMS untuk mendapatkan kode otp'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Nomor tidak terdaftar'
        ], 404);


        // $basic  = new \Vonage\Client\Credentials\Basic("74ddb9f3", "o0h3oyn8H2dTUV5l");
        // $client = new \Vonage\Client($basic);
        // $response = $client->sms()->send(
        //     new \Vonage\SMS\Message\SMS("6285713493551", 'SMS GATEWAY', 'Percobaan sms gateway')
        // );

        // $message = $response->current();

        // if ($message->getStatus() == 0) {
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'The message was sent successfully',
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "The message failed with status: " . $message->getStatus() . "\n"
        //     ], 400);
        // }
    }

    public function resetsms(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
            'password' => 'required|min:8'
        ]);

        $user = User::where('number', $request->number)->first();
        if ($user) {
            $reset = ResetPassword::where('number', $user->number)->first();
            if ($user->number == $reset->number && $reset->otp == $request->header('otp')) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Password sudah direset, Silahkan login dengan password baru'
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Nomor atau token tidak valid'
            ], 404);
        }
        return response()->json([
            'status' => false,
            'message' => 'Nomor tidak terdaftar'
        ], 404);
    }

    
}
