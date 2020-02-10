<script type="text/javascript">
$(document).ready(function(){
	$(".cardbtons").click(function(){
		$.ajax({
		type:"POST",
		url:"insert.php",
		data:{idcard:$(this).text()},
		success:function(data){
				$("#resultat").html(data);
            }
		});	 
	});
});
</script>