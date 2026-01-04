<?php $__env->startSection('content'); ?>

    
    <div class="d-flex justify-content-center align-items-center py-5"
        style="min-height: 100vh; background-color: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center">
                
                <div class="col-lg-10 col-xl-9">

                    
                    <div class="card shadow-xl border-0" style="border-radius: 0.75rem; overflow: hidden;">
                        <div class="row g-0">

                            
                            <div class="col-md-5 p-5 d-flex align-items-center justify-content-center bg-white text-center">
                                <div>
                                    
                                    <div style="max-width: 250px; margin: 0 auto;">
                                        <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="BBQ-Lagao Logo"
                                            style="max-width: 160px; height: auto !important;">
                                    </div>
                                    <h3 class="fw-bold mt-3">BBQ Lagao & Pares</h3>
                                    <p class="text-muted">Create your account to start ordering delicious food!</p>
                                </div>
                            </div>

                            
                            <div class="col-md-7 p-4 p-md-5 text-white position-relative" style="background: linear-gradient(135deg, #8B1A1A 0%, #A52A2A 50%, #C84B31 100%); 
                                        background-size: cover; 
                                        background-position: center; 
                                        border-radius: 0 0.75rem 0.75rem 0;
                                        overflow: hidden;">

                                
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                    style="opacity: 0.1; pointer-events: none;">
                                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; 
                                                background: white; border-radius: 50%; filter: blur(60px);"></div>
                                    <div style="position: absolute; bottom: -80px; left: -80px; width: 250px; height: 250px; 
                                                background: white; border-radius: 50%; filter: blur(80px);"></div>
                                </div>

                                
                                <div class="position-relative" style="z-index: 1;">
                                    <h2 class="fw-bold mb-4 text-center text-uppercase pb-3" style="border-bottom: 2px solid rgba(255,255,255,0.3); 
                                               letter-spacing: 1px;
                                               text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                                        Register Account
                                    </h2>

                                    
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-light text-dark py-2 small mb-4" role="alert">
                                            <ul class="mb-0 ps-3">
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('register')); ?>" id="register-form">
                                        <?php echo csrf_field(); ?>

                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-user me-1"></i> Full Name
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="text"
                                                    class="form-control form-control-enhanced <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="name" name="name" value="<?php echo e(old('name')); ?>" required autofocus
                                                    placeholder="Enter your full name">
                                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($message); ?></strong>
                                                    </span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-envelope me-1"></i> Email Address
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="email"
                                                    class="form-control form-control-enhanced <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="email" name="email" value="<?php echo e(old('email')); ?>" required
                                                    placeholder="Enter your email">
                                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($message); ?></strong>
                                                    </span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3">
                                            <label for="phone" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-phone me-1"></i> Phone Number <span
                                                    class="text-white-50">(Optional)</span>
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="tel"
                                                    class="form-control form-control-enhanced <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="phone" name="phone" value="<?php echo e(old('phone')); ?>"
                                                    placeholder="Enter your phone number">
                                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($message); ?></strong>
                                                    </span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3">
                                            <label for="address" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i> Delivery Address
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="text"
                                                    class="form-control form-control-enhanced <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="address" name="address" value="<?php echo e(old('address')); ?>" required
                                                    placeholder="Enter your complete delivery address">
                                                <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($message); ?></strong>
                                                    </span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-lock me-1"></i> Password
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="password"
                                                    class="form-control form-control-enhanced <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    id="password" name="password" required
                                                    placeholder="Enter your password">
                                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($message); ?></strong>
                                                    </span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                            <small class="form-text text-white-50 d-block mt-1 ms-1">Minimum 6
                                                characters</small>
                                        </div>

                                        
                                        <div class="mb-4">
                                            <label for="confirm_password"
                                                class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-lock me-1"></i> Confirm Password
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="password" class="form-control form-control-enhanced"
                                                    id="confirm_password" name="password_confirmation" required
                                                    placeholder="Confirm your password">
                                            </div>
                                        </div>

                                        
                                        <div class="d-grid gap-2 mt-4">
                                            <button type="submit"
                                                class="btn btn-light btn-lg fw-bold py-3 register-submit-btn" style="color: #A52A2A; 
                                                           border-radius: 0.5rem;
                                                           box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                                           transition: all 0.3s ease;">
                                                <i class="fas fa-user-plus me-2"></i> Register Account
                                            </button>
                                        </div>
                                    </form>

                                    
                                    <div class="text-center pt-4 mt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                                        <p class="mb-3 text-white-50">Already have an account?</p>
                                        <a href="<?php echo e(route('login')); ?>"
                                            class="btn btn-outline-light w-100 fw-bold py-2 login-link-btn" style="border-radius: 0.5rem; 
                                                  border-width: 2px;
                                                  transition: all 0.3s ease;">
                                            <i class="fas fa-sign-in-alt me-2"></i> Login Here
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        // Client-side password confirmation check
        document.getElementById('register-form')?.addEventListener('submit', function (e) {
            const passwordField = document.getElementById('password');
            const password = passwordField.value;
            const confirmPasswordField = document.getElementById('confirm_password');
            const confirmPassword = confirmPasswordField.value;

            // Remove existing error classes first
            confirmPasswordField.classList.remove('is-invalid');

            if (password !== confirmPassword) {
                e.preventDefault();
                // Add error visual to the confirm field
                confirmPasswordField.classList.add('is-invalid');

                alert('Passwords do not match!');
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Enhanced Form Control Styles (Matching Login) */
        .form-control-enhanced {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: #333 !important;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control-enhanced::placeholder {
            color: rgba(0, 0, 0, 0.4);
        }

        .form-control-enhanced:focus {
            background-color: rgba(255, 255, 255, 1) !important;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
            border-color: white;
            color: #333 !important;
            transform: translateY(-2px);
        }

        /* Register Submit Button Hover Effect */
        .register-submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            background-color: white !important;
        }

        .register-submit-btn:active {
            transform: translateY(-1px);
        }

        /* Login Link Button Hover Effect */
        .login-link-btn {
            border-color: rgba(255, 255, 255, 0.6);
            background-color: transparent;
        }

        .login-link-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Label Animation */
        .form-label {
            transition: all 0.3s ease;
        }

        .form-control-enhanced:focus+.form-label,
        .form-control-enhanced:not(:placeholder-shown)+.form-label {
            color: white !important;
        }

        /* Remove input group text styling if not needed */
        .input-group-text {
            display: none;
        }

        /* Alert styling for better visibility on gradient background */
        .alert-light {
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\projectIT12\IT12L_FullProject_ERP\resources\views/auth/register.blade.php ENDPATH**/ ?>