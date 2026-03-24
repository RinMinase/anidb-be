<x-mail::layout>
  <x-slot:header>
    <tr>
      <td class="header" style="text-align: center; padding: 25px 0;">
        <a href="{{ config('app.url') }}" style="display: inline-block;">Rin's AniDB</a>
      </td>
    </tr>
  </x-slot:header>

  # Hello!

  You are receiving this email because we received a password reset request for your account.

  <x-mail::button :url="$url">
    Reset Password
  </x-mail::button>

  This password reset link will expire in {{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') }} minutes.

  If you did not request a password reset, no further action is required.

  Regards,<br>
  System Admin

  <hr style="border: 0; border-top: 1px solid #e8e5ef; margin: 20px 0;">

  <x-slot:subcopy>
    <div style="font-size: 14px;">
      If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
      <br>
      <a href="{{ $url }}" style="word-break: break-all; color: #18181b">{{ $url }}</a>
    </div>
  </x-slot:subcopy>

  <x-slot:footer>
    <tr>
      <td class="footer" style="text-align: center; padding: 32px 0;"></td>
    </tr>
  </x-slot:footer>
</x-mail::layout>
