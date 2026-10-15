<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Log in') }}</title>
    
    <!-- ดึงไฟล์ CSS ที่เราสร้างใหม่ -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

    <div class="auth-container">
        
        <div class="kidtung">KidTung</div>
        <div class="auth-header">
            <h2>เข้าสู่ระบบ</h2>
            
        </div>

        <!-- แจ้งเตือนสถานะต่างๆ เช่น รีเซ็ตรหัสผ่านสำเร็จ -->
        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input @error('email') is-invalid @enderror" required autofocus placeholder="เช่น iteiei@gmail.com"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" id="password" name="password" class="form-input @error('password') is-invalid @enderror" required placeholder="กรอกรหัสผ่าน"
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>


            <!-- Submit Button -->
            <button type="submit" class="auth-button">
                เข้าสู่ระบบ
            </button>
        </form>

        <div class="auth-footer">
            ถ้าไม่มีบัญชีคลิ๊กตรงนี้เลย 
            <a href="{{ route('register') }}" class="auth-link">สมัครสมาชิก</a>
        </div>
    </div>

</body>
</html>