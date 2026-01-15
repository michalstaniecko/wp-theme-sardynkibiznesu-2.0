const navigationToggler = document.querySelector('.navigation__toggle')
const navigation = document.querySelector('.navigation')
const body = document.querySelector('body')

let navPushed = false

const toggleMenu = () => {
  const isOpen = navigation.classList.contains('navigation--pushed')

  if (!isOpen) {
    navigation.classList.add('navigation--pushed')
    body.classList.add('is-navigation--pushed')
    body.style.overflow = 'hidden'  // Prevent body scroll when menu is open
  } else {
    navigation.classList.remove('navigation--pushed')
    body.classList.remove('is-navigation--pushed')
    body.style.overflow = ''  // Restore body scroll
  }
}

document.addEventListener('click', (e) => {
  if (!e.target.closest('.navigation__toggle') && !e.target.closest('.navigation')) {
    navigation.classList.remove('navigation--pushed')
    body.classList.remove('is-navigation--pushed')
    body.style.overflow = ''  // Restore body scroll
  }
})

navigationToggler.addEventListener('click', () => {
  toggleMenu()
})


