
/* =================================================== *\
					JAVASCRIPT GENERAL
\* =================================================== */


function _(elm){
	return document.getElementById(elm);
}

function _name(elm){
    return document.getElementsByName(elm);
}
function _$(elm){
    var elms = document.getElementsByName(elm);
    if (elms.length >= 1){
        return document.getElementsByName(elm)[0];
    }else{
        return undefined;
    }
}

function createXmlHttpRequestObject(){
	var xmlHttp;
	try{
		xmlHttp = new XMLHttpRequest();
	}
	catch (e){
		try{
			xmlHttp = new ActiveXObject(Microsoft.XMLHTTP);
		}catch (e){ alert(e);}
	}
	if (!xmlHttp) {
		alert('erreur JavaScript sur AJAX!');
	}else{
		return xmlHttp;
	}
}

/**
 * permet d'avoir dans un JSON les 
 * @param  {[type]} typeof navigator_infos ! [description]
 * @return {[type]}        [description]
 */
if(typeof navigator_infos !=='function'){
        function navigator_infos(){
            infos = {
                'name': '',
                'version': '',
                'user' : ''
            };
            var agent = navigator.userAgent;
            var idx_rev = agent.indexOf('rv:');
            var idx_DotNet = agent.indexOf('.NET');
            var idx_opera = agent.indexOf('OPR/');
            var idx_firefox = agent.indexOf('Firefox/');
            var idx_go = agent.indexOf('Chrome/');
            var idx_saf = agent.indexOf('Safari/');
            var nav = idx_opera >= 0 ? 'opera' : (idx_DotNet >= 0 ? 'ie' : (idx_firefox >= 0 ? 'firefox' : (idx_go >= 0 ? 'chrome' : (idx_saf >= 0 ? 'safari' : ('other')))));
            var ver = idx_rev >= 0 ? agent.substring(idx_rev + 3, idx_rev + 7) : (idx_opera >= 0 ? agent.substring(idx_opera + 4, idx_opera + 8) : (idx_go >= 0 ? agent.substring(idx_go + 7, idx_go + 11) : (idx_saf >= 0 ? agent.substring(idx_saf + 7, idx_saf + 11) : ('0'))));
            ver = parseFloat(ver);
            infos.name = nav;
            infos.version = ver;
            infos.user = navigator.userAgent;
            return infos;
        }
}



/**
 * Permet d'ajouter de nouvelles methodes à tous les objet string'
*/

function add_HTML_Methods(){
    // le trim() sur les string
    if(typeof String.prototype.trim !== 'function') {
        String.prototype.trim = function() {
          return this.replace(/^\s+|\s+$/g, '');
        }
    }
    // verification de type numeric
    if(typeof String.prototype.isNumeric !== 'function') {
        String.prototype.isNumeric = function() {
            var elm = this;
            if (typeof(elm) == 'string'){
                if (parseInt(elm) > 0){
                    return true;
                }else{
                    if (parseInt(elm) == 0 && elm == '0') {
                        return true;
                    }else{return false;}
                }
            }else{
                if (elm != NaN ) {
                    return true;
                }else{return false;}
            }
        }
    }
    // verification de type Date
    if(typeof String.prototype.isDate !== 'function') {
        String.prototype.isDate = function() {
            var elm = this.trim();
            elm = elm.split(' ');
            elm = elm.join('');
            if (elm.length != 10){
                return false;
            }else{
                if ((elm.indexOf('/') == 2 && elm.lastIndexOf('/') == 5) || (elm.indexOf('/') == 4 && elm.lastIndexOf('/') == 7)){
                    
                    var Tdate = new Array();
                    Tdate = elm.split('/');
                    if ((Tdate[0].length == 2 && Tdate[1].length == 2 && Tdate[2].length == 4) || (Tdate[0].length == 4 && Tdate[1].length == 2 && Tdate[2].length == 2)){
                        if (Tdate[0].isNumeric() && Tdate[1].isNumeric() && Tdate[2].isNumeric()){
                            return true;
                        }else{return false;}
                    }else{
                        
                        return false;
                    }
                }else{ 
                    if ((elm.indexOf('-') == 2 && elm.lastIndexOf('-') == 5) || (elm.indexOf('-') == 4 && elm.lastIndexOf('-') == 7)){
                        var Tdate = new Array();
                        Tdate = elm.split('-');
                        if ((Tdate[0].length == 2 && Tdate[1].length == 2 && Tdate[2].length == 4) || (Tdate[0].length == 4 && Tdate[1].length == 2 && Tdate[2].length == 2)){
                            if (Tdate[0].isNumeric() && Tdate[1].isNumeric() && Tdate[2].isNumeric()){
                            return true;
                            }else{return false;}
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }
            }
        }
    }

    // verification de type numeric
    if(typeof String.prototype.isEmail !== 'function') {
        String.prototype.isEmail = function() {
            var elm = this;
            var taille = elm.length;
            var arrobase = elm.lastIndexOf('@');
            var point = elm.lastIndexOf('.');
            var space = elm.lastIndexOf(' ');
            if (taille >= 5 && space < 0) { /* au moins 1 lettre, 1 @, 1 lettre, 1 point, et 1 lettre*/
                if ((point >= 3 && point < taille - 1)) { /* le point est situé au moins en position 0-3 du mail minimal*/
                    if ((arrobase > 0) && (arrobase < point - 1)) { /* verifier que @ est au moins séparé du point et vient avant lui*/
                        var domaine = elm.split('@')[1];
                        var ismail = true;
                        var i = domaine.length-1;
                        do{
                            if (domaine[i] == '.' && domaine[i - 1] == '.') {
                                ismail = false;
                                //console.log('"' + domaine + '" n\' est pas un nom de domaine');
                            }
                            i--;
                        }while(ismail == true && i >= 0);
                        return ismail;
                    }else{return false;}
                }else{return false;}
            }else{return false;}
        }
    }
    /**
     *  Je rajoute une methode strCut() à String pour intercaller un motif dans une chaine
     */
    if(typeof String.prototype.str_cut !== 'function') {
        String.prototype.str_cut = function(usr_interval,cutter,usr_sens) {
            var chaine = this;
            var taille = chaine.length;
            var char = ' ';
            var sens = 'from-right';
            var interval = 3;
            if (typeof(cutter) !== 'undefined') {
                char = cutter;
            }
            if (typeof(usr_sens) !== 'undefined') {
                sens = usr_sens;
            }
            if (!isNaN(usr_interval)) {
                interval = parseInt(usr_interval,10);
            }

            if(chaine.length > interval){
                var res = '';
                var taille = chaine.length;
                var parcour = 0;
                if(sens == 'from-right'){
                    var i = taille - 1;
                    do{
                    if ((i-2) > 0) {
                        res = chaine.substr(i-2,3) + char +res;
                        i = i-3;
                        }else{
                            res = chaine.substr(0,i+1) + char + res;
                            i=-1;
                        }
                    }while(i >= 0);
                }else if(sens == 'from-left'){
                    i = 0;
                    do{
                        if ((i+2) < taille - 1) {
                            res = res + char + chaine.substr(i,3);
                            i = i+3;
                        }else{
                            rest = taille -i;
                            res = res + char + chaine.substr(i,rest);
                            i=0;
                        }
                    }while(i > 0);
                }

                return res.trim();
            }else{
                return chaine;
            }


        }//fin de la methode strCut
    }

    if(typeof String.prototype.number_to_FCFA !== 'function') {
        String.prototype.number_to_FCFA = function() {
            /**
             * le nombre à tansformer doit provenir d'un format Anglais
             */
            var result = this;
            result = result.replace(',','');
            result = result.replace(' ','');
            var chaine = result;
            if(chaine.length >=3){ // un chiffre de chaque coté et un point
            
                if (chaine.indexOf('.') != -1) {
                    result = chaine.split('.');
                    var nb_pat = result.length;
                    var tmp = '';
                    if (nb_pat == 2) {
                        tmp = result[0].str_cut(3);
                        if (parseInt(result[1]) > 0) {
                            tmp +=',' + result[1].str_cut(3,' ','from-left');
                        }
                    }
                    
                    result = tmp;
                }else{
                    result = result.str_cut(3);
                }
    
            }

            return result;

        }//fin de la methode strCut
    }
}
add_HTML_Methods();


/**
 * Permet de supprimer un élément du Tableau lorsqu'on connaît son index dans le tableau
 * @param tableau tableau indexé dans lequel a lieu la suppression
 * @param index Numéro de la case qui doit être supprimée
*/
function unsetByIndex(tableau,index){
    var output = new Array();
    if (tableau != undefined && tableau === Array && index !=undefined && index === Number) {
        var idx = parseInt(index,10);
        if (idx != NaN){
            for(var i=0; i < tableau.length; i++){
                if (i != idx) {
                    output.push(tableau[i]);
                }
            } 
        } else {
            console.log('unsetByIndex(), vous avez tenté d\'utiliser un NaN au lieu d\'un entier.');
        }
    } else {
        console.log('unsetByIndex(), vous avez transmis de mauvais paramètres.');
    }
    return output;
}


/**
 * Permet de supprimer un élément du Tableau lorsqu'on connaît son index dans le tableau
 * @param tableau tableau dans lequel a lieu la suppression
 * @param Valeur La valeur ou le contenu qui doit être supprimé(e)
 * @param CS True/False (optionel) : Precise si la function est sensible à la casse pour les valeurs de type chaine("False" par défaut).
*/
function unsetByValue(tableau,Valeur,CS){
    var output = [];
    
    if (tableau != undefined && (typeof(tableau) =='object') && Valeur != undefined) {
       
        if (CS != undefined && typeof(CS) != 'function' && CS == true && typeof (Valeur) == 'string') {
        
            for(var i=0; i <= tableau.length - 1 ; i++){
                if (tableau[i] === Valeur && tableau[i] == Valeur) {
                   //rien
                   
                } else {
                    output.push(tableau[i]);
                }
            } 

        }
        else if (CS == undefined || typeof(CS) == 'function' || CS == false && typeof(Valeur) == 'string') {
           
            var str = Valeur.toLowerCase();
            for (i = 0; i <= tableau.length - 1; i++){
                if (tableau[i].toLowerCase() === str && tableau[i].toLowerCase() == str) {
                    //rien
                    
                } else {
                    output.push(tableau[i]);
                }
            }
        }
        else if (Valeur != undefined && typeof(Valeur) != 'string') {
            for(var i=0; i <= tableau.length - 1 ; i++){
                if (tableau[i] === Valeur && tableau[i] == Valeur) {
                    //rien
                } else {
                    output.push(tableau[i]);
                }
            } 
        } else {
            console.log('unsetByValue(), Type de données non pris en charge.');
        }
        
    } else {
        console.log('unsetByValue(), les valeurs minimales en paramettre doivent être un tableau et un texte.');
    }
    return output;
}



/**
 * Permet d'ajouter une nouvelle classe à un objet avec JavaScript
 * @param elm Identifiant de l'élément qui doit recevoir la classe
 * @param new_class La classe qu'on veut ajouter
*/
function ajouter_classe(elm,new_class) {
    var str = new String;
    var list_class = new Array();
    var tab_elm = new Array; 
    var one_objet = '';
    if (typeof(elm) == 'string') {
        if (elm[0] != "#" && elm[0] != ".") {
            // non queryselector
            tab_elm = document.querySelectorAll("#" + elm);
        }else if(elm[0] == "#" || elm[0]== "."){
        // c'est un querySelector
            tab_elm = document.querySelectorAll(elm);
        }
        
    }else if(typeof(elm) == 'object'){
        one_objet = elm;
    }
    if (tab_elm.length > 0) {
        for (n=0; n<tab_elm.length; n++){
            str = tab_elm[n].getAttribute('class');
            list_class = str.split(' ');
            for (i = 0; i <= list_class.length - 1; i++){
                list_class[i] = list_class[i].trim();
            }
            list_class.push(new_class);
            //console.log('Avant nous avions ' + str);
            tab_elm[n].setAttribute('class', list_class.join(' '));
            //console.log('Maintenant nous avons ' + tableau.join(' '));
        }
    }else{
        if (one_objet != '') {
            str = one_objet.className;
            list_class = str.split(' ');
            for (i = 0; i <= list_class.length - 1; i++){
                list_class[i] = list_class[i].trim();
            }
            list_class.push(new_class);
            //console.log('Avant nous avions ' + str);
            one_objet.setAttribute('class', list_class.join(' '));
            //console.log('Maintenant nous avons ' + tableau.join(' '));

        }else{
            return false;
        }
    }
    

}

/**
 * Permet de retirer une classe à un objet avec JavaScript
 * @param elm Identifiant de l'élément qui doit recevoir la classe
 * @param new_class La classe qu'on veut ajouter
*/
function enlever_classe(elm,new_class) {
    var str = new String;
    var list_class = new Array();

    var tab_elm = new Array; 
    var one_objet = '';
    if (typeof(elm) == 'string') {
        if (elm[0] != "#" && elm[0]!= ".") {
            // non queryselector
            tab_elm = document.querySelectorAll("#" + elm);
        }else if(elm[0] == "#" || elm[0]== "."){
            // c'est un querySelector
            tab_elm = document.querySelectorAll(elm);
        }
        
    }else if(typeof(elm) == 'object'){
        one_objet = elm;
    }

    if (tab_elm.length > 0) {
        for (n=0; n<tab_elm.length; n++){
            str = tab_elm[n].getAttribute('class');
            list_class = str.split(' ');
            for (i = 0; i <= list_class.length - 1; i++){
                list_class[i] = list_class[i].trim();
            }
            var tableau = unsetByValue(list_class, new_class,true);
            //console.log('Avant nous avions ' + str);
            tab_elm[n].setAttribute('class', tableau.join(' '));
            //console.log('Maintenant nous avons ' + tableau.join(' '));
        }
    }else{
        if (one_objet != '') {
            str = one_objet.className;
            list_class = str.split(' ');
            for (i = 0; i <= list_class.length - 1; i++){
                list_class[i] = list_class[i].trim();
            }
            var tableau = unsetByValue(list_class, new_class,true);
            //console.log('Avant nous avions ' + str);
            one_objet.setAttribute('class', tableau.join(' '));
            //console.log('Maintenant nous avons ' + tableau.join(' '));

        }else{
            return false;
        }
    }
    
}



/**
 * Permet de retirer une classe à un objet avec JavaScript
 * @param elm Identifiant de l'élément qui doit recevoir la classe
 * @param new_class La classe qu'on veut ajouter
*/
function elm_HasClasse(elm, str) {
    var tab_elm = new Array();
    var tmp_str = str;
    if (str[0] == "#"){
        tmp_str = str.split('#')[1].trim();
    }else if (str[0] == "."){
        tmp_str = str.split('.')[1].trim();
    }

    var finded = false;

    var one_objet = '';
    if (typeof(elm) == 'string') {
        if (elm[0] != "#" && elm[0]!= ".") {
            // non queryselector
            tab_elm = document.querySelectorAll("#" + elm);
        }else if(elm[0] == "#" || elm[0]== "."){
            // c'est un querySelector
            tab_elm = document.querySelectorAll(elm);
        }
        
    }else if(typeof(elm) == 'object'){
        one_objet = elm;
    }
    

    if (tab_elm.length > 0) {
        //il exite bien un élément portant ce sélecteur
        var i = 0;
        do{
            var elm_clss = new Array();
            elm_clss = tab_elm[i].getAttribute('class').split(' ');
            var n=0;
            do{
                if (elm_clss[n].trim() == tmp_str) {
                    finded = true;
                }
                n++;
            }while(n<elm_clss.length && finded == false);
            
            i++;

        }while(i<tab_elm.length && finded == false);
        if (finded == true) {
            return true;
        }else{return false;}

    }else{
        if (one_objet != '') {
            elm_clss = one_objet.className.split(' ');
            var n=0;
            do{
                if (elm_clss[n].trim() == tmp_str) {
                    finded = true;
                }
                n++;
            }while(n<elm_clss.length && finded == false);
            if (finded == true) {
                return true;
            }else{return false;}

        }else{
            return false;
        }
    }

    
}








var scrollNiv = 0;
(function () {
    //ajouter_classe("header","Active");
    window.onscroll = function() {
        var header = _('header-header');
        scrollNiv = document.body.scrollTop || document.documentElement.scrollTop;
        if (scrollNiv > 100 && !elm_HasClasse(header, 'scrolled')) {
            ajouter_classe(header, "scrolled");
            ajouter_classe("scroller","scrolled");
        }else if(scrollNiv < 100 && elm_HasClasse(header, 'scrolled')){
            enlever_classe(header, "scrolled");
            enlever_classe("scroller","scrolled");
        }
        
     };
})();
function resetScroll(){
    var time = scrollNiv * 3;
    var scrl = scrollNiv;
    
    
    if (scrl > 0 && scrl < 300) {
        scrl = scrl - 25;
        window.scrollTo(0, scrl);
    }else{
        if (scrl > 300 && scrl < 900) {
            scrl = scrl - 50;
            window.scrollTo(0, scrl);
        }else{
            if (scrl > 900 && scrl < 1800) {
                scrl = scrl - 200;
                window.scrollTo(0, scrl);
            }else{
                if (scrl > 1800 && scrl < 3600) {
                    scrl = scrl - 400;
                    window.scrollTo(0, scrl);
                }else{
                    if (scrl > 3600) {
                        scrl = scrl - 600;
                        window.scrollTo(0, scrl);
                    }
                }
            }
        }
    }

    scrollNiv = scrl;
    if (scrl > 0) {
        setTimeout(resetScroll,1);
    }
}


function closeModal(){
    if (elm_HasClasse(_('modal_windows'),'visible')) {
        enlever_classe(_('modal_windows'),'visible');
    }
}

function appercu_fichier(elm,appercu){

    if (elm_HasClasse('.progress_loader','begin')) {
        enlever_classe('.progress_loader','begin');
    }
    if (elm_HasClasse('.progress_loader','ended')) {
        enlever_classe('.progress_loader','ended');
    }
    ajouter_classe('.progress_loader','loading');
    var fichier = '';
    var reader = new FileReader();
    reader.onload =  function(){
        fichier = reader.result;
        _(appercu).setAttribute('src',fichier);
        if (elm_HasClasse('.progress_loader','loading')) {
                enlever_classe('.progress_loader','loading');
                ajouter_classe('.progress_loader','ended');
            }
        };
    reader.readAsDataURL(_(elm).files[0]);
}


/*=================================================*/
var xhr_new_student = createXmlHttpRequestObject();
function new_student(event,id){
    id_st = '';
    if(typeof(id) === 'string'){
        id_st = id;
    }
    
    if(typeof(event)=='object'){
        event.preventDefault();
    }
    if (!elm_HasClasse(_('modal_windows'),'visible')){
        ajouter_classe(_('modal_windows'),'visible');
        _('modal_head_title').innerHTML = '';
        _('modal_content_body').innerHTML = '<span class="cycle_loader visible"></span>';
    }
    xhr_new_student.open('POST','inc/ajax/ajax_new_student.php');
    xhr_new_student.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr_new_student.send('client_type=ajax_bot&command=new_student&id=' + id_st);
    console.log(id_st);

    xhr_new_student.onreadystatechange = function(){
        if (xhr_new_student.readyState == 4 && xhr_new_student.status == 200){
        var reponse = xhr_new_student.responseText;
        try {
            // statements
            OBJ = JSON.parse(reponse);
            if(OBJ.status === "success"){
                enlever_classe('#modal_content_body span.cycle_loader','visible');
                setTimeout(function(){
                    _('modal_head_title').innerHTML = OBJ.title;
                    _('modal_content_body').innerHTML = OBJ.message;
                },5);
            }else{
                _('modal_head_title').innerHTML = OBJ.title;
                _('modal_content_body').innerHTML = OBJ.message;
            }
        } catch(e) {
            // statements
            console.log(e);
            enlever_classe('#modal_windows span.cycle_loader','visible');

            _('modal_head_title').innerHTML = 'Probleme survenu';
            _('modal_content_body').innerHTML = reponse;
            console.log(reponse);
        }
    };
}}






/*=================================================*/
var xhr_new_employee = createXmlHttpRequestObject();
function new_employee(event,id){
    id_p = '';
    if(typeof(id) === 'string'){
        id_p = id;
    }
    event.preventDefault();
    if (!elm_HasClasse(_('modal_windows'),'visible')){
        ajouter_classe(_('modal_windows'),'visible');
        _('modal_head_title').innerHTML = '';
        _('modal_content_body').innerHTML = '<span class="cycle_loader visible"></span>';
    }
    xhr_new_employee.open('POST','inc/ajax/ajax_management.php');
    xhr_new_employee.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr_new_employee.send('client_type=ajax_bot&command=new_employee&id=' + id_p);

    xhr_new_employee.onreadystatechange = function(){
        if (xhr_new_employee.readyState == 4 && xhr_new_employee.status == 200){
        var reponse = xhr_new_employee.responseText;
        try {
            // statements
            OBJ = JSON.parse(reponse);
            if(OBJ.status === "success"){
                enlever_classe('#modal_content_body span.cycle_loader','visible');
                setTimeout(function(){
                    _('modal_head_title').innerHTML = OBJ.title;
                    _('modal_content_body').innerHTML = OBJ.message;
                },5);
            }else{
                _('modal_head_title').innerHTML = OBJ.title;
                _('modal_content_body').innerHTML = OBJ.message;
            }
        } catch(e) {
            // statements
            console.log(e);
            enlever_classe('#modal_windows span.cycle_loader','visible');

            _('modal_head_title').innerHTML = 'Probleme survenu';
            _('modal_content_body').innerHTML = reponse;
            console.log(reponse);
        }
    };
}}



//=========================================================================

function lancer_cropperJS(event,input_OBJ,appercu,btnsave_Id,callback){
    event.preventDefault();
    var ratio = 1;
    if(typeof(callback) !== 'undefined' && callback == 'personnel'){
        ratio = 1;
    }

    // 2°) Pendant cet écoute, on récupere le fichier[0] de l'input
    var file = input_OBJ.files[0];
    // 3°) je charge l'appercu du fichier dans l'image
    var image = _(appercu);
    image.setAttribute('src', window.URL.createObjectURL(file));
    
    // 4°) je cree un objet crooperJS qui pointe ver l'appercu
    var cropperJS = new Cropper(image,{
        aspectRatio: ratio
    });
    // 5°) je lance la fenetre modale
    cropperJS.crop();
    
    //le probleme se pose au niveau du button de sauvegarde 
    _(btnsave_Id).addEventListener("click", function(){

        cropperJS.getCroppedCanvas().toBlob(function(blob){
            if(typeof(callback) !== 'undefined' && callback == 'personnel'){
                save_personnel(blob,'eventClick');
            }
            else if(typeof(callback) !== 'undefined' && callback == 'student'){
                save_student(blob,'eventClick');
            }
        });
    });
}



var ajax_save_personnel = createXmlHttpRequestObject();
var personnel_isrunning = false;
function save_personnel(){

    if (!elm_HasClasse(_('btn_save_personne_loader'),'visible')) {
        ajouter_classe(_('btn_save_personne_loader'),'visible');
    }
    
    var errors = '';
    var list_args = [];
    var images = [];
    var has_blob = false;
    var no_args = true;
    var empty_input = _('add_cover').files.length > 0 ? false : true;
    no_args = save_personnel.arguments.length > 0 ? false : true;
    save_personnel.arguments.length >=1 ? list_args = save_personnel.arguments : '';//console.log('aucun argument n\'a été fourni pour save_personnel()');
    //e,formulaire,fichier
    for (var i = list_args.length - 1; i >= 0; i--) {
        if (typeof(list_args[i]) === 'object') {
            if (list_args[i].type === "image/png") {
                images.push(list_args[i]);
                has_blob = true;
            }
            
        }
    }
  

    var form = new FormData();
    form.append('client_type','ajax_bot');
    form.append('command','save_personnel');
    form.append('id_personnel',_$('id_personnel').value.trim());
    form.append('id_ville',_$('id_ville').value.trim());
    form.append('code_sex',_$('code_sex').value.trim());
    form.append('code_matrim',_$('code_matrim').value.trim());
    form.append('mat_personnel',_$('mat_personnel').value.trim());
    form.append('nom_personnel',_$('nom_personnel').value.trim());
    form.append('prenom_personnel',_$('prenom_personnel').value.trim());
    form.append('tel1',_$('tel1').value.trim());
    form.append('tel2',_$('tel2').value.trim());

    if (_$('date_naiss').value.trim() !=='' && _$('date_naiss').value.trim().isDate()) {
        form.append('date_naiss',_$('date_naiss').value.trim());
        
    }else{
        errors += _$('date_naiss').value.trim() + 'Your Date input is not good!<br/>';
        //console.log(errors);
    }
    if (_$('email1').value.trim() ==='' || _$('email1').value.trim().isEmail()) {
        form.append('email1',_$('email1').value.trim());
        
    }else{
        errors += _$('email1').value.trim() + ' is not an email!<br/>';
        //console.log(errors);
    }
    if (_$('email2').value.trim() ==='' || _$('email2').value.trim().isEmail()) {
        form.append('email2',_$('email2').value.trim());
        
    }else{
        errors += _$('email2').value.trim() + ' is not an email!<br/>';
        //console.log(errors);
    }

    // n'envoyer qu'en cas de non-erreur
    if(errors == ""){
        // entrée simple et rien en cours
        if (no_args == true && personnel_isrunning == false && empty_input == true) {
            personnel_isrunning = true;
            ajax_save_personnel.open('POST','./inc/ajax/ajax_personnel.php');
            ajax_save_personnel.send(form);
    
        }else if(no_args == false && personnel_isrunning == false){
            personnel_isrunning = true;
            // je définie la variable form.cover_img uniquement si j'ai recu un fichier
            if (has_blob) {
                form.append('cover_img',images[0]);
            }
            ajax_save_personnel.open('POST','./inc/ajax/ajax_personnel.php');
            ajax_save_personnel.send(form);
        }
    }else{
        enlever_classe(_('btn_save_personne_loader'),'visible');
        _('div_error').innerHTML = '<pre class="color-red"><strong>Error:</strong> ' + errors + '</pre>';
        resetScroll();
        _('form_edit_personnel').parentNode.parentNode.scrollTo(0, 0);
        
    }

    

    ajax_save_personnel.onreadystatechange = function(){
        if (ajax_save_personnel.readyState == 4 && ajax_save_personnel.status == 200){
            var reponse = ajax_save_personnel.responseText;
            var OBJ = {};
            try {
                // statements
                OBJ = JSON.parse(reponse);
                //console.log(OBJ.count);
                if(OBJ.status === "success"){
                    setTimeout(function(){
                        enlever_classe(_('btn_save_personne_loader'),'visible');
                        _('div_error').innerHTML = OBJ.message;
                        resetScroll();
                        _('form_edit_personnel').parentNode.parentNode.scrollTo(0, 0);
                    },500);
                    setTimeout(function(){
                        document.location = 'http://localhost/dovas-link/?page=management&subpage=employees';
                    },1500);

                }else{
                    enlever_classe(_('btn_save_personne_loader'),'visible');
                    _('div_error').innerHTML = OBJ.message;
                    resetScroll();
                    _('form_edit_personnel').parentNode.parentNode.scrollTo(0, 0);
                }
            } catch(e) {
                // statements
                console.log(e);
                console.log(reponse);
                //_('div_error').innerHTML = reponse;
                enlever_classe(_('btn_save_personne_loader'),'visible');
            }
            
            
            personnel_isrunning = false;
            ajax_save_personnel = createXmlHttpRequestObject();
        }
    };

}




var ajax_save_parent = createXmlHttpRequestObject();
function save_parent(){

    if (!elm_HasClasse(_('btn_save_parent_loader'),'visible')) {
        ajouter_classe(_('btn_save_parent_loader'),'visible');
    }
    
    var errors = '';

    var form = new FormData();
    form.append('client_type','ajax_bot');
    form.append('command','save_parent');
    form.append('id_parent',_$('id_parent').value.trim());
    form.append('nom_parent',_$('nom_parent').value.trim());
    form.append('prenom_parent',_$('prenom_parent').value.trim());
    form.append('profession_parent',_$('profession_parent').value.trim());
    form.append('code_sex_parent',_$('code_sex_parent').value.trim());
    form.append('id_ville',_$('id_ville_parent').value.trim());
    form.append('code_phcd',_$('code_phcd_parent').value.trim());
    form.append('adresse_parent',_$('adresse_parent').value.trim());
    form.append('tel1',_$('tel1').value.trim());
    form.append('tel2',_$('tel2').value.trim());

    if (_$('date_naiss_parent').value.trim() !=='' && _$('date_naiss_parent').value.trim().isDate()) {
        form.append('date_naiss',_$('date_naiss_parent').value.trim());
        
    }else{
        form.append('date_naiss','1999-01-01');
        //console.log(errors);
    }
    if (_$('email1').value.trim() ==='' || _$('email1').value.trim().isEmail()) {
        form.append('email1',_$('email1').value.trim());
        
    }else{
        errors += _$('email1').value.trim() + ' is not an email!<br/>';
        //console.log(errors);
    }
    if (_$('email2').value.trim() ==='' || _$('email2').value.trim().isEmail()) {
        form.append('email2',_$('email2').value.trim());
        
    }else{
        errors += _$('email2').value.trim() + ' is not an email!<br/>';
        //console.log(errors);
    }
    if (_$('nom_parent').value.trim() == '' && _$('prenom_parent').value.trim() == '') {
        errors += 'the firstname and the lastname can\'t be empty at the same time!<br/>';
        
    }

    // n'envoyer qu'en cas de non-erreur
    if(errors == ""){

        ajax_save_parent.open('POST','./inc/ajax/ajax_parent.php');
        ajax_save_parent.send(form);
    }else{
        enlever_classe(_('btn_save_parent_loader'),'visible');
        _('div_error_parent').innerHTML = '<pre class="color-red"><strong>Error:</strong> ' + errors + '</pre>';
        resetScroll();
        _('slides_new_student').parentNode.parentNode.scrollTo(0, 0);
        
    }

    

    ajax_save_parent.onreadystatechange = function(){
        if (ajax_save_parent.readyState == 4 && ajax_save_parent.status == 200){
            var reponse = ajax_save_parent.responseText;
            var OBJ = {};
            try {
                // statements
                OBJ = JSON.parse(reponse);
                //console.log(OBJ.count);
                if(OBJ.status === "success"){

                    enlever_classe(_('btn_save_parent_loader'),'visible');
                    resetScroll();
                    _('slides_new_student').parentNode.parentNode.scrollTo(0, 0);
                    if(typeof(_$('id_mother') == 'object')){
                        var parent = OBJ.message.split('|');
                        var opt = document.createElement('option');
                        opt.setAttribute('value', parent[0]);
                        opt.innerText = parent[1];
                        var opt2 = opt.cloneNode(true);
                        var opt3 = opt.cloneNode(true);
                        _$('id_father').appendChild(opt);
                        _$('id_mother').appendChild(opt2);
                        _$('id_tutor').appendChild(opt3);
                        _('div_error_parent').innerHTML = parent[2];
                        setTimeout(function(){
                            slide_form_promote(event,'slides_new_student');
                        },1500);
                    }


                }else{
                    enlever_classe(_('btn_save_parent_loader'),'visible');
                    _('div_error_parent').innerHTML = OBJ.message;
                    resetScroll();
                    _('slides_new_student').parentNode.parentNode.scrollTo(0, 0);
                }
            } catch(e) {
                // statements
                console.log(e);
                console.log(reponse);
                //_('div_error_parent').innerHTML = reponse;
                enlever_classe(_('btn_save_personne_loader'),'visible');
            }
            
            
            personnel_isrunning = false;
            ajax_save_parent = createXmlHttpRequestObject();
        }
    };

}





var ajax_save_student = createXmlHttpRequestObject();
var student_isrunning = false;
function save_student(){

    if (!elm_HasClasse(_('btn_save_student_loader'),'visible')) {
        ajouter_classe(_('btn_save_student_loader'),'visible');
    }
    
    var errors = '';
    var list_args = [];
    var images = [];
    var has_blob = false;
    var no_args = true;
    var empty_input = _('add_cover').files.length > 0 ? false : true;
    no_args = save_student.arguments.length > 0 ? false : true;
    save_student.arguments.length >=1 ? list_args = save_student.arguments : '';//console.log('aucun argument n\'a été fourni pour save_personnel()');
    //e,formulaire,fichier
    for (var i = list_args.length - 1; i >= 0; i--) {
        if (typeof(list_args[i]) === 'object') {
            if (list_args[i].type === "image/png") {
                images.push(list_args[i]);
                has_blob = true;
            }
            
        }
    }
  

    var form = new FormData();
    form.append('client_type','ajax_bot');
    form.append('command','save_student');
    form.append('id_student',_$('id_student').value.trim());
    form.append('nom_student',_$('nom_student').value.trim());
    form.append('prenom_student',_$('prenom_student').value.trim());
    form.append('code_sex',_$('code_sex').value.trim());
    form.append('id_ville',_$('id_ville').value.trim());
    form.append('code_phcd',_$('code_phcd').value.trim());
    form.append('adresse_student',_$('adresse_student').value.trim());

    if (_$('date_naiss').value.trim() !=='' && _$('date_naiss').value.trim().isDate()) {
        form.append('date_naiss',_$('date_naiss').value.trim());
        
    }else{
        errors += '•' + _$('date_naiss').value.trim() + ' Your Date input is not good!<br/>';
        //console.log(errors);
    }
    if (_$('id_father').value.trim() !=='' && _$('id_mother').value.trim() !=='' && _$('id_tutor').value.trim() !=='') {
        form.append('id_father',_$('id_father').value.trim());
        form.append('id_mother',_$('id_mother').value.trim());
        form.append('id_tutor',_$('id_tutor').value.trim());
        
    }else{
        errors += '• All the parents must be specified!<br/>';
        //console.log(errors);
    }

    // n'envoyer qu'en cas de non-erreur
    if(errors == ""){
        // entrée simple et rien en cours
        if (no_args == true && student_isrunning == false && empty_input == true) {
            student_isrunning = true;
            ajax_save_student.open('POST','./inc/ajax/ajax_student.php');
            ajax_save_student.send(form);
    
        }else if(no_args == false && student_isrunning == false){
            student_isrunning = true;
            // je définie la variable form.cover_img uniquement si j'ai recu un fichier
            if (has_blob) {
                form.append('cover_img',images[0]);
            }
            ajax_save_student.open('POST','./inc/ajax/ajax_student.php');
            ajax_save_student.send(form);
        }
    }else{
        enlever_classe(_('btn_save_student_loader'),'visible');
        _('div_error').innerHTML = '<pre class="color-red"><strong>Error:</strong> <br/>' + errors + '</pre>';
        resetScroll();
        _('slides_new_student').parentNode.parentNode.scrollTo(0, 0);
        
    }

    

    ajax_save_student.onreadystatechange = function(){
        if (ajax_save_student.readyState == 4 && ajax_save_student.status == 200){
            var reponse = ajax_save_student.responseText;
            var OBJ = {};
            try {
                // statements
                OBJ = JSON.parse(reponse);
                //console.log(OBJ.count);
                if(OBJ.status === "success"){
                    setTimeout(function(){
                        enlever_classe(_('btn_save_student_loader'),'visible');
                        _('div_error').innerHTML = OBJ.message;
                        resetScroll();
                        _('slides_new_student').parentNode.parentNode.scrollTo(0, 0);
                    },500);
                    setTimeout(function(){
                        document.location = 'http://localhost/dovas-link/?page=management&subpage=students';
                    },1500);

                }else{
                    enlever_classe(_('btn_save_student_loader'),'visible');
                    _('div_error').innerHTML = OBJ.message;
                    resetScroll();
                    _('slides_new_student').parentNode.parentNode.scrollTo(0, 0);
                }
            } catch(e) {
                // statements
                console.log(e);
                console.log(reponse);
                //_('div_error').innerHTML = reponse;
                enlever_classe(_('btn_save_student_loader'),'visible');
            }
            
            
            student_isrunning = false;
            ajax_save_student = createXmlHttpRequestObject();
        }
    };

}

/*=================================================*/
var xhr_form_promotion = createXmlHttpRequestObject();
function form_promotion(event,id){
    id_p = '';
    if(typeof(id) === 'string'){
        id_p = id;
    }
    //event.preventDefault();
    if (!elm_HasClasse(_('modal_windows'),'visible')){
        ajouter_classe(_('modal_windows'),'visible');
        _('modal_head_title').innerHTML = '';
        _('modal_content_body').innerHTML = '<span class="cycle_loader visible"></span>';
    }
    xhr_form_promotion.open('POST','./inc/ajax/ajax_promotion.php');
    xhr_form_promotion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr_form_promotion.send('client_type=ajax_bot&command=form_promotion&id=' + id_p);

    xhr_form_promotion.onreadystatechange = function(){
        if (xhr_form_promotion.readyState == 4 && xhr_form_promotion.status == 200){
        var reponse = xhr_form_promotion.responseText;
        try {
            // statements
            OBJ = JSON.parse(reponse);
            if(OBJ.status === "success"){
                enlever_classe('#modal_content_body span.cycle_loader','visible');
                setTimeout(function(){
                    _('modal_head_title').innerHTML = OBJ.title;
                    _('modal_content_body').innerHTML = OBJ.message;
                },5);
            }else{
                _('modal_head_title').innerHTML = OBJ.title;
                _('modal_content_body').innerHTML = OBJ.message;
            }
        } catch(e) {
            // statements
            console.log(e);
            enlever_classe('#modal_windows span.cycle_loader','visible');

            _('modal_head_title').innerHTML = 'Probleme survenu';
            _('modal_content_body').innerHTML = reponse;
        }
    };
}}


/*=================================================*/
var xhr_student_form_inscription = createXmlHttpRequestObject();
function student_form_inscription(event,id){
    id_p = '';
    if(typeof(id) === 'string'){
        id_p = id;
    }
    //event.preventDefault();
    if (!elm_HasClasse(_('modal_windows'),'visible')){
        ajouter_classe(_('modal_windows'),'visible');
        _('modal_head_title').innerHTML = '';
        _('modal_content_body').innerHTML = '<span class="cycle_loader visible"></span>';
    }
    xhr_form_promotion.open('POST','./inc/ajax/ajax_student_form_inscription.php');
    xhr_form_promotion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr_form_promotion.send('client_type=ajax_bot&command=form_inscription&id=' + id_p);

    xhr_form_promotion.onreadystatechange = function(){
        if (xhr_form_promotion.readyState == 4 && xhr_form_promotion.status == 200){
        var reponse = xhr_form_promotion.responseText;
        try {
            // statements
            OBJ = JSON.parse(reponse);
            if(OBJ.status === "success"){
                enlever_classe('#modal_content_body span.cycle_loader','visible');
                setTimeout(function(){
                    _('modal_head_title').innerHTML = OBJ.title;
                    _('modal_content_body').innerHTML = OBJ.message;
                },5);
            }else{
                _('modal_head_title').innerHTML = OBJ.title;
                _('modal_content_body').innerHTML = OBJ.message;
            }
        } catch(e) {
            // statements
            console.log(e);
            enlever_classe('#modal_windows span.cycle_loader','visible');

            _('modal_head_title').innerHTML = 'Probleme survenu';
            _('modal_content_body').innerHTML = reponse;
        }
    };
}}


if(typeof delete_employee !== 'function') {
    function delete_employee(event,btn){
        event.preventDefault();
        var employee = btn.parentNode.parentNode.parentNode;
        if (!elm_HasClasse(employee,'fade-out')){
            ajouter_classe(employee,'fade-out');
        }
        

        var AJAX = createXmlHttpRequestObject();
        var id_personnel = employee.getAttribute('id');
        AJAX.open('POST','./inc/ajax/ajax_personnel.php');
        AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        AJAX.send('client_type=ajax_bot&command=delete_employee&id_personnel=' + id_personnel);
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        setTimeout(function(){
                            var list = employee.parentNode;
                            list.removeChild(employee);
                        },1000);
                    }else{
                        setTimeout(function(){
                            if (elm_HasClasse(employee, 'fade-out')) {
                                enlever_classe(employee,'fade-out');
                            }
                        },1000);
                    }
                } catch(e) {
                    setTimeout(function(){
                        if (elm_HasClasse(employee, 'fade-out')) {
                            enlever_classe(employee,'fade-out');
                        }
                    },1000);
                }
            }else{
                setTimeout(function(){
                    if (elm_HasClasse(employee, 'fade-out')) {
                        enlever_classe(employee,'fade-out');
                    }
                },1000);
            }
        };
    }
}


if(typeof delete_student !== 'function') {
    function delete_student(event,btn){
        event.preventDefault();
        var student = btn.parentNode.parentNode.parentNode;
        if (!elm_HasClasse(student,'fade-out')){
            ajouter_classe(student,'fade-out');
        }
        

        var AJAX = createXmlHttpRequestObject();
        var id_student = student.getAttribute('id');
        AJAX.open('POST','./inc/ajax/ajax_student.php');
        AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        AJAX.send('client_type=ajax_bot&command=delete_student&id_student=' + id_student);
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        setTimeout(function(){
                            var list = student.parentNode;
                            list.removeChild(student);
                        },1000);
                    }else{
                        setTimeout(function(){
                            if (elm_HasClasse(student, 'fade-out')) {
                                enlever_classe(student,'fade-out');
                            }
                        },1000);
                    }
                } catch(e) {
                    setTimeout(function(){
                        if (elm_HasClasse(student, 'fade-out')) {
                            enlever_classe(student,'fade-out');
                        }
                    },1000);
                }
            }else{
                setTimeout(function(){
                    if (elm_HasClasse(student, 'fade-out')) {
                        enlever_classe(student,'fade-out');
                    }
                },1000);
            }
        };
    }
}


if(typeof attacher_user !== 'function') {
    function attacher_user(event,id_personnel){
        event.preventDefault();
        var user = _radio('linked_user');
        var AJAX = createXmlHttpRequestObject();
        if (typeof user != 'undefined') {
            var id_user = user.getAttribute('value').trim();
            var id_pers = id_personnel.trim();
            //console.log(id_user);
            AJAX.open('POST','./inc/ajax/ajax_personnel.php');
            AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            AJAX.send('client_type=ajax_bot&command=attacher_user&id_personnel=' + id_pers + '&id_user=' + id_user);
        }
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        slide_form_promote(event,'form_affectation');
                    }else{
                        console.log(OBJ.message);
                    }
                } catch(e) {
                    console.log(reponse);
                }
            }
        };
    }
}


if(typeof cancel_employee_career !== 'function') {
    function cancel_employee_career(btn){
        var line = btn.parentNode.parentNode;
        var AJAX = createXmlHttpRequestObject();
        var id_affectation = line.getAttribute('id').trim();
        if (!elm_HasClasse(line,'fade-out')){
            ajouter_classe(line,'fade-out');
        }

        AJAX.open('POST','./inc/ajax/ajax_personnel.php');
        AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        AJAX.send('client_type=ajax_bot&command=cancel_employee_career&id_affectation=' + id_affectation);
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        setTimeout(function(){
                            var list = line.parentNode;
                            list.removeChild(line);
                        },1000);
                    }else{
                        console.log(OBJ.message);
                    }
                } catch(e) {
                    console.log(reponse);
                }
            }else{
                console.log(reponse);
            }
        };
    }
}

if(typeof affecter_personnel !== 'function') {
    function affecter_personnel(id){

        var id_personnel = id.trim();
        var errors = '';
        var id_structure = _$('id_structure').value.trim();
        if (id_structure == '') {errors += 'You must select a structure ! <br/>';}
        var id_service = _$('id_service').value.trim();
        if (id_service == '') {errors += 'You must select a service ! <br/>';}
        var id_fonction = _$('id_fonction').value.trim();
        if (id_fonction == '') {errors += 'You must select a function/poste ! <br/>';}
        var id_niveau = _$('id_niveau').value.trim();
        if (id_niveau == '') {errors += 'You must select a level/niveau ! <br/>';}
        var date_affectation = _$('date_affectation').value.trim();
        if (!date_affectation.isDate()) {errors += 'You must enter a valid date ! <br/>';}
        var AJAX = createXmlHttpRequestObject();
        if (errors == '') {
            AJAX.open('POST','./inc/ajax/ajax_personnel.php');
            AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            AJAX.send('client_type=ajax_bot&command=affecter_personnel&id_personnel=' 
                + id_personnel + '&id_structure=' + id_structure + '&id_service=' + id_service
                 + '&id_fonction=' + id_fonction + '&id_niveau=' + id_niveau
                  + '&date_affectation=' + date_affectation);
        }else{
            _('form_affectation_error').innerHTML = '<pre class="color-red">' + errors + '</pre>';
            resetScroll();
             _('fieldset_affectation').parentNode.parentNode.parentNode.scrollTo(0, 0);
        }
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        setTimeout(function(){
                            form_promotion(event,id_personnel);
                        },500);
                    }else{
                        console.log(OBJ.message);
                    }
                } catch(e) {
                    console.log(reponse);
                }
            }else{
                console.log(reponse);
            }
        };
    }
}


if(typeof cancel_student_subscription !== 'function') {
    function cancel_student_subscription(btn){
        var line = btn.parentNode.parentNode;
        var AJAX = createXmlHttpRequestObject();
        var id_inscription = line.getAttribute('id').trim();
        if (!elm_HasClasse(line,'fade-out')){
            ajouter_classe(line,'fade-out');
        }

        AJAX.open('POST','./inc/ajax/ajax_student.php');
        AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        AJAX.send('client_type=ajax_bot&command=cancel_student_subscription&id_inscription=' + id_inscription);
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        setTimeout(function(){
                            var list = line.parentNode;
                            list.removeChild(line);
                        },1000);
                    }else{
                        console.log(OBJ.message);
                    }
                } catch(e) {
                    console.log(reponse);
                }
            }else{
                console.log(reponse);
            }
        };
    }
}


if(typeof subscribe_student !== 'function') {
    function subscribe_student(id){

        var id_student = id.trim();
        var errors = '';
        var id_structure = _$('id_structure').value.trim();
        if (id_structure == '') {errors += 'You must select a structure ! <br/>';}
        var code_classe = _$('code_classe').value.trim();
        if (code_classe == '') {errors += 'You must select a classroom ! <br/>';}
        var code_annee = _$('code_annee').value.trim();
        if (code_annee == '') {errors += 'You must select a school year ! <br/>';}
        var code_filiere = _$('code_filiere').value.trim();
        if (code_filiere == '') {errors += 'You must select a speciality ! <br/>';}
        var date_inscription = _$('date_inscription').value.trim();
        if (!date_inscription.isDate()) {errors += 'You must enter a valid date ! <br/>';}
        var AJAX = createXmlHttpRequestObject();
        if (errors == '') {
            AJAX.open('POST','./inc/ajax/ajax_student.php');
            AJAX.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            AJAX.send('client_type=ajax_bot&command=subscribe_student&id_student=' 
                + id_student + '&id_structure=' + id_structure + '&code_classe=' + code_classe
                 + '&code_annee=' + code_annee + '&code_filiere=' + code_filiere
                  + '&date_inscription=' + date_inscription);
        }else{
            _('form_inscription_error').innerHTML = '<pre class="color-red">' + errors + '</pre>';
            resetScroll();
             _('student_form_subscription_slider').parentNode.parentNode.scrollTo(0, 0);
        }
        AJAX.onreadystatechange = function(){
            if (AJAX.readyState == 4 && AJAX.status == 200){
                var reponse = AJAX.responseText;
                try {
                    OBJ = JSON.parse(reponse);
                    if(OBJ.status === "success"){
                        setTimeout(function(){
                            student_form_inscription(event,id_student);
                        },500);
                    }else{
                        console.log(OBJ.message);
                    }
                } catch(e) {
                    console.log(reponse);
                }
            }else{
                console.log(reponse);
            }
        };
    }
}



function slide_form_promote(event,html_id){
    if (typeof(event) !== 'undefined') {
        event.preventDefault();
    }
    var slider = _(html_id);
    if (elm_HasClasse(slider, 'slided')) {
        enlever_classe(slider,'slided');
        resetScroll();
        slider.parentNode.parentNode.scrollTo(0, 0);
    }else{
        ajouter_classe(slider,'slided');
        resetScroll();
        slider.parentNode.parentNode.scrollTo(0, 0);
    }
    //console.log(slider);
}
