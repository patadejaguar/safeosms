var $ = jQuery.noConflict();

/** go top **/
$(window).scroll(function() {
	if($(this).scrollTop() > 300) {
        $('#gotoTop').css('display','block');
		$('#gotoTop').stop().animate({opacity: 1});
	} else {
		$('#gotoTop').stop().animate({opacity: 0}, function(){
			$(this).css('display','none');
        });
	}
});
$('#gotoTop').click(function() {
	$('body,html').animate({scrollTop:0},400);
    return false;
});
/** mobile menu **/
$('.menu').mobileMenu();