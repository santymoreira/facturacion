<script type="text/javascript">

    var currencies = <?php echo \Cache::get('currencies'); ?>;
    var currencyMap = {};
    for (var i=0; i<currencies.length; i++) {
        var currency = currencies[i];
        currencyMap[currency.id] = currency;
        currencyMap[currency.code] = currency;
    }

    var countries = <?php echo \Cache::get('countries'); ?>;
    var countryMap = {};
    for (var i=0; i<countries.length; i++) {
        var country = countries[i];
        countryMap[country.id] = country;
    }

    var NINJA = NINJA || {};
    <?php if(Auth::check()): ?>
    NINJA.primaryColor = "<?php echo e(Auth::user()->account->primary_color); ?>";
    NINJA.secondaryColor = "<?php echo e(Auth::user()->account->secondary_color); ?>";
    NINJA.fontSize = <?php echo e(Auth::user()->account->font_size ?: DEFAULT_FONT_SIZE); ?>;
    NINJA.headerFont = <?php echo json_encode(Auth::user()->account->getHeaderFontName()); ?>;
    NINJA.bodyFont = <?php echo json_encode(Auth::user()->account->getBodyFontName()); ?>;
    <?php else: ?>
    NINJA.fontSize = <?php echo e(DEFAULT_FONT_SIZE); ?>;
    <?php endif; ?>

    NINJA.parseFloat = function(str) {
        if (!str) return '';
        str = (str+'').replace(/[^0-9\.\-]/g, '');

        return window.parseFloat(str);
    }

    function formatMoneyInvoice(value, invoice, decorator) {
        var account = invoice.account;
        var client = invoice.client;

        return formatMoneyAccount(value, account, client, decorator);
    }

    function formatMoneyAccount(value, account, client, decorator) {
        var currencyId = false;
        var countryId = false;

        if (client && client.currency_id) {
            currencyId = client.currency_id;
        } else if (account && account.currency_id) {
            currencyId = account.currency_id;
        }

        if (client && client.country_id) {
            countryId = client.country_id;
        } else if (account && account.country_id) {
            countryId = account.country_id;
        }

        if (account && ! decorator) {
            decorator = parseInt(account.show_currency_code) ? 'code' : 'symbol';
        }

        return formatMoney(value, currencyId, countryId, decorator)
    }

    function formatMoney(value, currencyId, countryId, decorator) {
        value = NINJA.parseFloat(value);

        if (!currencyId) {
            currencyId = <?php echo e(Session::get(SESSION_CURRENCY, DEFAULT_CURRENCY)); ?>;
        }

        if (!decorator) {
            decorator = '<?php echo e(Session::get(SESSION_CURRENCY_DECORATOR, CURRENCY_DECORATOR_SYMBOL)); ?>';
        }

        var currency = currencyMap[currencyId];
        var precision = currency.precision;
        var thousand = currency.thousand_separator;
        var decimal = currency.decimal_separator;
        var code = currency.code;
        var swapSymbol = currency.swap_currency_symbol;

        if (countryId && currencyId == <?php echo e(CURRENCY_EURO); ?>) {
            var country = countryMap[countryId];
            swapSymbol = country.swap_currency_symbol;
            if (country.thousand_separator) {
                thousand = country.thousand_separator;
            }
            if (country.decimal_separator) {
                decimal = country.decimal_separator;
            }
        }

        value = accounting.formatMoney(value, '', precision, thousand, decimal);
        var symbol = currency.symbol;

        if (decorator == 'none') {
            return value;
        } else if (decorator == '<?php echo e(CURRENCY_DECORATOR_CODE); ?>' || ! symbol) {
            return value + ' ' + code;
        } else if (swapSymbol) {
            return value + ' ' + symbol.trim();
        } else {
            return symbol + value;
        }
    }

</script>