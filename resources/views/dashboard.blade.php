@extends('layouts.app')

@section('title', 'ภาพรวม | KidTung')

@section('content')
    <div class="dashboard-page">
        <div class="dashboard-heading">
            <div>
                <p class="page-eyebrow">YOUR MONEY, IN ONE PLACE</p>
                <h1>ภาพรวมของฉัน</h1>
                <p class="page-description">สวัสดี {{ Auth::user()->name }} — นี่คือสถานะค่าใช้จ่ายและรายการที่ต้องจัดการของคุณ</p>
            </div>
            <a class="kt-button kt-button-primary" href="{{ route('groups.create') }}"><span class="button-plus">＋</span> สร้างกลุ่มใหม่</a>
        </div>

        <section class="dashboard-stats-grid" aria-label="สถิติภาพรวม">
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>ยอดบิลในกลุ่ม</span><span class="stat-icon stat-icon-orange">฿</span></div>
                <strong class="dashboard-stat-value">฿{{ number_format($totalGroupSpending, 2) }}</strong>
                <p>รวมบิลของทุกกลุ่มที่คุณอยู่</p>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>ยอดที่ต้องจ่าย</span><span class="stat-icon stat-icon-red">↗</span></div>
                <strong class="dashboard-stat-value stat-value-red">฿{{ number_format($totalPayable, 2) }}</strong>
                <p>หนี้คงเหลือหลังหักยอดที่ยืนยันแล้ว</p>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>ยอดที่ต้องได้รับ</span><span class="stat-icon stat-icon-green">↙</span></div>
                <strong class="dashboard-stat-value stat-value-green">฿{{ number_format($totalReceivable, 2) }}</strong>
                <p>เงินที่สมาชิกคนอื่นยังต้องจ่ายคุณ</p>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>กลุ่มที่เข้าร่วม</span><span class="stat-icon stat-icon-neutral">▦</span></div>
                <strong class="dashboard-stat-value">{{ $myGroups->count() }} <small>กลุ่ม</small></strong>
                <p>กลุ่มที่ใช้งานอยู่ในบัญชีนี้</p>
            </article>
        </section>

        @if($pendingApprovals->count() || $rejectedPayments->count())
            <section class="dashboard-attention-panel">
                <div class="section-title-row">
                    <div><h2>รายการที่ต้องดำเนินการ</h2><p>ตรวจสอบก่อนที่จะเคลียร์ยอดให้เรียบร้อย</p></div>
                    <span class="attention-count">{{ $pendingApprovals->count() + $rejectedPayments->count() }} รายการ</span>
                </div>
                @foreach($pendingApprovals as $item)
                    <a class="attention-row" href="{{ route('settlements.show', $item->id) }}">
                        <span class="attention-mark attention-mark-orange">↙</span>
                        <span class="attention-copy"><strong>รอยืนยันการรับเงินจาก {{ $item->debtor->name ?? 'สมาชิก' }}</strong><small>{{ $item->group->name ?? 'กลุ่ม' }} · {{ $item->transfer_datetime ? \Carbon\Carbon::parse($item->transfer_datetime)->format('d/m/Y H:i') : 'ไม่ระบุวันเวลา' }}</small></span>
                        <strong class="attention-amount">฿{{ number_format($item->amount, 2) }}</strong>
                        <span class="attention-arrow">→</span>
                    </a>
                @endforeach
                @foreach($rejectedPayments as $item)
                    <a class="attention-row" href="{{ route('groups.summary', $item->group_id) }}">
                        <span class="attention-mark attention-mark-red">!</span>
                        <span class="attention-copy"><strong>สลิปของคุณถูกปฏิเสธ</strong><small>{{ $item->group->name ?? 'กลุ่ม' }} · กรุณาตรวจสอบแล้วส่งใหม่</small></span>
                        <strong class="attention-amount attention-amount-red">฿{{ number_format($item->amount, 2) }}</strong>
                        <span class="attention-arrow">→</span>
                    </a>
                @endforeach
            </section>
        @endif

        <div class="dashboard-content-grid">
            <section class="dashboard-section-card">
                <div class="section-title-row">
                    <div><h2>ยอดที่ต้องเคลียร์</h2><p>สรุปตามคนและกลุ่ม</p></div>
                    <a class="text-link" href="{{ route('settlements.index') }}">ดูรายการหนี้ทั้งหมด <span>→</span></a>
                </div>
                <div class="debt-preview-columns">
                    <div class="debt-preview-group">
                        <div class="mini-section-label"><span class="mini-dot mini-dot-red"></span> คุณต้องจ่าย</div>
                        @forelse(array_slice($payables, 0, 3) as $debt)
                            <a class="debt-preview-row" href="{{ route('groups.summary', $debt['group']->id) }}">
                                <span class="small-person-avatar">{{ mb_substr($debt['person']->name, 0, 1) }}</span>
                                <span class="debt-preview-copy"><strong>{{ $debt['person']->name }}</strong><small>{{ $debt['group']->name }}</small></span>
                                <strong class="debt-preview-amount debt-preview-amount-red">฿{{ number_format($debt['amount'], 2) }}</strong>
                            </a>
                        @empty
                            <p class="compact-empty">ไม่มีหนี้ที่ต้องจ่ายในตอนนี้</p>
                        @endforelse
                    </div>
                    <div class="debt-preview-group">
                        <div class="mini-section-label"><span class="mini-dot mini-dot-green"></span> คุณต้องได้รับ</div>
                        @forelse(array_slice($receivables, 0, 3) as $debt)
                            <a class="debt-preview-row" href="{{ route('groups.summary', $debt['group']->id) }}">
                                <span class="small-person-avatar small-person-avatar-green">{{ mb_substr($debt['person']->name, 0, 1) }}</span>
                                <span class="debt-preview-copy"><strong>{{ $debt['person']->name }}</strong><small>{{ $debt['group']->name }}</small></span>
                                <strong class="debt-preview-amount debt-preview-amount-green">฿{{ number_format($debt['amount'], 2) }}</strong>
                            </a>
                        @empty
                            <p class="compact-empty">ไม่มีเงินที่ต้องได้รับในตอนนี้</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="dashboard-section-card dashboard-groups-card">
                <div class="section-title-row">
                    <div><h2>กลุ่มของฉัน</h2><p>ทางลัดไปยังกลุ่มที่ใช้งาน</p></div>
                    <a class="text-link" href="{{ route('groups.index') }}">ดูทั้งหมด <span>→</span></a>
                </div>
                <div class="dashboard-group-list">
                    @forelse($myGroups->take(4) as $group)
                        <a class="dashboard-group-row" href="{{ route('groups.show', $group->id) }}">
                            <span class="dashboard-group-monogram">{{ mb_substr($group->name, 0, 1) }}</span>
                            <span class="dashboard-group-copy"><strong>{{ $group->name }}</strong><small>{{ $group->members_count ?? $group->members()->count() }} สมาชิก</small></span>
                            <span class="dashboard-group-arrow">→</span>
                        </a>
                    @empty
                        <div class="dashboard-empty-inline"><p>ยังไม่มีกลุ่มในบัญชีนี้</p><a href="{{ route('groups.index') }}">สร้างหรือเข้าร่วมกลุ่ม →</a></div>
                    @endforelse
                </div>
                @if($myGroups->count())
                    <a class="dashboard-card-bottom-link" href="{{ route('groups.create') }}">＋ สร้างกลุ่มใหม่</a>
                @endif
            </section>
        </div>

        <section class="dashboard-section-card dashboard-history-card">
            <div class="section-title-row">
                <div><h2>ความเคลื่อนไหวล่าสุด</h2><p>รายการโอนเงินที่คุณมีส่วนเกี่ยวข้อง</p></div>
                <a class="text-link" href="{{ route('history') }}">ดูประวัติทั้งหมด <span>→</span></a>
            </div>
            @if($recentSettlements->count())
                <div class="recent-table-wrap">
                    <table class="kt-data-table">
                        <thead><tr><th>รายการ</th><th>กลุ่ม</th><th>วันที่</th><th>สถานะ</th><th class="table-amount">ยอดเงิน</th></tr></thead>
                        <tbody>
                            @foreach($recentSettlements as $item)
                                <tr>
                                    <td><strong>{{ $item->debtor_id == Auth::id() ? 'คุณโอนให้ ' . ($item->creditor->name ?? 'สมาชิก') : ($item->debtor->name ?? 'สมาชิก') . ' โอนให้คุณ' }}</strong></td>
                                    <td>{{ $item->group->name ?? '—' }}</td>
                                    <td>{{ $item->transfer_datetime ? \Carbon\Carbon::parse($item->transfer_datetime)->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</td>
                                    <td><span class="status-pill status-{{ $item->status }}">{{ ['pending' => 'รอยืนยัน', 'approved' => 'ยืนยันแล้ว', 'rejected' => 'ปฏิเสธ'][$item->status] ?? $item->status }}</span></td>
                                    <td class="table-amount">฿{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="dashboard-empty-inline"><p>ยังไม่มีประวัติการโอนเงิน</p><small>เมื่อมีการส่งสลิปหรือยืนยันการรับเงิน รายการจะปรากฏที่นี่</small></div>
            @endif
        </section>
    </div>
@endsection
