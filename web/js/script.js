jQuery(function () {
    $('.pillbox').pillbox();
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
            if (initiator > 0 && !$tr.hasClass("initiator_" + initiator)) matchAll = false;
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
    $('.adder-row .aktion button').click(function() {
        var $adderRow = $('.adder-row'),
            data = {},
            params = {};
        data['titel'] = $adderRow.find("input[name=titel]").val();

        params['antrag'] = data;
        params[$("head meta[name=csrf-param]").attr("content")] = $("head meta[name=csrf-token]").attr("content");
        console.log(params);
        $.post($adderRow.data("target"), params, function(ret) {
            console.log("Return", ret);
        });
    });

});