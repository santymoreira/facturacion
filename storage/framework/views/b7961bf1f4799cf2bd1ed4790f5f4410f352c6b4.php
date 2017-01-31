<?php $__env->startSection('content'); ?>


<center>
    <?php if(!session(SESSION_USER_ACCOUNTS) || count(session(SESSION_USER_ACCOUNTS)) < 5): ?>
        <?php echo Button::success(trans('texts.add_company'))->asLinkTo(url('/login?new_company=true')); ?>

    <?php endif; ?>
</center>

<p>&nbsp;</p>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-body">
            <table class="table table-striped">
            <?php foreach(Session::get(SESSION_USER_ACCOUNTS) as $account): ?>
                <tr>
                    <td>
                    <?php if(isset($account->logo_url)): ?>
                        <?php echo HTML::image($account->logo_url.'?no_cache='.time(), 'Logo', ['width' => 100]); ?>

                    <?php endif; ?>
                    </td>
                    <td>
                        <h3><?php echo e($account->account_name); ?><br/>
                        <small><?php echo e($account->user_name); ?>

                            <?php if($account->user_id == Auth::user()->id): ?>
                            | <?php echo e(trans('texts.current_user')); ?>

                            <?php endif; ?>
                        </small></h3>
                    </td>
                    <td><?php echo Button::primary(trans('texts.unlink'))->withAttributes(['onclick'=>"return showUnlink({$account->id}, {$account->user_id})"]); ?></td>
                </tr>
            <?php endforeach; ?>
            </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unlinkModal" tabindex="-1" role="dialog" aria-labelledby="unlinkModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo e(trans('texts.unlink_account')); ?></h4>
      </div>

      <div class="container">
        <h3><?php echo e(trans('texts.are_you_sure')); ?></h3>
      </div>

      <div class="modal-footer" id="signUpFooter">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(trans('texts.cancel')); ?></button>
        <button type="button" class="btn btn-primary" onclick="unlinkAccount()"><?php echo e(trans('texts.unlink')); ?></button>
      </div>
    </div>
  </div>
</div>


    <script type="text/javascript">
      function showUnlink(userAccountId, userId) {
        NINJA.unlink = {
            'userAccountId': userAccountId,
            'userId': userId
        };
        $('#unlinkModal').modal('show');
        return false;
      }

      function unlinkAccount() {
        window.location = '<?php echo e(URL::to('/unlink_account')); ?>' + '/' + NINJA.unlink.userAccountId + '/' + NINJA.unlink.userId;
      }

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>