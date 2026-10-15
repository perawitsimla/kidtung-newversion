<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f7f7f4">
    <title>@yield('title', 'KidTung')</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kidtung-ui.css') }}">
    @yield('head_scripts')
</head>
<body class="kt-app">
    <div class="kt-layout">
        <aside class="kt-nav" aria-label="เมนูหลัก">
            <div class="kt-nav-top">
                <a class="kt-brand" href="{{ url('/dashboard') }}" aria-label="KidTung - ภาพรวม">
                    <span class="kt-brand-mark" aria-hidden="true">฿</span>
                    <span class="kt-brand-copy">
                        <strong>KidTung</strong>
                        <small>แชร์ค่าใช้จ่ายกับเพื่อน</small>
                    </span>
                </a>

                <p class="kt-nav-label">เมนูหลัก</p>
                <nav aria-label="พื้นที่ทำงาน">
                    <ul class="kt-nav-list">
                        <li>
                            <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}" {{ request()->is('dashboard') ? 'aria-current=page' : '' }}>
                                <span class="kt-nav-mark" aria-hidden="true">⌂</span>
                                <span>ภาพรวม</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('groups.index') }}" class="{{ request()->is('groups*') ? 'active' : '' }}" {{ request()->is('groups*') ? 'aria-current=page' : '' }}>
                                <span class="kt-nav-mark" aria-hidden="true">▦</span>
                                <span>กลุ่มของฉัน</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settlements.index') }}" class="{{ request()->is('settlements*') ? 'active' : '' }}" {{ request()->is('settlements*') ? 'aria-current=page' : '' }}>
                                <span class="kt-nav-mark" aria-hidden="true">↔</span>
                                <span>รายการหนี้</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('history') }}" class="{{ request()->is('history') ? 'active' : '' }}" {{ request()->is('history') ? 'aria-current=page' : '' }}>
                                <span class="kt-nav-mark" aria-hidden="true">◷</span>
                                <span>ประวัติ</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="kt-nav-bottom">
                <div class="kt-account">
                    <span class="kt-account-avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    <span class="kt-account-details">
                        <strong>{{ auth()->user()->name ?? 'ผู้ใช้งาน' }}</strong>
                        <small>บัญชีของคุณ</small>
                    </span>
                </div>
                <ul class="kt-nav-list kt-nav-secondary">
                    <li>
                        <a href="{{ route('profile.edit') }}" class="{{ request()->is('profile*') ? 'active' : '' }}" {{ request()->is('profile*') ? 'aria-current=page' : '' }}>
                            <span class="kt-nav-mark" aria-hidden="true">⚙</span>
                            <span>โปรไฟล์</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="kt-logout-button">
                                <span class="kt-nav-mark" aria-hidden="true">↪</span>
                                <span>ออกจากระบบ</span>
                            </button>
                        </form>
                    </li>
                </ul>
                <p class="kt-sidebar-footnote">KidTung <span>·</span> จัดการเงินให้ชัดเจน</p>
            </div>
        </aside>

        <main class="kt-main">
            <header class="kt-topbar">
                <span class="kt-topbar-label">หารด่วนเลยเดี๋ยวมีปัญหา</span>
                <span class="kt-topbar-user">
                    <span class="kt-topbar-dot" aria-hidden="true"></span>
                    {{ auth()->user()->name ?? 'ผู้ใช้งาน' }}
                </span>
            </header>

            <div class="kt-page-content">
                @foreach (['success', 'error', 'info', 'status'] as $messageType)
                    @if (session($messageType))
                        <div class="flash-message {{ $messageType === 'success' ? 'alert-success' : ($messageType === 'error' ? 'alert-error' : '') }}" role="status">
                            {{ session($messageType) }}
                        </div>
                    @endif
                @endforeach

                @if ($errors->any())
                    <div class="alert-error kt-validation-alert" role="alert">
                        <strong>กรุณาตรวจสอบข้อมูลอีกครั้ง</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
            
        </main>
    </div>
</body>
</html>
