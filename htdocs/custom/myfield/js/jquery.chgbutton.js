jQuery(document).ready(function () {

$('a', '.tabsAction').each(function(){ 
	// Button minimiser
	var action=$( this ).attr('href').match(/action=([a-z]+)/gi)
	$( this ).attr('title', $(this).text());
	$(this).addClass('myfieldbutton');

	if (	action == 'action=reopen' 
		||	action == 'action=edit'
		|| 	action == 'action=modif'
		|| 	action == 'action=modify'
		) {
		$(this).html('<span class=\'ui-icon ui-icon-pencil\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');

		$(this).css({background:'#80FFFF'});
	};
	if (action == 'action=presend' ) {
		$(this).html('<span class=\'ui-icon ui-icon-mail-closed\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#BCBD8C'});
	};
	if (action == 'action=clone' ) {
		$(this).html('<span class=\'ui-icon ui-icon-copy\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#DDDDDD'});
	};

	if (action == 'action=reconcile' ) {
		$(this).html('<span class=\'ui-icon ui-icon-flag\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#FFB164'});
	};
	
	// specifique à factory
		if (action == 'action=importexport' ) {
			$(this).html('<span class=\'ui-icon ui-icon-transferthick-e-w\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#FFB164'});
		};

	if (action == 'action=getdefaultprice' ) {
		$(this).html('<span class=\'ui-icon ui-icon-arrowthickstop-1-s\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#BF80FF'});
	};
	if (action == 'action=adjustprice' ) {
		$(this).html('<span class=\'ui-icon ui-icon-calculator\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#80FF80'});
	};

	if ($( this ).attr('href').match(/shipment.php/)=='shipment.php') {
		$(this).html('<span class=\'ui-icon ui-icon-tag\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#FFFF80'});
	};

	// pour les désactiver
	if ($( this ).attr('href')=='#' && $( this ).attr('id') == null ) {
		$(this).html('<span class=\'ui-icon ui-icon-circle-close\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#FFF'});
	};

	
	if (action == 'action=create' ) {
		if ($( this ).attr('href').match(/paiement.php/)=='paiement.php') {
			$(this).html('<span class=\'ui-icon ui-icon-star\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#FFFF80'});
		} else if ($( this ).attr('href').match(/comm\/propal/)=='comm/propal') {
			$(this).html('<span class=\'ui-icon ui-icon-calculator\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#80FF80'});
		} else if ($( this ).attr('href').match(/fourn\/facture/)=='fourn/facture') {
			$(this).html('<span class=\'ui-icon ui-icon-suitcase\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#76BABA'});
		} else if ($( this ).attr('href').match(/compta\/facture/)=='compta/facture') {
			$(this).html('<span class=\'ui-icon ui-icon-suitcase\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#76BABA'});
		} else if ($( this ).attr('href').match(/commande\/card.php/)=='commande/card.php') {
			$(this).html('<span class=\'ui-icon ui-icon-heart\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#53FF7E'});
		} else if ($( this ).attr('href').match(/fichinter\/card.php/)=='fichinter/card.php') {
			$(this).html('<span class=\'ui-icon ui-icon-wrench\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#FF48FF'});

		} else if ($( this ).attr('href').match(/contrat\/card.php/)=='contrat/card.php') {
			$(this).html('<span class=\'ui-icon ui-icon-script\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#F5CA89'});
		} else if ($( this ).attr('href').match(/contact\/card.php/)=='contact/card.php') {
			$(this).html('<span class=\'ui-icon ui-icon-person\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#F5CA89'});

		} else {
			$(this).html('<span class=\'ui-icon ui-icon-document\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
			$(this).css({background:'#DDDDDD'});
		};
	};
	if (	action == 'action=close' 
		||	action == 'action=cancel' 
		) {
		$(this).html('<span class=\'ui-icon ui-icon-closethick\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#80FF80'});
	};

	if (	action == 'action=classifyunbilled' 
		||	action == 'action=brouillonner' ) {
		$(this).html('<span class=\'ui-icon ui-icon-arrowreturnthick-1-w\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#BF80FF'});
	};


	if (	action == 'action=statut' 
		||	action == 'action=classifyclosed' 
		||	action == 'action=classifybilled'
		||	action == 'action=classifydone' 
		||	action == 'action=sendToValidate'
		||	action == 'action=valid'
		||	action == 'action=paid'
		||	action == 'action=set_paid'
		||	action == 'action=validate'
		||	action == 'action=save'

	) {
		$(this).html('<span class=\'ui-icon ui-icon-check\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#80FF80'});
	};
	if (action == 'action=merge' ) {
		$(this).html('<span class=\'ui-icon ui-icon-clipboard\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#DDDDDD'});
	};
	if (action == 'action=delete' ) {
		$(this).html('<span class=\'ui-icon ui-icon-trash\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#FF7373'});
	};
	if (	action == 'action=shipped' 
		||	action == 'action=sendbroadcastlist'
	) {
		$(this).html('<span class=\'ui-icon ui-icon-cart\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#80FF80'});
	};


	if (	action == 'action=canceled' 
		||	action == 'action=disable'
		||	action == 'action=refuse'
	) {
		$(this).html('<span class=\'ui-icon ui-icon-cancel\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
		$(this).css({background:'#DDDDDD'});
	};
});

$('#action-clone', '.tabsAction').each(function(){ 
	if ($(this).text() != "")
		$(this).attr('title', $(this).text());
	$(this).addClass('myfieldbutton');
	$(this).html('<span class=\'ui-icon ui-icon-copy\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
	$(this).css({background:'#FF7373'});
});


$('#action-delete', '.tabsAction').each(function(){ 
	$(this).attr('title', $(this).text());
	$(this).addClass('myfieldbutton');
	$(this).html('<span class=\'ui-icon ui-icon-trash\'></span><div style=\'display:none;\'>'+$(this).text()+'</div>');
	$(this).css({background:'#FF7373'});

});

$('.myfieldbutton').mouseover(function() {
	// pour mettre un petit délais avant l'expension du bouton
	$a = $(this).find('span');
	$b = $(this).find('div');
	$a.hide();
	$b.show();
});
$('.myfieldbutton').mouseout(function() {
	$a = $(this).find('span');
	$b = $(this).find('div');
	$a.show();
	$b.hide();
});


})