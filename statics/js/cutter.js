/*tooltip*/
$(function(){
	$('[rel=tooltip]').hover(function(){ 
		$('<div class="tooltip" style="display:none; top:'+($(this).offset().top+$(this).height()+5)+'px;left:'+$(this).offset().left+'px;">'+$(this).attr('title')+'<div class="arrow"></div></div>').appendTo('body').fadeIn();
		//$('body').append('<div class="tooltip" style="top:'+($(this).offset().top+$(this).height()+5)+'px;left:'+$(this).offset().left+'px;">'+$(this).attr('title')+'<div class="arrow"></div></div>');						  		
	},
	function(){
		$('.tooltip').fadeOut().remove();	
	})
	
	
	$('.topbar .collapse').click(function(){
		$('.topbar .module, .topbar .search, .topbar .sub').toggle();									  
	})
})


/*ѡ�Ч��*/
$(function(){
	$('.taber .head a').hover(function(){
		$('.taber .body').hide();
		$('.taber #'+$(this).attr('lang')).show();	
		
		$('.taber .head a').removeClass('selected');
		$(this).addClass('selected');
	})		   
})


/*ͷ�������˵�*/
$(function(){
	$('.topbar .module li').hover(function(){
		$(this).addClass('selected');
	},
	function(){
		$(this).removeClass('selected');
	})
})


/*heading��Ӧʽ�û�����*/
$(function(){
/*	$('.heading').hover(function(){
		$(this).animate({'height':'+=10'},300,function(){
														  
		})							 
	},
	function(){
		$(this).animate({'height':'-=10'},300,function(){
														  
		})		
	})	*/	   
})

$(function(){
	$('a[rel=popup]').click(function(){
		
		$('body').prepend('<div id="mask"></div>').find('#mask').css({opacity:0.5,  cursor:'pointer', background:'black', position:'absolute', zIndex:999, width:'100%',  height:$(document).height()});
		
		
		$($(this).attr('href')).fadeIn();
		
	})		   
	
	
	$('#mask, .popup del').live('click',function(){
		$('#mask').remove();
		$(this).parent('.popup').fadeOut(); $(this).parent().parent('.popup').fadeOut();
	})
})

/*��ͨ����*/
$(function(){
	setTimeout(function(){
			$('.cartoon').fadeIn();				
		},1000)		   
})

/*ͷ����ʾ��*/
$(function(){
	$('.spring del').click(function(){
		$('.spring').slideUp();								
	})		   
})

/*ͷ������������ �û�����*/
$(function(){
	$('.topbar input[type=text]').focus(function(){
		//$(this).animate({'width':'+=10px'},'fast')									 
	})			
})


/*�������̶�*/
$(document).ready(function(){
		
	$(window).bind('scroll',function() {
		if(Math.abs($(window).scrollTop())>300)
			{
				$('.topbar.js').hide().addClass('fixed').fadeIn('slow');
			}
			else
			{
				$('.topbar.js').removeClass('fixed');
			}
	});
	
});

/*�ص�����*/
$(document).ready(function(){
	
	if($.browser.msie&&($.browser.version == "6.0")&&!$.support.style){
		
	}
	else{
		$(window).bind('scroll',function() {
			if(Math.abs($(window).scrollTop())>600)
				{
					$('.scrolltotop').fadeIn();
				}
				else
				{
					$('.scrolltotop').fadeOut();
				}
		});	
	}
	
});


/*�õ�Ƭ*/
$(function(){
		
		$.extend({
			autoSlider:function(){
				
				if($('.slider .item.selected').next().size()==0){
					$('.slider .item.selected').removeClass('selected').parent().find('.item:first').addClass('selected');
				}
				else{
					$('.slider .item.selected').removeClass('selected').next().addClass('selected');
				}
			}
		})
		// �����ظ����ã�����jQuery�ķ���һ��Ҫ�������д�����壬�������ﲻ����Ч
		setInterval("$.autoSlider()",6000);

     $('.slider .prev').click(function(){
		
			if($('.slider .item.selected').next().size()==0){
					$('.slider .item.selected').removeClass('selected').parent().find('.item:first').addClass('selected');
				}
				else{
					$('.slider .item.selected').removeClass('selected').next().addClass('selected');
				}
		},
		function(){});
		
		$('.slider .next').click(function(){
		
			if($('.slider .item.selected').next().size()==0){
					$('.slider .item.selected').removeClass('selected').parent().find('.item:first').addClass('selected');
				}
				else{
					$('.slider .item.selected').removeClass('selected').next().addClass('selected');
				}
		},
		function(){})
	})


// ����prettify��ɫ���

// Load the stylesheet that we're demoing.
/*var script = document.createElement('script');
script.src = 'assets/js/prettify.js';
document.getElementsByTagName('head')[0].appendChild(script);

var link = document.createElement('link');
link.rel = 'stylesheet';
link.type = 'text/css';
link.href = 'assets/css/prettify.css';
document.getElementsByTagName('head')[0].appendChild(link);
  

$(function(){
  // ����prettify��ɫ���
  $('pre').addClass('prettyprint linenums');
  prettyPrint();
})*/

// $(function(){
	// $('.sidebar li strong').click(function(){
		// $(this).parent().find('.droper').toggle();								
	// })		   
// })



$(function(){
	$('.sidebar li strong').click(function(){
		$(this).parent().find('.droper').slideToggle(function(){ 
			$(this).parent().addClass("show");
		});
		$(this).parent().siblings(".show").find('.droper').slideToggle(function(){
			$(this).parent().removeClass("show");
		});
	})
})