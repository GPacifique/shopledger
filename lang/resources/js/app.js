import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('saleForm', () => ({
items: [],

// Payment status is part of the saleForm Alpine scope
paymentStatus: 'paid',

toast: {
    visible: false,
    message: '',
    timer: null,
},

init() {
    // Get the initial payment status from the Blade form
    this.paymentStatus = this.$root.dataset.paymentStatus || 'paid';

    // Add the first sale item
    this.addItem();
},

addItem() {
    this.items.push({
        uid: crypto.randomUUID(),
        productId: '',
        quantity: 1,
        unitPrice: 0,
        stock: 0,
    });
},

removeItem(index) {
    // Always require at least one item
    if (this.items.length <= 1) {
        alert(
            this.$root.dataset.i18nMinItem ||
            'At least one item is required'
        );
        return;
    }

    this.items.splice(index, 1);
},

onProductChange(item, event) {
    const select = event.target;

    if (!select || !select.selectedOptions.length) {
        item.unitPrice = 0;
        item.stock = 0;
        return;
    }

    const option = select.selectedOptions[0];

    item.unitPrice = parseFloat(option.dataset.price) || 0;
    item.stock = parseFloat(option.dataset.stock) || 0;

    // Check stock immediately after selecting the product
    if (
        item.productId &&
        Number(item.quantity) > Number(item.stock)
    ) {
        this.showToast(
            option.dataset.name || 'Product',
            item.quantity,
            item.stock
        );
    }
},

get grandTotal() {
    return this.items.reduce((sum, item) => {
        const quantity = parseFloat(item.quantity) || 0;
        const unitPrice = parseFloat(item.unitPrice) || 0;

        return sum + (quantity * unitPrice);
    }, 0);
},

formatCurrency(value) {
    const amount = Number(value) || 0;

    return 'RWF ' + amount.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });
},

showToast(name, requested, available) {
    this.toast.message =
        `"${name}" has only ${available} units available. You requested ${requested}.`;

    this.toast.visible = true;

    clearTimeout(this.toast.timer);

    this.toast.timer = setTimeout(() => {
        this.toast.visible = false;
    }, 5000);
},

onSubmit(e) {
    // Make sure at least one item exists
    if (!this.items.length) {
        e.preventDefault();

        alert(
            this.$root.dataset.i18nMinItem ||
            'At least one item is required'
        );

        return;
    }

    // Check for products exceeding available stock
    const overStock = this.items.some(item => {
        if (!item.productId) {
            return false;
        }

        const quantity = parseFloat(item.quantity) || 0;
        const stock = parseFloat(item.stock) || 0;

        return quantity > stock;
    });

    if (overStock) {
        e.preventDefault();

        alert(
            this.$root.dataset.i18nStockError ||
            'Cannot complete sale: One or more items exceed available stock. Please adjust quantities.'
        );

        return;
    }

    // Make sure every item has a valid product
    const missingProduct = this.items.some(item => !item.productId);

    if (missingProduct) {
        e.preventDefault();

        alert('Please select a product for every item.');

        return;
    }

    // Make sure quantities are valid
    const invalidQuantity = this.items.some(item => {
        const quantity = parseFloat(item.quantity) || 0;

        return quantity <= 0;
    });

    if (invalidQuantity) {
        e.preventDefault();

        alert('Quantity must be greater than zero.');

        return;
    }

    // Make sure prices are valid
    const invalidPrice = this.items.some(item => {
        const unitPrice = parseFloat(item.unitPrice);

        return isNaN(unitPrice) || unitPrice < 0;
    });

    if (invalidPrice) {
        e.preventDefault();

        alert('Please enter a valid unit price.');

        return;
    }

    // Allow the form to submit
},

}));

Alpine.start();
