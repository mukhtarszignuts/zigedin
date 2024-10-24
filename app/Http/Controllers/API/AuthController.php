<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordResetToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ForgotPasswordNotification;

class AuthController extends Controller
{
    /**
     * Process user login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return error(__('Invalid Credentials'));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ok(__('Logged in successfully'), ['user' => $user, 'token' => $token]);
    }


    public function registration(Request $request)
    {
        $request->validate([
            'first_name'=> 'required|string',
            'last_name' => 'nullable|string',
            'role'      => 'required|in:A,E,C',
            'email'     => 'required|email|unique:users,email|regex:/(.+)@(.+)\.(.+)/i',
            'phone'     => 'nullable',
            'password'  => ['required', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
            'city'      => 'nullable|string',
        ]);

        $data = $request->only('first_name','last_name', 'email', 'phone', 'password', 'city', 'headline','summary','role');
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return ok(__('Registration successfully..!'));
    }


    /*
     *Forget password for user
    */
    public function forgotPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        // check email is exist or not in user model
        $user = User::where('email', $request->email)->firstOrFail();

        $token = Str::random(20);

        PasswordResetToken::where('email', $request->email)->delete();

        PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token
        ]);

        Notification::send($user, new ForgotPasswordNotification($token, $request->email));

        return ok(__('Password reset link sent successfully'));
    }

    /*
     * Reset password  for user
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'token'            => 'required',
            'email'            => 'required|email|exists:users',
            'password'         => 'required|min:8',
            'confirm_password' => 'required|min:6|same:password',
        ]);

        $passwordReset = PasswordResetToken::where('token', $request->token)->where('email', $request->email)->firstOrFail();
        $user          = User::where('email', $request->email)->firstOrFail();
        $user->update([
            'password'   => Hash::make($request->password),
        ]);
        $passwordReset->where('email', $request->email)->where('token', $request->token)->delete();
        return ok(__('Password reset sucessfully'));
    }

    /**
     * logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return ok(__('Sucessfully logged out'));
    }

    /**
     * Change the user's password based on the provided request.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'current_password'  => 'required|min:8|string',
            'password'          => 'required|min:8|string|different:current_password',
            'confirm_password'  => 'required|min:8|string|same:password',
        ]);

        // Check if the provided current password matches the authenticated user's password
        if (Hash::check($request->current_password, Auth::user()->password)) {
            // Update the user's password with the new hashed password
            Auth::user()->update(['password' => Hash::make($request->password)]);

            // Return a success response
            return ok('Password changes successfully');
        }
        // Return an error response if the current password does not match
        return error('The current password is incorrect');
    }
}
