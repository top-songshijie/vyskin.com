
//点击列表选择商品
function shoppList(that) {
//	console.log(that[0].dataset.id)
	var True = that.find(".True img").attr("src")
	if(True == "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarNo.png") {
		that.find(".True img").attr("src", "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarYes.png")
	} else {
		that.find(".True img").attr("src", "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarNo.png")
	}
	totalPrice()
		window.event ? window.event.cancelBubble = true : e.stopPropagation();
}
//全选
function checkAll(that) {
	var True = that.find(" img").attr("src")
	if(True == "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarNo.png") {
		that.find("img").attr("src", "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarYes.png")
		$(".shoppList .True img").attr("src", "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarYes.png")
	} else {
		that.find(" img").attr("src", "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarNo.png")
		$(".shoppList .True img").attr("src", "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarNo.png")
	}
totalPrice()
}

//减法
function subtraction(that) {
	var id = that[0].dataset.id;
	$.ajax({
	type:"POST",
	url:"/Portal/Mycar/editCar",
	data:{
		"id":id,
	},
	success:function (data) {
		if(data.code==200){
			var num = parseInt(that.siblings().find(".num").html())
			if(num == 1){
				num = 1
			}else{
				num--
			}
			that.siblings().find(".num").html(num)
			totalPrice()
			window.event ? window.event.cancelBubble = true : e.stopPropagation();
		}
		else {
			alert(data.msg);
		}
	},
	error:function () {
		alert("网络错误");
	}
})
	
	window.event ? window.event.cancelBubble = true : e.stopPropagation();
}


//加法
function addition(that) {
	var id = that[0].dataset.id;
	$.ajax({
	type:"POST",
	url:"/Portal/Mycar/editCar",
	data:{
		"id":id,
		"caozuo":1,
	},
	success:function (data) {
		if(data.code==200){
			var num = parseInt(that.siblings().find(".num").html())
			if(num == 99){
				num = 99
			}else{
				num++
			}
			that.siblings().find(".num").html(num)
			totalPrice()


			window.event ? window.event.cancelBubble = true : e.stopPropagation();
		}
		else {
			alert(data.msg);
		}
	},
	error:function () {
		alert("网络错误");
	}
})
	
	
	window.event ? window.event.cancelBubble = true : e.stopPropagation();

}


//点击编辑
function compile(that) {
	$(".closeAnAccount").toggle()
	$(".del").toggle()
}
//删除
function del(that){
	$(".shoppList .True img").each(function(){
		if($(this).attr("src") == "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarYes.png"){
			$(this).parent().parent().remove()
		}
	})
}
//合计价格
function totalPrice(sta){
	var clas = $(".shoppList .True img")
	var money = 0
	clas.each(function(){
		if($(this).attr("src") == "http://vyskin.com/themes/pcyyj_mobile/Public/assets/img/shoppCarYes.png"){
money = money + $(this).parent().siblings().find("nav .num").html()*$(this).parent().siblings().find(".price").html().split("¥")[1]
		}
	})
	$(".closeAnAccount .total").html("合计：¥"+money)
}


