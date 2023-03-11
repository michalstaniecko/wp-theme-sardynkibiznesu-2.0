import {cookies} from "./cookie.class";
import AdsConversions from "./ads-conversions";
import {Offcanvas, Collapse} from "bootstrap";

class TrackingCodes {
  category;
  run;

  constructor(category, script) {
    this.category = category
    this.script = script
    this._executed = false
  }

  execute() {
    if (this._executed) return

    this.script.execute()
    this._executed = true
  }
}

export default class GDPR {
  categories = {
    technical: true,
    analytic: false,
    marketing: false
  }

  config = {
    cookieName: 'gdpr_selected',
    cookieExpires: 30
  }

  trackingCodes = []

  availableToSelect = ['analytic', 'marketing']

  settingsExtended = false

  gdprCheckboxContainer = document.querySelector('.gdpr__checkbox-container')
  gdprSubmitAll = document.querySelector('.gdpr__submit-all')
  gdprSubmitSelected = document.querySelector('.gdpr__submit-selected')
  gdprCheckboxes = document.querySelectorAll('.gdpr__checkbox')
  gdprOffcanvas = document.querySelector('#offcanvasGdpr')
  gdprToggleSettings = document.querySelector('.gdpr__settings-toggler')
  gdprAccordion = document.querySelector('.gdpr__accordion-collapse')

  constructor() {
    this.generateTrackingCodes()

    this.setTrackingCodesFromStorage()

    this.gdprCheckboxContainer.addEventListener('change', this.change.bind(this))
    this.gdprSubmitAll.addEventListener('click', this.submitAll.bind(this))
    this.gdprSubmitSelected.addEventListener('click', this.submit.bind(this))
    this.gdprToggleSettings.addEventListener('click', this.toggleGDPRsettings.bind(this))

  }

  generateTrackingCodes() {
    this.trackingCodes.push(new TrackingCodes('analytic', window.sb_google_analytics))
    this.trackingCodes.push(new TrackingCodes('analytic', window.sb_facebook_pixel))
    this.trackingCodes.push(new TrackingCodes('marketing', new AdsConversions()))
  }

  setTrackingCodesFromStorage() {
    const selectedGdpr = this.getGdprFromStorage()
    console.log('selectedGdpr', selectedGdpr)
    if (!selectedGdpr) {
      this.displayGDPR()
      return;
    }

    this.setCheckboxes(selectedGdpr).executeTrackingCodes()
  }

  setCheckboxes(selectedGdpr) {
    this.categories = selectedGdpr
    this.gdprCheckboxes.forEach(elem => elem.checked = this.categories[elem.value])
    return this
  }

  getGdprFromStorage() {
    return !cookies.get(this.config.cookieName) ? false : JSON.parse(cookies.get(this.config.cookieName))
  }

  displayGDPR() {
    this.offcanvas = new Offcanvas(this.gdprOffcanvas, {
      backdrop: 'static'
    })
    this.offcanvas.show()
  }

  hideGDPR() {
    this.offcanvas.hide()
  }

  toggleGDPRsettings(e) {
    e.preventDefault()
    this.accordion = new Collapse(this.gdprAccordion, {toggle: false})
    if (this.settingsExtended) this.collapseGDPR()
    if (!this.settingsExtended) this.extendGDPR()
    this.settingsExtended = !this.settingsExtended
  }

  extendGDPR() {
    this.gdprSubmitSelected.classList.remove('d-none')
    this.accordion.show()
  }

  collapseGDPR() {
    this.gdprSubmitSelected.classList.add('d-none')
    this.accordion.hide()
  }

  change(e) {
    if (!e.target.closest('.gdpr__checkbox')) return;

    const value = e.target.value

    this.categories[value] = !this.categories[value]
    return this
  }

  submitAll() {
    for (const key of Object.keys(this.categories)) this.categories[key] = true
    this.submit()
  }

  submit() {
    cookies.set(this.config.cookieName, JSON.stringify(this.categories), this.config.cookieExpires)
    this.executeTrackingCodes()
      .setCheckboxes(this.categories)
      .hideGDPR()
  }

  executeTrackingCodes() {
    this.trackingCodes.forEach((trackingCode) => {
      if (!this.categories[trackingCode.category]) return
      trackingCode.execute()
    })
    return this
  }
}
