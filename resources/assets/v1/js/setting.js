// ReGex persent
// (^100([.]0{1,2})?)$|(^\d{1,2}([.]\d{0,2})?)$
$.SettingApp = {};
$.SettingApp.general = {
	activated: function(){
		
		const _this = this;
		const $sideBarMenu = $('.sidebar-menu').children('.active');
        $sideBarMenu.parents('.treeview').addClass('menu-open');
        $sideBarMenu.parents('.treeview-menu').css('display', 'block');

        $('.callSelect2').callSelect2();

        $('.focused').focus(function(){
        	this.select();
        });

        $(document).off('keyup', '#filterNavigateMenu').on('keyup', '#filterNavigateMenu', function(){
	        $(this).filterNavigateMenu();
	    });
	    $(document).off('keyup', '#password').on('keyup', '#password', function(){
	        $(this).passwordStrength();
	    });
	    $('.modal').on('show.bs.modal', function(){
	        $(this).find('.modal-content').waitMeShow();
	        setTimeout(() => $(this).find('.modal-content').waitMeHide(), 1000);
	    });
	    $('.modal').on('shown.bs.modal', function(){
	        $('input:text:visible:first', this).focus();
	        $('.callSelect2').callSelect2();
	        // if($(this).hasClass('notValidate') == false){
	        //     $(this).parents('form').data('formValidation').resetForm(true);
	        // }
	        if($('.price').length){
	            $('.price').formatPrice();
	        }
	    });
	    $('.modal').on('hide.bs.modal', function(){
	        $(this).find('.modal-content').waitMeHide();
	        if($(this).hasClass('notValidate') === false){
	            $(this).parents('form').data('formValidation').resetForm(true);
	        }
	    });
	    // $('.lightgallery').lightGallery();
	    if($('#filterNavigateMenu').length){
	        new Typed('#filterNavigateMenu', {
	            strings: ['Filter Navigation', ''],
	            typeSpeed: 100,
	            backSpeed: 30,
	            backDelay: 30,
	            startDelay: 0,
	            attr: 'placeholder',
	            bindInputFocusEvents: true,
	            loop: true
	        });
	    }
	    if($('.callout-dimmis').length){
	        setTimeout(() => $('.callout-dimmis').fadeOut('slow'), 1000);
	    }
	    $('.btn-fullscreen').on('click', function(e){
	        e.preventDefault();
	        toggleFullscreen();
	        // if (screenfull.enabled) {
	        //     screenfull.toggle();
	        //     screenfull.on('change', () => {
	        //         const icon = screenfull.isFullscreen ? 'contract' : 'expand';
	        //         $(this).children('span').attr('class', `ion-android-${icon}`);
	        //     });
	        // } else {
	        //     warning('Request fullscreen disabled');
	        // }
	    });
	    $(document).on({
	        mouseenter: function(){
	            $(this).parent().addClass('is-focus');
	        },
	        mouseleave: function(){
	            $(this).parent().removeClass('is-focus');
	        }
	    }, '.can-focus');
	    if($('#back-to-top').length) {
	        var scrollTrigger = 300, // px
	            backToTop = function () {
	                var scrollTop = $(window).scrollTop();
	                (scrollTop > scrollTrigger) ? 
	                    $('.button-to-top').addClass('show') : 
	                $('.button-to-top').removeClass('show');
	            };
	        backToTop();
	        $(window).on('scroll', function () {
	            backToTop();
	        });
	        $('#back-to-top').on('click', function (e) {
	            e.preventDefault();
	            $('html,body').animate({
	                scrollTop: 0
	            }, 700);
	        });
	    }

	    $('.child-nav').each(function(){
	    	const i = $(this);
	    	if(i.attr('href') === window.location.href){
	    		i.parents().addClass('active');
	    	}
	    });
	    // https://github.com/HubSpot/offline#readme
	    console.log(Offline.check())
	}
}

$(function(){
    $.SettingApp.general.activated();
});