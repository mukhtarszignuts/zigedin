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
     * Process a user login attempt.
     *
     * Validates the request data, checks user credentials, and generates an authentication token if successful.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request containing login credentials.
     * @return \Illuminate\Http\JsonResponse  A JSON response indicating the result of the login attempt.
     *
     * @throws \Illuminate\Validation\ValidationException  If the validation fails for any of the input fields.
     */
    public function login(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email'    => 'required|exists:users,email',
            'password' => 'required',
        ]);

        // Retrieve the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and if the provided password matches the hashed password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return error(__('Invalid Credentials')); // Return an error response for invalid credentials
        }

        // Generate a new authentication token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Return a success response with user information and the token
        return ok(__('Logged in successfully'), ['user' => $user, 'token' => $token]);
    }

    /**
     * Register a new user.
     *
     * This function validates the incoming request data, creates a new user record in the database, 
     * and returns a success response upon successful registration.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request containing user data.
     * 
     * @return \Illuminate\Http\JsonResponse  A JSON response indicating the success of the registration.
     * 
     * @throws \Illuminate\Validation\ValidationException  If the validation fails for any of the input fields.
     * @throws \Illuminate\Database\QueryException  If there is an error during user creation.
     */
    public function registration(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'role'      => 'required|in:A,E,C',
            'email'     => 'required|email|unique:users,email|regex:/(.+)@(.+)\.(.+)/i',
            'phone'     => 'nullable',
            'password'  => ['required', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
            'city'      => 'nullable|string',
        ]);

        // Extract relevant data from the request for user creation
        $data = $request->only('first_name', 'last_name', 'email', 'phone', 'password', 'city', 'headline', 'summary', 'role');

        // Hash the password before storing it in the database
        $data['password'] = Hash::make($data['password']);

        // Create a new user record in the database with the provided data
        User::create($data);

        // Return a success response indicating registration was successful
        return ok(__('Registration successfully..!'));
    }


    /**
     * Handle the request to initiate the password reset process for the user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        // Validate the incoming request data to ensure email is provided and is valid
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        // check email is exist or not in user model
        $user = User::where('email', $request->email)->firstOrFail();

        // Generate a random token for password reset
        $token = Str::random(20);

        // Delete any existing password reset tokens for this email
        PasswordResetToken::where('email', $request->email)->delete();

        // Create a new password reset token record in the database
        PasswordResetToken::create([
            'email' => $user->email,
            'token' => $token
        ]);

        // Send a notification to the user with the password reset link
        Notification::send($user, new ForgotPasswordNotification($token, $request->email));

        // Return a success response indicating the reset link has been sent
        return ok(__('Password reset link sent successfully'));
    }

    /**
     * Reset the user's password based on the provided token and email.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function resetPassword(Request $request)
    {
        // Validate the incoming request data
        $this->validate($request, [
            'token'            => 'required',
            'email'            => 'required|email|exists:users',
            'password'         => 'required|min:8',
            'confirm_password' => 'required|min:6|same:password',
        ]);

        // Retrieve the password reset token for the given email and token
        $passwordReset = PasswordResetToken::where('token', $request->token)->where('email', $request->email)->firstOrFail();

        // Find the user associated with the provided email
        $user = User::where('email', $request->email)->firstOrFail();

        // Update the user's password with the new hashed password
        $user->update([
            'password'   => Hash::make($request->password),
        ]);

        // Delete the password reset token after successful password reset
        $passwordReset->delete();

        // Return a success response
        return ok(__('Password reset sucessfully'));
    }

    /**
     * Log out the authenticated user by deleting their current access token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Delete the current access token to log out the user
        $user->currentAccessToken()->delete();

        // Return a success response
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
