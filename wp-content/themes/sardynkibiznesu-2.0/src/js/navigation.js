const navigationToggler = document.querySelector('.navigation__toggle')
const navigation = document.querySelector('.navigation')
const body = document.querySelector('body')

let navPushed = false

const toggleMenu = () => {

  !navigation.classList.contains('navigation--pushed')
    ? navigation.classList.add('navigation--pushed')
    : navigation.classList.remove('navigation--pushed')


  !body.classList.contains('is-navigation--pushed')
    ? body.classList.add('is-navigation--pushed')
    : body.classList.remove('is-navigation--pushed')

}

document.addEventListener('click', (e) => {
  if (!e.target.closest('.navigation__toggle') && !e.target.closest('.navigation')) {
    navigation.classList.remove('navigation--pushed')
    body.classList.remove('is-navigation--pushed')
  }
})

navigationToggler.addEventListener('click', () => {
  toggleMenu()
})


