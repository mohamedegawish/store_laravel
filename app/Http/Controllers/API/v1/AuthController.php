<?php
namespace App\Http\Controllers\api\V1;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);

        $token = $user->createToken('API-token')->plainTextToken;

        return response()->json(compact('user','token'));
    }

    public function login(Request $request)
    {
        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json(['message'=>'Invalid'],401);
        }

        $token = $user->createToken('API-token')->plainTextToken;

        return response()->json(compact('user','token'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message'=>'Logged out']);
    }
    public function user(Request $request){
        return response()->json($request->user());
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otp = rand(100000, 999999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json([
            'message' => 'OTP sent to email successfully'
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'=>'required',
            'otp'=>'required',
            'password'=>'required|min:6'
        ]);

        $record = DB::table('password_resets')
            ->where('email',$request->email)
            ->where('otp',$request->otp)
            ->first();

        if(!$record){
            return response()->json(['message'=>'Invalid OTP'],400);
        }

        User::where('email',$request->email)->update([
            'password'=>bcrypt($request->password)
        ]);

        DB::table('password_resets')->where('email',$request->email)->delete();

        return response()->json(['message'=>'Password updated']);
    }
}
