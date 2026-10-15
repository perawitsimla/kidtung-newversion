@extends('layouts.app')

@section('content')
    <div>
        <h1>ประวัติการทำรายการ (Transaction History)</h1>
        <p>รวมประวัติการรับ-จ่ายเงินของคุณจากทุกกลุ่ม</p>
    </div>

    <hr>

    <div>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th>วัน-เวลาที่โอน</th>
                    <th>กลุ่ม</th>
                    <th>ผู้โอน (ลูกหนี้)</th>
                    <th>ผู้รับ (เจ้าหนี้)</th>
                    <th>จำนวนเงิน</th>
                    <th>สถานะ</th>
                    <th>หลักฐาน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $item)
                <tr>
                    <td>{{ $item->transfer_datetime ? \Carbon\Carbon::parse($item->transfer_datetime)->format('d/m/Y H:i') : '-' }}</td>
                    <td><a href="{{ route('groups.show', $item->group_id) }}">{{ $item->group->name }}</a></td>
                    
                    <!-- ไฮไลท์ชื่อเราให้ดูง่ายขึ้น -->
                    <td @if($item->debtor_id == Auth::id()) style="font-weight: bold; color: blue;" @endif>
                        {{ $item->debtor->name }}
                    </td>
                    
                    <td @if($item->creditor_id == Auth::id()) style="font-weight: bold; color: blue;" @endif>
                        {{ $item->creditor->name }}
                    </td>
                    
                    <td>{{ number_format($item->amount, 2) }}</td>
                    <td>
                        @if($item->status === 'approved')
                            <span style="color: green;">ชำระแล้ว</span>
                        @elseif($item->status === 'rejected')
                            <span style="color: red;">ปฏิเสธ</span>
                        @else
                            <span style="color: orange;">รอยืนยัน</span>
                        @endif
                    </td>
                    <td>
                        <!-- ลิงก์ไปดูสลิปหน้ารายละเอียด -->
                        <a href="{{ route('settlements.show', $item->id) }}" style="color: blue;">ดูสลิป</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: gray;">-- ยังไม่มีประวัติการทำรายการ --</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection