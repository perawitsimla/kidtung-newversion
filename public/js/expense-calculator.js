(function () {
    'use strict';

    const members = Array.isArray(window.groupMembers) ? window.groupMembers : [];
    let itemCount = 0;

    document.addEventListener('DOMContentLoaded', function () {
        const itemsContainer = document.getElementById('itemsContainer');
        const addItemButton = document.getElementById('addItemBtn');
        const expenseForm = document.getElementById('expenseForm');

        if (!itemsContainer || !addItemButton || !expenseForm) {
            return;
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                const entities = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return entities[character];
            });
        }

        function money(value) {
            return (Number(value) || 0).toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function getExistingSplitData(item) {
            const result = {
                selectedIds: [],
                amounts: {},
                percentages: {},
                method: item.split_method || 'equal'
            };

            if (Array.isArray(item.shares)) {
                result.selectedIds = item.shares.map(function (id) { return String(id); });
            }

            if (Array.isArray(item.splits)) {
                item.splits.forEach(function (split) {
                    const userId = String(split.user_id);
                    result.selectedIds.push(userId);
                    result.amounts[userId] = Number(split.amount) || 0;
                    if (split.split_method) {
                        result.method = split.split_method;
                    }
                });

                if (result.method === 'percent') {
                    const price = Number(item.price) || 0;
                    if (price > 0) {
                        Object.keys(result.amounts).forEach(function (userId) {
                            result.percentages[userId] = result.amounts[userId] / price * 100;
                        });
                    }
                }
            }

            if (item.share_amounts && typeof item.share_amounts === 'object') {
                Object.keys(item.share_amounts).forEach(function (userId) {
                    result.amounts[String(userId)] = item.share_amounts[userId];
                });
            }

            if (item.share_percents && typeof item.share_percents === 'object') {
                Object.keys(item.share_percents).forEach(function (userId) {
                    result.percentages[String(userId)] = item.share_percents[userId];
                });
            }

            result.selectedIds = Array.from(new Set(result.selectedIds));
            return result;
        }

        function buildMemberRows(index, initialItem, splitData) {
            const allSelectedByDefault = !initialItem;
            const rows = members.map(function (member) {
                const userId = String(member.id);
                const checked = allSelectedByDefault || splitData.selectedIds.includes(userId);
                const existingAmount = splitData.amounts[userId];
                const existingPercent = splitData.percentages[userId];
                const amountValue = existingAmount !== undefined ? existingAmount : '';
                const percentValue = existingPercent !== undefined ? existingPercent : '';

                return `
                    <div class="split-member-row">
                        <label class="split-member-check">
                            <input class="member-share" type="checkbox" name="items[${index}][shares][]" value="${escapeHtml(userId)}" ${checked ? 'checked' : ''}>
                            <span class="split-member-avatar">${escapeHtml(String(member.name || '?').trim().charAt(0).toUpperCase())}</span>
                            <span class="split-member-name">${escapeHtml(member.name)}</span>
                        </label>
                        <div class="share-value-wrap share-amount-wrap" hidden>
                            <input class="share-amount-input" type="number" name="items[${index}][share_amounts][${escapeHtml(userId)}]" min="0" step="0.01" value="${escapeHtml(amountValue)}" aria-label="ยอดที่ ${escapeHtml(member.name)} ต้องจ่าย">
                            <span>บาท</span>
                        </div>
                        <div class="share-value-wrap share-percent-wrap" hidden>
                            <input class="share-percent-input" type="number" name="items[${index}][share_percents][${escapeHtml(userId)}]" min="0" max="100" step="0.01" value="${escapeHtml(percentValue)}" aria-label="เปอร์เซ็นต์ของ ${escapeHtml(member.name)}">
                            <span>%</span>
                        </div>
                    </div>
                `;
            }).join('');

            return rows;
        }

        function createItemBlock(initialItem) {
            const index = itemCount;
            itemCount += 1;

            const item = initialItem || {};
            const splitData = getExistingSplitData(item);
            const method = ['equal', 'exact', 'percent'].includes(splitData.method) ? splitData.method : 'equal';
            const selectedPayerId = item.payer_id !== undefined ? String(item.payer_id) : String(members.length ? members[0].id : '');
            const payerOptions = members.map(function (member) {
                const selected = String(member.id) === selectedPayerId ? 'selected' : '';
                return `<option value="${escapeHtml(member.id)}" ${selected}>${escapeHtml(member.name)}</option>`;
            }).join('');

            const block = document.createElement('article');
            block.className = 'item-block';
            block.dataset.itemIndex = String(index);

            block.innerHTML = `
                <div class="item-block-heading">
                    <div class="item-number-badge">${String(index + 1).padStart(2, '0')}</div>
                    <div class="item-block-title-wrap">
                        <h3>รายการที่ ${index + 1}</h3>
                        <p>เลือกผู้จ่ายและวิธีแบ่งเงิน</p>
                    </div>
                    <button type="button" class="delete-item-btn" aria-label="ลบรายการนี้">ลบรายการ</button>
                </div>
                ${item.id ? `<input type="hidden" name="items[${index}][id]" value="${escapeHtml(item.id)}">` : ''}
                <div class="item-details-grid">
                    <div class="item-field item-field-wide">
                        <label for="item-name-${index}">ชื่อรายการ</label>
                        <input id="item-name-${index}" type="text" class="item-name" name="items[${index}][name]" value="${escapeHtml(item.name || '')}" maxlength="150" placeholder="เช่น ข้าวผัดกุ้ง" required>
                    </div>
                    <div class="item-field">
                        <label for="item-price-${index}">ราคา (บาท)</label>
                        <input id="item-price-${index}" type="number" class="item-price" name="items[${index}][price]" min="0.01" step="0.01" value="${escapeHtml(item.price ?? '')}" placeholder="0.00" required>
                    </div>
                    <div class="item-field">
                        <label for="item-payer-${index}">คนจ่ายเงิน</label>
                        <select id="item-payer-${index}" class="item-payer" name="items[${index}][payer_id]" required>${payerOptions}</select>
                    </div>
                </div>
                <div class="item-split-section">
                    <div class="item-split-heading">
                        <div><h4>แบ่งค่าใช้จ่าย</h4><p>เลือกคนที่ร่วมรับผิดชอบรายการนี้</p></div>
                        <div class="split-mode-field">
                            <label for="split-method-${index}">วิธีแบ่ง</label>
                            <select class="split-method" id="split-method-${index}" name="items[${index}][split_method]">
                                <option value="equal" ${method === 'equal' ? 'selected' : ''}>หารเท่ากัน</option>
                                <option value="exact" ${method === 'exact' ? 'selected' : ''}>ระบุยอดเงิน</option>
                                <option value="percent" ${method === 'percent' ? 'selected' : ''}>หารเปอร์เซ็นต์</option>
                            </select>
                        </div>
                    </div>
                    <div class="split-member-list">${buildMemberRows(index, initialItem, splitData)}</div>
                    <div class="item-split-note" aria-live="polite"></div>
                </div>
            `;

            itemsContainer.appendChild(block);
            syncItemBlock(block, true);
        }

        function distributeDefaultValues(block, mode) {
            const selectedRows = Array.from(block.querySelectorAll('.split-member-row')).filter(function (row) {
                return row.querySelector('.member-share').checked;
            });
            const price = Number(block.querySelector('.item-price').value) || 0;

            if (!selectedRows.length || price <= 0) {
                return;
            }

            selectedRows.forEach(function (row, index) {
                if (mode === 'exact') {
                    const input = row.querySelector('.share-amount-input');
                    if (input.value === '') {
                        const cents = Math.round(price * 100);
                        const baseCents = Math.floor(cents / selectedRows.length);
                        const remainder = cents % selectedRows.length;
                        input.value = ((baseCents + (index < remainder ? 1 : 0)) / 100).toFixed(2);
                    }
                }

                if (mode === 'percent') {
                    const input = row.querySelector('.share-percent-input');
                    if (input.value === '') {
                        const percent = 100 / selectedRows.length;
                        input.value = (index === selectedRows.length - 1
                            ? 100 - (Math.round(percent * 100) / 100) * index
                            : Math.round(percent * 100) / 100).toFixed(2);
                    }
                }
            });
        }

        function syncItemBlock(block, initializeValues) {
            const method = block.querySelector('.split-method').value;
            block.querySelectorAll('.split-member-row').forEach(function (row) {
                const selected = row.querySelector('.member-share').checked;
                row.classList.toggle('split-member-disabled', !selected);
                row.querySelector('.share-amount-wrap').hidden = method !== 'exact' || !selected;
                row.querySelector('.share-percent-wrap').hidden = method !== 'percent' || !selected;
                row.querySelector('.share-amount-input').disabled = method !== 'exact' || !selected;
                row.querySelector('.share-percent-input').disabled = method !== 'percent' || !selected;
            });

            if (initializeValues && (method === 'exact' || method === 'percent')) {
                distributeDefaultValues(block, method);
            }

            const note = block.querySelector('.item-split-note');
            if (method === 'equal') {
                note.textContent = 'ระบบแบ่งราคาให้ผู้ที่เลือกเท่า ๆ กัน และจัดการเศษสตางค์ให้อัตโนมัติ';
            } else if (method === 'exact') {
                note.textContent = 'ยอดเงินของคนที่เลือกต้องรวมกันเท่ากับราคาเต็มของรายการ';
            } else {
                note.textContent = 'เปอร์เซ็นต์ของคนที่เลือกต้องรวมกันเป็น 100%';
            }
        }

        function calculateEqualAmounts(price, selectedIds) {
            const result = {};
            const cents = Math.round(price * 100);
            const baseCents = Math.floor(cents / selectedIds.length);
            const remainder = cents % selectedIds.length;

            selectedIds.forEach(function (userId, index) {
                result[userId] = (baseCents + (index < remainder ? 1 : 0)) / 100;
            });

            return result;
        }

        function calculateTotal() {
            const personalTotals = {};
            const warnings = [];
            let subtotal = 0;

            members.forEach(function (member) {
                personalTotals[String(member.id)] = 0;
            });

            itemsContainer.querySelectorAll('.item-block').forEach(function (block, blockIndex) {
                const titleInput = block.querySelector('.item-name');
                const itemTitle = titleInput.value.trim() || `รายการที่ ${blockIndex + 1}`;
                const price = Number(block.querySelector('.item-price').value) || 0;
                const method = block.querySelector('.split-method').value;
                const selectedIds = Array.from(block.querySelectorAll('.member-share:checked')).map(function (checkbox) {
                    return String(checkbox.value);
                });
                const itemAmounts = {};
                let itemWarning = '';
                subtotal += price;

                if (!selectedIds.length) {
                    itemWarning = `${itemTitle}: เลือกคนหารอย่างน้อย 1 คน`;
                } else if (method === 'equal') {
                    Object.assign(itemAmounts, calculateEqualAmounts(price, selectedIds));
                } else if (method === 'exact') {
                    let amountTotal = 0;
                    selectedIds.forEach(function (userId) {
                        const row = block.querySelector(`.member-share[value="${userId}"]`).closest('.split-member-row');
                        const amount = Number(row.querySelector('.share-amount-input').value) || 0;
                        itemAmounts[userId] = amount;
                        amountTotal += amount;
                    });
                    if (Math.abs(Math.round(amountTotal * 100) - Math.round(price * 100)) > 0) {
                        itemWarning = `${itemTitle}: ยอดที่ระบุรวม ${money(amountTotal)} บาท ต้องเท่ากับ ${money(price)} บาท`;
                    }
                } else {
                    let percentTotal = 0;
                    const percentages = {};
                    selectedIds.forEach(function (userId) {
                        const row = block.querySelector(`.member-share[value="${userId}"]`).closest('.split-member-row');
                        percentages[userId] = Number(row.querySelector('.share-percent-input').value) || 0;
                        percentTotal += percentages[userId];
                        itemAmounts[userId] = price * percentages[userId] / 100;
                    });

                    if (Math.round(percentTotal * 100) !== 10000) {
                        itemWarning = `${itemTitle}: เปอร์เซ็นต์รวม ${percentTotal.toFixed(2)}% ต้องเท่ากับ 100%`;
                    } else {
                        const priceCents = Math.round(price * 100);
                        const amountCents = {};
                        const fractionalCents = {};
                        let assignedCents = 0;

                        selectedIds.forEach(function (userId) {
                            const exactCents = priceCents * percentages[userId] / 100;
                            const wholeCents = Math.floor(exactCents);
                            amountCents[userId] = wholeCents;
                            fractionalCents[userId] = exactCents - wholeCents;
                            assignedCents += wholeCents;
                        });

                        const remainderOrder = selectedIds.slice().sort(function (leftId, rightId) {
                            return fractionalCents[rightId] - fractionalCents[leftId];
                        });
                        let remainingCents = priceCents - assignedCents;
                        remainderOrder.forEach(function (userId) {
                            if (remainingCents > 0) {
                                amountCents[userId] += 1;
                                remainingCents -= 1;
                            }
                        });

                        selectedIds.forEach(function (userId) {
                            itemAmounts[userId] = amountCents[userId] / 100;
                        });
                    }
                }

                selectedIds.forEach(function (userId) {
                    if (personalTotals[userId] !== undefined) {
                        personalTotals[userId] += itemAmounts[userId] || 0;
                    }
                });

                const note = block.querySelector('.item-split-note');
                note.classList.toggle('item-split-note-warning', Boolean(itemWarning));
                if (itemWarning) {
                    note.textContent = itemWarning;
                    warnings.push(itemWarning);
                }
            });

            const hasServiceCharge = document.getElementById('has_sc').checked;
            const serviceChargePercent = hasServiceCharge ? Number(document.getElementById('sc_percent').value) || 0 : 0;
            const hasVat = document.getElementById('has_vat').checked;
            const vatPercent = hasVat ? Number(document.getElementById('vat_percent').value) || 0 : 0;
            const serviceCharge = subtotal * serviceChargePercent / 100;
            const afterServiceCharge = subtotal + serviceCharge;
            const vatAmount = afterServiceCharge * vatPercent / 100;
            const grandTotal = afterServiceCharge + vatAmount;

            document.getElementById('grandTotalDisplay').textContent = money(grandTotal);

            const summary = document.getElementById('summaryList');
            summary.innerHTML = '';
            members.forEach(function (member) {
                const userId = String(member.id);
                const personalSubtotal = personalTotals[userId] || 0;
                const proportion = subtotal > 0 ? personalSubtotal / subtotal : 0;
                const finalAmount = personalSubtotal + (serviceCharge + vatAmount) * proportion;
                const item = document.createElement('li');
                item.className = 'expense-summary-row';
                item.innerHTML = `
                    <span class="expense-summary-person"><span class="split-member-avatar">${escapeHtml(String(member.name || '?').trim().charAt(0).toUpperCase())}</span><span>${escapeHtml(member.name)}</span></span>
                    <strong>${money(finalAmount)} <small>บาท</small></strong>
                `;
                summary.appendChild(item);
            });

            const warningBox = document.getElementById('splitWarning');
            if (warnings.length) {
                warningBox.hidden = false;
                warningBox.innerHTML = `<strong>กรุณาตรวจสอบวิธีแบ่งเงินก่อนบันทึก</strong><ul>${warnings.map(function (warning) {
                    return `<li>${escapeHtml(warning)}</li>`;
                }).join('')}</ul>`;
            } else {
                warningBox.hidden = true;
                warningBox.innerHTML = '';
            }

            return warnings;
        }

        // โหลดค่าที่ผู้ใช้กรอกไว้ก่อนหน้า หรือรายการเดิมเมื่อต้องแก้ไข
        let initialItems = Array.isArray(window.initialExpenseItems) ? window.initialExpenseItems : [];
        if (!initialItems.length && window.existingExpense && Array.isArray(window.existingExpense.items)) {
            initialItems = window.existingExpense.items;
        }

        if (initialItems.length) {
            initialItems.forEach(function (item) {
                createItemBlock(item);
            });
        } else {
            createItemBlock(null);
        }

        addItemButton.addEventListener('click', function () {
            createItemBlock(null);
            calculateTotal();
        });

        itemsContainer.addEventListener('input', function (event) {
            const block = event.target.closest('.item-block');
            if (block && event.target.matches('.item-price')) {
                syncItemBlock(block, true);
            }
            calculateTotal();
        });

        itemsContainer.addEventListener('change', function (event) {
            const block = event.target.closest('.item-block');
            if (!block) {
                return;
            }

            if (event.target.matches('.split-method')) {
                syncItemBlock(block, true);
            } else if (event.target.matches('.member-share')) {
                syncItemBlock(block, true);
            }
            calculateTotal();
        });

        itemsContainer.addEventListener('click', function (event) {
            const deleteButton = event.target.closest('.delete-item-btn');
            if (!deleteButton) {
                return;
            }

            const blocks = itemsContainer.querySelectorAll('.item-block');
            if (blocks.length === 1) {
                const block = blocks[0];
                block.querySelector('.item-name').value = '';
                block.querySelector('.item-price').value = '';
                block.querySelector('.split-method').value = 'equal';
                const hiddenId = block.querySelector('input[type="hidden"][name$="[id]"]');
                if (hiddenId) {
                    hiddenId.remove();
                }
                block.querySelectorAll('.member-share').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
                block.querySelectorAll('.share-amount-input, .share-percent-input').forEach(function (input) {
                    input.value = '';
                });
                syncItemBlock(block, true);
            } else {
                deleteButton.closest('.item-block').remove();
                itemsContainer.querySelectorAll('.item-block').forEach(function (block, index) {
                    block.querySelector('.item-number-badge').textContent = String(index + 1).padStart(2, '0');
                    block.querySelector('.item-block-title-wrap h3').textContent = `รายการที่ ${index + 1}`;
                });
            }
            calculateTotal();
        });

        document.getElementById('has_sc').addEventListener('change', calculateTotal);
        document.getElementById('sc_percent').addEventListener('input', calculateTotal);
        document.getElementById('has_vat').addEventListener('change', calculateTotal);
        document.getElementById('vat_percent').addEventListener('input', calculateTotal);
        calculateTotal();

        expenseForm.addEventListener('submit', function (event) {
            const warnings = calculateTotal();
            if (warnings.length) {
                event.preventDefault();
                document.getElementById('splitWarning').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
})();
