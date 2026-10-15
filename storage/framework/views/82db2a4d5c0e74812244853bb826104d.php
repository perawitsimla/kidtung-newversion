<?php $__env->startSection('content'); ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($item->transfer_datetime ? \Carbon\Carbon::parse($item->transfer_datetime)->format('d/m/Y H:i') : '-'); ?></td>
                    <td><a href="<?php echo e(route('groups.show', $item->group_id)); ?>"><?php echo e($item->group->name); ?></a></td>
                    
                    <!-- ไฮไลท์ชื่อเราให้ดูง่ายขึ้น -->
                    <td <?php if($item->debtor_id == Auth::id()): ?> style="font-weight: bold; color: blue;" <?php endif; ?>>
                        <?php echo e($item->debtor->name); ?>

                    </td>
                    
                    <td <?php if($item->creditor_id == Auth::id()): ?> style="font-weight: bold; color: blue;" <?php endif; ?>>
                        <?php echo e($item->creditor->name); ?>

                    </td>
                    
                    <td><?php echo e(number_format($item->amount, 2)); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->status === 'approved'): ?>
                            <span style="color: green;">ชำระแล้ว</span>
                        <?php elseif($item->status === 'rejected'): ?>
                            <span style="color: red;">ปฏิเสธ</span>
                        <?php else: ?>
                            <span style="color: orange;">รอยืนยัน</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <!-- ลิงก์ไปดูสลิปหน้ารายละเอียด -->
                        <a href="<?php echo e(route('settlements.show', $item->id)); ?>" style="color: blue;">ดูสลิป</a>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: gray;">-- ยังไม่มีประวัติการทำรายการ --</td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/history.blade.php ENDPATH**/ ?>