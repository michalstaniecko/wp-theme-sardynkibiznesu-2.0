export default class AdsConversions {

  conversions = [
    {
      'name': 'ototax',
      'url': 'https://ototax.pl/rejestracja/?=SardynkiBiznesu.pl',
      'sendTo': 'AW-688741306/0wijCPnojdoDELq3tcgC'
    },
    {
      'name': 'ifirma',
      'url': 'https://www.ifirma.pl/app/wa/polecam?ref=OLUE2I6LK4',
      'sendTo': 'AW-688741306/gvIbCPuRvKIZELq3tcgC'
    }
  ]

  constructor() {
    this.links = document.querySelectorAll('a')
    this.execute()
  }

  execute() {
    console.log('execute ads conversion')
    this.links.forEach(item => {
      this.conversions.forEach(conversion => {
        if (item.getAttribute('href') == conversion.url) {
          item.addEventListener('click', (e) => {
            e.preventDefault()
            this.gtag_report_conversion(conversion.url, conversion.sendTo)
          }, {once: true})
        }
      })
    })
  }

  callback = (url) => () => {
    if (typeof(url) != 'undefined') {
      window.location = url;
    }
  };

  gtag_report_conversion(url, sendTo) {
    gtag('event', 'conversion', {
      'send_to': sendTo,
      'event_callback': this.callback(url)
    });
    return false;
  }
}
