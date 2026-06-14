import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.data('saleForm', () => ({
    items: [],
    toast: { visible: false, message: '', timer: null },

    init() {
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
        if (this.items.length <= 1) {
            alert(this.$root.dataset.i18nMinItem);
            return;
        }
        this.items.splice(index, 1);
    },

    onProductChange(item) {
        const select = event.target;
        const option = select.options[select.selectedIndex];
        item.unitPrice = parseFloat(option.dataset.price) || 0;
        item.stock = parseInt(option.dataset.stock) || 0;

        if (item.quantity > item.stock) {
            this.showToast(option.dataset.name, item.quantity, item.stock);
        }
    },

    get grandTotal() {
        return this.items.reduce((sum, i) => sum + (i.quantity * i.unitPrice || 0), 0);
    },

    formatCurrency(value) {
        return 'RWF ' + (value || 0).toLocaleString();
    },

    showToast(name, requested, available) {
        this.toast.message = `"${name}" has only ${available} units available. You requested ${requested}.`;
        this.toast.visible = true;
        clearTimeout(this.toast.timer);
        this.toast.timer = setTimeout(() => this.toast.visible = false, 5000);
    },

    onSubmit(e) {
        const overStock = this.items.some(i => i.productId && i.quantity > i.stock);
        if (overStock) {
            e.preventDefault();
            alert(this.$root.dataset.i18nStockError);
        }
    },
}));

Alpine.start();