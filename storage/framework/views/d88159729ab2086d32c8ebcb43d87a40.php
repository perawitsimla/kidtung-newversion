<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f7f7f4">
    <title><?php echo $__env->yieldContent('title', 'KidTung'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/theme.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/kidtung-ui.css')); ?>">
    <?php echo $__env->yieldContent('head_scripts'); ?>
</head>
<body class="kt-app">
    <div class="kt-layout">
        <aside class="kt-nav" aria-label="เมนูหลัก">
            <div class="kt-nav-top">
                <a class="kt-brand" href="<?php echo e(url('/dashboard')); ?>" aria-label="KidTung - ภาพรวม">
                    <span class="kt-brand-mark" aria-hidden="true">฿</span>
                    <span class="kt-brand-copy">
                        <strong>KidTung</strong>
                        <small>แชร์ค่าใช้จ่ายกับเพื่อน</small>
                    </span>
                </a>

                <p class="kt-nav-label">เมนูหลัก</p>
                <nav aria-label="พื้นที่ทำงาน">
                    <ul class="kt-nav-list">
                        <li>
                            <a href="<?php echo e(url('/dashboard')); ?>" class="<?php echo e(request()->is('dashboard') ? 'active' : ''); ?>" <?php echo e(request()->is('dashboard') ? 'aria-current=page' : ''); ?>>
                                <span class="kt-nav-mark" aria-hidden="true">⌂</span>
                                <span>ภาพรวม</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('groups.index')); ?>" class="<?php echo e(request()->is('groups*') ? 'active' : ''); ?>" <?php echo e(request()->is('groups*') ? 'aria-current=page' : ''); ?>>
                                <span class="kt-nav-mark" aria-hidden="true">▦</span>
                                <span>กลุ่มของฉัน</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('settlements.index')); ?>" class="<?php echo e(request()->is('settlements*') ? 'active' : ''); ?>" <?php echo e(request()->is('settlements*') ? 'aria-current=page' : ''); ?>>
                                <span class="kt-nav-mark" aria-hidden="true">↔</span>
                                <span>รายการหนี้</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('history')); ?>" class="<?php echo e(request()->is('history') ? 'active' : ''); ?>" <?php echo e(request()->is('history') ? 'aria-current=page' : ''); ?>>
                                <span class="kt-nav-mark" aria-hidden="true">◷</span>
                                <span>ประวัติ</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="kt-nav-bottom">
                <div class="kt-account">
                    <span class="kt-account-avatar" aria-hidden="true"><?php echo e(mb_substr(auth()->user()->name ?? 'U', 0, 1)); ?></span>
                    <span class="kt-account-details">
                        <strong><?php echo e(auth()->user()->name ?? 'ผู้ใช้งาน'); ?></strong>
                        <small>บัญชีของคุณ</small>
                    </span>
                </div>
                <ul class="kt-nav-list kt-nav-secondary">
                    <li>
                        <a href="<?php echo e(route('profile.edit')); ?>" class="<?php echo e(request()->is('profile*') ? 'active' : ''); ?>" <?php echo e(request()->is('profile*') ? 'aria-current=page' : ''); ?>>
                            <span class="kt-nav-mark" aria-hidden="true">⚙</span>
                            <span>โปรไฟล์</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="kt-logout-button">
                                <span class="kt-nav-mark" aria-hidden="true">↪</span>
                                <span>ออกจากระบบ</span>
                            </button>
                        </form>
                    </li>
                </ul>
                <p class="kt-sidebar-footnote">KidTung <span>·</span> จัดการเงินให้ชัดเจน</p>
            </div>
        </aside>

        <main class="kt-main">
            <header class="kt-topbar">
                <span class="kt-topbar-label">หารด่วนเลยเดี๋ยวมีปัญหา</span>
                <span class="kt-topbar-user">
                    <span class="kt-topbar-dot" aria-hidden="true"></span>
                    <?php echo e(auth()->user()->name ?? 'ผู้ใช้งาน'); ?>

                </span>
            </header>

            <div class="kt-page-content">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['success', 'error', 'info', 'status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $messageType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session($messageType)): ?>
                        <div class="flash-message <?php echo e($messageType === 'success' ? 'alert-success' : ($messageType === 'error' ? 'alert-error' : '')); ?>" role="status">
                            <?php echo e(session($messageType)); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                    <div class="alert-error kt-validation-alert" role="alert">
                        <strong>กรุณาตรวจสอบข้อมูลอีกครั้ง</strong>
                        <ul>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <li><?php echo e($error); ?></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
            
        </main>
    </div>
</body>
</html>
<?php /**PATH /Users/kiwkiw/Downloads/kidtung-newversion-main 3/resources/views/layouts/app.blade.php ENDPATH**/ ?>