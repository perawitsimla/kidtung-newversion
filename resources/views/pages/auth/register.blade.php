<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Register') }}</title>
    
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

    <div class="auth-container">
        
        <div class="kidtung">KidTung</div>

        <div class="auth-header">
            <h2>สมัครสมาชิก</h2>
        </div>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name" class="form-label">ชื่อเล่น</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    class="form-input @error('name') is-invalid @enderror" 
                    required 
                    autofocus 
                    placeholder="เช่น Kiw"
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">อีเมล</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="form-input @error('email') is-invalid @enderror" 
                    required 
                    placeholder="เช่น iteiei@gmail.com"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') is-invalid @enderror" 
                    required 
                    placeholder="กรอกรหัสผ่าน"
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input" 
                    required 
                    placeholder="กรอกรหัสผ่านต้องตรงกับข้างบน"
                >
            </div>

            <!-- Submit Button -->
            <button type="submit" class="auth-button" style="margin-top: 15px;">
                สมัครสมาชิก
            </button>
        </form>

        <div class="auth-footer">
            ถ้าเกิดเป็นสมาชิกอยู่แล้ว
            <a href="{{ route('login') }}" class="auth-link">ล้อคอิน</a>
        </div>
    </div>

</body>
</html>