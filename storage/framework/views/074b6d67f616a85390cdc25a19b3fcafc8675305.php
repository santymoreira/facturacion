<?php $__env->startSection('head'); ?>
    @parent

    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <style type="text/css">
        .input-group-addon {
            min-width: 40px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

	<?php echo Former::open($url)
        ->addClass('col-md-10 col-md-offset-1 warn-on-exit')
        ->onsubmit('onFormSubmit(event)')
        ->method($method)
        ->rules(array(
    		'client' => 'required',
    		'invoice' => 'required',
    		'amount' => 'required',
    	)); ?>


    <?php if($payment): ?>
        <?php echo Former::populate($payment); ?>

    <?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

    </span>

	<div class="row">
		<div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
            <div class="panel-body">

            <?php if(!$payment): ?>
			 <?php echo Former::select('client')->addOption('', '')->addGroupClass('client-select'); ?>

			 <?php echo Former::select('invoice')->addOption('', '')->addGroupClass('invoice-select'); ?>

			 <?php echo Former::text('amount'); ?>


             <?php if(isset($paymentTypeId) && $paymentTypeId): ?>
               <?php echo Former::populateField('payment_type_id', $paymentTypeId); ?>

             <?php endif; ?>
            <?php endif; ?>

            <?php if(!$payment || !$payment->account_gateway_id): ?>
			 <?php echo Former::select('payment_type_id')
                    ->addOption('','')
                    ->fromQuery($paymentTypes, 'name', 'id')
                    ->addGroupClass('payment-type-select'); ?>

            <?php endif; ?>

			<?php echo Former::text('payment_date')
                        ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT))
                        ->addGroupClass('payment_date')
                        ->append('<i class="glyphicon glyphicon-calendar"></i>'); ?>

			<?php echo Former::text('transaction_reference'); ?>


            <?php if(!$payment): ?>
                <?php echo Former::checkbox('email_receipt')->label('&nbsp;')->text(trans('texts.email_receipt')); ?>

            <?php endif; ?>

            </div>
            </div>

		</div>
	</div>


	<center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->appendIcon(Icon::create('remove-circle'))->asLinkTo(URL::to('/payments'))->large(); ?>

        <?php if(!$payment || !$payment->is_deleted): ?>
            <?php echo Button::success(trans('texts.save'))->withAttributes(['id' => 'saveButton'])->appendIcon(Icon::create('floppy-disk'))->submit()->large(); ?>

        <?php endif; ?>
	</center>

	<?php echo Former::close(); ?>


	<script type="text/javascript">

	var invoices = <?php echo $invoices; ?>;
	var clients = <?php echo $clients; ?>;

	$(function() {

        <?php if($payment): ?>
          $('#payment_date').datepicker('update', '<?php echo e($payment->payment_date); ?>')
          <?php if($payment->payment_type_id != PAYMENT_TYPE_CREDIT): ?>
            $("#payment_type_id option[value='<?php echo e(PAYMENT_TYPE_CREDIT); ?>']").remove();
          <?php endif; ?>
        <?php else: ?>
          $('#payment_date').datepicker('update', new Date());
		  populateInvoiceComboboxes(<?php echo e($clientPublicId); ?>, <?php echo e($invoicePublicId); ?>);
        <?php endif; ?>

		$('#payment_type_id').combobox();

        <?php if(!$payment && !$clientPublicId): ?>
            $('.client-select input.form-control').focus();
        <?php elseif(!$payment && !$invoicePublicId): ?>
            $('.invoice-select input.form-control').focus();
        <?php elseif(!$payment): ?>
            $('#amount').focus();
        <?php endif; ?>

        $('.payment_date .input-group-addon').click(function() {
            toggleDatePicker('payment_date');
        });
	});

    function onFormSubmit(event) {
        $('#saveButton').attr('disabled', true);
    }

	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>