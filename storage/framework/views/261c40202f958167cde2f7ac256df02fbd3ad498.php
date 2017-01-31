<?php $__env->startSection('content'); ?>
	@parent
	<?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <div class="pull-right">
  <?php echo Button::normal(trans('texts.documentation'))->asLinkTo(NINJA_WEB_URL.'/api-documentation/')->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('info-sign')); ?>

  <?php if(!Utils::isReseller()): ?>
    <?php echo Button::normal(trans('texts.zapier'))->asLinkTo(ZAPIER_URL)->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('globe')); ?>

  <?php endif; ?>
  <?php if(Utils::hasFeature(FEATURE_API)): ?>
    <?php echo Button::primary(trans('texts.add_token'))->asLinkTo(URL::to('/tokens/create'))->appendIcon(Icon::create('plus-sign')); ?>

  <?php endif; ?>
  </div>

  <?php echo $__env->make('partials.bulk_form', ['entityType' => ENTITY_TOKEN], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <?php echo Datatable::table()
      ->addColumn(
        trans('texts.name'),
        trans('texts.token'),
        trans('texts.action'))
      ->setUrl(url('api/tokens/'))
      ->setOptions('sPaginationType', 'bootstrap')
      ->setOptions('bFilter', false)
      ->setOptions('bAutoWidth', false)
      ->setOptions('aoColumns', [[ "sWidth"=> "40%" ], [ "sWidth"=> "40%" ], ["sWidth"=> "20%"]])
      ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[2]]])
      ->render('datatable'); ?>


  <script>

    window.onDatatableReady = actionListHandler;

  </script>

  <?php if(!Utils::isReseller()): ?>
    <p>&nbsp;</p>
    <script src="https://zapier.com/zapbook/embed/widget.js?guided_zaps=5627,6025,12216,8805,5628,6027&container=false&limit=6"></script>
  <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>