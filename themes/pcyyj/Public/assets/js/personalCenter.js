$(function(){
	$("#B .setLocation button").on("click",function(){
		$("#B .list").hide()
		$("#B .setLocation").hide()
		$(".compile").show()
		$(".compile .list").show()
		$(".userLeft #myTab li").removeClass("active")
				$(".userLeft #myTab li").eq(1).addClass("active")
				$(".userRight #myTabContent #A").removeClass("active")
				$(".userRight #myTabContent #A").removeClass("in")
				$(".userRight #myTabContent #B").addClass("active")
				$(".userRight #myTabContent #B").addClass("in")
				$(".compile").show().siblings().hide()
				$('#distpicker2').distpicker({
					province: '北京市',
					city: '北京市市辖区',
					district: '东城区'
			   });
	})

	$(".edit").on("click",function(){
        $("#B .list").hide()
        $("#B .setLocation").hide()
        $(".compile").show()
        $(".compile .list").show()
		var id = $(this)[0].dataset.id;
        $.ajax({
            type:"POST",
            url:"http://vyskin.com/Mine/getMyaddress",
            data:{
                "id":id,
            },
            success:function (data) {
                if(data.code==200){
                    $("#sh_name").val(data.data.sh_name);
                    $("#sh_mobile").val(data.data.sh_mobile);
                    var sheng = data.data.sheng;
                    var shi = data.data.shi;
                    var qu = data.data.qu;
                    $("#address_id").val(data.data.id);
                    $("#xiangxi").val(data.data.xiangxi);
					$('#distpicker2').distpicker({
					province: sheng,
					city: shi,
					district: qu
			   });
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
