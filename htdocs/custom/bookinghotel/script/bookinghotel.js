$(window).on('load', function() {
  $('#listsearchbymonth').show();
});
jQuery(document).ready(function() {

	// FOR PROPAL
	// Hide in show
	// if($("td.propal_extras_rs_modulebookinghotel").html() == ""){
	// 	$("td.propal_extras_rs_modulebookinghotel").parent().hide();
	// 	$("td.propal_extras_rs_modulebookinghotel_1").parent().hide();
	// 	$("td.propal_extras_rs_modulebookinghotel_2").parent().hide();
	// 	$("td.propal_extras_rs_modulebookinghotel_3").parent().hide();
	// 	$("td.propal_extras_rs_modulebookinghotel_4").parent().hide();
	// }
  
	// Remove EDIT icon
	// if($('a[href*="&action=edit_extras&attribute=rs_modulebookinghotel"]').length)
		// $('a[href*="&action=edit_extras&attribute=rs_modulebookinghotel"]').remove();



  
	// FOR FACTURE
	// Hide in show
	// if($("td.facture_extras_rs_modulebookinghotel_f").html() == ""){
	// 	$("td.facture_extras_rs_modulebookinghotel_f").parent().hide();
	// 	$("td.facture_extras_rs_modulebookinghotel_f_1").parent().hide();
	// 	$("td.facture_extras_rs_modulebookinghotel_f_2").parent().hide();
	// 	$("td.facture_extras_rs_modulebookinghotel_f_3").parent().hide();
	// 	$("td.facture_extras_rs_modulebookinghotel_f_4").parent().hide();
	// }
	// Remove EDIT icon
	// if($('a[href*="&action=edit_extras&attribute=rs_modulebookinghotel_f"]').length)
		// $('a[href*="&action=edit_extras&attribute=rs_modulebookinghotel_f"]').remove();






	$('.popupCloseButton').click(function(){
      if ($('.popupCloseButton').attr('changed') == "yes") {
        projet_choose_change();
      }
      $('.hover_bkgr_fricc').hide();
      $('.createorupdatetask').hide();
      $('.popupCloseButton').attr('changed','no');
      notask = '<tr class="oddeven"><td colspan="2" class="opacitymedium" align="center">Aucune sous-tâche</td></tr>';
      $("#sous_taches tbody").html(notask);
      $("#commentaires").html("");
    });
    $("form#lier_propal_form").submit(function(e) {
	    e.preventDefault();
	});



	// $('.leftrightthree .left_').on('click', function() {
 //      var date = $('#debut').datepicker('getDate');
 //      date.setTime(date.getTime() - (3*1000*60*60*24))
 //      $('#debut').datepicker("setDate", date);

 //      var date2 = $('#fin').datepicker('getDate');
 //      date2.setTime(date2.getTime() - (3*1000*60*60*24))
 //      $('#fin').datepicker("setDate", date2);

 //      $('input#go_button').click();
 //    });
 //    $('.leftrightthree .right_').on('click', function() {
 //      var date = $('#debut').datepicker('getDate');
 //      date.setTime(date.getTime() + (3*1000*60*60*24))
 //      $('#debut').datepicker("setDate", date);

 //      var date2 = $('#fin').datepicker('getDate');
 //      date2.setTime(date2.getTime() + (3*1000*60*60*24))
 //      $('#fin').datepicker("setDate", date2);
      
 //      $('input#go_button').click();
 //    });

    $('span#minsdayend').on('click', function() {
      var date = $('#fin').datepicker('getDate');
      date.setTime(date.getTime() - (1000*60*60*24))
      $('#fin').datepicker("setDate", date);
      setmaxdatpicker();
    });
    $('span#plusdayend').on('click', function() {
      var date = $('#fin').datepicker('getDate');
      date.setTime(date.getTime() + (1000*60*60*24))
      $('#fin').datepicker("setDate", date);
      setmaxdatpicker();
    });
    $('span#minsdaystart').on('click', function() {
      var date = $('#debut').datepicker('getDate');
      date.setTime(date.getTime() - (1000*60*60*24))
      $('#debut').datepicker("setDate", date);
      setmindatpicker();
    });
    $('span#plusdaystart').on('click', function() {
      var date = $('#debut').datepicker('getDate');
      date.setTime(date.getTime() + (1000*60*60*24))
      $('#debut').datepicker("setDate", date);
      setmindatpicker();
    });

    // a[href*="&action=edit_extras&attribute=rs_modulebookinghotel"],
     $('a[href*="admin/propal_extrafields.php?action=delete&attrname=rs_modulebookinghotel"],a[href*="propaldet_extrafields.php?action=delete&attrname=dolirefreservinlines"],a[href*="propaldet_extrafields.php?action=edit&attrname=dolirefreservinlines"],a[href*="admin/propal_extrafields.php?action=delete&attrname=rs_modulebookinghotel_1"],a[href*="admin/propal_extrafields.php?action=delete&attrname=rs_modulebookinghotel_2"],a[href*="admin/propal_extrafields.php?action=delete&attrname=rs_modulebookinghotel_3"],a[href*="admin/propal_extrafields.php?action=delete&attrname=rs_modulebookinghotel_4"],a[href*="admin/propal_extrafields.php?action=edit&attrname=rs_modulebookinghotel"],a[href*="admin/propal_extrafields.php?action=edit&attrname=rs_modulebookinghotel_1"],a[href*="admin/propal_extrafields.php?action=edit&attrname=rs_modulebookinghotel_2"],a[href*="admin/propal_extrafields.php?action=edit&attrname=rs_modulebookinghotel_3"],a[href*="admin/propal_extrafields.php?action=edit&attrname=rs_modulebookinghotel_4"],tr.propal_extras_rs_modulebookinghotel,tr.propal_extras_rs_modulebookinghotel_1,tr.propal_extras_rs_modulebookinghotel_2,tr.propal_extras_rs_modulebookinghotel_3,tr.propal_extras_rs_modulebookinghotel_4').remove();

     // a[href*="&action=edit_extras&attribute=rs_modulebookinghotel_f"],
    // $('a[href*="admin/facture_cust_extrafields.php?action=delete&attrname=rs_modulebookinghotel_f"],a[href*="admin/facture_cust_extrafields.php?action=delete&attrname=rs_modulebookinghotel_f_1"],a[href*="admin/facture_cust_extrafields.php?action=delete&attrname=rs_modulebookinghotel_f_2"],a[href*="admin/facture_cust_extrafields.php?action=delete&attrname=rs_modulebookinghotel_f_3"],a[href*="admin/facture_cust_extrafields.php?action=delete&attrname=rs_modulebookinghotel_f_4"],a[href*="admin/facture_cust_extrafields.php?action=edit&attrname=rs_modulebookinghotel_f"],a[href*="admin/facture_cust_extrafields.php?action=edit&attrname=rs_modulebookinghotel_f_1"],a[href*="admin/facture_cust_extrafields.php?action=edit&attrname=rs_modulebookinghotel_f_2"],a[href*="admin/facture_cust_extrafields.php?action=edit&attrname=rs_modulebookinghotel_f_3"],a[href*="admin/facture_cust_extrafields.php?action=edit&attrname=rs_modulebookinghotel_f_4"],tr.facture_extras_rs_modulebookinghotel_f,tr.facture_extras_rs_modulebookinghotel_f_1,tr.facture_extras_rs_modulebookinghotel_f_2,tr.facture_extras_rs_modulebookinghotel_f_3,tr.facture_extras_rs_modulebookinghotel_f_4').remove();
    
    $('a[href*="admin/product_extrafields.php?action=edit&attrname=rs_modulebookinghotel_occupied"],a[href*="admin/product_extrafields.php?action=delete&attrname=rs_modulebookinghotel_occupied"]').remove();
});

function setmaxdatpicker(){
  $("input#chngd_oth").val(1);
$("#debut").datepicker("option", "maxDate", $("#fin").val());
}
function setmindatpicker(){
  $("input#chngd_oth").val(1);
$("#fin").datepicker("option", "minDate", $("#debut").val());
}