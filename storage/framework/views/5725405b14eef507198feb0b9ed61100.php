<?php $__env->startSection('content'); ?>
    <h2>สร้างกลุ่มหารเงิน (Create Group)</h2>

    <hr>

    <!-- พื้นที่แสดง Flash Message (Success / Error) -->
    <div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 10px;">
                <strong><?php echo e(session('success')); ?></strong>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
                <strong><?php echo e(session('error')); ?></strong>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- พื้นที่แสดง Error จากการทำ Validation -->
    <div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
                <b>พบข้อผิดพลาด:</b>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- แบบฟอร์มสร้างกลุ่ม -->
    <div style="margin-top: 20px;">
        <form action="<?php echo e(route('groups.store')); ?>" method="POST">
            <?php echo csrf_field(); ?> <!-- ป้องกัน CSRF ตามที่เรียน -->
            
            <div>
                <label for="name">ชื่อกลุ่ม / ชื่อทริป: </label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required>
            </div>
            
            <br>
            <button type="submit">สร้างกลุ่ม</button>
        </form>
    </div>

    <br>
    <div>
        <a href="<?php echo e(route('groups.index')); ?>"><< กลับหน้ารวมกลุ่ม</a>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/groups/create.blade.php ENDPATH**/ ?>