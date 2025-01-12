window.addEventListener('load', () => {

})

if (typeof wp.data === "object") {
  wp.data.subscribe(() => {
    const links = document.querySelectorAll('.sb-block a')
    links.forEach((elem, index) => {
      elem.addEventListener('click', e => {
        e.preventDefault()
      })
    })
  })
}
