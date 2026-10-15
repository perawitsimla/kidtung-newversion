<?php $__env->startSection('title', 'ภาพรวม | KidTung'); ?>

<?php $__env->startSection('content'); ?>
    <div class="dashboard-page">
        <div class="dashboard-heading">
            <div>
                <p class="page-eyebrow">YOUR MONEY, IN ONE PLACE</p>
                <h1>ภาพรวมของฉัน</h1>
                <p class="page-description">สวัสดี <?php echo e(Auth::user()->name); ?> — นี่คือสถานะค่าใช้จ่ายและรายการที่ต้องจัดการของคุณ</p>
            </div>
            <a class="kt-button kt-button-primary" href="<?php echo e(route('groups.create')); ?>"><span class="button-plus">＋</span> สร้างกลุ่มใหม่</a>
        </div>

        <section class="dashboard-stats-grid" aria-label="สถิติภาพรวม">
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>ยอดบิลในกลุ่ม</span><span class="stat-icon stat-icon-orange">฿</span></div>
                <strong class="dashboard-stat-value">฿<?php echo e(number_format($totalGroupSpending, 2)); ?></strong>
                <p>รวมบิลของทุกกลุ่มที่คุณอยู่</p>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>ยอดที่ต้องจ่าย</span><span class="stat-icon stat-icon-red">↗</span></div>
                <strong class="dashboard-stat-value stat-value-red">฿<?php echo e(number_format($totalPayable, 2)); ?></strong>
                <p>หนี้คงเหลือหลังหักยอดที่ยืนยันแล้ว</p>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>ยอดที่ต้องได้รับ</span><span class="stat-icon stat-icon-green">↙</span></div>
                <strong class="dashboard-stat-value stat-value-green">฿<?php echo e(number_format($totalReceivable, 2)); ?></strong>
                <p>เงินที่สมาชิกคนอื่นยังต้องจ่ายคุณ</p>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-top"><span>กลุ่มที่เข้าร่วม</span><span class="stat-icon stat-icon-neutral">▦</span></div>
                <strong class="dashboard-stat-value"><?php echo e($myGroups->count()); ?> <small>กลุ่ม</small></strong>
                <p>กลุ่มที่ใช้งานอยู่ในบัญชีนี้</p>
            </article>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingApprovals->count() || $rejectedPayments->count()): ?>
            <section class="dashboard-attention-panel">
                <div class="section-title-row">
                    <div><h2>รายการที่ต้องดำเนินการ</h2><p>ตรวจสอบก่อนที่จะเคลียร์ยอดให้เรียบร้อย</p></div>
                    <span class="attention-count"><?php echo e($pendingApprovals->count() + $rejectedPayments->count()); ?> รายการ</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a class="attention-row" href="<?php echo e(route('settlements.show', $item->id)); ?>">
                        <span class="attention-mark attention-mark-orange">↙</span>
                        <span class="attention-copy"><strong>รอยืนยันการรับเงินจาก <?php echo e($item->debtor->name ?? 'สมาชิก'); ?></strong><small><?php echo e($item->group->name ?? 'กลุ่ม'); ?> · <?php echo e($item->transfer_datetime ? \Carbon\Carbon::parse($item->transfer_datetime)->format('d/m/Y H:i') : 'ไม่ระบุวันเวลา'); ?></small></span>
                        <strong class="attention-amount">฿<?php echo e(number_format($item->amount, 2)); ?></strong>
                        <span class="attention-arrow">→</span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rejectedPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a class="attention-row" href="<?php echo e(route('groups.summary', $item->group_id)); ?>">
                        <span class="attention-mark attention-mark-red">!</span>
                        <span class="attention-copy"><strong>สลิปของคุณถูกปฏิเสธ</strong><small><?php echo e($item->group->name ?? 'กลุ่ม'); ?> · กรุณาตรวจสอบแล้วส่งใหม่</small></span>
                        <strong class="attention-amount attention-amount-red">฿<?php echo e(number_format($item->amount, 2)); ?></strong>
                        <span class="attention-arrow">→</span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="dashboard-content-grid">
            <section class="dashboard-section-card">
                <div class="section-title-row">
                    <div><h2>ยอดที่ต้องเคลียร์</h2><p>สรุปตามคนและกลุ่ม</p></div>
                    <a class="text-link" href="<?php echo e(route('settlements.index')); ?>">ดูรายการหนี้ทั้งหมด <span>→</span></a>
                </div>
                <div class="debt-preview-columns">
                    <div class="debt-preview-group">
                        <div class="mini-section-label"><span class="mini-dot mini-dot-red"></span> คุณต้องจ่าย</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = array_slice($payables, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a class="debt-preview-row" href="<?php echo e(route('groups.summary', $debt['group']->id)); ?>">
                                <span class="small-person-avatar"><?php echo e(mb_substr($debt['person']->name, 0, 1)); ?></span>
                                <span class="debt-preview-copy"><strong><?php echo e($debt['person']->name); ?></strong><small><?php echo e($debt['group']->name); ?></small></span>
                                <strong class="debt-preview-amount debt-preview-amount-red">฿<?php echo e(number_format($debt['amount'], 2)); ?></strong>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p class="compact-empty">ไม่มีหนี้ที่ต้องจ่ายในตอนนี้</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="debt-preview-group">
                        <div class="mini-section-label"><span class="mini-dot mini-dot-green"></span> คุณต้องได้รับ</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = array_slice($receivables, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a class="debt-preview-row" href="<?php echo e(route('groups.summary', $debt['group']->id)); ?>">
                                <span class="small-person-avatar small-person-avatar-green"><?php echo e(mb_substr($debt['person']->name, 0, 1)); ?></span>
                                <span class="debt-preview-copy"><strong><?php echo e($debt['person']->name); ?></strong><small><?php echo e($debt['group']->name); ?></small></span>
                                <strong class="debt-preview-amount debt-preview-amount-green">฿<?php echo e(number_format($debt['amount'], 2)); ?></strong>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p class="compact-empty">ไม่มีเงินที่ต้องได้รับในตอนนี้</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="dashboard-section-card dashboard-groups-card">
                <div class="section-title-row">
                    <div><h2>กลุ่มของฉัน</h2><p>ทางลัดไปยังกลุ่มที่ใช้งาน</p></div>
                    <a class="text-link" href="<?php echo e(route('groups.index')); ?>">ดูทั้งหมด <span>→</span></a>
                </div>
                <div class="dashboard-group-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $myGroups->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a class="dashboard-group-row" href="<?php echo e(route('groups.show', $group->id)); ?>">
                            <span class="dashboard-group-monogram"><?php echo e(mb_substr($group->name, 0, 1)); ?></span>
                            <span class="dashboard-group-copy"><strong><?php echo e($group->name); ?></strong><small><?php echo e($group->members_count ?? $group->members()->count()); ?> สมาชิก</small></span>
                            <span class="dashboard-group-arrow">→</span>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="dashboard-empty-inline"><p>ยังไม่มีกลุ่มในบัญชีนี้</p><a href="<?php echo e(route('groups.index')); ?>">สร้างหรือเข้าร่วมกลุ่ม →</a></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myGroups->count()): ?>
                    <a class="dashboard-card-bottom-link" href="<?php echo e(route('groups.create')); ?>">＋ สร้างกลุ่มใหม่</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        </div>

        <section class="dashboard-section-card dashboard-history-card">
            <div class="section-title-row">
                <div><h2>ความเคลื่อนไหวล่าสุด</h2><p>รายการโอนเงินที่คุณมีส่วนเกี่ยวข้อง</p></div>
                <a class="text-link" href="<?php echo e(route('history')); ?>">ดูประวัติทั้งหมด <span>→</span></a>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentSettlements->count()): ?>
                <div class="recent-table-wrap">
                    <table class="kt-data-table">
                        <thead><tr><th>รายการ</th><th>กลุ่ม</th><th>วันที่</th><th>สถานะ</th><th class="table-amount">ยอดเงิน</th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentSettlements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <td><strong><?php echo e($item->debtor_id == Auth::id() ? 'คุณโอนให้ ' . ($item->creditor->name ?? 'สมาชิก') : ($item->debtor->name ?? 'สมาชิก') . ' โอนให้คุณ'); ?></strong></td>
                                    <td><?php echo e($item->group->name ?? '—'); ?></td>
                                    <td><?php echo e($item->transfer_datetime ? \Carbon\Carbon::parse($item->transfer_datetime)->format('d/m/Y') : $item->created_at->format('d/m/Y')); ?></td>
                                    <td><span class="status-pill status-<?php echo e($item->status); ?>"><?php echo e(['pending' => 'รอยืนยัน', 'approved' => 'ยืนยันแล้ว', 'rejected' => 'ปฏิเสธ'][$item->status] ?? $item->status); ?></span></td>
                                    <td class="table-amount">฿<?php echo e(number_format($item->amount, 2)); ?></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="dashboard-empty-inline"><p>ยังไม่มีประวัติการโอนเงิน</p><small>เมื่อมีการส่งสลิปหรือยืนยันการรับเงิน รายการจะปรากฏที่นี่</small></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/dashboard.blade.php ENDPATH**/ ?>