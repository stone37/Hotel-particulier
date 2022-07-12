$(document).ready(function() {

    /**
     * Gestion des modeles pour les voitures d'occassion
     */
    if (model) {
        $advertAutoModelSelect = $('select.app-advert-auto-model');

        let $content = $("<option>").attr({value: model, selected: "selected"}).text(model);

        $advertAutoModelSelect.append($content);
        $advertAutoModelSelect.materialSelect();
    }

    // Loading page
    let $btnSubmit = $('.app-ad-submit-btn');

    $btnSubmit.click(() => {
        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');
    })

    /**
     * Supprime une image d'annonce
     */
    $('button.remove').click(() => {
        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');

        $.ajax({
            url: Routing.generate('app_dashboard_advert_image_delete', {'id':  $(this).attr('data-id')}),
            success: (data) => {
                let $data = $.parseJSON(data), element = $('#'+$data.id);

                if (element.hasClass('principale')) {
                    $('#imgUpload-list .imgUpload-add > div:eq(1)').addClass('principale');
                }

                element.remove();
                nbrImages--;

                if (nbrImages === 0) {
                    let $route = Routing.generate('app_dashboard_advert_image_add', {'id': adId});
                    let $html = '<div class="text-center mt-4">' +
                        '<a href="'+$route+'" class="btn btn-lg btn-default">Ajouter des images à votre annonce <i class="fas fa-image ml-2"></i></a>' +
                        '</div>';

                    $('#imgUpload-list .imgUpload-add').append($html);
                }

                $('#loader .preloader-wrapper').removeClass('active');
                $(".page-content").removeClass('disabled');

                notification("", "Image supprimée", {}, 'success');
            }
        });
    });


    /**
     * Designe une image comme principale
     */
    $('div.img-principale').click(() => {
        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');

        $.ajax({
            url: Routing.generate('app_dashboard_advert_image_principale', {'id':  $(this).attr('data-id')}),
            success: (data) => {
                let $data = $.parseJSON(data);

                $(".scale-up-ver-top").removeClass('principale');
                $('#'+$data.id).addClass('principale')

                $('#loader .preloader-wrapper').removeClass('active');
                $(".page-content").removeClass('disabled');

                notification("", "Nouvelle image principale", {}, 'success');
            }
        });
    });
});


/**
 * Affiche des notifications
 *
 * @param titre
 * @param message
 * @param options
 * @param type
 */
function notification (titre, message, options, type) {
    if(typeof options == 'undefined') options = {};
    if(typeof type == 'undefined') type = "info";

    toastr[type](message, titre, options);
}




