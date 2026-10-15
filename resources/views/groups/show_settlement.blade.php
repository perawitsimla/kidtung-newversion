@extends('layouts.app')

@section('content')
    <!-- ส่วนลิงก์กลับ -->
    <div>
        <a href="{{ route('groups.summary', $settlement->group_id) }}">ย้อนกลับ</a>
    </div>

    <!-- ส่วนหัวข้อ -->
    <div>
        <h1>รายละเอียด</h1>
    </div>

    <!-- ส่วนข้อมูลการโอน -->
    <div>
        <p>ชื่อลูกหนี้ : {{ $settlement->debtor->name }}</p>
        <p>ชื่อเจ้าหนี้ : {{ $settlement->creditor->name }}</p>
        <p>ยอดค้างชำระ : {{ number_format($settlement->amount, 2) }} บาท</p>
        
        <p>สถานะ : 
            @if($settlement->status === 'approved')
                <span style="color: green; font-weight: bold;">ชำระแล้ว</span>
            @elseif($settlement->status === 'rejected')
                <span style="color: red; font-weight: bold;">ปฏิเสธ</span>
            @else
                <span style="color: #d48806; font-weight: bold;">รอยืนยัน</span>
            @endif
        </p>
        
        <p>เวลาที่ชำระ : {{ \Carbon\Carbon::parse($settlement->transfer_datetime)->format('H:i (d/m/Y)') }}</p>
    </div>

    <!-- ส่วนแสดงรูปสลิป (ถ้ามี) -->
    @if($settlement->slip_image)
    <div style="margin-top: 20px;">
        <img src="{{ asset('storage/' . $settlement->slip_image) }}" alt="Slip Image" style="max-width: 300px; border: 1px solid #ccc;">
    </div>
    @endif

    <!-- ส่วนปุ่ม Action (แสดงเฉพาะเจ้าหนี้ และสถานะต้องเป็น pending เท่านั้น) -->
    @if(Auth::id() == $settlement->creditor_id && $settlement->status === 'pending')
    <div style="margin-top: 20px;">
        <form action="{{ route('settlements.approve', $settlement->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')
            <button type="submit" style="color: green; padding: 5px 10px; cursor: pointer;">ยืนยันรับเงิน</button>
        </form>

        <form action="{{ route('settlements.reject', $settlement->id) }}" method="POST" style="display:inline; margin-left: 10px;">
            @csrf
            @method('PATCH')
            <button type="submit" style="color: red; padding: 5px 10px; cursor: pointer;" onclick="return confirm('แน่ใจหรือไม่ว่าต้องการปฏิเสธสลิปนี้?');">ปฏิเสธ</button>
        </form>
    </div>
    @endif

@endsection