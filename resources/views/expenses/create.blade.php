@extends('layouts.app')

@section('title', 'เพิ่มบิล | KidTung')

@section('head_scripts')
    <script>
        window.groupMembers = {{ Js::from($group->members) }};
        window.initialExpenseItems = {{ Js::from(old('items', [])) }};
    </script>
    <script src="{{ asset('js/expense-calculator.js') }}" defer></script>
@endsection

@section('content')
    <div class="expense-page">
        <a class="page-back-link" href="{{ route('groups.show', $group->id) }}">← กลับไปหน้ากลุ่ม</a>

        <div class="expense-page-heading">
            <div>
                <p class="page-eyebrow">{{ $group->name }}</p>
                <h1>เพิ่มรายการค่าใช้จ่าย</h1>
                <p class="page-description">เพิ่มรายการ เลือกคนจ่าย และกำหนดวิธีแบ่งเงินของแต่ละรายการได้</p>
            </div>
            <span class="expense-step-label">บันทึกบิลใหม่</span>
        </div>

        <form id="expenseForm" class="expense-form" action="{{ route('expenses.store', $group->id) }}" method="POST">
            @csrf

            <section class="expense-panel">
                <div class="expense-panel-heading">
                    <span class="expense-panel-number">01</span>
                    <div>
                        <h2>รายละเอียดบิล</h2>
                        <p>ตั้งชื่อบิลเพื่อให้เพื่อนในกลุ่มเข้าใจตรงกัน</p>
                    </div>
                </div>
                <label class="field-label" for="expenseTitle">ชื่อบิล / ร้านค้า</label>
                <input class="expense-title-input" id="expenseTitle" type="text" name="title" value="{{ old('title') }}" maxlength="150" placeholder="เช่น มื้อเย็นวันศุกร์" required>
            </section>

            <!-- แก้ไขส่วนข้อ 02 เพื่อจัด Layout ไม่ให้ตัวหนังสือโดนบีบเป็นแนวตั้ง -->
            <section class="expense-panel">
                <div class="expense-panel-heading expense-heading-spaced" style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; width: 100%;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem; flex: 1; min-width: 0;">
                        <span class="expense-panel-number" style="flex-shrink: 0;">02</span>
                        <div style="flex: 1; min-width: 0;">
                            <h2 style="margin: 0; word-break: normal;">รายการในบิล</h2>
                            <p style="margin: 0.25rem 0 0 0;">แต่ละรายการเลือกวิธีหารและคนที่ร่วมจ่ายได้ไม่เหมือนกัน</p>
                        </div>
                    </div>
                    <button type="button" id="addItemBtn" class="kt-button kt-button-secondary" style="flex-shrink: 0; white-space: nowrap; align-self: flex-start;">＋ เพิ่มรายการ</button>
                </div>
                <div id="itemsContainer" class="expense-items-container"></div>
            </section>

            <section class="expense-panel">
                <div class="expense-panel-heading">
                    <span class="expense-panel-number">03</span>
                    <div>
                        <h2>ค่าบริการและภาษี</h2>
                        <p>เปิดใช้เฉพาะรายการที่อยู่ในใบเสร็จ</p>
                    </div>
                </div>
                <div class="fee-options-grid">
                    <div class="fee-option-card">
                        <label class="fee-toggle" for="has_sc">
                            <input type="checkbox" id="has_sc" name="has_sc" value="1" {{ old('has_sc') ? 'checked' : '' }}>
                            <span><strong>Service charge</strong><small>ค่าบริการเพิ่มเติม</small></span>
                        </label>
                        <div class="fee-percent-field"><input type="number" id="sc_percent" name="sc_percent" min="0" max="100" step="0.01" value="{{ old('sc_percent', 10) }}"><span>%</span></div>
                    </div>
                    <div class="fee-option-card">
                        <label class="fee-toggle" for="has_vat">
                            <input type="checkbox" id="has_vat" name="has_vat" value="1" {{ old('has_vat') ? 'checked' : '' }}>
                            <span><strong>VAT</strong><small>ภาษีมูลค่าเพิ่ม</small></span>
                        </label>
                        <div class="fee-percent-field"><input type="number" id="vat_percent" name="vat_percent" min="0" max="100" step="0.01" value="{{ old('vat_percent', 7) }}"><span>%</span></div>
                    </div>
                </div>
            </section>

            <section class="expense-total-panel">
                <div class="expense-total-top">
                    <div>
                        <p class="total-caption">ยอดรวมสุทธิ</p>
                        <h2><span id="grandTotalDisplay">0.00</span> <small>บาท</small></h2>
                    </div>
                    <span class="total-live-label"><span></span> คำนวณอัตโนมัติ</span>
                </div>
                <div id="splitWarning" class="split-warning" hidden></div>
                <div class="split-summary-heading"><h3>สรุปยอดของสมาชิก</h3><p>รวม Service charge และ VAT ตามสัดส่วนของแต่ละคน</p></div>
                <ul id="summaryList" class="expense-summary-list"></ul>
            </section>

            <div class="expense-form-actions">
                <a class="kt-button kt-button-secondary" href="{{ route('groups.show', $group->id) }}">ยกเลิก</a>
                <button class="kt-button kt-button-primary" type="submit">บันทึกบิล</button>
            </div>
        </form>
    </div>
@endsection