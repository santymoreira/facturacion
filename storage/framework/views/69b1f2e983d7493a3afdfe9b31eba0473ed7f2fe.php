<?php $__env->startSection('onReady'); ?>
	$('input#name').focus();
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if($errors->first('contacts')): ?>
    <div class="alert alert-danger"><?php echo e(trans($errors->first('contacts'))); ?></div>
<?php endif; ?>

<div class="row">

	<?php echo Former::open($url)
            ->autocomplete('off')
            ->rules(
                ['email' => 'email']
            )->addClass('col-md-12 warn-on-exit')
            ->method($method); ?>


    <?php echo $__env->make('partials.autocomplete_fix', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<?php if($client): ?>
		<?php echo Former::populate($client); ?>

        <?php echo Former::hidden('public_id'); ?>

	<?php endif; ?>

	<div class="row">
		<div class="col-md-6">


        <div class="panel panel-default" style="min-height: 380px">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.organization'); ?></h3>
          </div>
            <div class="panel-body">

			<?php echo Former::text('name')->data_bind("attr { placeholder: placeholderName }"); ?>

			<?php echo Former::text('id_number'); ?>

                        <?php echo Former::text('vat_number'); ?>

                        <?php echo Former::text('website'); ?>

			<?php echo Former::text('work_phone'); ?>


			<?php if(Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS)): ?>
				<?php if($customLabel1): ?>
					<?php echo Former::text('custom_value1')->label($customLabel1); ?>

				<?php endif; ?>
				<?php if($customLabel2): ?>
					<?php echo Former::text('custom_value2')->label($customLabel2); ?>

				<?php endif; ?>
			<?php endif; ?>
            </div>
            </div>

        <div class="panel panel-default" style="min-height: 500px">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.address'); ?></h3>
          </div>
            <div class="panel-body">

			<?php echo Former::text('address1'); ?>

			<?php echo Former::text('address2'); ?>

			<?php echo Former::text('city'); ?>

			<?php echo Former::text('state'); ?>

			<?php echo Former::text('postal_code'); ?>

			<?php echo Former::select('country_id')->addOption('','')
				->fromQuery($countries, 'name', 'id'); ?>


        </div>
        </div>
		</div>
		<div class="col-md-6">


        <div class="panel panel-default" style="min-height: 380px">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.contacts'); ?></h3>
          </div>
            <div class="panel-body">

			<div data-bind='template: { foreach: contacts,
		                            beforeRemove: hideContact,
		                            afterAdd: showContact }'>
				<?php echo Former::hidden('public_id')->data_bind("value: public_id, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][public_id]'}"); ?>

				<?php echo Former::text('first_name')->data_bind("value: first_name, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][first_name]'}"); ?>

				<?php echo Former::text('last_name')->data_bind("value: last_name, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][last_name]'}"); ?>

				<?php echo Former::text('email')->data_bind("value: email, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][email]', id:'email'+\$index()}"); ?>

				<?php echo Former::text('phone')->data_bind("value: phone, valueUpdate: 'afterkeydown',
                        attr: {name: 'contacts[' + \$index() + '][phone]'}"); ?>

				<?php if($account->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $account->enable_portal_password): ?>
					<?php echo Former::password('password')->data_bind("value: password()?'-%unchanged%-':'', valueUpdate: 'afterkeydown',
						attr: {name: 'contacts[' + \$index() + '][password]'}"); ?>

			    <?php endif; ?>
				<div class="form-group">
					<div class="col-lg-8 col-lg-offset-4 bold">
						<span class="redlink bold" data-bind="visible: $parent.contacts().length > 1">
							<?php echo link_to('#', trans('texts.remove_contact').' -', array('data-bind'=>'click: $parent.removeContact')); ?>

						</span>
						<span data-bind="visible: $index() === ($parent.contacts().length - 1)" class="pull-right greenlink bold">
							<?php echo link_to('#', trans('texts.add_contact').' +', array('onclick'=>'return addContact()')); ?>

						</span>
					</div>
				</div>
			</div>
            </div>
            </div>


        <div class="panel panel-default" style="min-height: 500px">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.additional_info'); ?></h3>
          </div>
            <div class="panel-body">

            <?php echo Former::select('currency_id')->addOption('','')
                ->placeholder($account->currency ? $account->currency->name : '')
                ->fromQuery($currencies, 'name', 'id'); ?>

            <?php echo Former::select('language_id')->addOption('','')
                ->placeholder($account->language ? trans('texts.lang_'.$account->language->name) : '')
                ->fromQuery($languages, 'name', 'id'); ?>

			<?php echo Former::select('payment_terms')->addOption('','')
				->fromQuery($paymentTerms, 'name', 'num_days')
                ->help(trans('texts.payment_terms_help')); ?>

			<?php echo Former::select('size_id')->addOption('','')
				->fromQuery($sizes, 'name', 'id'); ?>

			<?php echo Former::select('industry_id')->addOption('','')
				->fromQuery($industries, 'name', 'id'); ?>

			<?php echo Former::textarea('private_notes'); ?>



            <?php if(Auth::user()->account->isNinjaAccount()): ?>
				<?php if(isset($planDetails)): ?>
					<?php echo Former::populateField('plan', $planDetails['plan']); ?>

					<?php echo Former::populateField('plan_term', $planDetails['term']); ?>

					<?php if(!empty($planDetails['paid'])): ?>
						<?php echo Former::populateField('plan_paid', $planDetails['paid']->format('Y-m-d')); ?>

					<?php endif; ?>
					<?php if(!empty($planDetails['expires'])): ?>
						<?php echo Former::populateField('plan_expires', $planDetails['expires']->format('Y-m-d')); ?>

					<?php endif; ?>
					<?php if(!empty($planDetails['started'])): ?>
						<?php echo Former::populateField('plan_started', $planDetails['started']->format('Y-m-d')); ?>

					<?php endif; ?>
				<?php endif; ?>
				<?php echo Former::select('plan')
							->addOption(trans('texts.plan_free'), PLAN_FREE)
							->addOption(trans('texts.plan_pro'), PLAN_PRO)
							->addOption(trans('texts.plan_enterprise'), PLAN_ENTERPRISE); ?>

				<?php echo Former::select('plan_term')
							->addOption()
							->addOption(trans('texts.plan_term_yearly'), PLAN_TERM_YEARLY)
							->addOption(trans('texts.plan_term_monthly'), PLAN_TERM_MONTHLY); ?>

				<?php echo Former::text('plan_started')
                            ->data_date_format('yyyy-mm-dd')
                            ->addGroupClass('plan_start_date')
                            ->append('<i class="glyphicon glyphicon-calendar"></i>'); ?>

                <?php echo Former::text('plan_paid')
                            ->data_date_format('yyyy-mm-dd')
                            ->addGroupClass('plan_paid_date')
                            ->append('<i class="glyphicon glyphicon-calendar"></i>'); ?>

				<?php echo Former::text('plan_expires')
                            ->data_date_format('yyyy-mm-dd')
                            ->addGroupClass('plan_expire_date')
                            ->append('<i class="glyphicon glyphicon-calendar"></i>'); ?>

                <script type="text/javascript">
                    $(function() {
                        $('#plan_started, #plan_paid, #plan_expires').datepicker();
                    });
                </script>
            <?php endif; ?>

            </div>
            </div>

		</div>
	</div>


	<?php echo Former::hidden('data')->data_bind("value: ko.toJSON(model)"); ?>


	<script type="text/javascript">

	$(function() {
		$('#country_id').combobox();
	});

	function ContactModel(data) {
		var self = this;
		self.public_id = ko.observable('');
		self.first_name = ko.observable('');
		self.last_name = ko.observable('');
		self.email = ko.observable('');
		self.phone = ko.observable('');
		self.password = ko.observable('');

		if (data) {
			ko.mapping.fromJS(data, {}, this);
		}
	}

	function ClientModel(data) {
		var self = this;

        self.contacts = ko.observableArray();

		self.mapping = {
		    'contacts': {
		    	create: function(options) {
		    		return new ContactModel(options.data);
		    	}
		    }
		}

		if (data) {
			ko.mapping.fromJS(data, self.mapping, this);
		} else {
			self.contacts.push(new ContactModel());
		}

		self.placeholderName = ko.computed(function() {
			if (self.contacts().length == 0) return '';
			var contact = self.contacts()[0];
			if (contact.first_name() || contact.last_name()) {
				return contact.first_name() + ' ' + contact.last_name();
			} else {
				return contact.email();
			}
		});
	}

    <?php if($data): ?>
        window.model = new ClientModel(<?php echo $data; ?>);
    <?php else: ?>
	    window.model = new ClientModel(<?php echo $client; ?>);
    <?php endif; ?>

	model.showContact = function(elem) { if (elem.nodeType === 1) $(elem).hide().slideDown() }
	model.hideContact = function(elem) { if (elem.nodeType === 1) $(elem).slideUp(function() { $(elem).remove(); }) }


	ko.applyBindings(model);

	function addContact() {
		model.contacts.push(new ContactModel());
		return false;
	}

	model.removeContact = function() {
		model.contacts.remove(this);
	}


	</script>

	<center class="buttons">
    	<?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/clients/' . ($client ? $client->public_id : '')))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

	</center>

	<?php echo Former::close(); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>