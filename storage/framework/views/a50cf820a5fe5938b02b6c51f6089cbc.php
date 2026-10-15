<?php $__env->startSection('title', 'โปรไฟล์ | KidTung'); ?>

<?php $__env->startSection('content'); ?>
    <div class="profile-page">
        <div class="profile-heading">
            <div>
                <p class="page-eyebrow">ACCOUNT SETTINGS</p>
                <h1>โปรไฟล์ของฉัน</h1>
                <p class="page-description">จัดการข้อมูลที่ใช้ในกลุ่มและช่องทางรับเงินของคุณ</p>
            </div>
        </div>

        <div class="profile-layout-grid">
            <aside class="profile-overview-column">
                <section class="profile-identity-card">
                    <div class="profile-identity-top">
                        <span class="profile-large-avatar"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                        <span class="profile-active-dot" title="บัญชีของคุณ"></span>
                    </div>
                    <h2><?php echo e($user->name); ?></h2>
                    <p class="profile-email"><?php echo e($user->email); ?></p>
                    <span class="profile-account-tag">บัญชี KidTung</span>
                    <div class="profile-identity-divider"></div>
                    <div class="profile-identity-meta"><span>สมาชิกตั้งแต่</span><strong><?php echo e($user->created_at ? $user->created_at->format('d/m/Y') : '—'); ?></strong></div>
                    <div class="profile-identity-meta"><span>ช่องทางรับเงิน</span><strong><?php echo e($user->promptpay_no ? 'ตั้งค่าแล้ว' : 'ยังไม่ได้ตั้งค่า'); ?></strong></div>
                </section>

               
            </aside>

            <section class="profile-settings-card">
                <div class="profile-settings-heading">
                    <span class="profile-settings-icon">⚙</span>
                    <div><h2>ข้อมูลส่วนตัว</h2><p>ข้อมูลเหล่านี้จะใช้แสดงในรายการและหน้ากลุ่ม</p></div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                    <div class="profile-success-message"><span>✓</span><?php echo e(session('success')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                    <div class="profile-error-message">
                        <strong>กรุณาตรวจสอบข้อมูล</strong>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><p><?php echo e($error); ?></p><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <form action="<?php echo e(route('profile.update')); ?>" method="POST" class="profile-settings-form">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="profile-field-group">
                        <label for="name">ชื่อที่แสดงในระบบ</label>
                        <input id="name" type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" maxlength="255" required>
                        <small>สมาชิกในกลุ่มจะเห็นชื่อนี้ในรายการแบ่งค่าใช้จ่าย</small>
                    </div>
                    <div class="profile-field-group">
                        <label for="email">อีเมล</label>
                        <input id="email" type="email" value="<?php echo e($user->email); ?>" readonly disabled>
                        <small>อีเมลใช้เข้าสู่ระบบ และยังไม่สามารถเปลี่ยนจากหน้านี้ได้</small>
                    </div>

                    <div class="profile-form-section-divider"></div>

                    <div class="profile-settings-heading profile-payment-heading">
                        <span class="profile-settings-icon profile-payment-icon">฿</span>
                        <div><h2>ข้อมูลรับเงิน</h2><p>ใช้แสดงให้เพื่อนเมื่อถึงเวลาชำระหนี้</p></div>
                    </div>
                    <div class="profile-field-group">
                        <label for="promptpay_no">เบอร์หรือเลขพร้อมเพย์</label>
                        <input id="promptpay_no" type="text" name="promptpay_no" value="<?php echo e(old('promptpay_no', $user->promptpay_no)); ?>" maxlength="20" placeholder="เช่น เบอร์โทรศัพท์ที่ผูกพร้อมเพย์">
                        <small>ข้อมูลนี้เป็นตัวช่วยให้เพื่อนโอนเงินถูกบัญชี ตรวจสอบข้อมูลก่อนบันทึก</small>
                    </div>

                    <div class="profile-form-actions">
                        <a class="kt-button kt-button-secondary" href="<?php echo e(route('dashboard')); ?>">กลับหน้าภาพรวม</a>
                        <button class="kt-button kt-button-primary" type="submit">บันทึกข้อมูล</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/profile/edit.blade.php ENDPATH**/ ?>