<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
    <h2>Welcome to {{ config('app.name') }}</h2>
    <p>
        It seems like you forgot your password for {{ config('app.name') }}. If this is true, click the link below to reset your password.
        
        <br><br>

        <a href="{{ $resetPasswordLink }}">reseting my password link</a>

        <br><br>

        or copy this link and paste to your browser please:

        <br><br>

        <p style="text-decoration: underline; word-break: break-all; width: 800px; background: #eee; border: 1px solid #aeaeae; border-radius: 0.8rem; padding: 8px;">{{ $resetPasswordLink }}</p>

        <br><br>

        If you did not forget your password, please disregard this email.

        <br><br>

        <h4 style="text-align: center">{{ config('app.name') }}</h4>
        <hr>
        <small style="display: block; text-align: center;">User support management staff | {{ now()->year }}</small>
    </p>
</body>
</html>