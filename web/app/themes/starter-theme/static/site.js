jQuery( document ).ready( function( $ ) {
  // Your JavaScript goes here

  // Parallax
  const rellax = new Rellax('.rellax')

  // Menu
  const ham = $('.hamburger')
  const showcase = $('#showcase')
  const zoom = $('.product-zoom')
  const nav = $('#nav-main')
  zoom.on('click', function(e) {
    const url = $(this).data('full')
    showcase.find('.showcase-img').attr('src', url)
    showcase.css('display', 'flex')
  })

  showcase.find('.close').on('click', function(e) {
    showcase.css('display', 'none')
    showcase.find('.showcase-img').attr('src', '')
  })

  ham.on('click', function(e) {
    $(this).siblings().show()
    $(this).hide()
    if (nav.hasClass('active')) {
      nav.css('transform', 'translateX(-100%)')
    } else {
      nav.css('transform', 'translateX(0)')
    }
    nav.toggleClass('active')
  })

});