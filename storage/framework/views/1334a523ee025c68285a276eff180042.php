<?php $__env->startSection('content'); ?>
    <!-- ส่วนหัวและปุ่มย้อนกลับ -->
    <div style="margin-bottom: 20px;">
        <a href="<?php echo e(route('groups.index')); ?>"><< กลับหน้ารวมกลุ่ม</a>
    </div>

    <!-- Container หลัก แบ่งซ้าย-ขวา -->
    <div style="display: flex; gap: 20px;">

        <!-- ================= ฝั่งซ้าย: ข้อมูลกลุ่มและสมาชิก (Left Column) ================= -->
        <div style="width: 30%; border: 1px solid black; padding: 15px; height: fit-content;">
            
            <!-- Block 1: ข้อมูลกลุ่ม -->
            <div>
                <h2><?php echo e($group->name); ?></h2>
                <p>รหัสเชิญ: <b><?php echo e($group->invite_code); ?></b></p>
                <p>สร้างเมื่อ: <?php echo e($group->created_at->format('Y-m-d')); ?></p>
                <p>สถานะของคุณในกลุ่มนี้: <b><?php echo e($myRole); ?></b></p>
            </div>

            <hr>

            <!-- Block 2: รายชื่อสมาชิก -->
            <div>
                <h3>สมาชิก (<?php echo e($group->members->count()); ?> คน)</h3>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li>
                            <?php echo e($member->name); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member->pivot->role === 'admin'): ?>
                                <span style="color: red;">[Admin]</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
            
        </div>


        <!-- ================= ฝั่งขวา: หนี้สิน และ รายการค่าใช้จ่าย (Right Column) ================= -->
        <div style="width: 70%;">
            
            <!-- Block 3: สรุปการเคลียร์หนี้ (Settlements) -->
            <div style="border: 1px solid black; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
                
                <!-- ส่วนหัวและปุ่มลิงก์ไปหน้า Summary -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="margin: 0;">ยอดค้างชำระของคุณ</h3>
                    
                    <a href="<?php echo e(route('groups.summary', $group->id)); ?>" style="padding: 8px 15px; background-color: #007bff; color: white; text-decoration: none; font-weight: bold; font-size: 14px;">
                    ดูสรุปยอดเคลียร์หนี้ทั้งหมด
                    </a>
                </div>

                <!-- แสดงรายการหนี้ที่ "ฉัน" ค้างคนอื่น -->
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $myDebts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li>
                            คุณติดหนี้ <b><?php echo e($debt['creditor_name']); ?></b> จำนวน <b style="color: red;"><?php echo e(number_format($debt['amount'], 2)); ?> บาท</b>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <li style="color: green; list-style-type: none;">คุณไม่มีหนี้ค้างชำระในกลุ่มนี้แล้ว</li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>

            </div>

            <!-- Block 4: ปุ่มสร้างรายการบิล -->
            <div style="margin-bottom: 20px;">
                <!-- ลิงก์ไปยังหน้าฟอร์มสร้างบิล -->
                <a href="<?php echo e(route('expenses.create', $group->id)); ?>" style="display: inline-block; padding: 10px 20px; background-color: #333; color: white; text-decoration: none; font-weight: bold;">
                    + เพิ่มค่าใช้จ่ายใหม่ (Add Expense)
                </a>
            </div>

            <!-- Block 5: ประวัติรายการบิล (Expense History) -->
            <div style="border: 1px solid black; padding: 15px;">
                <h3 style="margin-top: 0;">ประวัติค่าใช้จ่าย (Expenses)</h3>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group->expenses->count() > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group->expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            // คำนวณยอดเงินรวมของบิลใบนี้ด้วย PHP (ยอดดิบ + SC + VAT)
                            $subTotal = $expense->items->sum('price');
                            $scAmount = $subTotal * ($expense->sc_percent / 100);
                            $totalAfterSc = $subTotal + $scAmount;
                            $vatAmount = $totalAfterSc * ($expense->vat_percent / 100);
                            $grandTotal = $totalAfterSc + $vatAmount;

                            // ดึงชื่อคนที่ออกเงินไปก่อน (payer) ของบิลใบนี้ (เอาชื่อที่ไม่ซ้ำกันมารวมด้วย ,)
                            $payers = $expense->items->pluck('payer.name')->unique()->implode(', ');
                        ?>

                        <div style="border: 1px solid gray; padding: 10px; margin-bottom: 10px;">
                            <h4 style="margin: 0 0 10px 0;"><?php echo e($expense->title); ?></h4>
                            
                            <p style="margin: 0;">
                                ยอดรวม: <b><?php echo e(number_format($grandTotal, 2)); ?> บาท</b> 
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expense->sc_percent > 0): ?> | มี SC <?php echo e((float)$expense->sc_percent); ?>% <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($expense->vat_percent > 0): ?> | มี VAT <?php echo e((float)$expense->vat_percent); ?>% <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            
                            <p style="margin: 0; font-size: 14px; color: gray;">
                                จ่ายเงินโดย: <?php echo e($payers); ?> | วันที่บันทึก: <?php echo e($expense->created_at->format('Y-m-d H:i')); ?>

                            </p>
                            
                            <!-- ส่วน Block จัดการปุ่ม Action -->
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <!-- เช็คว่า เป็น Admin หรือ เป็นคนสร้างบิลนี้ ถึงจะเห็นปุ่ม -->
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myRole === 'admin' || $expense->created_by === Auth::id()): ?>
                                    <a href="<?php echo e(route('expenses.edit', $expense->id)); ?>" style="padding: 5px 10px; border: 1px solid black; background-color: #eee; text-decoration: none; color: black; font-size: 14px;">
                                        ดูรายละเอียด / แก้ไข
                                    </a>

                                    <form action="<?php echo e(route('expenses.destroy', $expense->id)); ?>" method="POST" style="margin: 0;" onsubmit="return confirm('แน่ใจหรือไม่ว่าต้องการลบบิลนี้?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" style="padding: 5px 10px; border: 1px solid red; background-color: #fee; color: red; cursor: pointer; font-size: 14px;">
                                            ลบบิลนี้
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <!-- ถ้าไม่มีสิทธิ์ ให้ขึ้นข้อความบอก หรือจะปล่อยว่างๆ ไว้ก็ได้ครับ -->
                                    <span style="color: gray; font-size: 14px;">(เฉพาะ Admin หรือผู้สร้างบิลที่แก้ไขได้)</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php else: ?>
                    <p style="color: gray; font-style: italic;">ยังไม่มีรายการค่าใช้จ่ายในกลุ่มนี้</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/groups/show.blade.php ENDPATH**/ ?>