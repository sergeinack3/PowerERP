<script type="text/javascript">
    $(window).on('load', function() {
        $('#select_all_hotelchambres').show();
        // gettotprodbycateg();
    });
    jQuery(document).ready(function() {
        var head = $('#tmenu_tooltip').height() + 20;
        $('#lightbox').css('padding-top',head);
        ShowNotesReservRepas();
        // $('#client').select2();

        $('select.h_select_products').select2();
        $("select#mode_reglement,select#to_centrale").select2();
        $('#select_onechambre>select').select2();
        // $("#select_onechambre>select").select2({
            
        //     templateResult: function(data) {
        //         return data.text;
        //     },
        //     sorter: function(data) {
        //         return data.sort(function(a, b) {
        //             return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
        //         });
        //     }
        // });

        $('input[type=radio][name=servicevirtuels]').change(function() {
            if (this.value == '1') {
                // console.log(this.value);
                SetServiceVirtuels();
                
            }
            else {
                $('#tr_servicesvertuels').hide();
            }
        });
    });
    function SetServiceVirtuels(){
        $('#tr_servicesvertuels').show();

        var somprs = 0;
        $("#select_onechambre>select option").prop("selected", false);
        $('#tr_servicesvertuels select > option:selected').each(function() {
            var slctdchambres = $(this).data('slcted');
            var persons = $(this).data('persons');
            // console.log("slctdchambres : "+slctdchambres);
            slctdchambres = slctdchambres+",";
            // $("input#nbrpersonne").val(persons);
            somprs += persons;
            // $("#select_onechambre>select option").prop("selected", false);
            $.each(slctdchambres.split(","), function(i,e){
                $("#select_onechambre>select option[value='" + e + "']").prop("selected", true);
            });
        });

        $("input#nbrpersonne").val(somprs);

        $('#select_onechambre>select').select2();


        // $('#tr_servicesvertuels').show();
        // var slctdchambres = $('#tr_servicesvertuels select option:selected').data('slcted');
        // var persons = $('#tr_servicesvertuels select option:selected').data('persons');
        // // console.log("slctdchambres : "+slctdchambres);
        // slctdchambres = slctdchambres+",";
        // $("input#nbrpersonne").val(persons);
        // $("#select_onechambre>select option").prop("selected", false);
        // $.each(slctdchambres.split(","), function(i,e){
        //     $("#select_onechambre>select option[value='" + e + "']").prop("selected", true);
        // });
        // $('#select_onechambre>select').select2();
    }



    function ShowNotesReservRepas(){
        var note = $('#select_reservation_typerepas').find('option:selected').data('notes');
        // console.log(note);
        $('#info_reser_repas').html(note);
    }
    function newchambreselected(nw) {
        checkdatesdisabled();
        $("input.datepickerdoli").val('');
    }
    function chambremultischanged() {
        var arraychambre = $("#chambre").val();
        values = arraychambre.toString();
        $("#select_onechambre select").find("option").prop("disabled", false);
        $.each(values.split(","), function(i,e){
            $("#select_onechambre select").find("option[value='" + e + "']").prop("disabled", true);
        });
        $("#select_onechambre select").select2();
        checkdatesdisabled();
    }


    function checkdatesdisabled() {
        var arrchambres = $("#select_onechambre>select").val();
        // console.log("arrchambres : "+arrchambres);


        if (arrchambres != null) {
            arrchambres = arrchambres.toString();
            var reservation_id = <?php echo $id;?>;
            // console.log("reservation_id : "+reservation_id);
            // console.log("arrchambres : "+arrchambres);
            // console.log("length : "+arrchambres.length);
            if(arrchambres.length > 0){

                var data = {
                  'arrchambres': arrchambres,
                  'reservation_id': reservation_id,
                  'action': "getDisabledDateFromSlctedServices"
                };
                $.ajax({
                  type: "POST",
                  url: "check.php",
                  data: data,
                  dataType: 'json',
                  success: function(found){
                    if (found) {
                        var array = found;
                        // console.log(array);
                        $("input.datepickerdoli").val('');
                        $("input.datepickerdoli").removeClass('hasDatepicker');
                        $("input.datepickerdoli").datepicker({
                            dateFormat: "dd/mm/yy",
                            beforeShowDay: function(date){
                                var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                                return [ array.indexOf(string) == -1 ]
                            },
                            orientation: "left"
                        });
                    }
                    checknbnuits();
                  }
                });
            }else{
                $("input.datepickerdoli").removeClass('hasDatepicker');
                $("input.datepickerdoli").datepicker({
                    dateFormat: "dd/mm/yy"
                });
            }
        }else{
            $("input.datepickerdoli").removeClass('hasDatepicker');
            $("input.datepickerdoli").datepicker({
                dateFormat: "dd/mm/yy"
            });
        }
    }


    function getAllChambresDisponible() {
        var debut = $("#debut").val();
        var fin = $("#fin").val();
        var selectedChmbrs = $("#select_onechambre>select").val();
        // var hourstart = $("select#hourstart").val();
        // var minstart = $("select#minstart").val();
        // var hourend = $("select#hourend").val();
        // var minend = $("select#minend").val();
        // console.log("debut : "+debut);
        // console.log(" fin : "+fin);

        hd = $("#hourstart").val();
        md = $("#minstart").val();

        hf = $("#hourend").val();
        mf = $("#minend").val();

        debut = $("#debut").val().split("/");
        fin = $("#fin").val().split("/");

        debut = debut[2]+'-'+debut[1]+'-'+debut[0]+' '+hd+':'+md+':00';
        fin = fin[2]+'-'+fin[1]+'-'+fin[0]+' '+hf+':'+mf+':00';



        var reservation_id = <?php echo $id;?>;

        selectedChmbrs = selectedChmbrs.toString();

        var data = {
          'debut': debut,
          'fin': fin,
          'reservation_id': reservation_id,
          'selectedChmbrs': selectedChmbrs,
          // 'hourstart': hourstart,
          // 'minstart': minstart,
          // 'hourend': hourend,
          // 'minend': minend,
          'action': "getAllChambresDisponible"
        };
        $.ajax({
          type: "POST",
          url: "check.php",
          data: data,
          dataType: 'json',
          success: function(found){
            if (found) {
            // console.log(found);
            $('#select_all_hotelchambres').html(found);

            $.each(selectedChmbrs.split(","), function(i,e){
                $("#select_onechambre>select option[value='" + e + "']").prop("selected", true);
            });

            $('#select_onechambre>select').select2();
            if($('input[type=radio][name=servicevirtuels]:checked').val() == "1")
                SetServiceVirtuels();









            // $("#select_onechambre>select").select2({
                
            //     templateResult: function(data) {
            //         return data.text;
            //     },
            //     sorter: function(data) {
            //         return data.sort(function(a, b) {
            //             return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
            //         });
            //     }
            // });


            // $("input.datepickerdoli").val('');
            //       $("input.datepickerdoli").removeClass('hasDatepicker');
            //       $("input.datepickerdoli").datepicker({
            //           dateFormat: "dd/mm/yy",
            //           beforeShowDay: function(date){
            //               var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
            //               return [ array.indexOf(string) == -1 ]
            //           },
            //           orientation: "left"
            //       });
            }
          }
        });
    }
</script>
<script type="text/javascript">
    $( function() {
        $('input.datepickerdoli#debut').change(function(){
            debut = $('input.datepickerdoli#debut').val();
            fin = $('input.datepickerdoli#fin').val();
            
            debut = debut.split("/");
            debut = debut[2]+"-"+debut[1]+"-"+debut[0];

            fin = fin.split("/");
            fin = fin[2]+"-"+fin[1]+"-"+fin[0];

            if (debut > fin) {
               $("input.datepickerdoli#fin").val('');
            }

            if (!$('input.datepickerdoli#fin').val() && $('input.datepickerdoli#debut').val()) {
               $("input.datepickerdoli#fin").val($('input.datepickerdoli#debut').val());
            }
            $('input.datepickerdoli#fin').trigger('change');
        });
        $('input.datepickerdoli#fin').change(function(){
            debut = $('input.datepickerdoli#debut').val();
            fin = $('input.datepickerdoli#fin').val();

            debut = debut.split("/");
            debut = debut[2]+"-"+debut[1]+"-"+debut[0];

            fin = fin.split("/");
            fin = fin[2]+"-"+fin[1]+"-"+fin[0];

            if (debut > fin) {
               $("input.datepickerdoli#debut").val('');
            }

            if (fin > debut && $("#client").val() > 0) {
                getAllChambresDisponible();
            }
            checknbnuits();
        });
        $('select.fulldaystarthour,select.fulldaystartmin').change(function(){
            debut = $('input.datepickerdoli#debut').val();
            fin = $('input.datepickerdoli#fin').val();

            debut = debut.split("/");
            debut = debut[2]+"-"+debut[1]+"-"+debut[0];

            fin = fin.split("/");
            fin = fin[2]+"-"+fin[1]+"-"+fin[0];

            if (fin > debut && $("#client").val() > 0) {
                getAllChambresDisponible();
            }
        });


        $('select#hourstart,select#minend,select#hourend,select#minstart').change(function(){
            checknbnuits();
        });


        $('#select_onechambre select#select_chambre').change(function(){
            gettotprodbycateg();
        });

    });

    //Method to ad
    function addDays(date, days) {
      var dat = date;
      dat.setDate(dat.getDate() + days);
      return dat;
    }

    function gettotprodbycateg(){

        var arrchambres = $("#select_onechambre>select").val();
        // console.log(arrchambres);
        $('#totprodctbycatgs').html('');
        if (arrchambres != null) {
            arrchambres = arrchambres.toString();
            if(arrchambres.length > 0){

                var data = {
                  'arrchambres': arrchambres,
                  'action': "getTotalProductsByCateg"
                };
                $.ajax({
                  type: "POST",
                  url: "check.php",
                  data: data,
                  dataType: 'json',
                  success: function(found){
                    if (found) {
                        var array = found;
                        $('#totprodctbycatgs').html(found);
                    }
                  }
                });
            }
        }
    }

    function checknbnuits(){
        if ($('input.datepickerdoli#fin').val() && $('input.datepickerdoli#debut').val()) {
            // console.log($('input.datepickerdoli#debut').val());
            // console.log($('input.datepickerdoli#fin').val());
            var startDay = new Date(debut);
            var endDay = new Date(fin);
            var millisecondsPerDay = 1000 * 60 * 60 * 24;

            var millisBetween = startDay.getTime() - endDay.getTime();
            var days = millisBetween / millisecondsPerDay;
            if(!isNaN(Math.abs(days))) {
            // Round down.
                $('#nbrnuits').val(Math.abs(days));
            }

            hd = $("#hourstart").val();
            md = $("#minstart").val();

            hf = $("#hourend").val();
            mf = $("#minend").val();

            if (Math.abs(days) == 0 && hd == hf && md == mf) {
                $('input[type="submit"]').attr('disabled',true);
                $('#nbrnuits').css({'border':'1px solid red'});
                // $('input#fin').css({'border':'1px solid red'});
            }else{
                $('input[type="submit"]').removeAttr('disabled',false);
                // $('input#fin').css({'border':'1px solid transparent'});
                $('#nbrnuits').css({'border':'1px solid transparent'});
            }

        }else{
            $('#nbrnuits').val('');
        }
    }

    function getMntProduct(inp){
        var mnt = $(inp).find(':selected').data('mnt');
        var minmnt = $(inp).find(':selected').data('min-mnt');
        $(inp).parent('td').parent('tr').find('td.prix_un_td input').val(mnt);
        $(inp).parent('td').parent('tr').find('td.prix_un_td input').attr("min",minmnt);
    }
    function delete_row_supplement(inp){
        $(inp).parent('td').parent('tr').remove();
    }
    function empty_tr_supplement(inp){
        $('#supplementtable tr.noresult').show();
    }

    function OpenPropalPop($t){
        var id_tache = $($t).find('.id_tache').val();
        $('.hover_bkgr_fricc').show();
    }
    function OpenPropalLinkForm($t){
        $('div#link_propal_form').show();
          $("html, body").animate({ scrollTop: $(document).height() }, 1000);
    }
    function reporterDatesForm($t){
        $('div#reporterdatesdiv').show();
        $("html, body").animate({ scrollTop: 0 }, 1000);
    }
    function lier_propal_form($t, e){
        e.preventDefault();
        var reservation_id  = $($t).parent().find('.reservation_id').val();
        var client_id  = $($t).parent().find('.client_id').val();
        var propal_id  = $('.select_propal_in_popup select').val();
        var data = {
          'reservation_id': reservation_id,
          'client_id': client_id,
          'propal_id': propal_id,
          'action': "lier_propal_form"
        };
        if (propal_id > 0) {
            $.ajax( {
                type: "POST",
                url: "check.php",
                data: data,
                dataType: 'json',
                success: function(found){
                    // console.log(found);
                    // if (found) {
                    // }
                }
            });
            // $.jnotify('<?php echo trim($langs->trans("Mise à jour réussie")); ?>',
            //     "500",
            //     false,
            //     { remove: function (){} } )
            location.reload();
            // setTimeout(function () {
            //     $('.hover_bkgr_fricc').hide();
            //     ;
            // }, 500);

            // setTimeout(function () {
            // }, 20);
        }
    }
</script>