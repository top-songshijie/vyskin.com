$(function(){
	
	var status = 1;
	$(".listYN").on("click",function(){
		var src = $(this).find(".images img").attr("src")
		if(src == "/themes/pcyyj_mobile/Public/assets/img/defaultYes.png"){
			 $(this).find(".images img").attr("src","/themes/pcyyj_mobile/Public/assets/img/defaultNo.png")
			 status = 0
		}else{
			$(this).find(".images img").attr("src","/themes/pcyyj_mobile/Public/assets/img/defaultYes.png")
			status = 1
		}
	})
	

	
	$(".saveAddress").click(function(){
		var sh_name = $('#sh_name').val();
		var sh_mobile = $('#sh_mobile').val();
		var xiangxi = $('#xiangxi').val();
		var sheng = resultVal[0].label;
		var shi = resultVal[1].label;
		var qu = resultVal[2].label;
		var xinjianbiaoji = $('#xinjianbiaoji').val();
		var cart_id = $('#cart_id').val();
		// alert(xinjianbiaoji);
		$.ajax({
			type:"POST",
			url:"Portal/Mine/addAddressPhone",
			data:{
				"sh_name":sh_name,
				"sh_mobile":sh_mobile,
				"sheng":sheng,
				"shi":shi,
				"qu":qu,
				"xiangxi":xiangxi,
				"status":status,
			},
			success:function (data) {
				if(data.code==200){
					if(xinjianbiaoji == "1"){

					// <a href="{:U('Order/orderConfirmation',array('id'=>$id))}">
						window.location.href="http://vyskin.com/Order/orderConfirmation?id="+cart_id;
						// alert('返回购物车购买流程');
					} else if (xinjianbiaoji == "2"){
						// window.location.href="{:U('Order/orderConfirmationphone')}";
						window.location.href="http://vyskin.com/Order/orderConfirmationphone";
						// alert('返回直接购买流程');
					} else {
                        alert(data.msg);
					}

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
	
	
	
	$(".listCit").on('click', function() {
			citypicker(".listLocation");
	})
})
