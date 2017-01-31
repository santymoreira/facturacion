<?php $__env->startSection('head'); ?>
@parent

    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <link href='https://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>

    <style>
    .checkbox-inline input[type="checkbox"] {
        margin-left:-20px !important;
    }
    </style>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
@parent

<?php echo Former::open_for_files()
->addClass('warn-on-exit'); ?>


<?php echo Former::populateField('enable_client_portal', intval($account->enable_client_portal)); ?>

<?php echo Former::populateField('enable_client_portal_dashboard', intval($account->enable_client_portal_dashboard)); ?>

<?php echo Former::populateField('client_view_css', $client_view_css); ?>

<?php echo Former::populateField('enable_portal_password', intval($enable_portal_password)); ?>

<?php echo Former::populateField('send_portal_password', intval($send_portal_password)); ?>

<?php echo Former::populateField('enable_buy_now_buttons', intval($account->enable_buy_now_buttons)); ?>

<?php echo Former::populateField('show_accept_invoice_terms', intval($account->show_accept_invoice_terms)); ?>

<?php echo Former::populateField('show_accept_quote_terms', intval($account->show_accept_quote_terms)); ?>

<?php echo Former::populateField('require_invoice_signature', intval($account->require_invoice_signature)); ?>

<?php echo Former::populateField('require_quote_signature', intval($account->require_quote_signature)); ?>


<?php if(!Utils::isNinja() && !Auth::user()->account->hasFeature(FEATURE_WHITE_LABEL)): ?>
<div class="alert alert-warning" style="font-size:larger;">
	<center>
		<?php echo trans('texts.white_label_custom_css', ['price' => WHITE_LABEL_PRICE, 'link'=>'<a href="#" onclick="$(\'#whiteLabelModal\').modal(\'show\');">'.trans('texts.white_label_purchase_link').'</a>']); ?>

	</center>
</div>
<?php endif; ?>

<?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_CLIENT_PORTAL], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo trans('texts.navigation'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="col-md-10 col-md-offset-1">
                    <?php echo Former::checkbox('enable_client_portal')
                        ->text(trans('texts.enable'))
                        ->help(trans('texts.enable_client_portal_help')); ?>

                </div>
                <div class="col-md-10 col-md-offset-1">
                    <?php echo Former::checkbox('enable_client_portal_dashboard')
                        ->text(trans('texts.enable'))
                        ->help(trans('texts.enable_client_portal_dashboard_help')); ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo trans('texts.authorization'); ?></h3>
            </div>
            <div class="panel-body">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist" style="border: none">
                        <li role="presentation" class="active"><a href="#password" aria-controls="password" role="tab" data-toggle="tab"><?php echo e(trans('texts.password')); ?></a></li>
                        <li role="presentation"><a href="#checkbox" aria-controls="checkbox" role="tab" data-toggle="tab"><?php echo e(trans('texts.checkbox')); ?></a></li>
                        <li role="presentation"><a href="#signature" aria-controls="signature" role="tab" data-toggle="tab"><?php echo e(trans('texts.invoice_signature')); ?></a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="password">
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <?php echo Former::checkbox('enable_portal_password')
                                    ->text(trans('texts.enable'))
                                    ->help(trans('texts.enable_portal_password_help'))
                                    ->label(trans('texts.enable_portal_password')); ?>

                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <?php echo Former::checkbox('send_portal_password')
                                    ->text(trans('texts.enable'))
                                    ->help(trans('texts.send_portal_password_help'))
                                    ->label(trans('texts.send_portal_password')); ?>

                            </div>
                        </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="checkbox">
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <?php echo Former::checkbox('show_accept_invoice_terms')
                                    ->text(trans('texts.enable'))
                                    ->help(trans('texts.show_accept_invoice_terms_help'))
                                    ->label(trans('texts.show_accept_invoice_terms')); ?>

                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <?php echo Former::checkbox('show_accept_quote_terms')
                                    ->text(trans('texts.enable'))
                                    ->help(trans('texts.show_accept_quote_terms_help'))
                                    ->label(trans('texts.show_accept_quote_terms')); ?>

                            </div>
                        </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="signature">
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <?php echo Former::checkbox('require_invoice_signature')
                                    ->text(trans('texts.enable'))
                                    ->help(trans('texts.require_invoice_signature_help'))
                                    ->label(trans('texts.require_invoice_signature')); ?>

                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <?php echo Former::checkbox('require_quote_signature')
                                    ->text(trans('texts.enable'))
                                    ->help(trans('texts.require_quote_signature_help'))
                                    ->label(trans('texts.require_quote_signature')); ?>

                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" id="buy_now">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo trans('texts.buy_now_buttons'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="col-md-10 col-md-offset-1">

                    <?php if(count($gateway_types) && count($products)): ?>

                        <?php echo Former::checkbox('enable_buy_now_buttons')
                            ->text(trans('texts.enable'))
                            ->label(' ')
                            ->help(trans('texts.enable_buy_now_buttons_help')); ?>


                        <?php if($account->enable_buy_now_buttons): ?>
                            <?php echo Former::select('product')
                                ->onchange('updateBuyNowButtons()')
                                ->addOption('', '')
                                ->inlineHelp('buy_now_buttons_warning')
                                ->addGroupClass('product-select'); ?>


                            <?php echo Former::text('redirect_url')
                                    ->onchange('updateBuyNowButtons()')
                                    ->placeholder('https://www.example.com')
                                    ->help('redirect_url_help'); ?>


                            <?php echo Former::checkboxes('client_fields')
                                    ->onchange('updateBuyNowButtons()')
                                    ->checkboxes([
                                        trans('texts.first_name') => ['value' => 'first_name', 'name' => 'first_name'],
                                        trans('texts.last_name') => ['value' => 'last_name', 'name' => 'last_name'],
                                        trans('texts.email') => ['value' => 'email', 'name' => 'email'],
                                    ]); ?>


                            <?php echo Former::inline_radios('landing_page')
                                    ->onchange('showPaymentTypes();updateBuyNowButtons();')
                                    ->radios([
                                        trans('texts.invoice') => ['value' => 'invoice', 'name' => 'landing_page_type'],
                                        trans('texts.payment') => ['value' => 'payment', 'name' => 'landing_page_type'],
                                    ])->check('invoice'); ?>


                            <div id="paymentTypesDiv" style="display:none">
                                <?php echo Former::select('payment_type')
                                    ->onchange('updateBuyNowButtons()')
                                    ->options($gateway_types); ?>

                            </div>

                            <p>&nbsp;</p>

                            <div role="tabpanel">
                                <ul class="nav nav-tabs" role="tablist" style="border: none">
                                    <li role="presentation" class="active">
                                        <a href="#form" aria-controls="form" role="tab" data-toggle="tab"><?php echo e(trans('texts.form')); ?></a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#link" aria-controls="link" role="tab" data-toggle="tab"><?php echo e(trans('texts.link')); ?></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="form">
                                    <textarea id="formTextarea" class="form-control" rows="4" readonly></textarea>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="link">
                                    <textarea id="linkTextarea" class="form-control" rows="4" readonly></textarea>
                                </div>
                            </div>

                        <?php endif; ?>

                    <?php else: ?>

                        <center style="font-size:16px;color:#888888;">
                            <?php echo e(trans('texts.buy_now_buttons_disabled')); ?>

                        </center>

                    <?php endif; ?>

                </div>
            </div>
        </div>

        <?php if(Utils::hasFeature(FEATURE_CLIENT_PORTAL_CSS)): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo trans('texts.custom_css'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="col-md-10 col-md-offset-1">
                    <?php echo Former::textarea('client_view_css')
                    ->label(trans('texts.custom_css'))
                    ->rows(10)
                    ->raw()
                    ->maxlength(60000)
                    ->style("min-width:100%;max-width:100%;font-family:'Roboto Mono', 'Lucida Console', Monaco, monospace;font-size:14px;'"); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

<center>
	<?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

</center>

<?php echo Former::close(); ?>


<script>

    var products = <?php echo $products; ?>;

    $(function() {
        var $productSelect = $('select#product');
        for (var i=0; i<products.length; i++) {
            var product = products[i];

            $productSelect.append(new Option(formatMoney(product.cost) + ' - ' + product.product_key, product.public_id));
        }
        $productSelect.combobox();

        fixCheckboxes();
        updateBuyNowButtons();
    })

	$('#enable_portal_password').change(fixCheckboxes);

	function fixCheckboxes() {
		var checked = $('#enable_portal_password').is(':checked');
		$('#send_portal_password').prop('disabled', !checked);
	}

    function showPaymentTypes() {
        var val = $('input[name=landing_page_type]:checked').val()
        if (val == '<?php echo e(ENTITY_PAYMENT); ?>') {
            $('#paymentTypesDiv').fadeIn();
        } else {
            $('#paymentTypesDiv').hide();
        }
    }

    function updateBuyNowButtons() {
        var productId = $('#product').val();
        var landingPage = $('input[name=landing_page_type]:checked').val()
        var paymentType = landingPage == 'payment' ? '/' + $('#payment_type').val() : '';
        var redirectUrl = $('#redirect_url').val();

        var form = '';
        var link = '';

        if (productId) {
            var link = '<?php echo e(url('/buy_now')); ?>' + paymentType +
                '?account_key=<?php echo e($account->account_key); ?>' +
                '&product_id=' + productId;

            var form = '<form action="<?php echo e(url('/buy_now')); ?>' + paymentType + '" method="post" target="_top">' + "\n" +
                        '<input type="hidden" name="account_key" value="<?php echo e($account->account_key); ?>"/>' + "\n" +
                        '<input type="hidden" name="product_id" value="' + productId + '"/>' + "\n";

            <?php foreach(['first_name', 'last_name', 'email'] as $field): ?>
                if ($('input#<?php echo e($field); ?>').is(':checked')) {
                    form += '<input type="<?php echo e($field == 'email' ? 'email' : 'text'); ?>" name="<?php echo e($field); ?>" placeholder="<?php echo e(trans("texts.{$field}")); ?>" required/>' + "\n";
                    link += '&<?php echo e($field); ?>=';
                }
            <?php endforeach; ?>

            if (redirectUrl) {
                link += '&redirect_url=' + encodeURIComponent(redirectUrl);
                form += '<input type="hidden" name="redirect_url" value="' + redirectUrl + '"/>' + "\n";
            }

            form += '<input type="submit" value="Buy Now" name="submit"/>' + "\n" + '</form>';
        }

        $('#formTextarea').text(form);
        $('#linkTextarea').text(link);
    }



</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>