<?php $__env->startSection('body'); ?>

<?php echo Form::open(array('url' => 'get_started?' . request()->getQueryString(), 'id' => 'startForm')); ?>

<?php echo Form::hidden('guest_key'); ?>

<?php echo Form::hidden('sign_up', Input::get('sign_up')); ?>

<?php echo Form::hidden('redirect_to', Input::get('redirect_to')); ?>

<?php echo Form::close(); ?>


<script>
    if (isStorageSupported()) {
        $('[name="guest_key"]').val(localStorage.getItem('guest_key'));
    }

    $(function() {
        $('#startForm').submit();
    })

    function isStorageSupported() {
        if ('localStorage' in window && window['localStorage'] !== null) {
          var storage = window.localStorage;
      } else {
          return false;
      }
      var testKey = 'test';
      try {
          storage.setItem(testKey, '1');
          storage.removeItem(testKey);
          return true;
      } catch (error) {
          return false;
      }
  }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>