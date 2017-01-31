<?php $__env->startSection('content'); ?>
  @parent
  <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_USER_MANAGEMENT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <?php echo Former::open($url)->method($method)->addClass('warn-on-exit')->rules(array(
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required|email',
  ));; ?>


  <?php if($user): ?>
    <?php echo Former::populate($user); ?>

    <?php echo e(Former::populateField('is_admin', intval($user->is_admin))); ?>

    <?php echo e(Former::populateField('permissions[create_all]', intval($user->hasPermission('create')))); ?>

    <?php echo e(Former::populateField('permissions[view_all]', intval($user->hasPermission('view_all')))); ?>

    <?php echo e(Former::populateField('permissions[edit_all]', intval($user->hasPermission('edit_all')))); ?>

  <?php endif; ?>

<div class="panel panel-default">
<div class="panel-heading">
    <h3 class="panel-title"><?php echo trans('texts.user_details'); ?></h3>
</div>
<div class="panel-body form-padding-right">

  <?php echo Former::text('first_name'); ?>

  <?php echo Former::text('last_name'); ?>

  <?php echo Former::text('email'); ?>


</div>
</div>

<div class="panel panel-default">
<div class="panel-heading">
    <h3 class="panel-title"><?php echo trans('texts.permissions'); ?></h3>
</div>
<div class="panel-body form-padding-right">

    <?php if( ! Utils::hasFeature(FEATURE_USER_PERMISSIONS)): ?>
      <div class="alert alert-warning"><?php echo e(trans('texts.upgrade_for_permissions')); ?></div>
      <script>
          $(function() {
              $('input[type=checkbox]').prop('disabled', true);
          })
      </script>
    <?php endif; ?>

  <?php echo Former::checkbox('is_admin')
      ->label('&nbsp;')
      ->text(trans('texts.administrator'))
      ->help(trans('texts.administrator_help')); ?>

  <?php echo Former::checkbox('permissions[create_all]')
      ->value('create_all')
      ->label('&nbsp;')
      ->id('permissions_create_all')
      ->text(trans('texts.user_create_all'))
      ->help(trans('texts.create_all_help')); ?>

  <?php echo Former::checkbox('permissions[view_all]')
      ->value('view_all')
      ->label('&nbsp;')
      ->id('permissions_view_all')
      ->text(trans('texts.user_view_all'))
      ->help(trans('texts.view_all_help')); ?>

  <?php echo Former::checkbox('permissions[edit_all]')
      ->value('edit_all')
      ->label('&nbsp;')
      ->id('permissions_edit_all')
      ->text(trans('texts.user_edit_all'))
      ->help(trans('texts.edit_all_help')); ?>


</div>
</div>

  <?php echo Former::actions(
      Button::normal(trans('texts.cancel'))->asLinkTo(URL::to('/settings/user_management'))->appendIcon(Icon::create('remove-circle'))->large(),
      Button::success(trans($user && $user->confirmed ? 'texts.save' : 'texts.send_invite'))->submit()->large()->appendIcon(Icon::create($user && $user->confirmed ? 'floppy-disk' : 'send'))
  ); ?>


  <?php echo Former::close(); ?>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('onReady'); ?>
    $('#first_name').focus();
	$('#is_admin, #permissions_view_all').change(fixCheckboxes);
	function fixCheckboxes(){
		var adminChecked = $('#is_admin').is(':checked');
		var viewChecked = $('#permissions_view_all').is(':checked');

		$('#permissions_view_all').prop('disabled', adminChecked);
        $('#permissions_create_all').prop('disabled', adminChecked);
        $('#permissions_edit_all').prop('disabled', adminChecked || !viewChecked);
        if(!viewChecked)$('#permissions_edit_all').prop('checked',false)
	}
	fixCheckboxes();
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>