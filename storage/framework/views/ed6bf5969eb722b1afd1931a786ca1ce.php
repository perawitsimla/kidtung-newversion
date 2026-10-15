<?php $__env->startSection('content'); ?>
    <!-- ส่วนลิงก์กลับหน้าเดิม (Block element) -->
    <div>
        <a href="<?php echo e(route('groups.show', $group->id)); ?>">Back to Group</a>
    </div>

    <!-- ส่วนหัวข้อ (Block element) -->
    <div>
        <h1>สรุปการเคลียร์หนี้ (Settlements)</h1>
        <h2>กลุ่ม: <?php echo e($group->name); ?></h2>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $summaryRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    
                    <td <?php if(Auth::id() == $row['debtor']->id): ?> style="font-weight: bold; color: blue;" <?php endif; ?>>
                        <?php echo e($row['debtor']->name); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $row['debtor']->id): ?> 
                            (คุณ) 
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    
                    <td <?php if(Auth::id() == $row['creditor']->id): ?> style="font-weight: bold; color: blue;" <?php endif; ?>>
                        <?php echo e($row['creditor']->name); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $row['creditor']->id): ?> 
                            (คุณ) 
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    
                    <td><?php echo e(number_format($row['amount'], 2)); ?></td>
                    
                    <td style="color: <?php echo e($row['status_color']); ?>; font-weight: bold;">
                        <?php echo e($row['status']); ?>

                    </td>
                    
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['action'] === 'pay'): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $row['debtor']->id): ?>
                                <a href="<?php echo e(route('settlements.pay', ['group' => $group->id, 'creditor' => $row['creditor']->id])); ?>">ชำระเงิน</a>
                            <?php else: ?>
                                -
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php elseif($row['action'] === 'detail'): ?>
                            <a href="<?php echo e(route('settlements.show', $row['settlement_id'])); ?>">รายละเอียด</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $settlements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($history->transfer_datetime)->format('d/m/Y H:i')); ?></td>
                    <td><?php echo e($history->debtor->name); ?></td>
                    <td><?php echo e($history->creditor->name); ?></td>
                    <td><?php echo e(number_format($history->amount, 2)); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($history->status === 'approved'): ?>
                            <span style="color: green;">ชำระแล้ว</span>
                        <?php elseif($history->status === 'rejected'): ?>
                            <span style="color: red;">ปฏิเสธ</span>
                        <?php else: ?>
                            <span style="color: orange;">รอยืนยัน</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td><a href="<?php echo e(route('settlements.show', $history->id)); ?>">ดูสลิป</a></td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="6" style="text-align: center;">ยังไม่มีประวัติการโอนเงินในกลุ่มนี้</td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/groups/summary.blade.php ENDPATH**/ ?>