<li class="<?php echo e(Request::is("{$option}*") ? 'active' : ''); ?>">

    <?php if($option == 'settings'): ?>

        <a type="button" class="btn btn-default btn-sm pull-right" title="<?php echo e(Utils::getReadableUrl(request()->path())); ?>"
            href="<?php echo e(Utils::getDocsUrl(request()->path())); ?>" target="_blank">
            <i class="fa fa-info-circle" style="width:20px"></i>
        </a>

    <?php elseif(Auth::user()->can('create', $option) || Auth::user()->can('create', substr($option, 0, -1))): ?>

        <a type="button" class="btn btn-primary btn-sm pull-right"
            href="<?php echo e(url("/{$option}/create")); ?>">
            <i class="fa fa-plus-circle" style="width:20px" title="<?php echo e(trans('texts.create_new')); ?>"></i>
        </a>

    <?php endif; ?>

    <a href="<?php echo e(url($option == 'recurring' ? 'recurring_invoice' : $option)); ?>"
        style="font-size:16px; padding-top:6px; padding-bottom:6px"
        class="<?php echo e(Request::is("{$option}*") ? 'active' : ''); ?>">
        <i class="fa fa-<?php echo e(empty($icon) ? \App\Models\EntityModel::getIcon($option) : $icon); ?>" style="width:46px; padding-right:10px"></i>
        <?php echo e(($option == 'recurring_invoices') ? trans('texts.recurring') : mtrans($option)); ?>

        <?php echo Utils::isTrial() && in_array($option, ['quotes', 'tasks', 'expenses', 'vendors']) ? '&nbsp;<sup>' . trans('texts.pro') . '</sup>' : ''; ?>

    </a>

</li>
