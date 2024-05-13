function Accordion(list, options) {
    this.ACTIVE_CLASS = 'faq__item--active';
    this.ITEM_CLASS = 'faq__item';
    this.OPEN_ALL_BUTTON = 'faq__open-all'
    this.CLOSE_ALL_BUTTON = 'faq__close-all'

    this.list = list;

    this.options = options

    this.openItem = function (e) {
        const link = e.target.closest('.faq__link');
        if (!link) return;

        e.preventDefault();

        if (!link.closest(`.${this.ACTIVE_CLASS}`))
            this.closeAll()
        link.closest(`.${this.ITEM_CLASS}`).classList.toggle(this.ACTIVE_CLASS);
    };

    this.closeAll = function () {
        const items = this.list.querySelectorAll(`.${this.ACTIVE_CLASS}`);
        items.forEach(active => active.classList.remove(this.ACTIVE_CLASS));
    };

    this.openAll = function() {
        console.log('open all')
        const items = this.list.querySelectorAll(`.${this.ITEM_CLASS}`)
        items.forEach(item => item.classList.add(this.ACTIVE_CLASS));
    }

    this.init = function () {
        this.list.addEventListener('click', this.openItem.bind(this))
        if (this.options.forceOpenAll) {
            this.list.querySelector(`.${this.OPEN_ALL_BUTTON}`).addEventListener('click', this.openAll.bind(this));
            this.list.querySelector(`.${this.CLOSE_ALL_BUTTON}`).addEventListener('click', this.closeAll.bind(this));
        }
    };
}

if (document.querySelectorAll('.faq__list')) {
    document.querySelectorAll('.faq__list').forEach(list => {
        const faq = new Accordion(list, {
            forceOpenAll: false
        });
        faq.init();
    })
}
