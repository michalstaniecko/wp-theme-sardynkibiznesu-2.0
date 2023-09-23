console.log('accordion');

function Accordion(list) {
    this.ACTIVE_CLASS = 'faq__item--active';
    this.ITEM_CLASS = 'faq__item';

    this.list = list;

    this.openItem = function (e) {
        e.preventDefault();
        const link = e.target.closest('.faq__link');
        if (!link) return false;
        if (!link.closest(`.${this.ACTIVE_CLASS}`))
            this.closeAll()
        link.closest(`.${this.ITEM_CLASS}`).classList.toggle(this.ACTIVE_CLASS);
    };

    this.closeAll = function () {
        const active = this.list.querySelector(`.${this.ACTIVE_CLASS}`)
        if (!active) return false;
        active.classList.remove(this.ACTIVE_CLASS)
    };

    this.init = function () {
        this.list.addEventListener('click', this.openItem.bind(this))
    };
}

if (document.querySelectorAll('.faq__list')) {
    document.querySelectorAll('.faq__list').forEach(list => {
        const faq = new Accordion(list);
        faq.init();
    })
}
