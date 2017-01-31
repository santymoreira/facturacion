<?php echo e(trans('texts.powered_by')); ?>

<?php /* Per our license, please do not remove or modify this section. */ ?>
<?php echo link_to('https://www.invoiceninja.com/?utm_source=powered_by', 'InvoiceNinja.com', ['target' => '_blank', 'title' => trans('texts.created_by', ['name' => 'Hillel Coren'])]); ?>


<script type="text/javascript">

    function showWhiteLabelModal() {
        loadImages('#whiteLabelModal');
        $('#whiteLabelModal').modal('show');
    }

    function buyProduct(affiliateKey, productId) {
        window.open('<?php echo e(Utils::isNinjaDev() ? '' : NINJA_APP_URL); ?>/license?affiliate_key=' + affiliateKey + '&product_id=' + productId + '&return_url=' + window.location);
    }

    function showApplyLicense() {
        $('#whiteLabelModal').modal('hide');
        $('#whiteLabelLicenseModal').modal('show');
    }

    function applyLicense() {
        var license = $('#white_label_license_key').val();
        window.location = "<?php echo e(url('')); ?>/dashboard?license_key=" + license + "&product_id=<?php echo e(PRODUCT_WHITE_LABEL); ?>";
    }

</script>
