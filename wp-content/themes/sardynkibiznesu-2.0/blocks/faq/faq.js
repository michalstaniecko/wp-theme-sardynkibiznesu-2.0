function Faq( list ) {
    this.ACTIVE_CLASS = 'faq__item--active';
    this.ITEM_CLASS = 'faq__item';

    this.list = list;

    this.openItem = function ( e ) {
        const link = e.target.closest( '.faq__link' );
        if (!link) return;

        e.preventDefault();

        if (!link.closest( `.${ this.ACTIVE_CLASS }` ))
            this.closeAll()
        link.closest( `.${ this.ITEM_CLASS }` ).classList.toggle( this.ACTIVE_CLASS );
    };

    this.closeAll = function () {
        const active = this.list.querySelector( `.${ this.ACTIVE_CLASS }` )
        if (!active) return false;
        active.classList.remove( this.ACTIVE_CLASS )
    };

    this.init = function () {
        this.list.addEventListener( 'click', this.openItem.bind( this ) )
    };
}

window.addEventListener( 'DOMContentLoaded', ( event ) => {
    if (document.querySelectorAll( '.faq-faq .faq__list' )) {
        document.querySelectorAll( '.faq-faq .faq__list' ).forEach( list => {
            const faq = new Faq( list );
            faq.init();
        } )
    }
} );
