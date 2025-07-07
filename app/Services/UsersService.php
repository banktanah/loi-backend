<?php

namespace App\Services;

use App\User;
use App\Models\Investor;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersService
{
     /**
     * Register a new User and their Investor Profile in a single transaction.
     * @param array $data Complete data, including the company_profile_url
     * @return User
     * @throws Exception
     */
    public function registerInvestorAndUser(array $data)
    {
        // Validations for existence are still good
        if (User::where('email', $data['email'])->exists()) {
            throw new Exception("User with this email already exists.", 409);
        }
        if (Investor::where('investor_id', $data['investor_id'])->exists()) {
            throw new Exception("Investor with this NIB/KTP already exists.", 409);
        }

        return DB::transaction(function () use ($data) {
            // 1. Create the User
            $user = new User([
                'email' => $data['email'],
                'status' => 'pending',
                'role' => 'investor',
            ]);
            $user->password_hash = Hash::make($data['password']);
            $user->save();

            // 2. Prepare Investor data
            $investorData = $data;
            $investorData['user_id'] = $user->id;

            // CHANGE: Assign the generated URL to the company_profile column
            $investorData['company_profile'] = $data['company_profile_url'];

            $investor = new Investor($investorData);
            $investor->save();
            
            return $user->load('investor');
        });
    }

    /**
     * Attempt to log in a user and generate a JWT using a manual check.
     * @param array $credentials
     * @return string|null The JWT token or null if login fails
     * @throws Exception
     */
    public function loginUser(array $credentials): ?string
    {
        // 1. Find the user by their email address.
        $user = User::where('email', $credentials['email'])->first();

        // 2. Check if a user was found AND if the provided password matches the stored hash.
        if (!$user || !Hash::check($credentials['password'], $user->getAuthPassword())) {
            // If either check fails, the login is invalid. Return null.
            return null;
        }

        // --- At this point, credentials are valid. ---

        // Check if the user's account is active.
        if ($user->status !== 'active') {
            throw new Exception("Your account is not active. Please contact support.", 403);
        }
        
        // 3. Log the user in with the 'api' guard and generate a token.
        // Auth::guard('api')->login($user) returns the JWT string directly.
        $token = Auth::guard('api')->login($user);
        
        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        return $token;
    }
}