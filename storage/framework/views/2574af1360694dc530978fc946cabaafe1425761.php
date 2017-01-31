<?php echo Former::open(Utils::pluralizeEntityType($entityType) . '/bulk')
		->addClass('listForm_' . $entityType); ?>


<div style="display:none">
	<?php echo Former::text('action')->id('action_' . $entityType); ?>

    <?php echo Former::text('public_id')->id('public_id_' . $entityType); ?>

    <?php echo Former::text('datatable')->value('true'); ?>

</div>

<div class="pull-left">
	<?php if (app('Illuminate\Contracts\Auth\Access\Gate')->check('create', 'invoice')): ?>
		<?php if($entityType == ENTITY_TASK): ?>
			<?php echo Button::primary(trans('texts.invoice'))->withAttributes(['class'=>'invoice', 'onclick' =>'submitForm_'.$entityType.'("invoice")'])->appendIcon(Icon::create('check')); ?>

		<?php endif; ?>
		<?php if($entityType == ENTITY_EXPENSE): ?>
			<?php echo Button::primary(trans('texts.invoice'))->withAttributes(['class'=>'invoice', 'onclick' =>'submitForm_'.$entityType.'("invoice")'])->appendIcon(Icon::create('check')); ?>

		<?php endif; ?>
	<?php endif; ?>

	<?php echo DropdownButton::normal(trans('texts.archive'))
			->withContents($datatable->bulkActions())
			->withAttributes(['class'=>'archive'])
			->split(); ?>


	&nbsp;
	<span id="statusWrapper_<?php echo e($entityType); ?>" style="display:none">
		<select class="form-control" style="width: 220px" id="statuses_<?php echo e($entityType); ?>" multiple="true">
			<?php if(count(\App\Models\EntityModel::getStatusesFor($entityType))): ?>
				<optgroup label="<?php echo e(trans('texts.entity_state')); ?>">
					<?php foreach(\App\Models\EntityModel::getStatesFor($entityType) as $key => $value): ?>
						<option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
					<?php endforeach; ?>
				</optgroup>
				<optgroup label="<?php echo e(trans('texts.status')); ?>">
					<?php foreach(\App\Models\EntityModel::getStatusesFor($entityType) as $key => $value): ?>
						<option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php else: ?>
				<?php foreach(\App\Models\EntityModel::getStatesFor($entityType) as $key => $value): ?>
					<option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</span>
</div>

<div id="top_right_buttons" class="pull-right">
	<input id="tableFilter_<?php echo e($entityType); ?>" type="text" style="width:140px;margin-right:17px;background-color: white !important"
        class="form-control pull-left" placeholder="<?php echo e(trans('texts.filter')); ?>" value="<?php echo e(Input::get('filter')); ?>"/>

    <?php if($entityType == ENTITY_EXPENSE): ?>
        <?php echo Button::normal(trans('texts.categories'))->asLinkTo(URL::to('/expense_categories'))->appendIcon(Icon::create('list')); ?>

	<?php elseif($entityType == ENTITY_TASK): ?>
		<?php echo Button::normal(trans('texts.projects'))->asLinkTo(URL::to('/projects'))->appendIcon(Icon::create('list')); ?>

    <?php endif; ?>

	<?php if(Auth::user()->can('create', $entityType)): ?>
    	<?php echo Button::primary(mtrans($entityType, "new_{$entityType}"))->asLinkTo(url(Utils::pluralizeEntityType($entityType) . '/create/' . (isset($clientId) ? $clientId : '')))->appendIcon(Icon::create('plus-sign')); ?>

	<?php endif; ?>

</div>


<?php echo Datatable::table()
	->addColumn(Utils::trans($datatable->columnFields()))
	->setUrl(url('api/' . Utils::pluralizeEntityType($entityType) . '/' . (isset($clientId) ? $clientId : (isset($vendorId) ? $vendorId : ''))))
    ->setCustomValues('rightAlign', isset($rightAlign) ? $rightAlign : [])
	->setCustomValues('entityType', Utils::pluralizeEntityType($entityType))
	->setCustomValues('clientId', isset($clientId) && $clientId)
	->setOptions('sPaginationType', 'bootstrap')
    ->setOptions('aaSorting', [[isset($clientId) ? ($datatable->sortCol-1) : $datatable->sortCol, 'desc']])
	->render('datatable'); ?>


<?php if($entityType == ENTITY_PAYMENT): ?>
	<div class="modal fade" id="paymentRefundModal" tabindex="-1" role="dialog" aria-labelledby="paymentRefundModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="min-width:150px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="paymentRefundModalLabel"><?php echo e(trans('texts.refund_payment')); ?></h4>
		  </div>

			<div class="modal-body">
				<div class="form-horizontal">
				  <div class="form-group">
					<label for="refundAmount" class="col-sm-offset-2 col-sm-2 control-label"><?php echo e(trans('texts.amount')); ?></label>
					<div class="col-sm-4">
						<div class="input-group">
								<span class="input-group-addon" id="refundCurrencySymbol"></span>
					  		<input type="number" class="form-control" id="refundAmount" name="amount" step="0.01" min="0.01" placeholder="<?php echo e(trans('texts.amount')); ?>">
						</div>
						<div class="help-block"><?php echo e(trans('texts.refund_max')); ?> <span id="refundMax"></span></div>
					</div>
				  </div>
				</div>
			</div>

		 <div class="modal-footer" style="margin-top: 0px">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(trans('texts.cancel')); ?></button>
			<button type="button" class="btn btn-primary" id="completeRefundButton"><?php echo e(trans('texts.refund')); ?></button>
		 </div>

		</div>
	  </div>
	</div>
<?php endif; ?>

<?php echo Former::close(); ?>


<script type="text/javascript">

	function submitForm_<?php echo e($entityType); ?>(action, id) {
		if (id) {
			$('#public_id_<?php echo e($entityType); ?>').val(id);
		}

		if (action == 'delete') {
	        sweetConfirm(function() {
	            $('#action_<?php echo e($entityType); ?>').val(action);
	    		$('form.listForm_<?php echo e($entityType); ?>').submit();
	        });
		} else {
			$('#action_<?php echo e($entityType); ?>').val(action);
			$('form.listForm_<?php echo e($entityType); ?>').submit();
	    }
	}

	<?php if($entityType == ENTITY_PAYMENT): ?>
		var paymentId = null;
		function showRefundModal(id, amount, formatted, symbol){
			paymentId = id;
			$('#refundCurrencySymbol').text(symbol);
			$('#refundMax').text(formatted);
			$('#refundAmount').val(amount).attr('max', amount);
			$('#paymentRefundModal').modal('show');
		}

		function handleRefundClicked(){
			submitForm_<?php echo e($entityType); ?>('refund', paymentId);
		}
	<?php endif; ?>

	$(function() {

		// Handle datatable filtering
	    var tableFilter = '';
	    var searchTimeout = false;

	    function filterTable_<?php echo e($entityType); ?>(val) {
	        if (val == tableFilter) {
	            return;
	        }
	        tableFilter = val;
			var oTable0 = $('.listForm_<?php echo e($entityType); ?> .data-table').dataTable();
	        oTable0.fnFilter(val);
	    }

	    $('#tableFilter_<?php echo e($entityType); ?>').on('keyup', function(){
	        if (searchTimeout) {
	            window.clearTimeout(searchTimeout);
	        }
	        searchTimeout = setTimeout(function() {
	            filterTable_<?php echo e($entityType); ?>($('#tableFilter_<?php echo e($entityType); ?>').val());
	        }, 500);
	    })

	    if ($('#tableFilter_<?php echo e($entityType); ?>').val()) {
	        filterTable_<?php echo e($entityType); ?>($('#tableFilter_<?php echo e($entityType); ?>').val());
	    }

		$('.listForm_<?php echo e($entityType); ?> .head0').click(function(event) {
			if (event.target.type !== 'checkbox') {
				$('.listForm_<?php echo e($entityType); ?> .head0 input[type=checkbox]').click();
			}
		});

		// Enable/disable bulk action buttons
	    window.onDatatableReady_<?php echo e(Utils::pluralizeEntityType($entityType)); ?> = function() {
	        $(':checkbox').click(function() {
	            setBulkActionsEnabled_<?php echo e($entityType); ?>();
	        });

	        $('.listForm_<?php echo e($entityType); ?> tbody tr').unbind('click').click(function(event) {
	            if (event.target.type !== 'checkbox' && event.target.type !== 'button' && event.target.tagName.toLowerCase() !== 'a') {
	                $checkbox = $(this).closest('tr').find(':checkbox:not(:disabled)');
	                var checked = $checkbox.prop('checked');
	                $checkbox.prop('checked', !checked);
	                setBulkActionsEnabled_<?php echo e($entityType); ?>();
	            }
	        });

	        actionListHandler();
	    }

		<?php if($entityType == ENTITY_PAYMENT): ?>
			$('#completeRefundButton').click(handleRefundClicked)
		<?php endif; ?>

	    $('.listForm_<?php echo e($entityType); ?> .archive, .invoice').prop('disabled', true);
	    $('.listForm_<?php echo e($entityType); ?> .archive:not(.dropdown-toggle)').click(function() {
	        submitForm_<?php echo e($entityType); ?>('archive');
	    });

	    $('.listForm_<?php echo e($entityType); ?> .selectAll').click(function() {
	        $(this).closest('table').find(':checkbox:not(:disabled)').prop('checked', this.checked);
	    });

	    function setBulkActionsEnabled_<?php echo e($entityType); ?>() {
	        var buttonLabel = "<?php echo e(trans('texts.archive')); ?>";
	        var count = $('.listForm_<?php echo e($entityType); ?> tbody :checkbox:checked').length;
	        $('.listForm_<?php echo e($entityType); ?> button.archive, .listForm_<?php echo e($entityType); ?> button.invoice').prop('disabled', !count);
	        if (count) {
	            buttonLabel += ' (' + count + ')';
	        }
	        $('.listForm_<?php echo e($entityType); ?> button.archive').not('.dropdown-toggle').text(buttonLabel);
	    }


		// Setup state/status filter
		$('#statuses_<?php echo e($entityType); ?>').select2({
			placeholder: "<?php echo e(trans('texts.status')); ?>",
			//allowClear: true,
			templateSelection: function(data, container) {
				if (data.id == 'archived') {
					$(container).css('color', '#fff');
					$(container).css('background-color', '#f0ad4e');
					$(container).css('border-color', '#eea236');
				} else if (data.id == 'deleted') {
					$(container).css('color', '#fff');
					$(container).css('background-color', '#d9534f');
					$(container).css('border-color', '#d43f3a');
				}
				return data.text;
			}
		}).val('<?php echo e(session('entity_state_filter:' . $entityType, STATUS_ACTIVE) . ',' . session('entity_status_filter:' . $entityType)); ?>'.split(','))
			  .trigger('change')
		  .on('change', function() {
			var filter = $('#statuses_<?php echo e($entityType); ?>').val();
			if (filter) {
				filter = filter.join(',');
			} else {
				filter = '';
			}
			var url = '<?php echo e(URL::to('set_entity_filter/' . $entityType)); ?>' + '/' + filter;
	        $.get(url, function(data) {
	            refreshDatatable();
	        })
		}).maximizeSelect2Height();

		$('#statusWrapper_<?php echo e($entityType); ?>').show();

	});

</script>
