const navigationToggler = document.querySelector('.navigation__toggle')
const navigation = document.querySelector('.navigation')

navigationToggler.addEventListener('click', () => {
  !navigation.classList.contains('navigation--pushed')
    ? navigation.classList.add('navigation--pushed')
    : navigation.classList.remove('navigation--pushed')
})
