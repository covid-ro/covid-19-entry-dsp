<?php $__env->startSection('content'); ?>
<div class="container">
    <?php if(session('message')): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card alert alert-<?php echo e(session('type')); ?> alert-dismissible fade show" role="alert">
                <span><?php echo e(session('message')); ?></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card alert ajax-msg alert-dismissible fade show">
                <span id="ajax-text-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <?php echo e(__('auth.Reset Password')); ?>

                    <div class="float-right">
                        <form method="POST" action="<?php echo e(route('reset-all-passwords')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <button type="submit" class="btn btn-danger btn-sm btn-top btn-reset-all">
                                    <?php echo e(__('app.Reset All Passwords')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('reset-password')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row">
                            <label for="user" class="col-md-4 col-form-label text-md-right"><?php echo e(__
                            ('app.User')); ?></label>

                            <div class="col-md-6">
                                <select id="user" name="user" class="form-control">
                                    <option value="" selected disabled style="display:none">
                                        <?php echo e(__('app.User select')); ?>

                                    </option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>">
                                            <?php echo e(ucwords(str_replace('-', ' ', trim($user->username)))); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('auth.Reset Password')); ?>

                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            $('.ajax-loader').css('visibility', 'visible');
        },
        complete: function(){
            $('.ajax-loader').css('visibility', 'hidden');
        }
    });

    $('.btn-reset-all').click(function(e){

        e.preventDefault();
        let userId = $('#user').val();

        $.ajax({
            type:'POST',
            url:"<?php echo e(route('reset-all-passwords')); ?>",
            data:{id:userId},
            success:function(data){
                if($.isEmptyObject(data.error)){
                    printAlertMsg(data.success, 'success');
                }else{
                    printAlertMsg(data.error, 'error');
                }
                setTimeout(function () {
                    if ($('.ajax-msg').is(':visible')){
                        $('.ajax-msg').fadeOut();
                    }
                }, 5000)
            }
        });

        function printAlertMsg (msg, type) {
            $('.ajax-msg').find('span#ajax-text-message').html(msg);
            $('.ajax-msg').addClass('alert-'+type);
            $('.ajax-msg').show();
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/reset_user_password.blade.php ENDPATH**/ ?>