<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MCustomer;
use App\Models\OtpCode;
use App\Services\ThirdPartyOtpService;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(ThirdPartyOtpService $otpService)
    {
        $this->otpService = $otpService;
    }

   
    public function checkEmail(Request $req)
    {
        $v = Validator::make($req->all(), ['email'=>'required|email']);
        if ($v->fails()) {
            return response()->json([ 'status' => false, 'message' => 'Invalid email', 'data'=>null ], 422);
        }

        $email = $req->email;
        $exists = MCustomer::where('email', $email)->exists();

        if ($exists) {
            return response()->json([[
                'status' => true,
                'message' => 'The email is registered',
                'data' => ['email' => $email]
            ]]);
        } else {
            return response()->json([[
                'status' => false,
                'message' => 'The email is not registered',
                'data' => ['email' => $email]
            ]]);
        }
    }

    public function login(Request $req)
    {
        $v = Validator::make($req->all(), ['email'=>'required|email']);
        if ($v->fails()) {
            return response()->json([ 'status' => false, 'message' => 'Invalid email', 'data'=>null ], 422);
        }

        $email = $req->email;
        $customer = MCustomer::where('email', $email)->first();

        if (!$customer) {
            return response()->json([[
                'status' => false,
                'message' => 'Login is failed',
                'data' => null
            ]]);
        }


        return response()->json([[
            'status' => true,
            'message' => 'Login is successful',
            'data' => [
                'email' => $customer->email,
                'customer_id' => (string)$customer->customer_id,
                'customer_name' => $customer->name ?? null
            ]
        ]]);
    }


    public function generateOtp(Request $req)
    {
        $v = Validator::make($req->all(), ['email'=>'required|email']);
        if ($v->fails()) {
            return response()->json([ 'status' => false, 'message' => 'Invalid email', 'data'=>null ], 422);
        }

        $email = $req->email;


        $otp = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

        $now = Carbon::now();
        $expires = $now->copy()->addMinutes(5);


        $record = OtpCode::create([
            'email' => $email,
            'otp' => $otp,
            'created_at' => $now->toDateTimeString(),
            'expires_at' => $expires->toDateTimeString(),
            'used' => false
        ]);


        $message = "Your OTP code is: {$otp}";
        $sent = $this->otpService->send($email, $message);

        if (!$sent) {
            return response()->json([[
                'status' => false,
                'message' => 'OTP Code failed to generate',
                'data' => null
            ]]);
        }

        return response()->json([[
            'status' => true,
            'message' => 'OTP Code generated successfully',
            'data' => [
                'otp' => $otp,
                'email' => $email,
                'created_at' => $now->toDateTimeString()
            ]
        ]]);
    }


    public function verifyOtp(Request $req)
    {
        $v = Validator::make($req->all(), [
            'email'=>'required|email',
            'otp' => 'required|string'
        ]);
        if ($v->fails()) {
            return response()->json([ 'status' => 'failed', 'message' => 'Invalid input', 'data'=>new \stdClass() ], 422);
        }

        $email = $req->email;
        $otp = $req->otp;

        $record = OtpCode::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->orderBy('created_at','desc')
            ->first();

        if (!$record) {
            return response()->json([[
                'status' => 'failed',
                'message' => 'OTP Code not found',
                'data' => new \stdClass()
            ]]);
        }

        if (Carbon::now()->gt(Carbon::parse($record->expires_at))) {
            return response()->json([[
                'status' => 'expired',
                'message' => 'OTP Code has been expired',
                'data' => new \stdClass()
            ]]);
        }


        $record->used = true;
        $record->save();

        return response()->json([[
            'status' => 'ok',
            'message' => 'OTP Code is valid',
            'data' => new \stdClass()
        ]]);
    }



    public function registerCustomer(Request $req)
    {
        $v = Validator::make($req->all(), [
            'email'=>'required|email',
            'name'=>'required|string',
            'password'=>'nullable|string|min:6'
        ]);
        if ($v->fails()) {
            return response()->json([ 'status' => false, 'message' => 'Invalid input', 'data'=>null ], 422);
        }

        $email = $req->email;

        if (MCustomer::where('email',$email)->exists()) {
            return response()->json([[
                'status' => false,
                'message' => 'Customer failed to insert (already exists)',
                'data' => null
            ]]);
        }

        $customer = MCustomer::create([
            'email' => $email,
            'name' => $req->name,
            'password' => $req->password ? Hash::make($req->password) : null
        ]);

        if (!$customer) {
            return response()->json([[
                'status' => false,
                'message' => 'Customer failed to insert',
                'data' => null
            ]]);
        }

        return response()->json([[
            'status' => true,
            'message' => 'Customer has been successfully inserted',
            'data' => [
                'customer_id' => (string)$customer->customer_id,
                'email' => $customer->email,
                'created_at' => $customer->created_at->toDateTimeString()
            ]
        ]]);
    }
}
