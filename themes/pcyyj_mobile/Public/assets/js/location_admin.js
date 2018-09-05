$(function(){
	$(".btn_").on("click",function(){

		$(this).find("img").attr("src","http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarYes.png")
		$(this).parent().parent().siblings().find(".btn_ img").attr("src","http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarNo.png");
		var id = $(this)[0].dataset.id;
		$.ajax({
		type:"POST",
		url:"Portal/Mine/addMraddress",
		data:{
			"id":id,
		},
		success:function (data) {
			if(data.code==200){
	//            alert(data.msg);
			}
			else {
	             alert(data.msg);
			}
		},
		error:function () {
			alert("网络错误");
		}
	})
		
		
		
	})
})
