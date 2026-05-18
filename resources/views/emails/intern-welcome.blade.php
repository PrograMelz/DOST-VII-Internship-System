<div style="max-width: 600px; margin: auto; padding: 20px; background-color: #ffffff; border-radius: 8px; font-family: Arial, sans-serif; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="color: #333; text-align: center; margin-bottom: 20px;">Welcome to the Internship System</h2>
    <p style="font-size: 18px; font-weight: bold;">Hello {{ $recipientName }},</p>
    <p style="font-size: 16px;">Your intern account has been created. Use the credentials below to log in for the first time:</p>

    <div style="background-color: #e8f4fd; padding: 20px; border-radius: 8px; margin: 24px 0; border: 2px solid #2E86C1;">
        <h3 style="color: #1B4F72; margin-top: 0;">Your login credentials</h3>
        <p style="margin: 8px 0;"><strong>Username:</strong> <code style="background: #fff; padding: 4px 8px; border-radius: 4px;">{{ $username }}</code></p>
        <p style="margin: 8px 0;"><strong>Password:</strong> <code style="background: #fff; padding: 4px 8px; border-radius: 4px;">{{ $password }}</code></p>
    </div>

    <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #ffc107;">
        <h4 style="color: #856404; margin-top: 0;">Important:</h4>
        <ul style="color: #856404; margin: 0; padding-left: 20px;">
            <li>Log in at the intern portal and change your password after your first login.</li>
            <li>Keep your credentials secure and do not share them with others.</li>
        </ul>
    </div>

    <hr style="margin: 20px 0;">
    <p style="font-size: 12px; color: #999; text-align: center;">This is a system-generated message. Please do not reply to this email.</p>
</div>
