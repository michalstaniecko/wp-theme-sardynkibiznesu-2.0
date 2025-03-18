import './navigation'
import './search-toggler'
import './scroll-to-top'
//import './faq'

//import './photoswipe'

import tablePrice from "./table-price";

import AdsConversions from "./ads-conversions";

import {countdownTimer} from "./countdown-timer";


window.addEventListener( 'load', () => {
    new AdsConversions()
    tablePrice();
    countdownTimer();
} )
