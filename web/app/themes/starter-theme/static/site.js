jQuery( document ).ready( function( $ ) {
  // Parallax
  let rellax = new Rellax('.rellax')

  // variables
  let cloned = false
  const ham = $('.hamburger')
  const showcase = $('#showcase')
  const zoom = $('.product-zoom')
  const nav = $('#nav-main')
  const body = $('body')
  const video = $('#disfrutos video')
  const disfrutos = $('#disfrutos img')
  let disfrutosShown = false
  const nosotros = $('#nosotros')
  let currentShowcase;

  // Showcase
  zoom.on('click', function(e) {
    const img = $(this).parent().find('.attachment-woocommerce_thumbnail')
    const verticalCenterPoint = $(window).height() / 2
    const horizontalCenterPoint = $(window).width() / 2
    const offset = img.offset()
    const width = img.width()
    const height = img.height()
    const x = horizontalCenterPoint - offset.left - (width / 2)
    const y = verticalCenterPoint - offset.top - (height / 2)
    body.css('height', '100vh')
    body.css('overflow', 'hidden')
    
    img.css('transform', `translate(${x}px, ${y}px)`)
    img.css('opacity', 0)

    const url = $(this).data('full')
    showcase.find('.showcase-img').attr('src', url)
    showcase.css('height', '100vh')
    showcase.css('visibility', 'visible')
    showcase.css('opacity', 1)
    currentShowcase = img
  })

  showcase.find('.close').on('click', function(e) {
    body.css('height', '')
    body.css('overflow', '')
    showcase.css('visibility', 'hidden')
    showcase.css('opacity', 0)
    showcase.css('height', 0)
    showcase.find('.showcase-img').attr('src', '')

    currentShowcase.css('transform', '')
    currentShowcase.css('opacity', 1)
  })

  // Menu
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

  // Video
  video.on('timeupdate', function(e) {
    if (this.currentTime >= 7 && this.currentTime <= this.duration - 1 && !disfrutosShown) {
      disfrutos.css('transform', 'scale(1) translateZ(0)')
      disfrutosShown = true
    } else if (this.currentTime < 7 && disfrutosShown) {
      disfrutos.css('transform', '')
      disfrutosShown = false
    }
  })

  video.on('ended', function() {
    
  })

  // Slider
  new Splide('.splide', {
    autoplay: true,
    loop: true,
  }).mount();

  // Waypoints
  nosotros.waypoint({
    handler(direction) {
      if (direction === 'down' && !cloned) {
        cloned = true
        const frutos = $('#home .rellax').clone()
        $('#nosotros .container').append(frutos)
        setTimeout(function() {
          rellax.destroy()
          rellax = new Rellax('.rellax')
        }, 500)
      }
    },
    offset: '100%'
  })
});