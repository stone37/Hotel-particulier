$(document).ready(function() {

    // Upload multiple
    let $ordPhoto = [];
    let $body = $('body');

    $('.btn-photos').on('click', function(){
        $('input[type="file"].input-photo').trigger('click');
    });

    $("#photosAnnonce").dmUploader({
        allowedTypes: 'image/*',
        maxFileSize: 8388608, // 8 Megs max
        extFilter: ['jpg', 'jpeg','png','gif'],
        url: Routing.generate('app_image_upload_add'),
        onFallbackMode: function() {},
        onDragEnter: function(){
            this.addClass('active');
        },
        onDragLeave: function(){
            this.removeClass('active');
        },
        onInit: function() {
            console.log('Callback: Plugin initialized');
        },
        onNewFile: function(id, file){

            if (typeof FileReader !== "undefined"){
                var reader = new FileReader();

                reader.onload = function (e) {
                    // Photo chargée, on affiche
                    ajoutPhotoLoaded({id: id, src: e.target.result});
                };

                reader.readAsDataURL(file);
            }

            ajoutPhoto({id: id, name: file.name, size: file.size, uploaded: false});

            // Compteur nbPhoto
            $ordPhoto.push(id);
        },
        onUploadProgress: function(id, percent){
            $('#'+id+' .progresslabel').html("Envoi "+percent+'%');
            $('#'+id+' circle').css({"stroke-dasharray": percent+" 100"});
        },
        onUploadSuccess: function(id, data){

            $('#'+id+'').addClass('ok');
            $('#'+id+' .progresslabel').html("Envoi terminé");
            $('#'+id+' .progress').addClass('hide');
            $('#'+id+' .remove').show().addClass('show');

            ajoutPhotoLoaded({id: id, src: data.url});
        },
        onUploadError: function(id){
            $('#'+id+'').addClass('error');
            $('#'+id+' .progresslabel').html("Envoi échoué");
            $('#'+id+' .progress').hide();
            $('#'+id+' .remove').show().addClass('show');
        },
        onFileSizeError: function(file){
            console.error('File \'' + file.name + '\' cannot be added: size excess limit');

            notification(
                'Fichier refusé',
                'L\'image est trop volumineuse (supérieur a 8Mo).',
                {"timeOut": "8000", "closeButton": true},
                'error');
        },
        onFileTypeError: function(file){
            console.error('File \'' + file.name + '\' cannot be added: must be an image (type error)');

            notification(
                'Fichier refusé',
                'Le type du fichier n\'est pas supporté.',
                {"timeOut": "8000", "closeButton": true},
                'error');
        },
        onFileExtError: function(file){
            console.error('File \'' + file.name + '\' cannot be added: must be an image (extension error)');

            notification(
                'Fichier refusé',
                'L\'extension du fichier n\'est pas supporté.',
                {"timeOut": "8000", "closeButton": true},
                'error');
        }
    });

    /**
     * Suppression de l'image
     */
    $body.on('click', 'button.remove', function(e) {
        e.preventDefault();

        uploadRemove($(this).attr('data-id'), $ordPhoto);
    });

    /**
     * Annuler le telechargement de l'image
     */
    $body.on('click', 'button.progress', function(e) {
        e.preventDefault();

        uploadCancel($(this).attr('data-id'));
    });
});

function ajoutPhoto(file) {
    let filesize = getReadableFileSizeString(file.size);

    let $html = '<div class="scale-up-ver-top col-lg-2 col-md-4 col-6" id="'+file.id+'">';

    $html +='	<img src="'+file.src+'" alt="Image de l\'annonce" class="img-fluid z-depth-4">';
    $html +='	    <div class="info small font-weight-stone-500">';
    $html +='	        <span>'+file.name+'</span>';
    $html +='	        <span>'+filesize+'</span>';
    $html +='	        <span class="progresslabel">Envoi '+((file.uploaded)?'terminé':'0%')+'</span>';
    $html +='	    </div>';
    $html +='	    <div class="action">';
    $html +='		    <button class="progress '+((file.uploaded)?'hide':'')+'" data-id="'+file.id+'"  title="Annuler"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><circle r="16" cx="16" cy="16"></circle></svg></button>';
    $html +='		    <button class="remove '+((file.uploaded)?'show':'')+'" data-id="'+file.id+'" title="Supprimer"><svg viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M11.586 13l-2.293 2.293a1 1 0 0 0 1.414 1.414L13 14.414l2.293 2.293a1 1 0 0 0 1.414-1.414L14.414 13l2.293-2.293a1 1 0 0 0-1.414-1.414L13 11.586l-2.293-2.293a1 1 0 0 0-1.414 1.414L11.586 13z" fill="currentColor" fill-rule="nonzero"></path></svg></button>';
    $html +='	    </div>';
    $html +='	</div>';

    $('#imgUpload-list').prepend($html);
}

/**
 * Charge une image
 *
 * @param file
 */
function ajoutPhotoLoaded(file) {
    $('#'+file.id+' img').attr('src', file.src);
}

/**
 * Rend la taille de l'image
 *
 * @param fileSizeInBytes
 * @returns {string}
 */
function getReadableFileSizeString(fileSizeInBytes) {
    let i = -1;

    let byteUnits = [' Ko', ' Mo', ' Go', ' To', 'Po', 'Eo', 'Zo', 'Yo'];

    do {
        fileSizeInBytes = fileSizeInBytes / 1024;
        i++;
    } while (fileSizeInBytes > 1024);

    return Math.max(fileSizeInBytes, 1).toFixed(0) + byteUnits[i];
}

/**
 * Supprime une image
 *
 * @param id
 * @param ordPhoto
 * @returns {boolean}
 */
function uploadRemove(id, ordPhoto) {
    showLoading();

    let pos = ordPhoto.indexOf(id);
    ordPhoto.splice(pos, 1);

    if ($('#'+id+'').hasClass('error')) {
        $('#'+id).addClass('fade-out-bck');
        setTimeout(function(){ $('#'+id).remove(); }, 400);

        hideLoading();

        return;
    }

    $.ajax({
        url: Routing.generate('app_image_upload_delete', {'pos': pos}),
        success: function() {

            $('#'+id).addClass('fade-out-bck');
            setTimeout(function(){ $('#'+id).remove(); }, 400);

            hideLoading();
        }
    });
}

/**
 * Annule le telechargement de l'image
 *
 * @param id
 * @returns {boolean}
 */
function uploadCancel(id) {
    $('#photosAnnonce').dmUploader("cancel", id);
    $('#'+id).remove();

    return false;
}







