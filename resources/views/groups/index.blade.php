@extends('layouts.app')

@section('title', 'กลุ่มของฉัน | KidTung')

@section('content')
<div class="groups-page">
    <header class="groups-heading">
        <div class="groups-heading-copy">
            <p class="groups-eyebrow">WORKSPACE <span>/</span> GROUPS</p>
            <h1>กลุ่มของฉัน</h1>
            <p class="groups-intro">รวมกลุ่มที่ใช้แชร์ค่าใช้จ่ายไว้ในที่เดียว</p>
        </div>
        <a href="{{ route('groups.create') }}" class="kt-button kt-button-primary">
            <span class="kt-button-plus" aria-hidden="true">+</span>
            สร้างกลุ่มใหม่
        </a>
    </header>

    <section class="groups-summary" aria-label="สรุปกลุ่ม">
        <div class="groups-summary-number">{{ $groups->count() }}</div>
        <div class="groups-summary-copy">
            <strong>กลุ่มที่คุณเข้าร่วม</strong>
            <span>รายการกลุ่มที่คุณเป็นสมาชิกอยู่ในขณะนี้</span>
        </div>
    </section>

    <section class="groups-tools" aria-label="ค้นหาหรือเข้าร่วมกลุ่ม">
        <div class="groups-tool-card">
            <div class="groups-tool-heading">
                <span class="groups-tool-icon" aria-hidden="true">⌕</span>
                <div>
                    <h2>ค้นหากลุ่ม</h2>
                    <p>ค้นหาจากชื่อกลุ่มที่คุณเข้าร่วมแล้ว</p>
                </div>
            </div>
            <form method="GET" action="{{ route('groups.index') }}" class="groups-inline-form">
                <label class="sr-only" for="search">ชื่อกลุ่ม</label>
                <input type="search" name="search" id="search" value="{{ $search ?? '' }}" placeholder="พิมพ์ชื่อกลุ่ม...">
                <button type="submit" class="kt-button kt-button-dark">ค้นหา</button>
            </form>
        </div>

        <div class="groups-tool-card groups-join-card">
            <div class="groups-tool-heading">
                <span class="groups-tool-icon groups-tool-icon-code" aria-hidden="true">#</span>
                <div>
                    <h2>เข้าร่วมด้วยรหัส</h2>
                    <p>ใส่รหัสเชิญ 4 ตัวที่เพื่อนส่งให้</p>
                </div>
            </div>
            <form method="POST" action="{{ route('groups.join') }}" class="groups-inline-form">
                @csrf
                <label class="sr-only" for="invite_code">รหัสเชิญ 4 ตัว</label>
                <input type="text" name="invite_code" id="invite_code" maxlength="4" minlength="4" required autocomplete="off" autocapitalize="characters" placeholder="เช่น A7K2" value="{{ old('invite_code') }}" class="groups-invite-input">
                <button type="submit" class="kt-button kt-button-primary">เข้าร่วม</button>
            </form>
        </div>
    </section>

    <section class="groups-list-section" aria-labelledby="groups-list-title">
        <div class="groups-section-heading">
            <div>
                <h2 id="groups-list-title">กลุ่มที่คุณอยู่</h2>
                <p>เปิดกลุ่มเพื่อดูรายการและจัดการค่าใช้จ่าย</p>
            </div>
            <span class="groups-result-count">{{ $groups->count() }} กลุ่ม</span>
        </div>

        @if ($groups->isNotEmpty())
            <div class="groups-grid">
                @foreach ($groups as $group)
                    <article class="group-card">
                        <div class="group-card-topline">
                            <span class="group-monogram" aria-hidden="true">{{ mb_substr($group->name, 0, 1) }}</span>
                            @if ($group->pivot->role === 'admin')
                                <span class="group-role group-role-admin">ผู้ดูแล</span>
                            @else
                                <span class="group-role">สมาชิก</span>
                            @endif
                        </div>

                        <h3 class="group-card-title">{{ $group->name }}</h3>
                        <p class="group-member-count">{{ $group->members_count }} คนในกลุ่ม</p>

                        <div class="group-invite-row">
                            <span>รหัสเชิญ</span>
                            <code>{{ $group->invite_code }}</code>
                        </div>

                        <a href="{{ route('groups.show', $group->id) }}" class="group-open-link">
                            เปิดหน้ากลุ่ม
                            <span aria-hidden="true">→</span>
                        </a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="groups-empty-state">
                <div class="groups-empty-mark" aria-hidden="true">＋</div>
                @if (!empty($search))
                    <h3>ไม่พบกลุ่มที่ตรงกับคำค้นหา</h3>
                    <p>ลองใช้คำค้นอื่น หรือค้นหาด้วยชื่อที่สั้นลง</p>
                    <a href="{{ route('groups.index') }}" class="groups-empty-link">ล้างคำค้นหา</a>
                @else
                    <h3>ยังไม่มีกลุ่มที่คุณเข้าร่วม</h3>
                    <p>สร้างกลุ่มใหม่ หรือเข้าร่วมกลุ่มด้วยรหัสเชิญจากเพื่อน</p>
                    <a href="{{ route('groups.create') }}" class="groups-empty-link">สร้างกลุ่มแรกของคุณ <span aria-hidden="true">→</span></a>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
