$(document).ready(function() {
    //$('.checkin-datepicker').pickadate();
    //$('.checkout-datepicker').pickadate();

    /*console.log(window.hostel.BOOKING_CHECKIN);
    console.log(window.hostel.BOOKING_CHECKOUT);*/

    let $checkin_date = $('.checkin-datepicker'),
        $checkout_date = $('.checkout-datepicker'),
        $booking_checkin_btn = $('#booking-checkin-btn'),
        $booking_checkout_btn = $('#booking-checkout-btn'),
        $booking_booker_btn = $('#booking-booker-btn'),
        $booking_sm_booker_btn = $('#booking-sm-booker-btn'),
        $booking_booker_modal = $('#booking-booker-modal'),
        $booking_sm_checkin_btn = $('#booking-sm-checkin-btn'),
        $booking_sm_checkout_btn = $('#booking-sm-checkout-btn'),
        $booking_checkin_date = $booking_checkin_btn.find('.date-content'),
        $booking_checkout_date = $booking_checkout_btn.find('.date-content'),
        $booking_sm_checkin_date = $booking_sm_checkin_btn.find('.date-content'),
        $booking_sm_checkout_date = $booking_sm_checkout_btn.find('.date-content'),
        $booking_adult_field = $('input.booking_data_adult'),
        $booking_children_field = $('input.booking_data_children'),
        $booking_room_field = $('input.booking_data_room'),
        /*$maximum_adults = maximumAdults,
        $maximum_children = maximumChildren,
        $adults = parseInt($booking_adult_field.val()),
        $children = parseInt($booking_children_field.val()),
        $rooms = $booking_room_field.val(),*/
        $maximum_adults = window.hostel.MAX_ADULT,
        $maximum_children = window.hostel.MAX_CHILDREN,
        $adults = window.hostel.DEFAULT_ADULT,
        $children = window.hostel.DEFAULT_CHILDREN,
        $rooms = window.hostel.DEFAULT_ROOM,
        $booking_booker_adult_field = $('#booking-booker-modal .modal-body .adults a'),
        $booking_booker_children_field = $('#booking-booker-modal .modal-body .children a'),
        $booking_booker_room_field = $('#booking-booker-modal .modal-body .room a'),
        $booking_booker_button = $('#booking-booker-modal button');
        /*$today = new Date(),
        $tomorrow = new Date();
        $tomorrow.setDate($today.getDate() + 1);*/


    let $checkin_datepicker = $checkin_date.pickadate({
            min: window.hostel.BOOKING_CHECKIN,
            selectMonths: false,
            selectYears: false,
            weekdaysFull: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            weekdaysShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            format: 'dd mmm yyyy',
            formatSubmit: 'yyyy-mm-dd',
            today: '',
            clear: '',
            close: 'Fermer',
            onClose: function() {
                $booking_checkin_btn.removeClass('active');
                $booking_sm_checkin_btn.removeClass('active');
            },
            onSet: function(context) {
                if ($checkin_picker.get('select')) {
                    let selected = $checkin_picker.get('select');

                    $checkout_picker.set('min', new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate()+1));
                    $booking_checkin_date.text($checkin_picker.get('value'));
                    $booking_sm_checkin_date.text($checkin_picker.get('value'));
                }
            }
        }),
        $checkin_picker = $checkin_datepicker.pickadate('picker');

    let $checkout_datepicker = $checkout_date.pickadate({
            min: window.hostel.BOOKING_CHECKOUT,
            selectMonths: false,
            selectYears: false,
            weekdaysFull: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            weekdaysShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            format: 'dd mmm yyyy',
            formatSubmit: 'yyyy-mm-dd',
            today: '',
            clear: '',
            close: 'Fermer',
            onClose: function() {
                $booking_checkout_btn.removeClass('active');
                $booking_sm_checkout_btn.removeClass('active');
            },
            onSet: function(context) {
                if (!$checkout_picker.get('select') && $checkin_picker.get('select')) {
                    let selected = $checkin_picker.get('select'),
                        $d = new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate()+1);

                    $booking_checkout_date.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                    $booking_sm_checkout_date.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                }

                if ($checkout_picker.get('select')) {
                    let selected = $checkout_picker.get('select'),
                        $d = new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate());

                    $booking_checkout_date.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                    $booking_sm_checkout_date.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                }
            }
        }),
        $checkout_picker = $checkout_datepicker.pickadate('picker');
        //$checkout_picker.set('min', $tomorrow);

    $booking_checkin_btn.click(function (e) {
        e.preventDefault();

        $(this).addClass('active');

        $checkin_date.trigger('click');
    });

    $booking_checkout_btn.click(function (e) {
        e.preventDefault();

        $(this).addClass('active');

        $checkout_date.trigger('click');
    });

    $booking_booker_btn.click(function(e){
        e.preventDefault();

        $(this).addClass('active');

        $booking_booker_modal.modal()
    });

    $booking_booker_modal.on('show.bs.modal', function () {
        $('body').css('overflow', 'hidden');
    });

    $booking_booker_modal.on('hide.bs.modal', function () {
        $booking_booker_btn.removeClass('active');
        $booking_sm_booker_btn.removeClass('active');

        $('body').css('overflow', 'auto');
    });

    if ($adults === $maximum_adults) {
        $('#booking-booker-modal .modal-body .adults a.addition').addClass('disabled')
    }

    $booking_booker_adult_field.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $adults--;

            if ($adults === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            //let $element = $this.parents('.data').find('span');
            $($element[0]).text($adults)

            //$booking_adult_field.val($adults);

            //alert($booking_adult_field.val())
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $adults++;

            if ($adults === $maximum_adults) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            //let $element = $this.parents('.data').find('span');
            $($element[0]).text($adults)

            //$booking_adult_field.val($adults);

            //alert($booking_adult_field.val())
        }
    });

    if ($children === $maximum_children) {
        $('#booking-booker-modal .modal-body .children a.addition').addClass('disabled')
    }

    ///console.log(parseInt($booking_children_field));

    /*if ($children === 0) {
        $('#booking-booker-modal .modal-body .children a.addition').addClass('disabled')
    }*/

    $booking_booker_children_field.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling =  $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $children--;

            if ($children === 0) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            //let $element = $this.parents('.data').find('span');
            $($element[0]).text($children)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $children++;

            if ($children === $maximum_children) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            //let $element = $this.parents('.data').find('span');
            $($element[0]).text($children)
        }
    });

    $booking_booker_room_field.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $rooms--;

            if ($rooms === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            //let $element = $this.parents('.data').find('span');
            $($element[0]).text($rooms)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $rooms++;

            if ($rooms === 9) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            //let $element = $this.parents('.data').find('span');
            $($element[0]).text($rooms)
        }
    });

    $booking_booker_button.click(function (e) {
        e.preventDefault();

        let $room = $($booking_booker_btn.find('.room')),
            $customer = $($booking_booker_btn.find('.customer')),
            $child = $($booking_booker_btn.find('.children')),
            $roomSm = $($booking_sm_booker_btn.find('.room')),
            $customerSm = $($booking_sm_booker_btn.find('.customer')),
            $childSm = $($booking_sm_booker_btn.find('.children'));

        $booking_adult_field.val($adults);
        $booking_children_field.val($children);
        $booking_room_field.val($rooms);

        $room.text($rooms + ' chambre, ');
        $customer.text($adults + ' adultes, ');
        $child.text($children + ' enfants');
        $roomSm.text($rooms + ' chambre, ');
        $customerSm.text($adults + ' adultes, ');
        $childSm.text($children + ' enfants');

        $('#booking-booker-modal').modal('hide')
    });

    // Booking mobile
    let $app_room_booking_sm_modal = $('#app-room-booking-sm-modal'),
        $booking_room_modal_sm_btn = $('#booking_room_modal_sm_btn');

    $app_room_booking_sm_modal.on('show.bs.modal', function () {
        $('body').css('overflow', 'hidden');
    });

    $app_room_booking_sm_modal.on('hide.bs.modal', function () {
        $booking_room_modal_sm_btn.show();

        $('body').css('overflow', 'auto');
    });

    $booking_room_modal_sm_btn.click(function(e){
        e.preventDefault();

        $(this).hide();

        $app_room_booking_sm_modal.modal()
    });

    $booking_sm_checkin_btn.click(function (e) {
        e.preventDefault();

        $(this).addClass('active');

        $checkin_date.trigger('click');
    });

    $booking_sm_checkout_btn.click(function (e) {
        e.preventDefault();

        $(this).addClass('active');

        $checkout_date.trigger('click');
    });

    $booking_sm_booker_btn.click(function(e){
        e.preventDefault();

        $(this).addClass('active');

        $booking_booker_modal.modal()
    });




    // Booking occupant data

    let $wrapper = $('#booking-form-occupant-wrapper'),
        $nameTitle = '<small class="form-text text-muted" style="margin-top: -5px">Ce champ est obligatoire </small>',
        $emailTitle = '<small class="form-text text-primary" style="margin-top: -5px">Nous envoyons des e-mails uniquement pour communiquer des informations relatives aux reservations.</small>',
        prototype = $wrapper.data('prototype');

    for (let index = 0; index < window.hostel.DEFAULT_ROOM; index++) {

        if (!$wrapper.length) {
            return;
        }

        let newForm = prototype.replace(/__name__/g, index);

        $wrapper.append(content(index))

        let $booking_content =  $('#booking_room_booker_info_'+index+ ' .card-body');
        $booking_content.append(newForm);

        $('#booking_occupants_'+index).addClass('row');

        let $booker_input = $('#booking_occupants_'+index+' .md-form.md-outline');
        $booker_input.wrap('<div class="col-12 col-md-6 booker-wrap" />');

        $('#booking_occupants_'+index+' .booker-wrap').each(function (i, e) {
            (i === 0) ? $(e).append($nameTitle) : $(e).append($emailTitle);
        })
    }














    











    /*
    if ($adults === $maximumChildren) {
        $('#booking-user-modal .modal-body .children a.addition').addClass('disabled')
    }

    $bookingUserNumberInputChildren.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling =  $($this.siblings('.addition')[0]);

            $children--;

            if ($children === 0) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($children)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $children++;

            if ($children === $maximumChildren) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($children)
        }
    });

    $bookingUserNumberInputRoom.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]);

            $rooms--;

            if ($rooms === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($rooms)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $rooms++;

            if ($rooms === 9) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($rooms)
        }
    });

    $bookingUserButton.click(function (e) {
        e.preventDefault();

        let $room = $($bookingUser.find('.room')),
            $roomM = $($bookingUserM.find('.room')),
            $customer = $($bookingUser.find('.customer')),
            $customerM = $($bookingUserM.find('.customer')),
            $child = $($bookingUser.find('.children')),
            $childM = $($bookingUserM.find('.children'));

        $room.text($rooms + ' chambre, ');
        $customer.text($adults + ' adultes, ');
        $child.text($children + ' enfants');

        $roomM.text($rooms + ' chambre, ');
        $customerM.text($adults + ' adultes, ');
        $childM.text($children + ' enfants');

        $('#booking-user-modal').modal('hide')
    });
    */








    // Booking form
    /*let $bookingBulk = $('.booking-form-bulk .booking-form .booking-bulk'),
        $bookingBulkM = $('.booking-mobile-form-bulk .booking-form .booking-bulk'),
        $bookingUser = $('.booking-form-bulk .booking-form .user'),
        $bookingUserM = $('.booking-mobile-form-bulk .booking-form .user'),
        $bookingCheckin = $('.booking-form-bulk .booking-form .checkin'),
        $bookingCheckinM = $('.booking-mobile-form-bulk .booking-form .checkin'),
        $bookingCheckout = $('.booking-form-bulk .booking-form .checkout'),
        $bookingCheckoutM = $('.booking-mobile-form-bulk .booking-form .checkout'),
        $bookingUserNumberInputAdults = $('#booking-user-modal .modal-body .adults a'),
        $bookingUserNumberInputChildren = $('#booking-user-modal .modal-body .children a'),
        $bookingUserNumberInputRoom = $('#booking-user-modal .modal-body .room a'),
        $adults = 2, $children = 0, $rooms = 1,
        $maximumAdults = maximumAdults,
        $maximumChildren = maximumChildren,
        $bookingUserButton = $('#booking-user-modal button');

    $bookingUser.click(function(e){
        e.preventDefault();

        $bookingBulk.css('border', '1.6px solid #FFFFFF');
        $bookingUser.css('border', '1.6px solid #FF9A16');

        $('#booking-user-modal').modal()
    });

    $bookingUserM.click(function(e){
        e.preventDefault();

        $bookingBulkM.css('border', '1.6px solid #FFFFFF');
        $bookingUserM.css('border', '1.6px solid #FF9A16');

        $('#booking-user-modal').modal()
    });

    $('#booking-user-modal').on('hide.bs.modal', function () {
        $bookingUser.css('border', '1.6px solid #FFFFFF');
        $bookingUserM.css('border', '1.6px solid #FFFFFF');
    });

    if ($adults === $maximumAdults) {
        $('#booking-user-modal .modal-body .adults a.addition').addClass('disabled')
    }

    $bookingUserNumberInputAdults.click(function (e) {
        e.preventDefault();
        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]);

            $adults--;

            if ($adults === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($adults)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $adults++;

            if ($adults === $maximumAdults) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($adults)
        }
    });

    if ($adults === $maximumChildren) {
        $('#booking-user-modal .modal-body .children a.addition').addClass('disabled')
    }

    $bookingUserNumberInputChildren.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling =  $($this.siblings('.addition')[0]);

            $children--;

            if ($children === 0) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($children)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $children++;

            if ($children === $maximumChildren) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($children)
        }
    });

    $bookingUserNumberInputRoom.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]);

            $rooms--;

            if ($rooms === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($rooms)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $rooms++;

            if ($rooms === 9) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($rooms)
        }
    });

    $bookingUserButton.click(function (e) {
        e.preventDefault();

        let $room = $($bookingUser.find('.room')),
            $roomM = $($bookingUserM.find('.room')),
            $customer = $($bookingUser.find('.customer')),
            $customerM = $($bookingUserM.find('.customer')),
            $child = $($bookingUser.find('.children')),
            $childM = $($bookingUserM.find('.children'));

        $room.text($rooms + ' chambre, ');
        $customer.text($adults + ' adultes, ');
        $child.text($children + ' enfants');

        $roomM.text($rooms + ' chambre, ');
        $customerM.text($adults + ' adultes, ');
        $childM.text($children + ' enfants');

        $('#booking-user-modal').modal('hide')
    });

    let $checkIn = new Date(),
        $checkOut = new Date($checkIn.getFullYear(), $checkIn.getMonth(), $checkIn.getDate()+1),
        $checkInDateContent = $bookingCheckin.find('.date-content'),
        $checkOutDateContent = $bookingCheckout.find('.date-content'),
        $checkInDateContentM = $bookingCheckinM.find('.date-content'),
        $checkOutDateContentM = $bookingCheckoutM.find('.date-content'),
        $checkInDateContentNumber = $bookingCheckinM.find('.date-content-number'),
        $checkOutDateContentNumber = $bookingCheckoutM.find('.date-content-number'),
        $checkInDateContentTilte = $bookingCheckinM.find('.date-content-title'),
        $checkOutDateContentTilte = $bookingCheckoutM.find('.date-content-title');

    $checkInDateContent.text($checkIn.getDate()+' '+getMonthsShort($checkIn.getMonth())+' '+$checkIn.getFullYear());
    $checkOutDateContent.text($checkOut.getDate()+' '+getMonthsShort($checkOut.getMonth())+' '+$checkOut.getFullYear());
    $checkInDateContentM.text($checkIn.getDate()+' '+getMonthsShort($checkIn.getMonth())+' '+$checkIn.getFullYear());
    $checkOutDateContentM.text($checkOut.getDate()+' '+getMonthsShort($checkOut.getMonth())+' '+$checkOut.getFullYear());
    $checkInDateContentNumber.text($checkIn.getDate());
    $checkOutDateContentNumber.text($checkOut.getDate());
    $checkInDateContentTilte.text(getMonthsShort($checkIn.getMonth())+', '+$checkIn.getFullYear());
    $checkOutDateContentTilte.text(getMonthsShort($checkOut.getMonth())+', '+$checkOut.getFullYear());


    let $inputCheckIn = $('#checkInDate').pickadate({
            min: true,
            selectMonths: false,
            selectYears: false,
            weekdaysFull: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            weekdaysShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            format: 'dd mmm yyyy',
            today: '',
            clear: '',
            close: 'Fermer',
            onClose: function() {
                $bookingCheckin.css('border', '1.6px solid #FFFFFF');
                $bookingCheckinM.css('border', '1.6px solid #FFFFFF');
            },
            onSet: function(context) {
                if ($checkInPicker.get('select')) {
                    let selected = $checkInPicker.get('select');
                    // selected.obj => date selectionnée
                    $checkOutPicker.set('min', new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate()+1));

                    $checkInDateContent.text($checkInPicker.get('value'));
                    $checkInDateContentM.text($checkInPicker.get('value'));
                    $checkInDateContentNumber.text(selected.obj.getDate());
                    $checkInDateContentTilte.text(getMonthsShort(selected.obj.getMonth())+', '+selected.obj.getFullYear());
                }
            }
        }),
        $checkInPicker = $inputCheckIn.pickadate('picker');

    let $inputCheckOut = $('#checkOutDate').pickadate({
            min: 1,
            selectMonths: false,
            selectYears: false,
            weekdaysFull: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            weekdaysShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            format: 'dd mmm yyyy',
            today: '',
            clear: '',
            close: 'Fermer',
            onClose: function() {
                $bookingCheckout.css('border', '1.6px solid #FFFFFF');
                $bookingCheckoutM.css('border', '1.6px solid #FFFFFF');
            },
            onSet: function(context) {
                if (!$checkOutPicker.get('select') && $checkInPicker.get('select')) {
                    let selected = $checkInPicker.get('select'),
                        $d = new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate()+1);

                    $checkOutDateContent.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                    $checkOutDateContentM.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                    $checkOutDateContentNumber.text($d.getDate());
                    $checkOutDateContentTilte.text(getMonthsShort($d.getMonth())+', '+$d.getFullYear());
                }

                if ($checkOutPicker.get('select')) {
                    let selected = $checkOutPicker.get('select'),
                        $d = new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate());

                    $checkOutDateContent.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                    $checkOutDateContentM.text($d.getDate()+' '+getMonthsShort($d.getMonth())+' '+$d.getFullYear());
                    $checkOutDateContentNumber.text($d.getDate());
                    $checkOutDateContentTilte.text(getMonthsShort($d.getMonth())+', '+$d.getFullYear());
                }
            }
        }),
        $checkOutPicker = $inputCheckOut.pickadate('picker');


    $bookingCheckin.click(function (e) {
        e.preventDefault();

        $bookingBulk.css('border', '1.6px solid #FFFFFF');
        $bookingCheckin.css('border', '1.6px solid #FF9A16');

        $('#checkInDate').trigger('click');
    });

    $bookingCheckout.click(function (e) {
        e.preventDefault();

        $bookingBulk.css('border', '1.6px solid #FFFFFF');
        $bookingCheckout.css('border', '1.6px solid #FF9A16');

        $('#checkOutDate').trigger('click');
    });

    $bookingCheckinM.click(function (e) {
        e.preventDefault();

        $bookingBulkM.css('border', '1.6px solid #FFFFFF');
        $bookingCheckinM.css('border', '1.6px solid #FF9A16');

        $('#checkInDate').trigger('click');
    });

    $bookingCheckoutM.click(function (e) {
        e.preventDefault();

        $bookingBulkM.css('border', '1.6px solid #FFFFFF');
        $bookingCheckoutM.css('border', '1.6px solid #FF9A16');

        $('#checkOutDate').trigger('click');
    });

    // MODAL BOOKING

    let $roomBooking = $('.app-room-booking-btn'),
        $roomBookingBulk = $('#app-room-booking-modal .booking-bulk'),
        $roomBookingUser = $('#app-room-booking-modal .booking-form .user'),
        $roomBookingUserNumberInputAdults = $('#app-modal-booking-user .modal-body .adults a'),
        $roomBookingUserNumberInputChildren = $('#app-modal-booking-user .modal-body .children a'),
        $roomBookingUserNumberInputRoom = $('#app-modal-booking-user .modal-body .room a'),
        $roomBookingCheckin = $('#app-room-booking-modal .booking-form .checkin'),
        $roomBookingCheckout = $('#app-room-booking-modal .booking-form .checkout'),
        $roomAdults = 2, $roomChildren = 0, $roomRooms = 1,
        $roomMaximumAdults = maximumAdults,
        $roomMaximumChildren = maximumChildren,
        $roomBookingUserButton = $('#app-modal-booking-user button');

    $roomBooking.click(function(e) {
        e.preventDefault();

        $('#app-room-booking-modal').modal()
    });

    $roomBookingUser.click(function(e){
        e.preventDefault();

        //$roomBookingBulk.css('border', '1.6px solid #eee');
        //$roomBookingUser.css('border', '1.6px solid #FF9A16');

        $roomBookingUser.addClass('active');

        $('#app-modal-booking-user').modal()
    });

    $('#app-modal-booking-user').on('hide.bs.modal', function () {
        //$roomBookingUser.css('border', '1.6px solid #eee');

        $roomBookingUser.removeClass('active');
    });

    if ($roomAdults === $roomMaximumAdults) {
        $('#app-modal-booking-user .modal-body .adults a.addition').addClass('disabled')
    }

    $roomBookingUserNumberInputAdults.click(function (e) {
        e.preventDefault();
        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]);

            $roomAdults--;

            if ($roomAdults === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($roomAdults)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $roomAdults++;

            if ($roomAdults === $roomMaximumAdults) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($roomAdults)
        }
    });

    if ($roomAdults === $roomMaximumChildren) {
        $('#booking-user-modal .modal-body .children a.addition').addClass('disabled')
    }

    $roomBookingUserNumberInputChildren.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling =  $($this.siblings('.addition')[0]);

            $roomChildren--;

            if ($roomChildren === 0) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($roomChildren)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $roomChildren++;

            if ($roomChildren === $roomMaximumChildren) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($roomChildren)
        }
    });

    $roomBookingUserNumberInputRoom.click(function (e) {
        e.preventDefault();

        let $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]);

            $roomRooms--;

            if ($roomRooms === 1) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($roomRooms)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]);

            $roomRooms++;

            if ($roomRooms === 9) $this.addClass('disabled');

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            let $element = $this.parents('.data').find('span');
            $($element[0]).text($roomRooms)
        }
    });

    $roomBookingUserButton.click(function (e) {
        e.preventDefault();

        let $room = $($roomBookingUser.find('.room')),
            $customer = $($roomBookingUser.find('.customer')),
            $child = $($roomBookingUser.find('.children'));

        $room.text($roomRooms + ' chambre, ');
        $customer.text($roomAdults + ' adultes, ');
        $child.text($roomChildren + ' enfants');

        $('#app-modal-booking-user').modal('hide')
    });

*/




});

function getScrollbarWidth() {

    var $window = $(window);
    var $html = $(document.documentElement);

    if ($html.height() <= $window.height()) {
        return 0;
    }

    var $outer = $('<div style="visibility:hidden;width:100px" />').
    appendTo('body');

    // Get the width without scrollbars.
    var widthWithoutScroll = $outer[0].offsetWidth;

    // Force adding scrollbars.
    $outer.css( 'overflow', 'scroll' );

    // Add the inner div.
    var $inner = $('<div style="width:100%" />' ).appendTo($outer);

    // Get the width with scrollbars.
    var widthWithScroll = $inner[0].offsetWidth;

    // Remove the divs.
    $outer.remove();

    // Return the difference between the widths.
    return widthWithoutScroll - widthWithScroll;
}

