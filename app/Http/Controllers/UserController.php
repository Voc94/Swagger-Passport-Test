<?php

namespace App\Http\Controllers;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function send_invitation(Request $request)
    {
        $email = $request->input('email');
        $user = $request->user()->name;
        $encodedEmail = base64_encode($email);
        $link_users = route('user.receive-invitation', ['invitation' => base64_encode($encodedEmail)]);

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
        $email = decrypt(base64_decode($invitation));
        return response()->json(['message' => 'Invitation received for ' . $email ]);
    }
}
