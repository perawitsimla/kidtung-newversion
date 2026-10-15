@extends('layouts.app')

@section('title', 'แก้ไขบิล | KidTung')

@section('head_scripts')
    <script>
        window.groupMembers = {{ Js::from($group->members) }};
        window.initialExpenseItems = {{ Js::from(old('items', [])) }};
        window.existingExpense = {{ Js::from($expense) }};
    </script>
    <script src="{{ asset('js/expense-calculator.js') }}" defer></script>
@endsection

@section('content')
    <div class="expense-page">
        <a class="page-back-link" href="{{ route('groups.show', $group->id) }}">← กลับไปหน้ากลุ่ม</a>

        <div class="expense-page-heading">
            <div>
                <p class="page-eyebrow">{{ $group->name }}</p>
                <h1>แก้ไขรายการค่าใช้จ่าย</h1>
                <p class="page-description">ปรับรายการ คนจ่าย หรือวิธีแบ่งเงิน แล้วตรวจยอดรวมก่อนบันทึก</p>
            </div>
            <span class="expense-step-label">แก้ไขบิล</span>
        </div>

        <form id="expenseForm" class="expense-form" action="{{ route('expenses.update', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')

            <section class="expense-panel">
                <div class="expense-panel-heading">
                    <span class="expense-panel-number">01</span>
                    <div><h2>รายละเอียดบิล</h2><p>ชื่อบิลที่แสดงให้สมาชิกในกลุ่ม</p></div>
                </div>
                <label class="field-label" for="expenseTitle">ชื่อบิล / ร้านค้า</label>
                <input class="expense-title-input" id="expenseTitle" type="text" name="title" value="{{ old('title', $expense->title) }}" maxlength="150" required>
            </section>

            <section class="expense-panel">
                <div class="expense-panel-heading expense-heading-spaced">
                    <span class="expense-panel-number">02</span>
                    <div><h2>รายการในบิล</h2><p>แบ่งเท่ากัน ระบุยอดเงิน หรือแบ่งตามเปอร์เซ็นต์</p></div>
                    <button type="button" id="addItemBtn" class="kt-button kt-button-secondary">＋ เพิ่มรายการ</button>
                </div>
                <div id="itemsContainer" class="expense-items-container"></div>
            </section>

            <section class="expense-panel">
                <div class="expense-panel-heading">
                    <span class="expense-panel-number">03</span>
                    <div><h2>ค่าบริการและภาษี</h2><p>อิงเปอร์เซ็นต์จากใบเสร็จ</p></div>
                </div>
                <div class="fee-options-grid">
                    <div class="fee-option-card">
                        <label class="fee-toggle" for="has_sc">
                            <input type="checkbox" id="has_sc" name="has_sc" value="1" {{ old('has_sc', $expense->sc_percent > 0) ? 'checked' : '' }}>
                            <span><strong>Service charge</strong><small>ค่าบริการเพิ่มเติม</small></span>
                        </label>
                        <div class="fee-percent-field"><input type="number" id="sc_percent" name="sc_percent" min="0" max="100" step="0.01" value="{{ old('sc_percent', $expense->sc_percent > 0 ? rtrim(rtrim($expense->sc_percent, '0'), '.') : 10) }}"><span>%</span></div>
                    </div>
                    <div class="fee-option-card">
                        <label class="fee-toggle" for="has_vat">
                            <input type="checkbox" id="has_vat" name="has_vat" value="1" {{ old('has_vat', $expense->vat_percent > 0) ? 'checked' : '' }}>
                            <span><strong>VAT</strong><small>ภาษีมูลค่าเพิ่ม</small></span>
                        </label>
                        <div class="fee-percent-field"><input type="number" id="vat_percent" name="vat_percent" min="0" max="100" step="0.01" value="{{ old('vat_percent', $expense->vat_percent > 0 ? rtrim(rtrim($expense->vat_percent, '0'), '.') : 7) }}"><span>%</span></div>
                    </div>
                </div>
            </section>

            <section class="expense-total-panel">
                <div class="expense-total-top">
                    <div><p class="total-caption">ยอดรวมสุทธิ</p><h2><span id="grandTotalDisplay">0.00</span> <small>บาท</small></h2></div>
                    <span class="total-live-label"><span></span> คำนวณอัตโนมัติ</span>
                </div>
                <div id="splitWarning" class="split-warning" hidden></div>
                <div class="split-summary-heading"><h3>สรุปยอดของสมาชิก</h3><p>รวม Service charge และ VAT ตามสัดส่วนของแต่ละคน</p></div>
                <ul id="summaryList" class="expense-summary-list"></ul>
            </section>

            <div class="expense-form-actions">
                <a class="kt-button kt-button-secondary" href="{{ route('groups.show', $group->id) }}">ยกเลิก</a>
                <button class="kt-button kt-button-primary" type="submit">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
@endsection
