<script type="text/javascript">

function ViewModel(data) {
    var self = this;
    self.showMore = ko.observable(false);

    //self.invoice = data ? false : new InvoiceModel();
    self.invoice = ko.observable(data ? false : new InvoiceModel());
    self.expense_currency_id = ko.observable();
    self.products = <?php echo $products; ?>;

    self.loadClient = function(client) {
        ko.mapping.fromJS(client, model.invoice().client().mapping, model.invoice().client);
        <?php if(!$invoice->id): ?>
            self.setDueDate();
        <?php endif; ?>
    }

    self.showMoreFields = function() {
        self.showMore(!self.showMore());
    }

    self.setDueDate = function() {
        <?php if($entityType == ENTITY_INVOICE): ?>
            var paymentTerms = parseInt(self.invoice().client().payment_terms());
            if (paymentTerms && paymentTerms != 0 && !self.invoice().due_date())
            {
                if (paymentTerms == -1) paymentTerms = 0;
                var dueDate = $('#invoice_date').datepicker('getDate');
                dueDate.setDate(dueDate.getDate() + paymentTerms);
                self.invoice().due_date(dueDate);
                // We're using the datepicker to handle the date formatting
                self.invoice().due_date($('#due_date').val());
            }
        <?php endif; ?>
    }

    self.invoice_taxes = ko.observable(<?php echo e(Auth::user()->account->invoice_taxes ? 'true' : 'false'); ?>);
    self.invoice_item_taxes = ko.observable(<?php echo e(Auth::user()->account->invoice_item_taxes ? 'true' : 'false'); ?>);
    self.show_item_taxes = ko.observable(<?php echo e(Auth::user()->account->show_item_taxes ? 'true' : 'false'); ?>);

    self.mapping = {
        'invoice': {
            create: function(options) {
                return new InvoiceModel(options.data);
            }
        },
    }

    if (data) {
        ko.mapping.fromJS(data, self.mapping, self);
    }

    self.invoice_taxes.show = ko.computed(function() {
        if (self.invoice().tax_name1() || self.invoice().tax_name2()) {
            return true;
        }

        return self.invoice_taxes() && <?php echo e(count($taxRateOptions) ? 'true' : 'false'); ?>;
    });

    self.invoice_item_taxes.show = ko.computed(function() {
        if (self.invoice_item_taxes()) {
            return true;
        }
        for (var i=0; i<self.invoice().invoice_items().length; i++) {
            var item = self.invoice().invoice_items()[i];
            if (item.tax_name1() || item.tax_name2()) {
                return true;
            }
        }
        return false;
    });

    self.showClientForm = function() {
        trackEvent('/activity', '/view_client_form');
        self.clientBackup = ko.mapping.toJS(self.invoice().client);

        $('#emailError').css( "display", "none" );
        $('#clientModal').modal('show');
    }

    self.clientFormComplete = function() {
        trackEvent('/activity', '/save_client_form');

        var email = $("[name='client[contacts][0][email]']").val();
        var firstName = $("[name='client[contacts][0][first_name]']").val();
        var lastName = $("[name='client[contacts][0][last_name]']").val();
        var name = $("[name='client[name]']").val();

        if (name) {
            //
        } else if (firstName || lastName) {
            name = firstName + ' ' + lastName;
        } else {
            name = email;
        }

        var isValid = true;
        $('input.client-email').each(function(item, value) {
            var $email = $(value);
            var email = $(value).val();

            // Trim whitespace
            email = (email || '').trim();
            $email.val(email);

            if (!firstName && (!email || !isValidEmailAddress(email))) {
                isValid = false;
            }
        });

        if (!isValid) {
            $('#emailError').css( "display", "inline" );
            return;
        }

        if (self.invoice().client().public_id() == 0) {
            self.invoice().client().public_id(-1);
            self.invoice().client().invoice_number_counter = 1;
            self.invoice().client().quote_number_counter = 1;
        }

        model.setDueDate();
        setComboboxValue($('.client_select'), -1, name);

        var client = $.parseJSON(ko.toJSON(self.invoice().client()));
        setInvoiceNumber(client);

        //$('.client_select select').combobox('setSelected');
        //$('.client_select input.form-control').val(name);
        //$('.client_select .combobox-container').addClass('combobox-selected');

        $('#emailError').css( "display", "none" );

        refreshPDF(true);
        model.clientBackup = false;
        $('#clientModal').modal('hide');
    }

    self.clientLinkText = ko.computed(function() {
        if (self.invoice().client().public_id())
        {
            return "<?php echo e(trans('texts.edit_client')); ?>";
        }
        else
        {
            if (clients.length > <?php echo e(Auth::user()->getMaxNumClients()); ?>)
            {
                return '';
            }
            else
            {
                return "<?php echo e(trans('texts.create_new_client')); ?>";
            }
        }
    });
}

function InvoiceModel(data) {
    var self = this;
    this.client = ko.observable(data ? false : new ClientModel());
    this.is_public = ko.observable(0);
    self.account = <?php echo $account; ?>;
    self.id = ko.observable('');
    self.discount = ko.observable('');
    self.is_amount_discount = ko.observable(0);
    self.frequency_id = ko.observable(4); // default to monthly
    self.terms = ko.observable('');
    self.default_terms = ko.observable(account.<?php echo e($entityType); ?>_terms);
    self.terms_placeholder = ko.observable(<?php echo e(!$invoice->id && $account->{"{$entityType}_terms"} ? "account.{$entityType}_terms" : false); ?>);
    self.set_default_terms = ko.observable(false);
    self.invoice_footer = ko.observable('');
    self.default_footer = ko.observable(account.invoice_footer);
    self.footer_placeholder = ko.observable(<?php echo e(!$invoice->id && $account->invoice_footer ? 'account.invoice_footer' : false); ?>);
    self.set_default_footer = ko.observable(false);
    self.public_notes = ko.observable('');
    self.po_number = ko.observable('');
    self.invoice_date = ko.observable('');
    self.invoice_number = ko.observable('');
    self.due_date = ko.observable('');
    self.recurring_due_date = ko.observable('');
    self.start_date = ko.observable('');
    self.start_date_orig = ko.observable('');
    self.end_date = ko.observable('');
    self.last_sent_date = ko.observable('');
    self.tax_name1 = ko.observable();
    self.tax_rate1 = ko.observable();
    self.tax_name2 = ko.observable();
    self.tax_rate2 = ko.observable();
    self.is_recurring = ko.observable(0);
    self.is_quote = ko.observable(<?php echo e($entityType == ENTITY_QUOTE ? '1' : '0'); ?>);
    self.auto_bill = ko.observable(0);
    self.client_enable_auto_bill = ko.observable(false);
    self.invoice_status_id = ko.observable(0);
    self.invoice_items = ko.observableArray();
    self.documents = ko.observableArray();
    self.expenses = ko.observableArray();
    self.amount = ko.observable(0);
    self.balance = ko.observable(0);
    self.invoice_design_id = ko.observable(1);
    self.partial = ko.observable(0);
    self.has_tasks = ko.observable();
    self.has_expenses = ko.observable();

    self.custom_value1 = ko.observable(0);
    self.custom_value2 = ko.observable(0);
    self.custom_taxes1 = ko.observable(false);
    self.custom_taxes2 = ko.observable(false);
    self.custom_text_value1 = ko.observable();
    self.custom_text_value2 = ko.observable();

    self.mapping = {
        'client': {
            create: function(options) {
                return new ClientModel(options.data);
            }
        },
        'invoice_items': {
            create: function(options) {
                return new ItemModel(options.data);
            }
        },
        'documents': {
            create: function(options) {
                return new DocumentModel(options.data);
            }
        },
        'expenses': {
            create: function(options) {
                return new ExpenseModel(options.data);
            }
        },
    }

    self.addItem = function() {
        if (self.invoice_items().length >= <?php echo e(MAX_INVOICE_ITEMS); ?>) {
            return false;
        }
        var itemModel = new ItemModel();
        <?php if($account->hide_quantity): ?>
            itemModel.qty(1);
        <?php endif; ?>
        self.invoice_items.push(itemModel);
        applyComboboxListeners();
        return itemModel;
    }

    self.addDocument = function() {
        var documentModel = new DocumentModel();
        self.documents.push(documentModel);
        return documentModel;
    }

    self.removeDocument = function(doc) {
         var public_id = doc.public_id?doc.public_id():doc;
         self.documents.remove(function(document) {
            return document.public_id() == public_id;
        });
    }

    if (data) {
        ko.mapping.fromJS(data, self.mapping, self);
    } else {
        self.addItem();
    }

    self.qtyLabel = ko.computed(function() {
        return self.has_tasks() ? invoiceLabels['hours'] : invoiceLabels['quantity'];
    }, this);

    self.costLabel = ko.computed(function() {
        return self.has_tasks() ? invoiceLabels['rate'] : invoiceLabels['unit_cost'];
    }, this);

    this.tax1 = ko.computed({
        read: function () {
            return self.tax_rate1() + ' ' + self.tax_name1();
        },
        write: function(value) {
            var rate = value.substr(0, value.indexOf(' '));
            var name = value.substr(value.indexOf(' ') + 1);
            self.tax_name1(name);
            self.tax_rate1(rate);
        }
    })

    this.tax2 = ko.computed({
        read: function () {
            return self.tax_rate2() + ' ' + self.tax_name2();
        },
        write: function(value) {
            var rate = value.substr(0, value.indexOf(' '));
            var name = value.substr(value.indexOf(' ') + 1);
            self.tax_name2(name);
            self.tax_rate2(rate);
        }
    })

    self.removeItem = function(item) {
        self.invoice_items.remove(item);
        refreshPDF(true);
    }

    self.formatMoney = function(amount) {
        var client = $.parseJSON(ko.toJSON(self.client()));
        return formatMoneyAccount(amount, self.account, client);
    }

    self.totals = ko.observable();

    self.totals.rawSubtotal = ko.computed(function() {
        var total = 0;
        for(var p=0; p < self.invoice_items().length; ++p) {
            var item = self.invoice_items()[p];
            total += item.totals.rawTotal();
            total = roundToTwo(total);
        }
        return total;
    });

    self.totals.subtotal = ko.computed(function() {
        var total = self.totals.rawSubtotal();
        return self.formatMoney(total);
    });

    self.totals.rawDiscounted = ko.computed(function() {
        if (parseInt(self.is_amount_discount())) {
            return roundToTwo(self.discount());
        } else {
            return roundToTwo(self.totals.rawSubtotal() * (self.discount()/100));
        }
    });

    self.totals.discounted = ko.computed(function() {
        return self.formatMoney(self.totals.rawDiscounted());
    });

    self.totals.taxAmount = ko.computed(function() {
        var total = self.totals.rawSubtotal();
        var discount = self.totals.rawDiscounted();
        total -= discount;

        var customValue1 = roundToTwo(self.custom_value1());
        var customValue2 = roundToTwo(self.custom_value2());
        var customTaxes1 = self.custom_taxes1() == 1;
        var customTaxes2 = self.custom_taxes2() == 1;

        if (customValue1 && customTaxes1) {
            total = NINJA.parseFloat(total) + customValue1;
        }
        if (customValue2 && customTaxes2) {
            total = NINJA.parseFloat(total) + customValue2;
        }

        var taxRate1 = parseFloat(self.tax_rate1());
        var tax1 = roundToTwo(total * (taxRate1/100));

        var taxRate2 = parseFloat(self.tax_rate2());
        var tax2 = roundToTwo(total * (taxRate2/100));

        return self.formatMoney(tax1 + tax2);
    });

    self.totals.itemTaxes = ko.computed(function() {
        var taxes = {};
        var total = self.totals.rawSubtotal();
        for(var i=0; i<self.invoice_items().length; i++) {
            var item = self.invoice_items()[i];
            var lineTotal = item.totals.rawTotal();
            if (self.discount()) {
                if (parseInt(self.is_amount_discount())) {
                    lineTotal -= roundToTwo((lineTotal/total) * self.discount());
                } else {
                    lineTotal -= roundToTwo(lineTotal * (self.discount()/100));
                }
            }

            var taxAmount = roundToTwo(lineTotal * item.tax_rate1() / 100);
            if (taxAmount) {
                var key = item.tax_name1() + item.tax_rate1();
                if (taxes.hasOwnProperty(key)) {
                    taxes[key].amount += taxAmount;
                } else {
                    taxes[key] = {name:item.tax_name1(), rate:item.tax_rate1(), amount:taxAmount};
                }
            }

            var taxAmount = roundToTwo(lineTotal * item.tax_rate2() / 100);
            if (taxAmount) {
                var key = item.tax_name2() + item.tax_rate2();
                if (taxes.hasOwnProperty(key)) {
                    taxes[key].amount += taxAmount;
                } else {
                    taxes[key] = {name:item.tax_name2(), rate:item.tax_rate2(), amount:taxAmount};
                }
            }
        }
        return taxes;
    });

    self.totals.hasItemTaxes = ko.computed(function() {
        var count = 0;
        var taxes = self.totals.itemTaxes();
        for (var key in taxes) {
            if (taxes.hasOwnProperty(key)) {
                count++;
            }
        }
        return count > 0;
    });

    self.totals.itemTaxRates = ko.computed(function() {
        var taxes = self.totals.itemTaxes();
        var parts = [];
        for (var key in taxes) {
            if (taxes.hasOwnProperty(key)) {
                parts.push(taxes[key].name + ' ' + (taxes[key].rate*1) + '%');
            }
        }
        return parts.join('<br/>');
    });

    self.totals.itemTaxAmounts = ko.computed(function() {
        var taxes = self.totals.itemTaxes();
        var parts = [];
        for (var key in taxes) {
            if (taxes.hasOwnProperty(key)) {
                parts.push(self.formatMoney(taxes[key].amount));
            }
        }
        return parts.join('<br/>');
    });

    self.totals.rawPaidToDate = ko.computed(function() {
        return roundToTwo(accounting.toFixed(self.amount(),2) - accounting.toFixed(self.balance(),2));
    });

    self.totals.paidToDate = ko.computed(function() {
        var total = self.totals.rawPaidToDate();
        return self.formatMoney(total);
    });

    self.totals.rawTotal = ko.computed(function() {
        var total = accounting.toFixed(self.totals.rawSubtotal(),2);
        var discount = self.totals.rawDiscounted();
        total -= discount;

        var customValue1 = roundToTwo(self.custom_value1());
        var customValue2 = roundToTwo(self.custom_value2());
        var customTaxes1 = self.custom_taxes1() == 1;
        var customTaxes2 = self.custom_taxes2() == 1;

        if (customValue1 && customTaxes1) {
            total = NINJA.parseFloat(total) + customValue1;
        }
        if (customValue2 && customTaxes2) {
            total = NINJA.parseFloat(total) + customValue2;
        }

        var taxAmount1 = roundToTwo(total * (parseFloat(self.tax_rate1())/100));
        var taxAmount2 = roundToTwo(total * (parseFloat(self.tax_rate2())/100));
        total = NINJA.parseFloat(total) + taxAmount1 + taxAmount2;
        total = roundToTwo(total);

        var taxes = self.totals.itemTaxes();
        for (var key in taxes) {
            if (taxes.hasOwnProperty(key)) {
                total += taxes[key].amount;
                total = roundToTwo(total);
            }
        }

        if (customValue1 && !customTaxes1) {
            total = NINJA.parseFloat(total) + customValue1;
        }
        if (customValue2 && !customTaxes2) {
            total = NINJA.parseFloat(total) + customValue2;
        }

        var paid = self.totals.rawPaidToDate();
        if (paid > 0) {
            total -= paid;
        }

        return total;
    });

    self.totals.total = ko.computed(function() {
        return self.formatMoney(self.totals.rawTotal());
    });

    self.totals.partial = ko.computed(function() {
        return self.formatMoney(self.partial());
    });

    self.onDragged = function(item) {
        refreshPDF(true);
    }

    self.showResetTerms = function() {
        return self.default_terms() && self.terms() && self.terms() != self.default_terms();
    }

    self.showResetFooter = function() {
        return self.default_footer() && self.invoice_footer() && self.invoice_footer() != self.default_footer();
    }
}

function ClientModel(data) {
    var self = this;
    self.public_id = ko.observable(0);
    self.name = ko.observable('');
    self.id_number = ko.observable('');
    self.vat_number = ko.observable('');
    self.work_phone = ko.observable('');
    self.custom_value1 = ko.observable('');
    self.custom_value2 = ko.observable('');
    self.private_notes = ko.observable('');
    self.address1 = ko.observable('');
    self.address2 = ko.observable('');
    self.city = ko.observable('');
    self.state = ko.observable('');
    self.postal_code = ko.observable('');
    self.country_id = ko.observable('');
    self.size_id = ko.observable('');
    self.industry_id = ko.observable('');
    self.currency_id = ko.observable('');
    self.language_id = ko.observable('');
    self.website = ko.observable('');
    self.payment_terms = ko.observable(0);
    self.contacts = ko.observableArray();

    self.mapping = {
        'contacts': {
            create: function(options) {
                var model = new ContactModel(options.data);
                model.send_invoice(options.data.send_invoice == '1');
                return model;
            }
        }
    }

    self.showContact = function(elem) { if (elem.nodeType === 1) $(elem).hide().slideDown() }
    self.hideContact = function(elem) { if (elem.nodeType === 1) $(elem).slideUp(function() { $(elem).remove(); }) }

    self.addContact = function() {
        var contact = new ContactModel();
        contact.send_invoice(true);
        self.contacts.push(contact);
        return false;
    }

    self.removeContact = function() {
        self.contacts.remove(this);
    }

    self.name.display = ko.computed(function() {
        if (self.name()) {
            return self.name();
        }
        if (self.contacts().length == 0) return;
        var contact = self.contacts()[0];
        if (contact.first_name() || contact.last_name()) {
            return contact.first_name() + ' ' + contact.last_name();
        } else {
            return contact.email();
        }
    });

    self.name.placeholder = ko.computed(function() {
        if (self.contacts().length == 0) return '';
        var contact = self.contacts()[0];
        if (contact.first_name() || contact.last_name()) {
            return contact.first_name() + ' ' + contact.last_name();
        } else {
            return contact.email();
        }
    });

    if (data) {
        ko.mapping.fromJS(data, {}, this);
    } else {
        self.addContact();
    }
}

function ContactModel(data) {
    var self = this;
    self.public_id = ko.observable('');
    self.first_name = ko.observable('');
    self.last_name = ko.observable('');
    self.email = ko.observable('');
    self.phone = ko.observable('');
    self.send_invoice = ko.observable(false);
    self.invitation_link = ko.observable('');
    self.invitation_status = ko.observable('');
    self.invitation_openend = ko.observable(false);
    self.invitation_viewed = ko.observable(false);
    self.email_error = ko.observable('');
    self.invitation_signature_svg = ko.observable('');
    self.invitation_signature_date = ko.observable('');

    if (data) {
        ko.mapping.fromJS(data, {}, this);
    }

    self.displayName = ko.computed(function() {
        var str = '';
        if (self.first_name() || self.last_name()) {
            str += (self.first_name() || '') + ' ' + (self.last_name() || '') + '\n';
        }
        if (self.email()) {
            str += self.email() + '\n';
        }

        return str;
    });

    self.email.display = ko.computed(function() {
        var str = '';

        if (self.first_name() || self.last_name()) {
            str += (self.first_name() || '') + ' ' + (self.last_name() || '') + '<br/>';
        }
        if (self.email()) {
            str += self.email() + '<br/>';
        }
        return str;
    });

    self.view_as_recipient = ko.computed(function() {
        var str = '';
        <?php if(Utils::isConfirmed()): ?>
        if (self.invitation_link()) {
            str += '<a href="' + self.invitation_link() + '" target="_blank"><?php echo e(trans('texts.view_as_recipient')); ?></a>';
        }
        <?php endif; ?>

        return str;
    });

    self.info_color = ko.computed(function() {
        if (self.invitation_viewed()) {
            return '#57D172';
        } else if (self.invitation_openend()) {
            return '#FFCC00';
        } else {
            return '#B1B5BA';
        }
    });
}

function ItemModel(data) {
    var self = this;
    self.product_key = ko.observable('');
    self.notes = ko.observable('');
    self.cost = ko.observable(0);
    self.qty = ko.observable(0);
    self.custom_value1 = ko.observable('');
    self.custom_value2 = ko.observable('');
    self.tax_name1 = ko.observable('');
    self.tax_rate1 = ko.observable(0);
    self.tax_name2 = ko.observable('');
    self.tax_rate2 = ko.observable(0);
    self.task_public_id = ko.observable('');
    self.expense_public_id = ko.observable('');
    self.actionsVisible = ko.observable(false);

    this.tax1 = ko.computed({
        read: function () {
            return self.tax_rate1() + ' ' + self.tax_name1();
        },
        write: function(value) {
            var rate = value.substr(0, value.indexOf(' '));
            var name = value.substr(value.indexOf(' ') + 1);
            self.tax_name1(name);
            self.tax_rate1(rate);
        }
    })

    this.tax2 = ko.computed({
        read: function () {
            return self.tax_rate2() + ' ' + self.tax_name2();
        },
        write: function(value) {
            var rate = value.substr(0, value.indexOf(' '));
            var name = value.substr(value.indexOf(' ') + 1);
            self.tax_name2(name);
            self.tax_rate2(rate);
        }
    })

    this.prettyQty = ko.computed({
        read: function () {
            return NINJA.parseFloat(this.qty()) ? NINJA.parseFloat(this.qty()) : '';
        },
        write: function (value) {
            this.qty(value);
        },
        owner: this
    });

    this.prettyCost = ko.computed({
        read: function () {
            return this.cost() ? this.cost() : '';
        },
        write: function (value) {
            this.cost(value);
        },
        owner: this
    });

    if (data) {
        ko.mapping.fromJS(data, {}, this);
    }

    this.totals = ko.observable();

    this.totals.rawTotal = ko.computed(function() {
        var cost = roundToTwo(NINJA.parseFloat(self.cost()));
        var qty = roundToTwo(NINJA.parseFloat(self.qty()));
        var value = cost * qty;
        return value ? roundToTwo(value) : 0;
    });

    this.totals.total = ko.computed(function() {
        var total = self.totals.rawTotal();
        return window.hasOwnProperty('model') && total ? model.invoice().formatMoney(total) : '';
    });

    this.hideActions = function() {
        this.actionsVisible(false);
    }

    this.showActions = function() {
        this.actionsVisible(true);
    }

    this.isEmpty = function() {
        return !self.product_key() && !self.notes() && !self.cost() && (!self.qty() || <?php echo e($account->hide_quantity ? 'true' : 'false'); ?>);
    }

    this.onSelect = function() {}
}

function DocumentModel(data) {
    var self = this;
    self.public_id = ko.observable(0);
    self.size = ko.observable(0);
    self.name = ko.observable('');
    self.type = ko.observable('');
    self.url = ko.observable('');

    self.update = function(data){
        ko.mapping.fromJS(data, {}, this);
    }

    if (data) {
        self.update(data);
    }
}

function CategoryModel(data) {
    var self = this;
    self.name = ko.observable('')

    self.update = function(data){
        ko.mapping.fromJS(data, {}, this);
    }

    if (data) {
        self.update(data);
    }
}

var ExpenseModel = function(data) {
    var self = this;

    self.mapping = {
        'documents': {
            create: function(options) {
                return new DocumentModel(options.data);
            }
        },
        'expense_category': {
            create: function(options) {
                return new CategoryModel(options.data);
            }
        }
    }

    self.description = ko.observable('');
    self.qty = ko.observable(0);
    self.public_id = ko.observable(0);
    self.amount = ko.observable();
    self.converted_amount = ko.observable();

    if (data) {
        ko.mapping.fromJS(data, self.mapping, this);
    }
};

/* Custom binding for product key typeahead */
ko.bindingHandlers.productTypeahead = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        var $element = $(element);
        var allBindings = allBindingsAccessor();

        $element.typeahead({
            highlight: true,
            minLength: 0,
        },
        {
            name: 'data',
            display: allBindings.key,
            limit: 50,
            source: searchData(allBindings.items, allBindings.key)
        }).on('typeahead:select', function(element, datum, name) {
            <?php if(Auth::user()->account->fill_products): ?>
                var model = ko.dataFor(this);
                if (model.expense_public_id()) {
                    return;
                }
                if (datum.notes) {
                    model.notes(datum.notes);
                }
                if (datum.cost) {
                    model.cost(accounting.toFixed(datum.cost, 2));
                }
                if (!model.qty()) {
                    model.qty(1);
                }
                <?php if($account->invoice_item_taxes): ?>
                    if (datum.default_tax_rate) {
                        model.tax_rate1(datum.default_tax_rate.rate);
                        model.tax_name1(datum.default_tax_rate.name);
                        model.tax1(datum.default_tax_rate.rate + ' ' + datum.default_tax_rate.name);
                    }
                <?php endif; ?>
            <?php endif; ?>
            onItemChange();
        }).on('typeahead:change', function(element, datum, name) {
            var value = valueAccessor();
            value(datum);
            onItemChange();
            refreshPDF(true);
        });
    },

    update: function (element, valueAccessor) {
        var value = ko.utils.unwrapObservable(valueAccessor());
        if (value) {
            $(element).typeahead('val', value);
        }
    }
};

function checkInvoiceNumber() {
    var url = '<?php echo e(url('check_invoice_number')); ?>/<?php echo e($invoice->exists ? $invoice->public_id : ''); ?>?invoice_number=' + encodeURIComponent($('#invoice_number').val());
    $.get(url, function(data) {
        var isValid = data == '<?php echo e(RESULT_SUCCESS); ?>' ? true : false;
        if (isValid) {
            $('.invoice-number')
                .removeClass('has-error')
                .find('span')
                .hide();
        } else {
            if ($('.invoice-number').hasClass('has-error')) {
                return;
            }
            $('.invoice-number')
                .addClass('has-error')
                .find('div')
                .append('<span class="help-block"><?php echo e(trans('validation.unique', ['attribute' => trans('texts.invoice_number')])); ?></span>');
        }
    });
}

</script>
