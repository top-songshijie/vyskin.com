$(function(){
    id=""
	$(".model .listBody .listOk").on("click",function(){
		$(this).css({
			"background-image":"url(http://vyskin.com/themes/pcyyj/Public/assets/images/ddqr_adress_bg1.png)"
		}).siblings().css({
			"background-image":"url(http://vyskin.com/themes/pcyyj/Public/assets/images/ddqr_adress_bg2.png)"
		})

		if($(this)[0].dataset.id){
            id = $(this)[0].dataset.id;
		}else{
            id = 0
		}
		
        // $(".submitiIndent .btnSubmit").on("click",function(){
        //     console.log(id)
        // })
	})








})
  