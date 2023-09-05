jQuery(document).ready(function ($) {
    $('.tkt-fags h5').click(function (e) {
        let clicked = $(this);
        clicked.next('p').slideToggle();
        clicked.parentsUntil('.tkt-fags').toggleClass('tkt-collapse');
    });

    $('#tkt-parent-department').change(function (e) {
        let clicked = $(this);
        let value = clicked.val();
        if (!value) {
            $('.tkt-child-department').hide();
            $('.tkt-child-department-0').show();
            return false;
        }

        $('.tkt-child-department').hide();
        $(`.tkt-child-department-${value}`).show().prop('selectedIndex', 0);
        $('.tkt-description-wrapper').hide();

    })

    $('.tkt-child-department').change(function () {
        let clicked = $(this);
        let value = clicked.val();

        $('.tkt-description-wrapper').hide();
        $(`.tkt-description-wrapper-${value}`).show();
    })

    $('#tkt-submit-ticket').submit(function (e) {
        e.preventDefault();
        let btn = $('.tkt-submit');
        let loader = btn.find('.tkt-loader');

        btn.prop('disabled', true);
        loader.show();


        let form_data = new FormData();
        form_data.append('action', 'tkt_submit_ticket');
        form_data.append('nonce', TKT_DATA.nonce);
        form_data.append('parent_department', $('#tkt-parent-department').val());
        form_data.append('child_department', $('.tkt-child-department:visible').val());
        form_data.append('title', $('#tkt-title').val());
        form_data.append('priority', $('#tkt-priority').val());
        form_data.append('body', $('#tkt-content').val());
        form_data.append('file', $('#tkt-file').prop('files')[0]);

        // Toast alert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })


        $.ajax({
            type: 'post',
            url: TKT_DATA.ajax_url,
            data: form_data,
            contentType: false,
            processData: false,

            success: function (response) {


                if (response.__success) {
                    Toast.fire({
                        icon: 'success',
                        title: 'تیکت شما با موفقیت ثبت شد'
                    })

                    setTimeout(function () {
                        window.location.href = response.result
                    }, 3000);

                } else {
                    Swal.fire({
                        title: response.result.toString().replace(',', '<br>'),
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    })
                }
            },

            error: function (error) {

            },

            beforeSend: function () {
                btn.prop('disabled', true);
                loader.show();
            },

            complete: function () {
                btn.prop('disabled', false);
                loader.hide();

            }
        })
    })

    $('#tkt-submit-ticket-reply').submit(function (e) {
        e.preventDefault();

        let btn = $('.tkt-submit');
        let loader = btn.find('.tkt-loader');

        let form_data = new FormData();
        form_data.append('action', 'tkt_submit_reply');
        form_data.append('nonce', TKT_DATA.nonce);
        form_data.append('ticket_id', $('#tkt-ticket-id').val());
        form_data.append('body', $('#tkt-content').val());
        form_data.append('file', $('#tkt-file').prop('files')[0]);
        form_data.append('status', $('#tkt-status').is(':checked') ? $('#tkt-status').val() : '');

        // Toast alert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })


        $.ajax({
            type: 'post',
            url: TKT_DATA.ajax_url,
            data: form_data,
            contentType: false,
            processData: false,

            success: function (response) {




                if (response.__success) {

                    $('.tkt-replies').html(response.replies_html);
                    $('.tkt-widget-status .tkt-status').remove();
                    $('.tkt-widget-status').prepend(response.__status);

                    Toast.fire({
                        icon: 'success',
                        title: 'پاسخ شما با موفقیت ثبت شد '
                    })
                }else {
                    Toast.fire({
                        icon: 'error',
                        title:  response.result
                    })
                }
            },

            error: function () {

            },

            beforeSend: function () {
                btn.prop('disabled', true);
                loader.show();
            },

            complete: function () {
                btn.prop('disabled', false);
                loader.hide();
                $('#tkt-content').val('');
            }
        })

    })

})