import {Collapse} from "bootstrap";

function tablePrice() {
    const tablePrice = document.querySelector('.table-price');

    if (!tablePrice) {
        return;
    }

    const collapseList = tablePrice.querySelectorAll('.collapse');

    const initCollapse = () => {
        collapseList.forEach((collapseItem) => {
            const collapse = new Collapse(collapseItem, {
                toggle: false
            });
            const link = collapseItem.closest('.table__row-head').querySelector('a');
            link.addEventListener('click', (e) => {
                e.preventDefault();
                collapse.toggle();
            })
        });
    }

    initCollapse();
}

export default tablePrice;
