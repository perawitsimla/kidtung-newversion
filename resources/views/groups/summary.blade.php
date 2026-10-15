@extends('layouts.app')

@section('content')
    <!-- ส่วนลิงก์กลับหน้าเดิม (Block element) -->
    <div>
        <a href="{{ route('groups.show', $group->id) }}">Back to Group</a>
    </div>

    <!-- ส่วนหัวข้อ (Block element) -->
    <div>
        <h1>สรุปการเคลียร์หนี้ (Settlements)</h1>
        <h2>กลุ่ม: {{ $group->name }}</h2>
    </div>

    <!-- ส่วนตาราง (Block element) -->
    <div>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ลูกหนี้</th>
                    <th>เจ้าหนี้</th>
                    <th>ยอดค้างชำระ</th>
                    <th>สถานะ</th>
                    <th>รายการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaryRows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    
                    <td @if(Auth::id() == $row['debtor']->id) style="font-weight: bold; color: blue;" @endif>
                        {{ $row['debtor']->name }}
                        @if(Auth::id() == $row['debtor']->id) 
                            (คุณ) 
                        @endif
                    </td>
                    
                    <td @if(Auth::id() == $row['creditor']->id) style="font-weight: bold; color: blue;" @endif>
                        {{ $row['creditor']->name }}
                        @if(Auth::id() == $row['creditor']->id) 
                            (คุณ) 
                        @endif
                    </td>
                    
                    <td>{{ number_format($row['amount'], 2) }}</td>
                    
                    <td style="color: {{ $row['status_color'] }}; font-weight: bold;">
                        {{ $row['status'] }}
                    </td>
                    
                    <td>
                        @if($row['action'] === 'pay')
                            @if(Auth::id() == $row['debtor']->id)
                                <a href="{{ route('settlements.pay', ['group' => $group->id, 'creditor' => $row['creditor']->id]) }}">ชำระเงิน</a>
                            @else
                                -
                            @endif
                        @elseif($row['action'] === 'detail')
                            <a href="{{ route('settlements.show', $row['settlement_id']) }}">รายละเอียด</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
<br><br>
 <h2>ประวัติการชำระเงิน (Payment History)</h2>
    <!-- ส่วนตารางประวัติการโอนเงิน (แสดงทิ้งไว้เป็นหลักฐานตลอดไป) -->
    <div>
        
       
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background-color: #f9f9f9;">
                <tr>
                    <th>วันที่โอน</th>
                    <th>ผู้โอน</th>
                    <th>ผู้รับ</th>
                    <th>จำนวนเงิน</th>
                    <th>สถานะ</th>
                    <th>หลักฐาน</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($settlements as $history)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($history->transfer_datetime)->format('d/m/Y H:i') }}</td>
                    <td>{{ $history->debtor->name }}</td>
                    <td>{{ $history->creditor->name }}</td>
                    <td>{{ number_format($history->amount, 2) }}</td>
                    <td>
                        @if($history->status === 'approved')
                            <span style="color: green;">ชำระแล้ว</span>
                        @elseif($history->status === 'rejected')
                            <span style="color: red;">ปฏิเสธ</span>
                        @else
                            <span style="color: orange;">รอยืนยัน</span>
                        @endif
                    </td>
                    <td><a href="{{ route('settlements.show', $history->id) }}">ดูสลิป</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">ยังไม่มีประวัติการโอนเงินในกลุ่มนี้</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection