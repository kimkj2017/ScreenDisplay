<script>
$(document).ready(function() {
	$(".itemDelete").click(function(event) {
		var answer = confirm("Really delete?");
		if (answer!==true)
		event.preventDefault();
		});
});
</script>
