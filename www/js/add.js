<script type="text/javascript">
$(document).ready(function(){
	$(".addbtn").click(function(){
		$.ajax({
		type:"POST",
		url:"add.php",
		data:{idcard:$("#idcard").val(), comment:$("#formcomment").val()},
		success:function(data){
				$("#rescomment").html(data);
				$("#formcomment").val("");
            }
		});	 
	});
});
</script>