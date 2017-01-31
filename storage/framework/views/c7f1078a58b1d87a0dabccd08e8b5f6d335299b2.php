<?php $__env->startSection('content'); ?>
@parent
<?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_BANKS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php if(Auth::user()->hasFeature(FEATURE_EXPENSES)): ?>
    <div class="pull-right">
        <?php echo Button::normal(trans('texts.import_ofx'))
            ->asLinkTo(URL::to('/bank_accounts/import_ofx'))
            ->appendIcon(Icon::create('open')); ?>

        <?php echo Button::primary(trans('texts.add_bank_account'))
            ->asLinkTo(URL::to('/bank_accounts/create'))
            ->appendIcon(Icon::create('plus-sign')); ?>

    </div>
<?php endif; ?>

<?php echo $__env->make('partials.bulk_form', ['entityType' => ENTITY_BANK_ACCOUNT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo Datatable::table()
    ->addColumn(
        trans('texts.name'),
        trans('texts.integration_type'),
        trans('texts.action'))
    ->setUrl(url('api/bank_accounts/'))
    ->setOptions('sPaginationType', 'bootstrap')
    ->setOptions('bFilter', false)
    ->setOptions('bAutoWidth', false)
    ->setOptions('aoColumns', [[ "sWidth"=> "50%" ], [ "sWidth"=> "30%" ], ["sWidth"=> "20%"]])
    ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[2]]])
    ->render('datatable'); ?>


<script>
    window.onDatatableReady = actionListHandler;
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>