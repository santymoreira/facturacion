<?php $__env->startSection('content'); ?>

	<?php echo Former::open($url)
            ->addClass('col-md-10 col-md-offset-1 warn-on-exit')
            ->method($method)
            ->rules([
                'name' => 'required',
				'client_id' => 'required',
            ]); ?>


    <?php if($project): ?>
        <?php echo Former::populate($project); ?>

    <?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

    </span>

	<div class="row">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo trans('texts.project'); ?></h3>
            </div>
            <div class="panel-body">

				<?php if($project): ?>
					<?php echo Former::plaintext('client_name')
							->value($project->client->getDisplayName()); ?>

				<?php else: ?>
					<?php echo Former::select('client_id')
							->addOption('', '')
							->label(trans('texts.client'))
							->addGroupClass('client-select'); ?>

				<?php endif; ?>

                <?php echo Former::text('name'); ?>



            </div>
            </div>

        </div>
    </div>


	<center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(url('/expense_categories'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

	</center>

	<?php echo Former::close(); ?>


    <script>

		var clients = <?php echo $clients; ?>;

        $(function() {
			var $clientSelect = $('select#client_id');
            for (var i=0; i<clients.length; i++) {
                var client = clients[i];
                var clientName = getClientDisplayName(client);
                if (!clientName) {
                    continue;
                }
                $clientSelect.append(new Option(clientName, client.public_id));
            }
			<?php if($clientPublicId): ?>
				$clientSelect.val(<?php echo e($clientPublicId); ?>);
			<?php endif; ?>
			$clientSelect.combobox().focus();

			$('.client-select input.form-control').focus();
        });

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>