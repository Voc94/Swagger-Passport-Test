<?php

namespace App\Http\Controllers;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function send_invitation(Request $request)
    {
        $email = $request->input('email');
        $user = $request->user()->name;
        $encodedEmail = base64_encode($email);
        $link_users = route('user.receive-invitation', ['invitation' => $encodedEmail]);

        $invitation = Invitation::create([
            'user_id' => $request->user()->id,
            'email' => $encodedEmail,
            'status' => 0
        ]);

        Mail::to($email)->send(new InvitationEmail($email, $user, $link_users));

        return response()->json(['message' => 'Invitation sent successfully!']);
    }

    public function receive_invitation(Request $request, $invitation)
    {
        $decodedEmail = base64_decode($invitation);

        Log::info('Receiving invitation', ['invitation' => $invitation, 'decodedEmail' => $decodedEmail]);

        $user = $request->user();

        if ($user) {
            $invitationRecord = Invitation::where('email', base64_encode($decodedEmail))->where('status', 0)->first();

            if ($invitationRecord) {
                if ($invitationRecord->user_id !== $user->id) {
                    $invitingUser = User::find($invitationRecord->user_id);

                    $invitingUser->friends()->attach($user->id);
                    $user->friends()->attach($invitationRecord->user_id);

                    $invitationRecord->status = 1;
                    $invitationRecord->save();

                    Log::info('Friends relationship created and invitation status updated', ['invitingUser' => $invitingUser->id, 'newUser' => $user->id]);

                    return response()->json(['message' => 'Invitation accepted and friendship created!']);
                } else {
                    Log::warning('Inviting user and new user are the same', ['user' => $user->id]);
                    return response()->json(['error' => 'You cannot accept your own invitation.'], 400);
                }
            } else {
                Log::error('Invitation not found or already used', ['invitationEmail' => $decodedEmail]);
                return response()->json(['error' => 'Invitation not found or already used.'], 404);
            }
        } else {
            return response()->json(['message' => 'Invitation received for ' . $decodedEmail . '. Please log in or register to accept the invitation.']);
        }
    }
}
