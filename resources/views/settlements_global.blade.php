@extends('layouts.app')

@section('title', 'รายการหนี้ | KidTung')

@section('content')
    @php
        $totalPayable = collect($myPayables)->sum('amount');
        $totalReceivable = collect($myReceivables)->sum('amount');
        $debtCount = count($myPayables) + count($myReceivables);
    @endphp

    <div class="debts-page">
        <div class="debts-heading">
            <div>
                <p class="page-eyebrow">PERSONAL BALANCE</p>
                <h1>รายการหนี้ของฉัน</h1>
                <p class="page-description">ดูยอดที่ต้องจ่ายและยอดที่รอรับจากทุกกลุ่มได้ในหน้าเดียว</p>
            </div>
            <span class="debt-count-badge">{{ $debtCount }} รายการคงค้าง</span>
        </div>

        <section class="debts-overview-grid">
            <article class="debts-overview-card debts-payable-card">
                <div class="debts-overview-top"><span class="debts-overview-icon">↗</span><span class="debts-overview-label">ยอดที่คุณต้องจ่าย</span></div>
                <strong class="debts-overview-amount">฿{{ number_format($totalPayable, 2) }}</strong>
                <p>หักยอดโอนที่ได้รับการยืนยันแล้ว</p>
                <div class="debts-overview-foot"><span class="overview-foot-dot dot-red"></span> {{ count($myPayables) }} รายการค้างชำระ</div>
            </article>
            <article class="debts-overview-card debts-receivable-card">
                <div class="debts-overview-top"><span class="debts-overview-icon">↙</span><span class="debts-overview-label">ยอดที่คุณต้องได้รับ</span></div>
                <strong class="debts-overview-amount">฿{{ number_format($totalReceivable, 2) }}</strong>
                <p>เงินที่สมาชิกคนอื่นยังต้องจ่ายคุณ</p>
                <div class="debts-overview-foot"><span class="overview-foot-dot dot-green"></span> {{ count($myReceivables) }} รายการรอรับเงิน</div>
            </article>
            <article class="debts-net-card">
                <span class="net-card-label">ส่วนต่างสุทธิ</span>
                <strong>฿{{ number_format(abs($totalReceivable - $totalPayable), 2) }}</strong>
                <p>
                    @if($totalPayable > $totalReceivable)
                        ตอนนี้ยอดที่คุณต้องจ่ายมากกว่ายอดที่ต้องได้รับ
                    @elseif($totalReceivable > $totalPayable)
                        ตอนนี้ยอดที่คุณต้องได้รับมากกว่ายอดที่ต้องจ่าย
                    @else
                        ยอดที่ต้องจ่ายและรับเท่ากัน
                    @endif
                </p>
                <a href="{{ route('history') }}">ดูประวัติการโอนเงิน <span>→</span></a>
            </article>
        </section>

        <div class="debts-list-grid">
            <section class="debt-list-panel">
                <div class="debt-list-heading">
                    <div class="debt-list-title-mark debt-title-mark-red">↗</div>
                    <div><h2>คุณต้องจ่าย</h2><p>รายการที่คุณยังมียอดค้างกับสมาชิก</p></div>
                    <span class="debt-list-count">{{ count($myPayables) }}</span>
                </div>
                <div class="debt-list-items">
                    @forelse($myPayables as $pay)
                        <article class="debt-person-row">
                            <span class="debt-person-avatar">{{ mb_substr($pay['person']->name, 0, 1) }}</span>
                            <div class="debt-person-info">
                                <strong>{{ $pay['person']->name }}</strong>
                                <span>{{ $pay['group']->name }}</span>
                            </div>
                            <div class="debt-person-action">
                                <strong class="debt-person-amount debt-person-amount-red">฿{{ number_format($pay['amount'], 2) }}</strong>
                                <a href="{{ route('groups.summary', $pay['group']->id) }}">ไปชำระเงิน <span>→</span></a>
                            </div>
                        </article>
                    @empty
                        <div class="debt-empty-state"><span class="debt-empty-icon">✓</span><strong>ไม่มีหนี้ค้างชำระ</strong><p>ตอนนี้คุณไม่มียอดที่ต้องจ่ายให้สมาชิกคนอื่น</p></div>
                    @endforelse
                </div>
            </section>

            <section class="debt-list-panel">
                <div class="debt-list-heading">
                    <div class="debt-list-title-mark debt-title-mark-green">↙</div>
                    <div><h2>คุณต้องได้รับ</h2><p>รายการที่สมาชิกยังต้องจ่ายคืนให้คุณ</p></div>
                    <span class="debt-list-count">{{ count($myReceivables) }}</span>
                </div>
                <div class="debt-list-items">
                    @forelse($myReceivables as $rec)
                        <article class="debt-person-row">
                            <span class="debt-person-avatar debt-person-avatar-green">{{ mb_substr($rec['person']->name, 0, 1) }}</span>
                            <div class="debt-person-info">
                                <strong>{{ $rec['person']->name }}</strong>
                                <span>{{ $rec['group']->name }}</span>
                            </div>
                            <div class="debt-person-action">
                                <strong class="debt-person-amount debt-person-amount-green">฿{{ number_format($rec['amount'], 2) }}</strong>
                                <a href="{{ route('groups.summary', $rec['group']->id) }}">ดูรายละเอียด <span>→</span></a>
                            </div>
                        </article>
                    @empty
                        <div class="debt-empty-state"><span class="debt-empty-icon debt-empty-icon-muted">—</span><strong>ยังไม่มีเงินที่ต้องรับ</strong><p>เมื่อมีสมาชิกค้างจ่ายคุณ รายการจะปรากฏตรงนี้</p></div>
                    @endforelse
                </div>
            </section>
        </div>

        <p class="debts-page-note">ยอดคงเหลือคำนวณจากบิลในแต่ละกลุ่ม และหักเฉพาะการโอนที่เจ้าหนี้ยืนยันแล้วเท่านั้น</p>
    </div>
@endsection
