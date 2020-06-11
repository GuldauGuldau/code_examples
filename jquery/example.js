$(function() {

  // Slick Sliders -----------------------------------
  var options = {
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
  }
  $('.banner__slider').slick(Object.assign(options, {dots: true}));

  $('.banner__slider1').slick(Object.assign(options, {draggable: false}));

  $('.banner__slider2').slick(Object.assign(options, {draggable: false, fade: true}));

  $('.etabout__slider').slick(Object.assign(options, {dots: true}));

  $('.pblog__slider').slick(Object.assign(options, {dots: true}));

  $('.product__thumb-slider').slick(Object.assign(options, {fade: true, autoplay: true}));

  // Phone imask----------------------------------------
  var element = document.querySelector('input[name="telephone"]');
  if(element !== null) {
    var maskOptions = {
      mask: '+7 (000) 000-00-00',
      lazy: false
    };
    var mask = new IMask( element, maskOptions);
  }

  // Tabs -----------------------------------------------
  var activeTab = $('.tab-link.active').attr('tab-id');
  $('.tab-box').hide();
  $('.tab-box[tab-box="'+activeTab+'"]').show();
  $('.tab-link').click(function(){
    if($(this).hasClass('active')) return;
    var cat = $(this).attr('tab-id');
    var parent = $(this).closest('.tabs');
    parent.find('.tab-link').removeClass('active');
    $(this).addClass('active');
    parent.find( ".tab-box[tab-box]" ).animate({
      opacity: 0
      }, 200, function() {
      parent.find( ".tab-box[tab-box]" ).hide();
      parent.find('.tab-box[tab-box="'+cat+'"]').show();
      });
      $('.tab-box[tab-box="'+cat+'"]').animate({
        opacity: 1
      }, 200)
  })

  // Animation Location List--------------------------------
  $('.location__current').click(function(){
    if($(this).hasClass('open')) {
      $('.location__list').velocity({
          top: "100px",
          opacity: 0
      }, {
          display: "none",
          duration: 200,
          delay: 0,
          easing: "ease-out",
      });
  }  else {
    $('.location__list').velocity({
          top: "60px",
          opacity: 1
      }, {
          display: "block",
          duration: 50,
          delay: 0,
          easing: "ease-out",
      });
    }
    $(this).toggleClass('open');
  })

  // Events Listener--------------------------------------------
  $('.l-modal__close').click(function() {
    $('.l-modal').removeClass('open');
  });

  $('.l-modal__default').click(function() {
    $('.l-modal').removeClass('open');
    $('.location__list').velocity({
        top: "60px",
        opacity: 1
    }, {
        display: "block",
        duration: 50,
        delay: 0,
        easing: "ease-out",
    });
  });

  $('.l-modal__active').click(function(){
    $('.l-modal').removeClass('open');
  });

  $('.icon-arrowbm').click(function(){
    $("html, body").animate({ scrollTop: $('.etblockmenu__row').offset().top - 150 }, "slow");
    return false;
  })

  $(".validate-form").submit(function(e){
      var value = mask.unmaskedValue;
      if(value.length < 10) {
        e.preventDefault();
        $('input[name="telephone"]').addClass('error');
      }
  });

  $('.product__thumb-slider-next').click(function(){
    $(this).closest('.product__thumb-slider').slick('slickNext')
  });

  $('.banner__next').click(function(){
    $('.banner__slider1').slick('slickNext')
    $('.banner__slider2').slick('slickNext')
  });

  $('.banner__prev').click(function(){
    $('.banner__slider1').slick('slickPrev')
    $('.banner__slider2').slick('slickPrev')
  });

  $('.et-select__active').click(function(){
    $(this).closest('.et-select').toggleClass('open');
  })

  $('.et-select__list li').click(function(){
    var parent = $(this).closest('.et-select');
    parent.find('.et-select__active span').text($(this).text());
    parent.find('.et-select__input').attr('value', $(this).attr('data-id'));
    parent.toggleClass('open');
  })

  $('#button-coupon').on('click', function() {
    $('.etcoupon__error').text('');
    $.ajax({
      url: 'index.php?route=extension/total/coupon/coupon',
      type: 'post',
      data: 'coupon=' + encodeURIComponent($('input[name=\'coupon\']').val()),
      dataType: 'json',
      success: function(json) {
        if (json['error']) {
          $('.etcoupon__error').text(json['error']);
        }
        if (json['redirect']) {
          location = json['redirect'];
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  });

  $('.alert-close').click(function(){
    $(this).closest('.add_cart_alert').remove();
  })

  $('.msort__active').click(function(){
    $(this).closest('.msort').toggleClass('open');
  })

});
