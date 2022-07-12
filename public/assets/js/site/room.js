$(document).ready(function() {

    /**
     * Room gallery lightBox plus
     */
    $('.lightbox-plus').click(function () {
        $('img#img-plus').trigger('click');
    });

    // Room option selected
    /*let $selected = $('.room-option'),
        $selectedBtn = $('.room-option .btn-primary'),
        $selectedCheckbox = $('input.selected.filled-in');

    $selectedBtn.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        $selected.removeClass('active');
        $selectedBtn.removeClass('disabled');
        $selectedBtn.text('Sélectionnée maintenant');
        $selectedCheckbox.prop('checked', false);

        $this.text('Sélectionnée');
        $this.addClass('disabled');
        $this.parents('.room-option').addClass('active');
        $this.parents('.room-option').find('input[type="checkbox"]').prop('checked', true)
    });

    $selectedCheckbox.click(function () {

        let $this = $(this);

        $selected.removeClass('active');
        $selectedBtn.removeClass('disabled');
        $selectedBtn.text('Sélectionnée maintenant');
        $selectedCheckbox.prop('checked', false);

        $this.prop('checked', true);

        $this.parents('.room-option').addClass('active');
        $this.parents('.room-option').find('a.btn.btn-primary').addClass('disabled');
        $this.parents('.room-option').find('a.btn.btn-primary').text('Sélectionnée');
    });*/

});




