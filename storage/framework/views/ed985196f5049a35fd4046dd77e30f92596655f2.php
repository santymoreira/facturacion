<?php $__env->startSection('content'); ?>
    @parent

    <?php echo Former::open_for_files()->addClass('warn-on-exit')->rules(array(
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'email|required'
    )); ?>


    <?php echo e(Former::populate($account)); ?>

    <?php echo e(Former::populateField('first_name', $user->first_name)); ?>

    <?php echo e(Former::populateField('last_name', $user->last_name)); ?>

    <?php echo e(Former::populateField('email', $user->email)); ?>

    <?php echo e(Former::populateField('phone', $user->phone)); ?>


    <?php if(Utils::isNinjaDev()): ?>
        <?php echo e(Former::populateField('dark_mode', intval($user->dark_mode))); ?>

    <?php endif; ?>

    <?php if(Input::has('affiliate')): ?>
        <?php echo e(Former::populateField('referral_code', true)); ?>

    <?php endif; ?>

    <?php if(Utils::isAdmin()): ?>
        <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_USER_DETAILS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><?php echo trans('texts.user_details'); ?></h3>
              </div>
                <div class="panel-body form-padding-right">
                <?php echo Former::text('first_name'); ?>

                <?php echo Former::text('last_name'); ?>

                <?php echo Former::text('email'); ?>

                <?php echo Former::text('phone'); ?>


                <br/>

                <?php if(Utils::isOAuthEnabled()): ?>
                    <?php echo Former::plaintext('oneclick_login')->value(
                            $user->oauth_provider_id ?
                                $oauthProviderName . ' - ' . link_to('#', trans('texts.disable'), ['onclick' => 'disableSocialLogin()']) :
                                DropdownButton::primary(trans('texts.enable'))->withContents($oauthLoginUrls)->small()
                        )->help('oneclick_login_help'); ?>

                <?php endif; ?>

                <?php if(Utils::isNinja()): ?>
                    <?php if($user->referral_code): ?>
                        <?php echo e(Former::setOption('capitalize_translations', false)); ?>

                        <?php echo Former::plaintext('referral_code')
                                ->help($referralCounts['free'] . ' ' . trans('texts.free') . ' | ' .
                                    $referralCounts['pro'] . ' ' . trans('texts.pro') .
                                    '<a href="'.REFERRAL_PROGRAM_URL.'" target="_blank" title="'.trans('texts.learn_more').'">' . Icon::create('question-sign') . '</a> ')
                                ->value(NINJA_APP_URL . '/invoice_now?rc=' . $user->referral_code); ?>

                    <?php else: ?>
                        <?php echo Former::checkbox('referral_code')
                                ->help(trans('texts.referral_code_help'))
                                ->text(trans('texts.enable') . ' <a href="'.REFERRAL_PROGRAM_URL.'" target="_blank" title="'.trans('texts.learn_more').'">' . Icon::create('question-sign') . '</a>'); ?>

                    <?php endif; ?>
                <?php endif; ?>

                <?php if(false && Utils::isNinjaDev()): ?>
                    <?php echo Former::checkbox('dark_mode')->text(trans('texts.dark_mode_help')); ?>

                <?php endif; ?>

                </div>
            </div>

        </div>
        <center>
            <?php if(Auth::user()->confirmed): ?>
                <?php echo Button::primary(trans('texts.change_password'))
                        ->appendIcon(Icon::create('lock'))
                        ->large()->withAttributes(['onclick'=>'showChangePassword()']); ?>

            <?php elseif(Auth::user()->registered && Utils::isNinja()): ?>
                <?php echo Button::primary(trans('texts.resend_confirmation'))
                        ->appendIcon(Icon::create('send'))
                        ->asLinkTo(URL::to('/resend_confirmation'))->large(); ?>

            <?php endif; ?>
            <?php echo Button::success(trans('texts.save'))
                    ->submit()->large()
                    ->appendIcon(Icon::create('floppy-disk')); ?>

        </center>
    </div>

    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="passwordModalLabel"><?php echo e(trans('texts.change_password')); ?></h4>
                </div>

                <div style="background-color: #fff" id="changePasswordDiv" onkeyup="validateChangePassword()" onclick="validateChangePassword()" onkeydown="checkForEnter(event)">
                    &nbsp;

                    <?php echo Former::password('current_password')->style('width:300px'); ?>

                    <?php echo Former::password('newer_password')->style('width:300px')->label(trans('texts.new_password')); ?>

                    <?php echo Former::password('confirm_password')->style('width:300px'); ?>


                    &nbsp;
                    <br/>
                    <center>
                        <div id="changePasswordError"></div>
                    </center>
                    <br/>
                </div>

                <div style="padding-left:40px;padding-right:40px;display:none;min-height:130px" id="working">
                    <h3><?php echo e(trans('texts.working')); ?>...</h3>
                    <div class="progress progress-striped active">
                        <div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>

                <div style="background-color: #fff; padding-right:20px;padding-left:20px; display:none" id="successDiv">
                    <br/>
                    <h3><?php echo e(trans('texts.success')); ?></h3>
                    <?php echo e(trans('texts.updated_password')); ?>

                    <br/>
                    &nbsp;
                    <br/>
                </div>

                <div class="modal-footer" style="margin-top: 0px" id="changePasswordFooter">
                    <button type="button" class="btn btn-default" id="cancelChangePasswordButton" data-dismiss="modal">
                        <?php echo e(trans('texts.cancel')); ?>

                        <i class="glyphicon glyphicon-remove-circle"></i>
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitChangePassword()" id="changePasswordButton" disabled>
                        <?php echo e(trans('texts.save')); ?>

                        <i class="glyphicon glyphicon-floppy-disk"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>


    <?php echo Former::close(); ?>


    <script type="text/javascript">

        $(function() {
            $('#passwordModal').on('hidden.bs.modal', function () {
                $(['current_password', 'newer_password', 'confirm_password']).each(function(i, field) {
                    var $input = $('form #'+field);
                    $input.val('');
                    $input.closest('div.form-group').removeClass('has-success');
                });
                $('#changePasswordButton').prop('disabled', true);
            })

            $('#passwordModal').on('shown.bs.modal', function () {
                $('#current_password').focus();
           })

            localStorage.setItem('auth_provider', '<?php echo e(strtolower($oauthProviderName)); ?>');
        });

        function showChangePassword() {
            $('#passwordModal').modal('show');
        }

        function validateChangePassword(showError)
        {
            var isFormValid = true;
            $(['current_password', 'newer_password', 'confirm_password']).each(function(i, field) {
                var $input = $('form #'+field),
                val = $.trim($input.val());
                var isValid = val;

                if (field != 'current_password') {
                    isValid = val.length >= 6;
                }

                if (isValid && field == 'confirm_password') {
                    isValid = val == $.trim($('#newer_password').val());
                }

                if (isValid) {
                    $input.closest('div.form-group').removeClass('has-error').addClass('has-success');
                } else {
                    isFormValid = false;
                    $input.closest('div.form-group').removeClass('has-success');
                    if (showError) {
                        $input.closest('div.form-group').addClass('has-error');
                    }
                }
            });

            $('#changePasswordButton').prop('disabled', !isFormValid);

            return isFormValid;
        }

        function submitChangePassword()
        {
            if (!validateChangePassword(true)) {
                return;
            }

            $('#changePasswordDiv, #changePasswordFooter').hide();
            $('#working').show();

            $.ajax({
              type: 'POST',
              url: '<?php echo e(URL::to('/users/change_password')); ?>',
              data: 'current_password=' + encodeURIComponent($('form #current_password').val()) +
              '&new_password=' + encodeURIComponent($('form #newer_password').val()) +
              '&confirm_password=' + encodeURIComponent($('form #confirm_password').val()),
              success: function(result) {
                if (result == 'success') {
                  NINJA.formIsChanged = false;
                  $('#changePasswordButton').hide();
                  $('#successDiv').show();
                  $('#cancelChangePasswordButton').html('<?php echo e(trans('texts.close')); ?>');
                } else {
                  $('#changePasswordError').html(result);
                  $('#changePasswordDiv').show();
                }
                $('#changePasswordFooter').show();
                $('#working').hide();
              }
            });
        }

        function disableSocialLogin() {
            sweetConfirm(function() {
                window.location = '<?php echo e(URL::to('/auth_unlink')); ?>';
            });
        }
    </script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('onReady'); ?>
    $('#first_name').focus();
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>