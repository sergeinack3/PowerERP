$(document).ready(function() {
	if ($.fn.datepicker) {
		$('.datepicker55').datepicker();
	}
	
	$('.user_date').find('.fa-edit').click(function() {
		console.log('rfgdfgdfddf');
		$('.user_date').hide();
		$('.edit_date').show();
	})

	$('#userid').change(function(){
		var id_user = $(this).val();
		var root = $('#salairecontrat_root').val();
		var option = {id_user: id_user};
		$.post(root + 'ajax/contrat.php', option, function(response) {
			if (response.status == true) {
				$('.edit_date').hide();
				$('#start_date_').val(response.date_emb);
				$('.user_date').find('span').html(response.date_emb);
				$('.user_date').show();
			}
		}, 'json');
	})

	// console.log(new Date(2018, 10 - 1, 25));

	// $('#type').change(function(){
	// 	var id = $(this).val();
	// 	var date_d = $('#start_date_').val();
	// 	if(id == '2'){
	// 		var d = new Date(date_d);
	// 	    var year = d.getFullYear();
	// 	    var month = d.getMonth();
	// 	    var day = d.getDate();
	// 		date = new Date(year + 1, month, day);

	// 		$(".datepicker").datepicker({ minDate: d, maxDate: date });
	// 		console.log(date);		
	// 	}
	// });

})