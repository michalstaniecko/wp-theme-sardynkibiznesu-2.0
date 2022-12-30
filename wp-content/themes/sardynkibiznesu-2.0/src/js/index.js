import './navigation'
import './search-toggler'
import './scroll-to-top'

//import './photoswipe'

import GDPR from "./gdpr";
//import AdsConversions from "./ads-conversions";

window.addEventListener('load', () => {
  new GDPR()
  //new AdsConversions()
})
