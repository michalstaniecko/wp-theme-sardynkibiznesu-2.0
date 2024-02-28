console.log( 'faq' );

function Faq( list ) {
    this.ACTIVE_CLASS = 'faq__item--active';
    this.ITEM_CLASS = 'faq__item';
    this.OPEN_ALL_BUTTON = 'faq__open-all'
    this.CLOSE_ALL_BUTTON = 'faq__close-all'

    this.list = list;

    this.options = {
        forceOpenAll: false
    };

    this.openItem = function ( e ) {
        e.preventDefault();
        const link = e.target.closest( '.faq__link' );
        if (!link) return false;
        if (!link.closest( `.${ this.ACTIVE_CLASS }` ))
            this.closeAllWithoutActive()
        link.closest( `.${ this.ITEM_CLASS }` ).classList.toggle( this.ACTIVE_CLASS );
    };

    this.closeAllWithoutActive = function () {
        const active = this.list.querySelector( `.${ this.ACTIVE_CLASS }` )
        if (!active) return false;
        active.classList.remove( this.ACTIVE_CLASS )
    };

    this.closeAll = function () {
        const items = this.list.querySelectorAll( `.${ this.ACTIVE_CLASS }` )

        items.forEach( item => item.classList.remove( this.ACTIVE_CLASS ) )
    };

    this.openAll = function () {
        const items = this.list.querySelectorAll( `.${ this.ITEM_CLASS }` )
        items.forEach( item => item.classList.add( this.ACTIVE_CLASS ) );
    }

    this.setup = function () {
        if (this.list.dataset.forceOpenAll === '1') {
            this.options.forceOpenAll = true;
        }
    }

    this.init = function () {
        this.setup();
        this.list.addEventListener( 'click', this.openItem.bind( this ) );
        if (this.options.forceOpenAll) {
            if (this.list.querySelector( `.${ this.OPEN_ALL_BUTTON }` ) && this.list.querySelector( `.${ this.CLOSE_ALL_BUTTON }` )) {
                this.list.querySelector( `.${ this.OPEN_ALL_BUTTON }` ).addEventListener( 'click', this.openAll.bind( this ) );
                this.list.querySelector( `.${ this.CLOSE_ALL_BUTTON }` ).addEventListener( 'click', this.closeAll.bind( this ) );
            }
        }
    };
}

if (document.querySelectorAll( '.faq__list' )) {
    document.querySelectorAll( '.faq__list' ).forEach( list => {
        const faq = new Faq( list );
        faq.init();
    } )
}
