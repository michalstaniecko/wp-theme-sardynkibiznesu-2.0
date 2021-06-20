const searchIcon = document.querySelector('.header__search-icon')
const searchForm = document.querySelector('.header__search')

searchIcon.addEventListener('click', () => {searchFormToggle()})

const searchFormToggle = () => {
  if (searchForm.classList.contains('header__search--display')) {
    searchForm.classList.remove('header__search--display')
  } else {
    searchForm.classList.add('header__search--display')
  }
}
