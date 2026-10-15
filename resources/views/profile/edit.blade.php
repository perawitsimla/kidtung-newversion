@extends('layouts.app')

@section('title', 'โปรไฟล์ | KidTung')

@section('content')
    <div class="profile-page">
        <div class="profile-heading">
            <div>
                <p class="page-eyebrow">ACCOUNT SETTINGS</p>
                <h1>โปรไฟล์ของฉัน</h1>
                <p class="page-description">จัดการข้อมูลที่ใช้ในกลุ่มและช่องทางรับเงินของคุณ</p>
            </div>
        </div>

        <div class="profile-layout-grid">
            <aside class="profile-overview-column">
                <section class="profile-identity-card">
                    <div class="profile-identity-top">
                        <span class="profile-large-avatar">{{ mb_substr($user->name, 0, 1) }}</span>
                        <span class="profile-active-dot" title="บัญชีของคุณ"></span>
                    </div>
                    <h2>{{ $user->name }}</h2>
                    <p class="profile-email">{{ $user->email }}</p>
                    <span class="profile-account-tag">บัญชี KidTung</span>
                    <div class="profile-identity-divider"></div>
                    <div class="profile-identity-meta"><span>สมาชิกตั้งแต่</span><strong>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</strong></div>
                    <div class="profile-identity-meta"><span>ช่องทางรับเงิน</span><strong>{{ $user->promptpay_no ? 'ตั้งค่าแล้ว' : 'ยังไม่ได้ตั้งค่า' }}</strong></div>
                </section>

               
            </aside>

            <section class="profile-settings-card">
                <div class="profile-settings-heading">
                    <span class="profile-settings-icon">⚙</span>
                    <div><h2>ข้อมูลส่วนตัว</h2><p>ข้อมูลเหล่านี้จะใช้แสดงในรายการและหน้ากลุ่ม</p></div>
                </div>

                @if(session('success'))
                    <div class="profile-success-message"><span>✓</span>{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="profile-error-message">
                        <strong>กรุณาตรวจสอบข้อมูล</strong>
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="profile-settings-form">
                    @csrf
                    @method('PATCH')
                    <div class="profile-field-group">
                        <label for="name">ชื่อที่แสดงในระบบ</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required>
                        <small>สมาชิกในกลุ่มจะเห็นชื่อนี้ในรายการแบ่งค่าใช้จ่าย</small>
                    </div>
                    <div class="profile-field-group">
                        <label for="email">อีเมล</label>
                        <input id="email" type="email" value="{{ $user->email }}" readonly disabled>
                        <small>อีเมลใช้เข้าสู่ระบบ และยังไม่สามารถเปลี่ยนจากหน้านี้ได้</small>
                    </div>

                    <div class="profile-form-section-divider"></div>

                    <div class="profile-settings-heading profile-payment-heading">
                        <span class="profile-settings-icon profile-payment-icon">฿</span>
                        <div><h2>ข้อมูลรับเงิน</h2><p>ใช้แสดงให้เพื่อนเมื่อถึงเวลาชำระหนี้</p></div>
                    </div>
                    <div class="profile-field-group">
                        <label for="promptpay_no">เบอร์หรือเลขพร้อมเพย์</label>
                        <input id="promptpay_no" type="text" name="promptpay_no" value="{{ old('promptpay_no', $user->promptpay_no) }}" maxlength="20" placeholder="เช่น เบอร์โทรศัพท์ที่ผูกพร้อมเพย์">
                        <small>ข้อมูลนี้เป็นตัวช่วยให้เพื่อนโอนเงินถูกบัญชี ตรวจสอบข้อมูลก่อนบันทึก</small>
                    </div>

                    <div class="profile-form-actions">
                        <a class="kt-button kt-button-secondary" href="{{ route('dashboard') }}">กลับหน้าภาพรวม</a>
                        <button class="kt-button kt-button-primary" type="submit">บันทึกข้อมูล</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
