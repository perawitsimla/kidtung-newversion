@extends('layouts.app')

@section('content')
    <!-- ส่วนลิงก์กลับหน้าเดิม -->
    <div>
        <a href="{{ route('groups.summary', $group->id) }}">Back to Summary</a>
    </div>

    <!-- ส่วนหัวข้อ -->
    <div>
        <h1>ชำระเงิน (Settle Up)</h1>
        <h2>กลุ่ม: {{ $group->name }}</h2>
    </div>

    <hr>

    <!-- ส่วนข้อมูลการโอนเงิน -->
    <div>
        <p><strong>โอนเงินให้:</strong> {{ $creditor->name }}</p>
        <p><strong>เบอร์ PromptPay:</strong> {{ $creditor->promptpay_no ?? 'ไม่ได้ระบุเบอร์' }}</p>
        <p><strong>ยอดค้างชำระ:</strong> <span style="color: red; font-weight: bold;">{{ number_format($remainingDebt, 2) }} บาท</span></p>
        <p><i>(กรุณาโอนเงินตามยอดที่ระบุ และแนบสลิปด้านล่าง)</i></p>
    </div>

    <!-- ส่วนฟอร์มอัปโหลดสลิป -->
    <div>
        <form action="{{ route('settlements.store', ['group' => $group->id, 'creditor' => $creditor->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- ซ่อนยอดเงินไว้ เพื่อส่งไปเซฟใน Database (บังคับจ่ายเต็ม) -->
            <input type="hidden" name="amount" value="{{ $remainingDebt }}">

            <!-- ช่องกรอกวันเวลา -->
            <div style="margin-top: 15px;">
                <label for="transfer_datetime"><b>วันและเวลาที่โอน (ตามสลิป):</b></label><br><br>
                <input type="datetime-local" id="transfer_datetime" name="transfer_datetime" required>
            </div>

            <div style="margin-top: 15px; margin-bottom: 15px;">
                <label for="slip_image"><b>อัปโหลดสลิปโอนเงิน:</b></label><br><br>
                <input type="file" id="slip_image" name="slip_image" required accept="image/*">
            </div>

            <div>
                <button type="submit">บันทึกการชำระเงิน</button>
            </div>
        </form>
    </div>
@endsection