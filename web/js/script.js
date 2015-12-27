jQuery(function () {
    $('.selectlist').selectlist();
    $('.checkbox').checkbox();


    $('.antrag_datum').datetimepicker({
        locale: 'de',
        format: 'L'
    });


    var tagnames = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        /*
         prefetch: {
         url: 'assets/citynames.json',
         filter: function (list) {
         return $.map(list, function (cityname) {
         return {name: cityname};
         });
         }
         }
         */
        local: [
            {"name": "Verkehr"},
            {"name": "Stadtplanung"},
            {"name": "Kultur"},
            {"name": "Soziales"},
            {"name": "Gleichstellung"},
            {"name": "Arbeit"}
        ]
    });
    tagnames.initialize();

    $('.entertags').tagsinput({
        typeaheadjs: {
            name: 'tagnames',
            displayKey: 'name',
            valueKey: 'name',
            source: tagnames.ttAdapter()
        }
    });

    var rebuildList = function () {
        var initiator = $("input[name=filter_initiator]").val(),
            status = $("input[name=filter_status]").val(),
            thema = $("input[name=filter_thema]").val(),
            abgelaufen = $("input[name=filter_abgelaufen]").prop("checked"),
            titel = $("#filter_titel").val();

        $("#antragsliste").find("tbody tr").each(function () {
            var $tr = $(this),
                matchAll = true;
            if ($tr.hasClass("adder-row")) return;
            if (initiator > 0 && !$tr.hasClass("stadtraetin_" + initiator)) matchAll = false;
            if (thema > 0 && !$tr.hasClass("thema_" + thema)) matchAll = false;
            if (status > 0 && !$tr.hasClass("status_" + status)) matchAll = false;
            if (abgelaufen && !$tr.hasClass("abgelaufen")) matchAll = false;
            if (titel != '' && $tr.find("a").text().toLowerCase().indexOf(titel.toLowerCase()) == -1) matchAll = false;

            if (matchAll) {
                $tr.show();
            } else {
                $tr.hide();
            }
        });
    };


    $(".filter_initiator").on("changed.fu.selectlist", rebuildList);
    $(".filter_thema").on("changed.fu.selectlist", rebuildList);
    $(".filter_status").on("changed.fu.selectlist", rebuildList);
    $(".filter_abgelaufen").on("changed.fu.checkbox", rebuildList);
    $("#filter_titel").on("keyup change", rebuildList);


    $('.eintrag_add_button').click(function () {
        $('.adder-row').removeClass('hidden');
        $('.adder-row').find("input").first().focus();
    });
    $('.adder-row .aktion button').click(function () {
        var $adderRow = $('.adder-row'),
            data = {},
            params = {};

        data['titel'] = $adderRow.find("input[name=titel]").val();
        data['tags'] = $adderRow.find(".entertags").val();
        data['notiz'] = $adderRow.find("textarea[name=notiz]").val();
        data['typ'] = $adderRow.find("input[name=typ]").val();
        data['status'] = $adderRow.find("textarea[name=status]").val();
        data['stadtraetinnen'] = [];
        $adderRow.find(".antragstellerin :checked").each(function() {
            data['stadtraetinnen'].push($(this).val());
        });
        var erstellt_am = $adderRow.find("input[name=erstellt_am]").val().split(".");
        if (erstellt_am.length == 3) {
            data['gestellt_am'] = erstellt_am[2] + '-' + erstellt_am[1] + '-' + erstellt_am[0];
        }
        var frist = $adderRow.find("input[name=bearbeitungsfrist]").val().split(".");
        if (frist.length == 3) {
            data['bearbeitungsfrist'] = frist[2] + '-' + frist[1] + '-' + frist[0];
        }

        params['antrag'] = data;
        params[$("head meta[name=csrf-param]").attr("content")] = $("head meta[name=csrf-token]").attr("content");
        console.log(params);
        $.post($adderRow.data("target"), params, function (ret) {
            console.log("Return", ret);
        });
    });


    $('#antragsliste').on('click', '.save-button', function () {
        var $row = $(this).parents("tr").first(),
            data = {},
            params = {};

        data['tags'] = $row.find(".entertags").val();
        data['notiz'] = $row.find("textarea[name=notiz]").val();

        params['antrag'] = data;
        params[$("head meta[name=csrf-param]").attr("content")] = $("head meta[name=csrf-token]").attr("content");

        $.post($row.data("target"), params, function (ret) {
            if (ret['error'] !== undefined) {
                alert(ret['error']);
                return;
            }
            var $newRow = $(ret['content']);
            $row.replaceWith($newRow);

            $newRow.find('.selectlist').selectlist();
            $newRow.find('.checkbox').checkbox();
            $newRow.find('.antrag_datum').datetimepicker({
                locale: 'de',
                format: 'L'
            });
            $newRow.find('.entertags').tagsinput({
                typeaheadjs: {
                    name: 'tagnames',
                    displayKey: 'name',
                    valueKey: 'name',
                    source: tagnames.ttAdapter()
                }
            });

            $newRow.find(".aktion button").addClass("hidden");
            $newRow.find(".aktion .saved").removeClass("hidden");
            window.setTimeout(function () {
                $newRow.find(".aktion button").removeClass("hidden");
                $newRow.find(".aktion .saved").addClass("hidden");
            }, 1000);
        });
    })
});