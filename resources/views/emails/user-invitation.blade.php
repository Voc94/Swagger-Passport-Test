<!DOCTYPE html>
<html>
<head>
    <title>Invitation Email</title>
</head>
<body>
<p>Hey {{ $email }}</p>
<p>{{ $user }} has invited you to join our platform.</p>
<p>Click <a href="{{ $link_users }}">here</a> to accept the invitation.</p>
</body>
</html>
