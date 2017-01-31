<div style="display:none">
    <?php echo Former::open($entityType . 's/bulk')->addClass('bulk-form'); ?>

    <?php echo Former::text('bulk_action'); ?>

    <?php echo Former::text('bulk_public_id'); ?>

    <?php echo Former::close(); ?>

</div>

<script type="text/javascript">
    function submitForm_<?php echo e($entityType); ?>(action, id) {
        if (action == 'delete') {
            if (!confirm('<?php echo trans("texts.are_you_sure"); ?>')) {
                return;
            }
        }

        $('#bulk_public_id').val(id);
        $('#bulk_action').val(action);

        $('form.bulk-form').submit();
    }
</script>
