jQuery(document).ready(($) => {
    let answerable = $('#department-answerable');
    answerable.select2({
        ajax: {
            url: TKT_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type: 'post',
            timeout: '20000',
            data: function (params) {
                return {
                    action: 'tkt_search_user',
                    term: params.term,
                };
            },
            processResults: function (data) {
                let items = [];
                if (data) {
                    $.each(data, (index, user) => {
                        items.push({
                            id: user[0],
                            text: user[1]
                        });
                    })
                }

                return {
                    results: items
                }
            },
            cache: true
        }
    })

    let searchCreator = $('#tkt-creator-id');
    let searchUser = $('#tkt-user-id');
    searchCreator.select2({
        ajax: {
            url: TKT_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type: 'post',
            timeout: '20000',
            data: function (params) {
                return {
                    action: 'tkt_search_user',
                    term: params.term,
                };
            },
            processResults: function (data) {
                let items = [];
                if (data) {
                    $.each(data, (index, user) => {
                        items.push({
                            id: user[0],
                            text: user[1]
                        });
                    })
                }

                return {
                    results: items
                }
            },
            cache: true
        }
    });
    searchUser.select2({
        ajax: {
            url: TKT_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type: 'post',
            timeout: '20000',
            data: function (params) {
                return {
                    action: 'tkt_search_user',
                    term: params.term,
                };
            },
            processResults: function (data) {
                let items = [];
                if (data) {
                    $.each(data, (index, user) => {
                        items.push({
                            id: user[0],
                            text: user[1]
                        });
                    })
                }

                return {
                    results: items
                }
            },
            cache: true
        }
    })

    $('.tkt-upload-file').click(function () {
        var $this = $(this);
        var file = wp.media({
            multiple: false,
        }).open().on('select', function () {
            var fileURL = file.state().get('selection').first().toJSON().url;
            $this.val(fileURL);
        })

    });

    $('.tkt-edit-date').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.next('input').toggle();
    })

    $('.tkt-toggle-edit').click(function(e){
        e.preventDefault();
        var $this = $(this);
        $this.parentsUntil('#tkt-replies').next('.tkt-editor').slideToggle();

    })
})