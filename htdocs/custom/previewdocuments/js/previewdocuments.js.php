<?php

	if(is_file('../../master.inc.php'))  

		require('../../master.inc.php');

	else 

		require('../../../master.inc.php');

	

	$langs->load('previewdocuments@previewdocuments');

?>



function previewdocuments_set_link() {

	$('#id-right a[href]:not(.relative_div_)').each(function() {

		

		var url = $(this).attr('href');

		var mime = $(this).attr('mime');



		if (url.indexOf('document.php?') != -1 

			&& url.indexOf('action=delete') == -1 

			&& url.indexOf('action=edit') == -1 

			&& url.indexOf('image/png') == -1 

			&& !mime)

		{

			

			 
			// url.toLowerCase().indexOf('.pdf')!=-1 || 

			if( 

			url.toLowerCase().indexOf('.docx')!=-1 || 

			url.toLowerCase().indexOf('.dotx')!=-1 || 

			url.toLowerCase().indexOf('.dotm')!=-1 || 

			url.toLowerCase().indexOf('.doc')!=-1 || 

			url.toLowerCase().indexOf('.docm')!=-1 || 

			url.toLowerCase().indexOf('.xls')!=-1 || 

			url.toLowerCase().indexOf('.xlsx')!=-1 || 

			url.toLowerCase().indexOf('.xlsb')!=-1 || 

			url.toLowerCase().indexOf('.xlsm')!=-1 ||

			url.toLowerCase().indexOf('.pptx')!=-1 || 

			url.toLowerCase().indexOf('.ppt')!=-1 ) {



				filename = $(this).text();

				if(filename == '') filename = $(this).find('img').attr('alt');



				if(filename)

				filename = filename.replace(/'/g, "\\'");



				url = url.replace("&amp;", "&");

				module = url.split('modulepart=');
				module = module[1].split('&');
				module = module[0];

				url_root = "<?php echo $powererp_main_url_root; ?>";

				url_documents = "<?php echo $powererp_main_data_root; ?>";



				big_url = url.split("file=");

				big_url = big_url[1].split("/");
				big = big_url.pop();
				big_url = big_url.toString();	
				big_url = big_url.replace(/\,/g,'/');


				var urldoc = url_documents+"/"+module+"/"+big_url;

				// if(big_url.indexOf("htdocs") != -1){

				// 	folder_documents = big_url.split("htdocs");

				// 	// console.log("folder_documents : "+folder_documents);

				// 	doc_ = folder_documents[1];

				// 	// console.log("doc_ : "+doc_);

				// 	last_url_nc = url_root+doc_;



				// }else{

				// 	root_url = url_root.split("/");

				// 	intranet = root_url[root_url.length-2];

				// 	var_ = big_url.split(intranet);

				// 	doc_ = var_[var_.length-1];

				// 	last_url_nc = url_root.replace("/htdocs", doc_);

				// }



				name_file = url.split("file=");

				name_file = name_file[1];

				name_file = url.split("/");
				name_new = name_file[name_file.length-1];

				if(name_file.indexOf('$entity=')){
					name_file = url.split("$entity=");
					name_file = name_file[0];
				}

				var extention = name_new.split(".");

				extention = extention[1];



				url = "javascript:previewdocuments_pop('"+name_new+"', '"+extention+"','"+urldoc+"')";

				// url = "javascript:previewdocuments_pop('https://view.officeapps.live.com/op/embed.aspx?src="+last_url_nc+"', '"+name_new+"', '"+last_url_nc+"','"+extention+"','"+urldoc+"')";



				if( url.toLowerCase().indexOf('.pdf')!=-1 ) {
					url = "javascript:previewdocuments_pop_pdf('"+name_new+"', '"+extention+"','"+urldoc+"')";
				} 
				// else {

				// 	url = "javascript:previewdocuments_pop('https://view.officeapps.live.com/op/embed.aspx?src="+last_url_nc+"', '"+name_new+"')";

				// }

				

				link = '&nbsp;<a class="added_nc" href="'+url+'"><?php echo img_object($langs->trans('Preview'),'previewdocuments@previewdocuments') ?></a>';

				

				$(this).after(link);

			}

			// if( url.toLowerCase().indexOf('.png')!=-1 || url.toLowerCase().indexOf('.jpg')!=-1 || url.toLowerCase().indexOf('.jpeg')!=-1 ) {

			

			// 	src_file = window.location.origin+url;

			// 	link = '&nbsp;&nbsp;<a class="added_nc lightbox_trigger_nc_" href="'+src_file+'"><?php echo img_object($langs->trans('Preview'),'previewdocuments@previewdocuments') ?></a>';

				

			// 	$(this).after(link);

			// }



		}



		$(this).addClass("relative_div_");

		$(".added_nc").addClass("relative_div_");

	});

}


function previewdocuments_pop_pdf(filename, extention=null, urldoc=null) {

	// console.log("urlfile : "+urlfile);

	$('#previewdocuments').remove();



	<?php

		// $dircustom = dol_buildpath('/previewdocuments/');

		// $customtxt = "";

		// if (!is_dir($dircustom)) {

		// 	$customtxt = $powererp_main_url_root_alt;

		// }

	?>

	// $('#previewdocuments iframe').attr('src', '<div style="text-align:center;"><?php echo dol_buildpath('/previewdocuments/img/loading.gif',2) ?></div>');

	// $('div#previewdocuments').css({"background":"url(<?php echo dol_buildpath('/previewdocuments/img/loading.gif',2) ?>) center center no-repeat"});

	var data = {

		// 'urlfile' : urlfile,

		'filename' : filename,

		'extention' : extention,

		'urldoc' : urldoc,

		'action' : "readThisFile"

	};

	$.ajax({

		type: "POST",

		url: "<?php echo dol_buildpath('/previewdocuments/check.php',2); ?>",

		data: data, 

		dataType: 'json',

		success: function(found){

			if (found != "") {

				if($('#previewdocuments').length==0) {

					$('body').append('<div id="previewdocuments"><iframe src="#" style="display:none;" width="100%" height="100%" allowfullscreen webkitallowfullscreen frameborder=0></iframe></div>');

				}

				// $('div#previewdocuments').css({"background":"url(<?php echo dol_buildpath(' /previewdocuments/img/loading.gif',2) ?>) center center no-repeat"});

				// console.log("new link : "+found);

				$('#previewdocuments').dialog({

					title: "<?php echo preg_replace( "/\r|\n/", "", $langs->trans('PreviewOf')); ?> " + filename

					,width:'80%'

					,height:600

					,modal:true

					,resizable: true

					,close:function() {

						$('#previewdocuments iframe').attr('src', '#');

					}

				});

				var result = found;

				// console.log("result :"+result);

				$('#previewdocuments iframe').attr('src', result);

				setTimeout(function() { $("#previewdocuments iframe").show(); }, 500);

			}

		}

	});



	// $('#previewdocuments').dialog({

	// 	title: "<?php echo preg_replace( "/\r|\n/", "", $langs->trans('PreviewOf')); ?> " + filename

	// 	,width:'80%'

	// 	,height:600

	// 	,modal:true

	// 	,resizable: false

	// 	,close:function() {

	// 		$('#previewdocuments iframe').attr('src', '#');

	// 	}

	// });

	

	// $('#previewdocuments iframe').attr('src', url);
}


function previewdocuments_pop(filename, extention=null, urldoc=null) {

	// console.log("urlfile : "+urlfile);

	$('#previewdocuments').remove();



	<?php

		// $dircustom = DOL_DOCUMENT_ROOT.'/previewdocuments/';

		// $customtxt = "";

		// if (!is_dir($dircustom)) {

		// 	$customtxt = $powererp_main_url_root_alt;

		// }

	?>

	// $('#previewdocuments iframe').attr('src', '<div style="text-align:center;"><?php echo DOL_MAIN_URL_ROOT.$customtxt; ?>/previewdocuments/img/loading.gif</div>');

	// $('div#previewdocuments').css({"background":"url(<?php echo DOL_MAIN_URL_ROOT.$customtxt; ?>/previewdocuments/img/loading.gif) center center no-repeat"});

	var data = {

		// 'urlfile' : urlfile,

		'filename' : filename,

		'extention' : extention,

		'urldoc' : urldoc,

		'action' : "readThisFile"

	};

	$.ajax({

		type: "POST",

		url: "<?php echo dol_buildpath('/previewdocuments/check.php',2); ?>",

		data: data, 

		success: function(found){
			console.log(found);
			if (found != "") {
				if($('#previewdocuments').length==0) {

					$('body').append('<div id="previewdocuments"><iframe src="#" style="display:none;" width="100%" height="100%" allowfullscreen webkitallowfullscreen frameborder=0></iframe></div>');

				}

				// $('div#previewdocuments').css({"background":"url(<?php echo DOL_MAIN_URL_ROOT.$customtxt; ?>/previewdocuments/img/loading.gif) center center no-repeat"});

				// console.log("new link : "+found);

				$('#previewdocuments').dialog({

					title: "<?php echo preg_replace( "/\r|\n/", "", $langs->trans('PreviewOf')); ?> " + filename

					,width:'80%'

					,height:600

					,modal:true

					,resizable: true

					,close:function() {

						$('#previewdocuments iframe').attr('src', '#');

					}

				});

				var result = 'https://view.officeapps.live.com/op/embed.aspx?src='+found;

				// console.log("result :"+result);

				$('#previewdocuments iframe').attr('src', result);

				setTimeout(function() { $("#previewdocuments iframe").show(); }, 500);

			}

		}

	});



	// $('#previewdocuments').dialog({

	// 	title: "<?php echo preg_replace( "/\r|\n/", "", $langs->trans('PreviewOf')); ?> " + filename

	// 	,width:'80%'

	// 	,height:600

	// 	,modal:true

	// 	,resizable: false

	// 	,close:function() {

	// 		$('#previewdocuments iframe').attr('src', '#');

	// 	}

	// });

	

	// $('#previewdocuments iframe').attr('src', url);

	

}





function preview_images_pop() {

	$('.lightbox_trigger_nc_').click(function(e) {

		e.preventDefault();

		var image_href = $(this).attr("href");

	    $('.lightbox_nc .content_img').html('<img src="' + image_href + '" />');

	    $('.lightbox_nc').show();

    });

}

$(window).on('load', function() {
	
	$('body').append('<div class="lightbox_nc" style="display:none;"><p>X</p><div class="content_img"><img src="" /></div></div>');

	var top_menu = ($('#tmenu_tooltip').height() + 10);

	$('.lightbox_nc p').css({"margin-top":top_menu+"px","margin-right":top_menu+"px","padding-right":"30px"});

	$('.lightbox_nc,.lightbox_nc p').click(function() {$('.lightbox_nc').hide();});

	previewdocuments_set_link();

	preview_images_pop();

});

