/**
 * VIMI
 * 2017-08-1
 * */

window.onload = function(){
	var numEl = document.getElementById('num');
	var num = parseInt(numEl.innerText);
	$(".imgbox").on('click','.checkbox',numCount) 
	//点击放大图片
	$('.photoList').on('click','.single_photo',function(){
		$(this).addClass('open');
		$('.home').html('返回');
		var that = $(this).find('.checkbox') ;
		var src = $(this).find('img')[0].src;
		var dataId = $(this).find('img').attr('data-id');
		$('#showImg').attr({'src':src,'data-id':dataId});
		if(that.hasClass('active')){
			$('.recheck').addClass('default active');
			$('.single_img_box').addClass('active');
		}else{
			$('.recheck').addClass('default');
			$('.single_img_box').addClass('active');
		}
	})
	//点击返回或者返回
	$('.foot').on('click','.home',function(){
		if($(this).html() == '返回'){
			suofang();
			$('.single_img_box').removeClass('active');
			$(this).html('首页');
		}else{
            window.location.href='/';
		}
	})
	//收缩单个展示
	$('.mui-content').on('click','.single_img_box',suofang)
	//recheck 选中与否
	$('.numbox').on('click','.recheck',numCount);
	function suofang(){
		if($('.recheck').hasClass('default active')){
			$('.single_photo.open').find('.checkbox').addClass('active')
		}else(
			$('.single_photo.open').find('.checkbox').removeClass('active')
		)
		$('.home').html('首页');
		$('#img_box').removeClass('active');
		$('.recheck').removeClass('default active') ;
		$('.single_photo').removeClass('open');
	}
	//选中并计算
	function numCount(){
		event.stopPropagation();
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			$('.single_photo.open').find('.checkbox').removeClass('active')
			num--;
			numEl.innerHTML=num;
		}else{
			$(this).addClass('active');
			$('.single_photo.open').find('.checkbox').addClass('active')
			num++;
			numEl.innerHTML=num;
		}
	}
	//左右滑动
	mui('#showImg')[0].addEventListener("swipeleft",function(){
		if($('.open').next().length > 0){
			$('.single_photo.open').removeClass('open').next().addClass('open');
			swipe();
		}else{
			mui.toast('最后一页了')
		}
	});
	mui('#showImg')[0].addEventListener("swiperight",function(){
		if($('.open').prev().length > 0){
			$('.single_photo.open').removeClass('open').prev().addClass('open');
			swipe();
		}else{
			mui.toast('最后一页了')
		}
	});
	function swipe(){
		
		var src = $('.single_photo.open').find('img')[0].src ;

		$("#showImg").animate({height:'toggle'},"fast","swing",function(){
			$("#showImg").attr("src",src);
			$("#showImg").animate({height:'toggle'},"fast","swing");
		});
		var that = $('.single_photo.open').find('.checkbox') ;
		
		$('.recheck').removeClass('default active');
		if(that.hasClass('active')){
			$('.recheck').addClass('default active');
			$('.single_img_box').addClass('active');
		}else{
			$('.recheck').addClass('default');
			$('.single_img_box').addClass('active');
		}
	}
}