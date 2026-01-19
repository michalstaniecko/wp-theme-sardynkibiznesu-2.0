const navigationToggler = document.querySelector('.navigation__toggle')
const navigation = document.querySelector('.navigation')
const body = document.querySelector('body')

// Guard clause: exit if navigation elements don't exist on this page
if (!navigationToggler || !navigation) {
  // Navigation elements not found - skip initialization
} else {
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
}

// Header scroll shadow effect
const header = document.querySelector('.header')

if (header) {
  const handleScroll = () => {
    if (window.scrollY > 10) {
      header.classList.add('header--scrolled')
    } else {
      header.classList.remove('header--scrolled')
    }
  }

  // Initial check
  handleScroll()

  // Listen for scroll events with passive flag for performance
  window.addEventListener('scroll', handleScroll, { passive: true })
}
