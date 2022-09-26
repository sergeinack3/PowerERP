$(document).ready(function() {

    $('select#projetid').select2();
    $('select#select_id_post').select2();

    $('#moadl_adttend select.employe').select2();
    $('#new_elmnt_hrm').click(function(){
        $('#moadl_adttend').show();
    });

    $('.fa-clock-o').click(function(){
        $(this).parent().find('.timepicker').focus();
    });

    $('.close, .cancel').click(function(){
        $('select.employe').val('-1');
        // console.log($('select.employe').val());
        $('#moadl_adttend').hide();
    });
    $('.edit_present').click(function(){
        $('#moadl_adttend').show();
    });

    $('#pop_edit').find('a.present').click(function() {
        // console.log('present');
        $(this).hide();
        $('.nopresent').show();
        $('[name="status"]').val('nopresent');
       
        $('#moadl_adttend').find('.timepicker').prop('disabled', true);
        $('.fa-clock-o').hide();
    });


    $('#pop_edit').find('a.nopresent').click(function() {
        // console.log('nopresent');
        $(this).hide();
        $('input[name="status"]').val('present');
        $('.present').show();
        $('#moadl_adttend').find('.timepicker').prop('disabled', false);
        $('.fa-clock-o').show();
    });


    $('.disable').find('.timepicker').prop('disabled', true);
    $('.disable').find('.fa-clock-o').hide();

    $('table.disable').find('.present').hide();
    $('table.disable').find('.nopresent').show();

    $('.all_user').change(function(){
        if($(this).is(':checked')){
            $('.employe').prop('disabled', 'disabled');
            $('.tr_employe').css('opacity', '0.5');
        }else{
            $('.employe').prop('disabled',false);
            $('.tr_employe').css('opacity', '1');
        }
    });
        
    $('#moadl_adttend #employe').select2();
    $('#moadl_adttend .type_award').select2();
    $('#moadl_adttend #complain').select2();
    $('#moadl_adttend #against').select2();

    $('.award_month').datepicker({
       dateFormat: 'mm/yy',
    });

    $('.menu_contenu_gestionhrm_hrm_presences_index ').find('a').prepend('<span class="icon_hrm_presenc"></span>');
    $('.menu_contenu_gestionhrm_hrm_award_index').find('a').prepend('<span class="icon_hrm_award"></span>');
    $('.menu_contenu_gestionhrm_hrm_complain_index ').find('a').prepend('<span class="icon_hrm_complain"></span>');
    $('.menu_contenu_gestionhrm_hrm_warning_index ').find('a').prepend('<span class="icon_hrm_warning"></span>');
    $('.menu_contenu_gestionhrm_hrm_resignation_index ').find('a').prepend('<span class="icon_hrm_resignation"></span>');
    $('.menu_contenu_gestionhrm_hrm_termination_index ').find('a').prepend('<span class="icon_hrm_termination"></span>');
    $('.menu_contenu_gestionhrm_hrm_holiday_index ').find('a').prepend('<span class="icon_hrm_holiday"></span>');
    $('.menu_contenu_gestionhrm_dashbord').find('a').prepend('<i class="fa fa-bar-chart fa-fw paddingright"></i>');


}); 

$(window).on('load', function() {
    $(".datepicker55").datepicker("destroy");
    $('.datepicker55').removeClass('hasDatepicker');
    $("input.datepicker55").datepicker({
        dateFormat: "dd/mm/yy"
    });
    // $('.timepicker99').timepicker({
    //     format: 'H:i',
    // });
    
});

function show_popup(that,etat="show") {

    $('#moadl_adttend').find('table').hide();
    $('#moadl_adttend').show();
    $('#pop_'+etat).show();


    if(etat == 'edit'){
        $('.action').val('update');
    }else{
        $('.action').val('create');
    }

    if(etat == 'show'){
        var tr =$(that).parents('tr');
        var id_ = tr.find('.id').val();
        $('.id_edit').val(id_);
        // console.log(id_);
        var in_time = tr.find('.val_intime').html();
        var out_time = tr.find('.val_outtime').html();
        var date = tr.find('.val_date').html();
        var status = tr.find('.val_status').find('span').data('status');
        $('.date_sp').text(date);
        $('input.date_sp').val(date);
        if(in_time.indexOf(':') != -1 && out_time.indexOf(':') != -1){

            $('.pop_show').find('input.in').val(in_time);
            $('.pop_show').find('input.out').val(out_time);
        }else{

            $('.pop_show').find('input.in').val('');
            $('.pop_show').find('input.out').val('');
        }
            $('.pop_show').find('span.in').text(in_time);
            $('.pop_show').find('span.out').text(out_time);
        
        if(status == 'present') {
            $('.pop_show').find('a.present').show();
            $('.pop_show').find('a.nopresent').hide();
        }else{
            $('.pop_show').find('a.present').hide();
            $('.pop_show').find('a.nopresent').show();
            $('.pop_show').find('a.nopresent').css('display','initial');
        }

    }

}

function show_popup_cmpl(that,etat="show") {

    $('#moadl_adttend').find('table').hide();
    $('#moadl_adttend').show();
    $('#pop_'+etat+'_cmpl').show();


    if(etat == 'edit'){
        $('.action').val('update');
    }else{
        $('.action').val('create');
    }

    if(etat == 'show'){
        var tr =$(that).parents('tr');
        var id_ = tr.find('.id').val();
        $('.id_edit').val(id_);
        // console.log(id_);
        var in_time = tr.find('.val_intime').html();
        var out_time = tr.find('.val_outtime').html();
        var date = tr.find('.val_date').html();
        var status = tr.find('.val_status').find('span').data('status');
        $('.date_sp').text(date);
        $('input.date_sp').val(date);
        if(in_time.indexOf(':') != -1 && out_time.indexOf(':') != -1){

            $('.pop_show').find('input.in').val(in_time);
            $('.pop_show').find('input.out').val(out_time);
        }else{

            $('.pop_show').find('input.in').val('');
            $('.pop_show').find('input.out').val('');
        }
            $('.pop_show').find('span.in').text(in_time);
            $('.pop_show').find('span.out').text(out_time);
        
        if(status == 'present') {
            $('.pop_show').find('a.present').show();
            $('.pop_show').find('a.nopresent').hide();
        }else{
            $('.pop_show').find('a.present').hide();
            $('.pop_show').find('a.nopresent').show();
            $('.pop_show').find('a.nopresent').css('display','initial');
        }

    }

}


function textarea_autosize(that){
    $(that).height($(that)[0].scrollHeight);
    $(that).css('resize', 'none');
}
