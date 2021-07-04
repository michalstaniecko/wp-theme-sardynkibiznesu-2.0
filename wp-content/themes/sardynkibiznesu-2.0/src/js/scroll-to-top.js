const scrollToTop = document.querySelector('.scroll-to-top')

window.addEventListener('scroll', (e) => {
  scrollToTop.classList.toggle('scroll-to-top--active', window.pageYOffset > 200)
})

scrollToTop.addEventListener('click', (e) => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
})
