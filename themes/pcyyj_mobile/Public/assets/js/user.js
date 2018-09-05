$(function(){
	$(".user_5 div:last-child").css("border-right","0")
	
	
	
	
	
	$(".LogOut").on("click",function(){
		$(".LogOutMoudel").show()
	})
	$(".LogOutMoudel .moudel .cancel").on("click",function(){
		$(".LogOutMoudel").hide()
	})
	$(".LogOutMoudel .moudel .exit").on("click",function(){
		alert("退出")
	})
})
