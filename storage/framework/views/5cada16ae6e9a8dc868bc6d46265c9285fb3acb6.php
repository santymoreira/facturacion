<?php $__env->startSection('head'); ?>
    @parent

    <style type="text/css">
        .iframe_url {
            display: none;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    @parent
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_EMAIL_SETTINGS, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Former::open()->rules([
            'iframe_url' => 'url'
        ])->addClass('warn-on-exit'); ?>

    <?php echo e(Former::populate($account)); ?>

    <?php echo e(Former::populateField('pdf_email_attachment', intval($account->pdf_email_attachment))); ?>

    <?php echo e(Former::populateField('document_email_attachment', intval($account->document_email_attachment))); ?>

    <?php echo e(Former::populateField('enable_email_markup', intval($account->enable_email_markup))); ?>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.email_settings'); ?></h3>
        </div>
        <div class="panel-body form-padding-right">
            <?php echo Former::checkbox('pdf_email_attachment')->text(trans('texts.enable')); ?>

            <?php echo Former::checkbox('document_email_attachment')->text(trans('texts.enable')); ?>


            &nbsp;

            <?php /* Former::select('recurring_hour')->options($recurringHours) */ ?>

            <?php echo Former::inline_radios('custom_invoice_link')
                    ->onchange('onCustomLinkChange()')
                    ->label(trans('texts.invoice_link'))
                    ->radios([
                        trans('texts.subdomain') => ['value' => 'subdomain', 'name' => 'custom_link'],
                        trans('texts.website') => ['value' => 'website', 'name' => 'custom_link'],
                    ])->check($account->iframe_url ? 'website' : 'subdomain'); ?>

            <?php echo e(Former::setOption('capitalize_translations', false)); ?>


            <?php echo Former::text('subdomain')
                        ->placeholder(trans('texts.www'))
                        ->onchange('onSubdomainChange()')
                        ->addGroupClass('subdomain')
                        ->label(' ')
                        ->help(trans('texts.subdomain_help')); ?>


            <?php echo Former::text('iframe_url')
                        ->placeholder('https://www.example.com/invoice')
                        ->appendIcon('question-sign')
                        ->addGroupClass('iframe_url')
                        ->label(' ')
                        ->help(trans('texts.subdomain_help')); ?>


        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.email_design'); ?></h3>
        </div>
        <div class="panel-body form-padding-right">

            <?php echo Former::select('email_design_id')
                        ->appendIcon('question-sign')
                        ->addGroupClass('email_design_id')
                        ->addOption(trans('texts.plain'), EMAIL_DESIGN_PLAIN)
                        ->addOption(trans('texts.light'), EMAIL_DESIGN_LIGHT)
                        ->addOption(trans('texts.dark'), EMAIL_DESIGN_DARK)
                        ->help(trans('texts.email_design_help')); ?>


            &nbsp;

            <?php if(Utils::isNinja()): ?>
                <?php echo Former::checkbox('enable_email_markup')
                        ->text(trans('texts.enable') .
                            '<a href="'.EMAIL_MARKUP_URL.'" target="_blank" title="'.trans('texts.learn_more').'">' . Icon::create('question-sign') . '</a> ')
                        ->help(trans('texts.enable_email_markup_help')); ?>

            <?php endif; ?>
        </div>
    </div>

    <?php if(Auth::user()->hasFeature(FEATURE_CUSTOM_EMAILS)): ?>
        <center>
            <?php echo Button::success(trans('texts.save'))->large()->submit()->appendIcon(Icon::create('floppy-disk')); ?>

        </center>
    <?php endif; ?>

    <div class="modal fade" id="iframeHelpModal" tabindex="-1" role="dialog" aria-labelledby="iframeHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width:150px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="iframeHelpModalLabel"><?php echo e(trans('texts.iframe_url')); ?></h4>
                </div>

                <div class="modal-body">
                    <p><?php echo e(trans('texts.iframe_url_help1')); ?></p>
                    <pre>&lt;center&gt;
    &lt;iframe id="invoiceIFrame" width="100%" height="1200" style="max-width:1000px"&gt;&lt;/iframe&gt;
&lt;center&gt;
&lt;script language="javascript"&gt;
    var iframe = document.getElementById('invoiceIFrame');
    iframe.src = '<?php echo e(rtrim(SITE_URL ,'/')); ?>/view/'
                 + window.location.search.substring(1);
&lt;/script&gt;</pre>
                    <p><?php echo e(trans('texts.iframe_url_help2')); ?></p>
                    <p><b><?php echo e(trans('texts.iframe_url_help3')); ?></b></p>
                    </div>

                <div class="modal-footer" style="margin-top: 0px">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo e(trans('texts.close')); ?></button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="designHelpModal" tabindex="-1" role="dialog" aria-labelledby="designHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width:150px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="designHelpModalLabel"><?php echo e(trans('texts.email_designs')); ?></h4>
                </div>

                <div class="modal-body">
                    <div class="row" style="text-align:center">
                        <div class="col-md-4">
                            <h4><?php echo e(trans('texts.plain')); ?></h4><br/>
                            <img src="<?php echo e(asset('images/emails/plain.png')); ?>" class="img-responsive"/>
                        </div>
                        <div class="col-md-4">
                            <h4><?php echo e(trans('texts.light')); ?></h4><br/>
                            <img src="<?php echo e(asset('images/emails/light.png')); ?>" class="img-responsive"/>
                        </div>
                        <div class="col-md-4">
                            <h4><?php echo e(trans('texts.dark')); ?></h4><br/>
                            <img src="<?php echo e(asset('images/emails/dark.png')); ?>" class="img-responsive"/>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 0px">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo e(trans('texts.close')); ?></button>
                </div>

            </div>
        </div>
    </div>

    <?php echo Former::close(); ?>


    <script type="text/javascript">

    function onSubdomainChange() {
        var input = $('#subdomain');
        var val = input.val();
        if (!val) return;
        val = val.replace(/[^a-zA-Z0-9_\-]/g, '').toLowerCase().substring(0, <?php echo e(MAX_SUBDOMAIN_LENGTH); ?>);
        input.val(val);
    }

    function onCustomLinkChange() {
        var val = $('input[name=custom_link]:checked').val()
        if (val == 'subdomain') {
            $('.subdomain').show();
            $('.iframe_url').hide();
        } else {
            $('.subdomain').hide();
            $('.iframe_url').show();
        }
    }

    $('.iframe_url .input-group-addon').click(function() {
        $('#iframeHelpModal').modal('show');
    });

    $('.email_design_id .input-group-addon').click(function() {
        $('#designHelpModal').modal('show');
    });

    $(function() {
        onCustomLinkChange();

        $('#subdomain').change(function() {
            $('#iframe_url').val('');
        });
        $('#iframe_url').change(function() {
            $('#subdomain').val('');
        });
    });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>