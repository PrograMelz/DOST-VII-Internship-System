<div style="max-width: 600px; margin: auto; padding: 20px; background-color: #ffffff; border-radius: 8px; font-family: Arial, sans-serif; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="color: #333; text-align: center; margin-bottom: 20px;">Verify your new email address</h2>
    <p style="font-size: 18px; font-weight: bold;">Hello {{ $recipientName }},</p>
    <p style="font-size: 16px;">You requested to change your email address. Use the 6-digit code below to confirm that you own this email:</p>

    <div style="text-align: center; margin: 24px 0;">
        <div style="display: inline-block; background-color: #e8f4fd; padding: 16px 28px; border-radius: 8px; border: 2px solid #2E86C1;">
            <span style="font-size: 28px; font-weight: 700; letter-spacing: 0.25em; color: #1B4F72;">{{ $code }}</span>
        </div>
    </div>

    <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #ffc107;">
        <h4 style="color: #856404; margin-top: 0;">Important:</h4>
        <ul style="color: #856404; margin: 0; padding-left: 20px;">
            <li>This code expires in 15 minutes.</li>
            <li>If you did not request this change, you can ignore this email. Your email address will not be changed.</li>
        </ul>
    </div>

    <hr style="margin: 20px 0;">
    <p style="font-size: 12px; color: #999; text-align: center;">This is a system-generated message. Please do not reply to this email.</p>
</div>
