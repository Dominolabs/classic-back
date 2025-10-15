/**
 * We'll load the axios HTTP library
 */
var vocabulary = {
    номер: {
        uk: {
            zero: 'номерів',
            one: 'номер',
            few: 'номери',
            many: 'номерів'
        },
        en: {
            zero: 'rooms',
            one: 'room',
            few: 'rooms',
            many: 'rooms'
        }
    },
    ніч: {
        uk: {
            zero: 'ночей',
            one: 'ніч',
            few: 'ночі',
            many: 'ночей'
        },
        en: {
            zero: 'nights',
            one: 'night',
            few: 'nights',
            many: 'nights'
        }
    },
    гість: {
        uk: {
            zero: 'гостей',
            one: 'гість',
            few: 'гостя',
            many: 'гостей'
        },
        en: {
            zero: 'guests',
            one: 'guest',
            few: 'guests',
            many: 'guests'
        }
    },
    дорослий: {
        uk: {
            zero: 'дорослих',
            one: 'дорослий',
            few: 'дорослих',
            many: 'дорослих'
        },
        en: {
            zero: 'adults',
            one: 'adult',
            few: 'adults',
            many: 'adult'
        }
    },
    дитина: {
        uk: {
            zero: 'дітей',
            one: 'дитина',
            few: 'дитини',
            many: 'дітей'
        },
        en: {
            zero: 'children',
            one: 'child',
            few: 'children',
            many: 'children'
        }
    },
}

function getCorrectSpellKey (quantity) {
    quantity = parseInt(quantity);
    switch (true){
        case quantity === 0:
            return 'zero';
            break;
        case quantity % 10 === 1 || quantity === 1:
            return 'one';
            break;
        case quantity > 1 && quantity < 5:
            return 'few';
            break;
        default:
            return 'many';
    }
}


$(document).ready(() => {

    $('#add-booking-to-cart').on('click', function(e){
        var cartContent = JSON.parse($('#booking-cart-value').val());
        if(!cartContent.length){
            e.preventDefault();
        }
    });

    function clearCart() {
        var $cart = $('#booking-fixed-cart');
        $('#booking-cart-value').val(JSON.stringify([]));
        $cart.find('[data-rooms-quantity]').text('0');
        $cart.find('[data-persons-quantity]').text('0');
        $cart.find('[data-price]').text('0');

        $('[data-booking-item]').each(function (i, wrap) {
            var $wrap = $(wrap);
            var max_room_quantity = $wrap.attr('data-booking-rooms_max-available-quantity')
            $wrap.attr('data-booking-rooms-quantity-max', max_room_quantity);
            $wrap.find('.room-booking__no-rooms').css('display', 'none');
            $wrap.find('.room-booking__booking-head').css('display', 'block');
            $wrap.find('.persons-quantity').css('display', 'block');
            $wrap.find('.room-booking__booking-free-rooms span').text(max_room_quantity)
        });
    }

    let isClearCartProcessing;
    $('button#clear-booking-cart').on('click', function (e) {
        e.preventDefault();
        if (isClearCartProcessing) return;
        isClearCartProcessing = true;
        let url = 'booking/clear-booking-cart'
        $.ajax({
            type: "POST",
            url: url,
            data: {},
            success: (response) => {
                clearCart();
                isClearCartProcessing = false;
            },
            error: (error) => {
                isClearCartProcessing = false;
            }
        });
    });


    let isOrderCreating;
    $('button#create-order-btn').on('click', function (e) {
        e.preventDefault();
        if (isOrderCreating) return;
        isOrderCreating = true;
        let url = 'order/create';

        if ($('#create-order-btn span').hasClass('__visible')) {
            $('#create-order-btn span').removeClass('__visible').addClass('__invisible');
        }
        if ($('#create-order-btn img').hasClass('__invisible')) {
            $('#create-order-btn img').removeClass('__invisible').addClass('__visible');
        }


        let formData = {
            lastname: $("#booking-ordering-form input[name='lastname']").val(),
            name: $("#booking-ordering-form input[name='name']").val(),
            surname: $("#booking-ordering-form input[name='surname']").val(),
            birth_date: $("#booking-ordering-form input[name='birth_date']").val(),
            country_id: $("#booking-ordering-form select[name='country_id']").val(),
            phone_code_id: $("#booking-ordering-form select[name='phone_code_id']").val(),
            phone: $("#booking-ordering-form input[name='phone']").val(),
            email: $("#booking-ordering-form input[name='email']").val(),
            checkin_at: $("#booking-ordering-form input[name='checkin_at']").val(),
            departure_at: $("#booking-ordering-form input[name='departure_at']").val(),
            payment_type: $("#booking-ordering-form select[name='payment_type']").val(),
            comment: $("#booking-ordering-form textarea[name='comment']").val(),
        }

        let form = {};
        for (let key in formData) {
            if (formData[key]) {
                form[key] = formData[key]
            }
        }


        $.ajax({
            type: "POST",
            url: url,
            data: form,
            success: (response) => {
                if (response.status && response.status === 'error') {
                    for (let key in response.errors) {
                        let selector = 'label#label-' + key;
                        $(selector).toggleClass('_error')
                        $(selector).find('.input__error').text(response.errors[key])
                    }
                } else if (response.error) {
                    $('h5#internal-error-note').text("Ooops... Internal server error!");
                } else if (response.status === 'success') {
                    $(location).attr('href', '/');
                    clearAllValidationErrors()
                }
                isOrderCreating = false;

                if ($('#create-order-btn span').hasClass('__invisible')) {
                    $('#create-order-btn span').removeClass('__invisible').addClass('__visible');
                }
                if ($('#create-order-btn img').hasClass('__visible')) {
                    $('#create-order-btn img').removeClass('__visible').addClass('__invisible');
                }
            },
            error: (error) => {
                $('h2#internal-error-note').text("Ooops... Internal server error!");
                isOrderCreating = false;
                if ($('#create-order-btn span').hasClass('__invisible')) {
                    $('#create-order-btn span').removeClass('__invisible').addClass('__visible');
                }
                if ($('#create-order-btn img').hasClass('__visible')) {
                    $('#create-order-btn img').removeClass('__visible').addClass('__invisible');
                }
            }
        });
    });



    function clearAllValidationErrors() {
        $('#booking-ordering-form label').each(function () {
            $(this).removeClass('_error');
            $(this).find('.input__error').text('')
            $('h2#internal-error-note').text('');
        })
    }


    //CLEAR VALIDATION ERROR ON FOCUS
    $('#booking-ordering-form input').focus(function () {
        let label = $(this).closest('label');
        $(label).removeClass('_error');
        $(label).find('.input__error').text('');
    })

})


$(document).ready(function () {
    var priceFormatter = new Intl.NumberFormat();

    $('[data-booking-item]').each(function (i, wrap) {
        var $wrap = $(wrap);

        setQuantity($wrap);
        setSum($wrap);
        checkMaxRooms($wrap);

        $wrap.on('input', '[data-adult], [data-children]', function (e) {
            setSum($wrap)
        });

        $wrap.on('click', '[data-booking-add-room]', function (e) {
            e.preventDefault();

            var newItem = $wrap.find('[data-booking-room]:first-child').clone();
            newItem.find('[data-adult]').val('1');
            newItem.find('[data-children]').val('0');
            $wrap.find('[data-booking-rooms-list]').append(newItem);
            $wrap.find('[data-booking-room] [data-booking-room-count]').each(function (index, item) {
                $(item).text(index + 1)
            });

            var language = $wrap.find('.rooms-noun').attr('lang');
            var rooms_quantity = parseInt($wrap.find('.room-noun-quantity').text()) + 1;
            $wrap.find('.rooms-noun').text(' ' + vocabulary['номер'][language][getCorrectSpellKey(rooms_quantity)])
            setSum($wrap);
            checkMaxRooms($wrap);
        });

        $wrap.on('click', '[data-booking-delete-room]', function (e) {
            e.preventDefault();

            $(this).closest('[data-booking-room]').remove();
            $wrap.find('[data-booking-room] [data-booking-room-count]').each(function (index, item) {
                $(item).text(index + 1)
            });

            setSum($wrap);
            checkMaxRooms($wrap);
        });

        $wrap.on('input', '[data-adult]', function (e) {
            onAdultInput(this, $wrap)
        });

        $wrap.on('input', '[data-children]', function (e) {
            onChildrenInput(this, $wrap)
        });

        $wrap.on('click', '[data-booking-to-cart]', function (e) {
            e.preventDefault();

            addToCart($wrap)
        });
    });


    function onAdultInput(context, $wrap) {
        var $room = $(context).closest('[data-booking-room]');
        var maxQuantity = parseFloat($wrap.attr('data-booking-persons-quantity-max')) || 1;

        var adultQuantity = parseFloat($(context).val()) || 1;
        var childrenQuantity = (maxQuantity - adultQuantity) || 0;

        $room.find('[data-children]').closest('.quantity').attr('data-quantity-max', childrenQuantity)
    }

    function onChildrenInput(context, $wrap) {
        var $room = $(context).closest('[data-booking-room]');
        var maxQuantity = parseFloat($wrap.attr('data-booking-persons-quantity-max')) || 1;

        var adultQuantity = parseFloat($(context).val()) || 0;
        var childrenQuantity = (maxQuantity - adultQuantity) || 0;

        $room.find('[data-adult]').closest('.quantity').attr('data-quantity-max', childrenQuantity)
    }


    function setSum($wrap) {
        var romsQuantity = $wrap.find('[data-booking-room]').length;
        var priceContainer = $wrap.find('[data-booking-price]');
        var days = parseFloat($wrap.attr('data-booking-days')) || 1;
        var price = parseFloat(priceContainer.attr('data-booking-price')) || 0;
        price = price * romsQuantity * days;
        price = priceFormatter.format(price);
        priceContainer.text(price);
        $wrap.find('[data-booking-roms-quantity]').text(romsQuantity);
    }

    function setQuantity($wrap) {
        var $rooms = $wrap.find('[data-booking-room]');
        var maxQuantity = parseFloat($wrap.attr('data-booking-persons-quantity-max')) || 1;

        $rooms.each(function (index, room) {
            var $room = $(room);
            $room.find('[data-adult]').closest('.quantity').attr('data-quantity-max', maxQuantity);
            $room.find('[data-children]').closest('.quantity').attr('data-quantity-max', maxQuantity - 1);
        });
    }

    function addToCart($wrap) {
        var id = parseFloat($wrap.closest('[data-room-id]').attr('data-room-id'));
        if (!id) {
            return console.warn('Room id not found');
        }
        var $cartValueInput = $('#booking-cart-value');
        var $rooms = $wrap.find('[data-booking-room]');
        var $price = $wrap.find('[data-booking-price]');
        var days = parseFloat($wrap.attr('data-booking-days')) || 1;
        var price = parseFloat($price.attr('data-booking-price')) || 0;

        var cart = $cartValueInput.val();
        if (cart) cart = JSON.parse(cart);
        if (!Array.isArray(cart)) cart = [];

        var one_click_cart = [];
        var quantity = 0;
        $rooms.each(function (index, room) {
            var $room = $(room);
            var adult = parseFloat($room.find('[data-adult]').val()) || 0;
            var children = parseFloat($room.find('[data-children]').val()) || 0;

            var position = {
                id: id,
                days: days,
                pricePerDay: price,
                adult: adult,
                children: children
            }

            cart.push(position);
            one_click_cart.push(position);
            quantity++;
        });

        var free_rooms = $wrap.find('.room-booking__booking-free-rooms span').text();
        var new_free_rooms = parseInt(free_rooms) - quantity;
        $wrap.find('.room-booking__booking-free-rooms span').text(new_free_rooms);

        if(new_free_rooms === 0){
            $wrap.find('.room-booking__no-rooms').css('display', 'block');
            $wrap.find('.room-booking__booking-head').css('display', 'none');
            $wrap.find('.persons-quantity').css('display', 'none');
        }



        if (!cart.length || !one_click_cart.length) return;


        //SENDING REQUEST TO SERVER
        let isAddToCardProcessing;
        if (isAddToCardProcessing) return;
        isAddToCardProcessing = true;

        var $img = $wrap.find('.add-room-to-cart img');
        var $svg = $wrap.find('.add-room-to-cart svg');

        let url = 'booking/add-booking-to-cart'
        if ($svg.hasClass('__visible')) {
            $svg.removeClass('__visible').addClass('__invisible');
        }
        if ($img.hasClass('__invisible')) {
            $img.removeClass('__invisible').addClass('__visible');
        }

        $.ajax({
            type: "POST",
            url: url,
            data: {cart: JSON.stringify(one_click_cart)},
            success: (response) => {
                $cartValueInput.val(JSON.stringify(cart));

                var $cart = $('#booking-fixed-cart');
                var $cartRoomsQuantity = $cart.find('[data-rooms-quantity]');
                var $cartPersonsQuantity = $cart.find('[data-persons-quantity]');
                var $cartPrice = $cart.find('[data-price]');

                var roomsQuantity;
                var personsQuantity;
                var priceSum;

                roomsQuantity = cart.length;

                personsQuantity = cart.reduce((sum, item) => {
                    return sum + item.adult + item.children
                }, 0);

                $cartRoomsQuantity.text(roomsQuantity);
                $cartPersonsQuantity.text(personsQuantity);

                priceSum = cart.reduce((sum, item) => {
                    return sum + item.days * item.pricePerDay
                }, 0);

                priceSum = priceFormatter.format(priceSum);

                $cartPrice.text(priceSum);

                isAddToCardProcessing = false;
                if ($svg.hasClass('__invisible')) {
                    $svg.removeClass('__invisible').addClass('__visible');
                }
                if ($img.hasClass('__visible')) {
                    $img.removeClass('__visible').addClass('__invisible');
                }

                //Correct spell (plural)
                var lang = $wrap.find('.rooms-noun').attr('lang');
                var rooms_quantity = $('span#cart-rooms-quantity-span').text();
                var guests_quantity = $('span#cart-rooms-quests-span').text();
                var nights_quantity = $('span#cart-nights-quantity-span').text();
                $('span#cart-rooms-quantity-noun-span').text(' ' + vocabulary['номер'][lang][getCorrectSpellKey(rooms_quantity)]);
                $('span#cart-rooms-quests-noun-span').text(' ' + vocabulary['гість'][lang][getCorrectSpellKey(guests_quantity)]);
                $('span#cart-nights-quantity-noun-span').text(' ' + vocabulary['ніч'][lang][getCorrectSpellKey(nights_quantity)]);
            },
            error: (error) => {
                if ($svg.hasClass('__invisible')) {
                    $svg.removeClass('__invisible').addClass('__visible');
                }
                if ($img.hasClass('__visible')) {
                    $img.removeClass('__visible').addClass('__invisible');
                }
                isAddToCardProcessing = false;
            }
        });
        // ! SENDING REQUEST TO SERVER
    }

    function checkMaxRooms($wrap) {
        var maxQuantity = parseFloat($wrap.attr('data-booking-rooms-quantity-max')) || 0;
        var roomsQuantity = $wrap.find('[data-booking-room]').length || 0;

        if (maxQuantity === 0) {
            $wrap.find('.room-booking__booking-head, .persons-quantity').hide();
            $wrap.find('.room-booking__no-rooms').show();
        }

        if (roomsQuantity >= maxQuantity) {
            $wrap.find('[data-booking-add-room]').hide();
        } else {
            $wrap.find('[data-booking-add-room]').show();
        }
    }


});
