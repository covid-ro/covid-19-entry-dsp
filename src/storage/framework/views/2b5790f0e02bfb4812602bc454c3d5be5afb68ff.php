<?php if( Auth::user()->username !== env('ADMIN_USER') ): ?>
<?php $__env->startSection('js_scripts'); ?>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/document-font-bold.js' )); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/document-font-normal.js' )); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/document-trans.js' )); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/document.js' )); ?>"></script>
<?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <?php if(session('message')): ?>
    <div class="alert alert-<?php echo e(session('type')); ?> alert-dismissible fade show" role="alert">
        <?php echo e(session('message')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    <?php if( Auth::user()->username !== env('ADMIN_USER') ): ?>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card alert ajax-msg alert-dismissible fade show">
                <span id="ajax-text-message"></span>
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="top-title float-left">
                    <?php echo e(__('app.Declaration header')); ?>

                    <?php if($declaration): ?> <strong><?php echo e($declaration['code']); ?></strong> <?php endif; ?>
                    </h5>
                    <?php if(!empty($signature)): ?>
                        <img src="/icons/check.svg" alt="" width="20px" height="20px">
                    <?php else: ?>
                        <img src="/icons/attention.svg" alt="" width="20px" height="20px">
                    <?php endif; ?>
                    <div class="float-right">
                        <form method="POST" action="<?php echo e(route('change-lang')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group row" id="change-language">
                                <select id="lang" name="lang" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="ro"<?php echo e(( app()->getLocale()== 'ro') ? ' selected' : ''); ?>><?php echo e(__('app.romanian')); ?></option>
                                    <option value="en"<?php echo e(( app()->getLocale()== 'en') ? ' selected' : ''); ?>><?php echo e(__('app.english')); ?></option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="float-right">
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-secondary btn-sm btn-top" role="button"
                           aria-pressed="true">
                            <?php echo e(__('app.Declarations list')); ?>

                        </a>
                    </div>
                    <?php if( Auth::user()->username !== env('ADMIN_USER') ): ?>
                    <div class="float-right">
                        <a href="javascript:void(0);" id="print-declaration" class="btn btn-danger btn-sm btn-top"
                           role="button"
                           aria-pressed="true">
                            <?php echo e(__('app.Print')); ?>

                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($declaration): ?>
                    <section id="declaration-view">
                        <div class="row border border-dark" id="header-declaration">
                            <div class="col-md-4 offset-4 text-center">
                                <h4 class="text-uppercase"><?php echo e(__('app.Declaration')); ?></h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <h4 class="text-uppercase"><?php echo e(( app()->getLocale()== 'ro') ? 'RO/EN' : 'EN/RO'); ?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <?php if( app()->getLocale() == 'ro' ): ?>
                                    <tr>
                                        <td width="20%"><?php echo e(__('app.Name in declaration')); ?>:</td>
                                        <td><strong class="text-uppercase"><?php echo e($declaration['name']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%"><?php echo e(__('app.Surname')); ?>:</td>
                                        <td><strong class="text-uppercase"><?php echo e($declaration['surname']); ?></strong></td>
                                    </tr>
                                    <?php else: ?>
                                    <tr>
                                        <td width="20%"><?php echo e(__('app.Surname')); ?>:</td>
                                        <td><strong class="text-uppercase"><?php echo e($declaration['surname']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%"><?php echo e(__('app.Name in declaration')); ?>:</td>
                                        <td><strong class="text-uppercase"><?php echo e($declaration['name']); ?></strong></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td width="20%"><?php echo e(__('app.Sex')); ?>:</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="sex-male"
                                                       onclick="return false;"<?php echo e($declaration['sex'] === 'M' ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="sex-male"><strong>M</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="sex-female"
                                                       onclick="return false;" <?php echo e($declaration['sex'] === 'F' ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="sex-female"><strong>F</strong></label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><?php echo e(__('app.Travelling from country')); ?>:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                <?php echo e($declaration['travelling_from_country']); ?>

                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><?php echo e(__('app.City')); ?>:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                <?php echo e($declaration['travelling_from_city']); ?>

                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><?php echo e(__('app.Date')); ?>:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                <?php echo e($declaration['travelling_from_date']); ?>

                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">
                                            <?php if( $declaration['document_type'] == 'passport'): ?>
                                            <?php echo e(__('app.Passport')); ?> /
                                            <span style="text-decoration: line-through;"><?php echo e(__('app.ID')); ?>:</span>
                                            <?php else: ?>
                                            <span style="text-decoration: line-through;">
                                                <?php echo e(__('app.Passport')); ?>

                                            </span> / <?php echo e(__('app.ID')); ?>:
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e(__('app.Series')); ?>:
                                            <strong class="text-uppercase">
                                                <?php echo e($declaration['document_series']); ?>

                                            </strong>
                                            <?php echo e(__('app.No')); ?>:
                                            <strong class="text-uppercase">
                                                <?php echo e($declaration['document_number']); ?>

                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><?php echo e(__('app.Date of birth')); ?></td>
                                        <td>
                                            <strong>
                                                <?php echo e($declaration['birth_date']); ?>

                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><?php echo e(__('app.Date of arrival')); ?></td>
                                        <?php if(is_null($declaration['border_validated_at'])): ?>
                                        <td>___________________</td>
                                        <?php else: ?>
                                        <td>
                                            <strong>
                                                <?php echo e($declaration['border_validated_at']); ?>

                                            </strong>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-right">
                                <img src="<?php echo e($qrCode); ?>" alt="" title="" />
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom">
                                    <strong><?php echo e(__('app.I estimate that I will be staying in Romania')); ?>:</strong>
                                </p>
                                <table class="table table-bordered border border-dark">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" width="50px" scope="col">
                                                <?php echo __('app.Table No'); ?>

                                            </th>
                                            <th class="text-center align-middle" scope="col"><?php echo __('app.Table Location (town/city)'); ?></th>
                                            <th class="text-center align-middle" scope="col"><?php echo __('app.Table Date of arrival'); ?></th>
                                            <th class="text-center align-middle" scope="col"><?php echo __('app.Table Date of departure'); ?></th>
                                            <th class="text-center align-middle" scope="col"><?php echo e(__('app.Table Complete address')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if( count($declaration['isolation_addresses']) > 0): ?>
                                        <?php $__currentLoopData = $declaration['isolation_addresses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($address['city']); ?>, <?php echo e($address['county']); ?></td>
                                            <td><?php echo e($address['city_arrival_date']); ?></td>
                                            <td><?php echo e($address['city_departure_date']); ?></td>
                                            <td><?php echo e($address['city_full_address']); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <?php for($i = 1; $i <= 3; $i++): ?>
                                        <tr>
                                            <?php for($j = 1; $j <= 5; $j++): ?>
                                            <td style="height: 2.5rem;"></td>
                                            <?php endfor; ?>
                                        </tr>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong><?php echo e(__('app.During my stay')); ?>:</strong></p>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>
                                            <?php echo e(__('app.Table Phone')); ?>: <strong><?php echo e($declaration['phone']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo e(__('app.Table E-mail')); ?>: <strong><?php echo e($declaration['email']); ?></strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong><?php echo e(__('app.Have you lived in')); ?>:</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" <?php echo e($declaration['q_visited'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label">
                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" <?php echo e(!$declaration['q_visited'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label">
                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong><?php echo e(__('app.Have you come in direct')); ?>:</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" <?php echo e($declaration['q_contacted'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label">
                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" <?php echo e(!$declaration['q_contacted'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label">
                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong><?php echo e(__('app.Have you been hospitalized')); ?>:</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" <?php echo e($declaration['q_hospitalized'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label">
                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" <?php echo e(!$declaration['q_hospitalized'] ? 'checked' : ''); ?>>
                                    <label class="form-check-label">
                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong><?php echo e(__('app.Have you had one')); ?>:</strong></p>
                                <table class="table table-bordered border border-dark">
                                    <tbody>
                                        <tr>
                                            <td><strong class="table-padding-left"><?php echo e(__('app.Fever')); ?></strong></td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e($declaration['fever'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e(!$declaration['fever'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="table-padding-left">
                                                    <?php echo e(__('app.Difficulty in swallowing')); ?>

                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e($declaration['swallow'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e(!$declaration['swallow'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="table-padding-left">
                                                    <?php echo e(__('app.Difficulty in breathing')); ?>

                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e($declaration['breath'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e(!$declaration['breath'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="table-padding-left">
                                                    <?php echo e(__('app.Intense coughing')); ?>

                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e($declaration['cough'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer Yes')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        <?php echo e(!$declaration['cough'] ? 'checked' : ''); ?>>
                                                    <label class="form-check-label">
                                                        <strong><?php echo e(__('app.Answer No')); ?></strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p>
                                    <?php echo __('app.Important notice and agreement'); ?>

                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    <?php echo __('app.I am aware that the refusal'); ?>

                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    <?php echo __('app.Acknowledging the provisions'); ?>&nbsp;
                                    <?php if(strlen($declaration['itinerary']) > 0): ?>
                                       <?php echo $declaration['itinerary']; ?>

                                    <?php else: ?>
                                        ____________________________________________________
                                    <?php endif; ?>
                                    &nbsp;<?php echo __('app.and that I will follow'); ?>&nbsp;
                                    <?php if(strlen($declaration['border']) > 0): ?>
                                        <strong><?php echo e($declaration['border']); ?></strong>.
                                    <?php else: ?>
                                        ____________________________________________________ <?php echo e(__('app.(name)')); ?>.
                                    <?php endif; ?>
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    <?php echo __('app.I declare on my own responsibility'); ?>:&nbsp;
                                    <?php if(strlen($declaration['travel_route']) > 0): ?>
                                        <strong><?php echo $declaration['travel_route']; ?></strong>,
                                    <?php else: ?>
                                        ____________________________________________________________________,
                                    <?php endif; ?>
                                    &nbsp;<?php echo __('app.for self-isolation or quarantine'); ?>:&nbsp;
                                    <?php if(strlen($declaration['vehicle_registration_no']) > 0): ?>
                                        <?php echo e(__('app.' . $declaration['vehicle_type'])); ?>

                                        <strong><?php echo e($declaration['vehicle_registration_no']); ?></strong>
                                    <?php else: ?>
                                        _____________________ <?php echo e(__('app.indicate car or ambulance')); ?>

                                    <?php endif; ?>
                                    &nbsp;, <?php echo e(__('app.following the route')); ?>:<br />
                                    __________________________________________________________________________________ .
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    <?php echo e(__('app.I agree that the provided information')); ?>.
                                </p>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong><?php echo e(__('app.Date and place')); ?></strong>:</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php echo e($declaration['current_date']); ?>,&nbsp;
                                            <?php if(strlen($declaration['border']) > 0): ?>
                                                <?php echo e($declaration['border']); ?>

                                            <?php else: ?>
                                                ________________________________
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="legend-top-margin">
                                            <small>
                                                <?php echo __('app.Legend for DSP staff'); ?>

                                            </small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong><?php echo e(__('app.Signature')); ?></strong>:</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php if(strlen($signature) > 0): ?>
                                                <img src="<?php echo e($signature); ?>" alt="" title="" />
                                            <?php else: ?>
                                                ________________________________
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </section>
                    <?php if( Auth::user()->username !== env('ADMIN_USER') ): ?>
                    <script type="text/javascript">
                        $(document).ready( function () {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $('#print-declaration').click( function (e) {
                                let declarationCode = "<?php echo e($declaration['code']); ?>";
                                let signature = '<?php echo e($signature); ?>';
                                let qrcode = '<?php echo e($qrCode); ?>';
                                let dataPdf = <?php echo $pdfData; ?>;
                                let doc = new Document();

                                e.preventDefault();
                                $.ajax({
                                    type:'POST',
                                    url:"<?php echo e(route('register-declaration')); ?>",
                                    data:{code:declarationCode},
                                    success:function(data){
                                        if($.isEmptyObject(data.error)){
                                            $.ajax({
                                                type:'POST',
                                                url:"<?php echo e(route('refresh-list')); ?>",
                                                data:{refresh:true},
                                                success:function(data){
                                                    doc.download(dataPdf, signature, qrcode);
                                                }
                                            });
                                        }else{
                                            printAlertMsg(data.error, 'danger');
                                        }
                                        setTimeout(function () {
                                            $('.ajax-msg').removeClass('alert-danger alert-success');
                                            if ($('.ajax-msg').is(':visible')){
                                                $('.ajax-msg').fadeOut();
                                            }
                                        }, 5000)
                                    }
                                });
                            });

                            function printAlertMsg (msg, type) {
                                $('.ajax-msg').find('span#ajax-text-message').html(msg);
                                $('.ajax-msg').addClass('alert-'+type);
                                $('.ajax-msg').show();
                            }

                            $('.alert button').click(function(e){
                                e.preventDefault();
                                $(this).parent().hide().removeClass('alert-danger alert-success');
                                return false;
                            });
                        });
                    </script>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/declaration.blade.php ENDPATH**/ ?>