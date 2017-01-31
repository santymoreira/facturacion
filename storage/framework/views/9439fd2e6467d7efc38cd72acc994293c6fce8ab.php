<div class="col-lg-3 col-md-6">

    <?php echo Former::select("{$section}_select")
            ->placeholder(trans("texts.{$fields}"))
            ->options($account->getAllInvoiceFields()[$fields])
            ->onchange("addField('{$section}')")
            ->raw(); ?>


    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: <?php echo e($section); ?>, as: 'field', afterMove: onDragged }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:pointer" title="<?php echo e(trans('texts.remove')); ?>"
                        data-bind="click: $root.<?php echo e(Utils::toCamelCase('remove' . ucwords($section))); ?>"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
