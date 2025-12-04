<?php $__env->startSection('content'); ?>


<div class="d-flex justify-content-center align-items-center py-5" style="min-height: 100vh; background-color: #f8f9fa;">
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
                                <p class="text-muted">Welcome back! Please login to continue.</p>
                            </div>
                        </div>

                        
                        <div class="col-md-7 p-4 p-md-5 text-white" style="background-color: #A52A2A; background-size: cover; background-position: center; border-radius: 0 0.75rem 0.75rem 0;">
                            
                            <h2 class="fw-bold mb-4 text-center text-uppercase border-bottom pb-3">Account Login</h2>

                            <form method="POST" action="<?php echo e(route('login')); ?>">
                                <?php echo csrf_field(); ?>

                                
                                <div class="mb-3">
                                    <label for="email" class="form-label visually-hidden">Email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-envelope"></i></span>
                                        <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus placeholder="Email Address">
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

                                
                                <div class="mb-4">
                                    <label for="password" class="form-label visually-hidden">Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-lock"></i></span>
                                        <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required autocomplete="current-password" placeholder="Password">
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
                                </div>

                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-light btn-lg fw-bold mb-3" style="color: #A52A2A;">
                                        <i class="fas fa-sign-in-alt me-2"></i> Login
                                    </button>
                                </div>
                            </form>
                            
                            
                            <div class="text-center pt-3 border-top border-white border-opacity-25">
                                <p class="mb-2">Don't have an account?</p>
                                <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-light w-100 fw-bold">
                                    Register Here
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                
                <div class="card mt-3 shadow-sm border-0" style="border-radius: 0.5rem;">
                    <div class="card-body p-3">
                        <h6 class="card-title fw-bold mb-2">
                            <i class="fas fa-info-circle text-info me-1"></i> Demo Credentials
                        </h6>
                        <p class="card-text small mb-1 text-muted">
                            <span class="fw-bold">Admin:</span>
                            Email: admin@foodorder.com | Password: admin12345
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Define Primary Color to match the dark side of the design */
:root {
    --bs-primary: #A52A2A; 
}

/* Custom styles for inputs on the dark background */
.col-md-7 input.form-control {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 0.3rem;
}
.col-md-7 input.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}
.col-md-7 input.form-control:focus {
    background-color: rgba(255, 255, 255, 0.25);
    box-shadow: none; /* Removed default Bootstrap shadow for cleaner look */
    border-color: white;
}

/* Customizing input group text for the dark side */
.input-group-text {
    border-right: none !important;
}

/* Styling the secondary register button */
.btn-outline-light {
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transition: all 0.3s ease;
}
.btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.15);
    color: white;
    border-color: white;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\llena\IT12L_FullProject_ERP\resources\views/auth/login.blade.php ENDPATH**/ ?>