
//全选
function allOK(that) {
	if(that.siblings("input").prop("checked") == false) {
		$(".options input").prop("checked", true)
		that.prop("checked", true)
		that.addClass("optionActive")
		$(".options .option").addClass("optionActive")
	} else {
		$(".options input").prop("checked", false)
		that.removeClass("optionActive")
		$(".options .option").removeClass("optionActive")
	}
	totalPrice()
	totalNum()
}
//列表项点击选择
function allList(that) {
	if(that.siblings("input").prop("checked") == false) {
		that.addClass("optionActive")
		
	} else {
		
		that.removeClass("optionActive")
		$("#allOk").prop("checked",false)
		$("#allOk").siblings(".option").removeClass("optionActive")
		
	}
	totalPrice($(".model .modelList .options .option").siblings())
	totalNum()
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
			var val = that.siblings().find("input").val()
			if(val == 1) {
				val = 1
			} else {
				val--
			}
			that.siblings().find("input").val(val)
			that.parent().parent().parent().find(".price .num").html("×"+val)
			that.parent().parent().parent().find(".subtotal .allPrice").html("¥"+(val*data.data.price))
			totalPrice()
			totalNum()
		}
		else {
			alert(data.msg);
		}
	},
	error:function () {
		alert("网络错误");
	}
})
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
				var val = that.siblings().find("input").val()
				if(val == 99) {
					val = 99
				} else {
					val++
				}
				that.siblings().find("input").val(val)
				that.parent().parent().parent().find(".price .num").html("×"+val)
				that.parent().parent().parent().find(".subtotal .allPrice").html("¥"+(val*data.data.price))
				totalPrice()
				totalNum()
		}
		else {
			alert(data.msg);
		}
	},
	error:function () {
		alert("网络错误");
	}
})

}
//输入事件触发
function num(that) {
	var val = parseInt(that.val())
//	console.log(val*150)
	that.parent().parent().parent().parent().parent().find(".price .num").html("×"+val)
	that.parent().parent().parent().parent().parent().find(".subtotal .allPrice").html("¥"+(val*150))
	totalPrice()
	totalNum()
}
//合计价格
function totalPrice(sta){
	var clas = $(".model .modelList .options .option")
	var money = 0
	clas.each(function(){
		if($(this).hasClass("optionActive")){
			money = money + parseFloat($(this).parent().parent().siblings(".listFrame").find(".subtotal .allPrice").html().split("¥")[1])
		}
	})
	console.log(money)
	$(".totalPrice").html("¥"+money)
}
//合计数量
function totalNum(sta){
	var clas = $(".model .modelList .options .option")
	var money = 0
	clas.each(function(){
		if($(this).hasClass("optionActive")){
			money = money + parseInt($(this).parent().parent().siblings(".listFrame").find(".num_ .pagination input").val())
		}
	})
	console.log(money)
	$(".aggregate .aggregatePrice .num").html(money)
}
