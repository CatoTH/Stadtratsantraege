jQuery(function () {
    var $antragsliste = $("#antragsliste"),
        $adderRow = $('.adder-row');

    $('.selectlist').selectlist();
    $('.checkbox-custom').checkbox();


    $('.antrag_datum').datetimepicker({
        locale: 'de',
        format: 'L'
    });

    /*
    var tagnames = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
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
     */


    var scrolled = false,
        onScrollFunc = function () {
            var $lis = $antragsliste.find("> li:visible"),
                foundVisible = false,
                foundInvisibleAfterVisible = false;
            for (var i = 0; i < $lis.length && !foundInvisibleAfterVisible; i++) {
                var $li = $($lis[i]),
                    visible = $li.is(":in-viewport");
                if (visible) {
                    foundVisible = true;
                } else if (foundVisible) {
                    foundInvisibleAfterVisible = true;
                }
                /*
                if (!$li.data("entertags_inited")) {
                    $li.data("entertags_inited", "1");
                    $li.find('.entertags').tagsinput({
                        typeaheadjs: {
                            name: 'tagnames',
                            displayKey: 'name',
                            valueKey: 'name',
                            source: tagnames.ttAdapter()
                        }
                    });
                }
                 */
            }

        };
    window.setInterval(function () {
        if (scrolled) {
            scrolled = false;
            onScrollFunc();
        }
    }, 100);
    $(window).scroll(function () {
        scrolled = true;
    });
    onScrollFunc();

    var rebuildList = function () {
        var initiator = $("input[name=filter_initiator]").val(),
            status = $("input[name=filter_status]").val(),
            typ = $("input[name=filter_typ]").val(),
            thema = $("input[name=filter_thema]").val(),
            abgelaufen = $("input[name=filter_abgelaufen]").prop("checked"),
            titel = $("#filter_titel").val();

        console.log(thema, initiator, status, abgelaufen, typ, titel);

        $("#antragsliste").find("> li").each(function () {
            var $li = $(this),
                matchAll = true;
            if ($li.hasClass("adder-row")) return;
            if (initiator >= 0 && !$li.hasClass("stadtraetin_" + initiator)) matchAll = false;
            if (thema >= 0 && !$li.hasClass("tag_" + thema)) matchAll = false;
            if (status >= 0 && !$li.hasClass("status_" + status)) matchAll = false;
            if (abgelaufen && !$li.hasClass("abgelaufen")) matchAll = false;
            if (typ >= 0 && !$li.hasClass("typ_" + typ)) matchAll = false;
            if (titel !== '' && $li.find("a").text().toLowerCase().indexOf(titel.toLowerCase()) === -1) matchAll = false;

            if (matchAll) {
                $li.show();
            } else {
                $li.hide();
            }
        });
    };


    $(".filter_initiator").on("changed.fu.selectlist", rebuildList);
    $(".filter_thema").on("changed.fu.selectlist", rebuildList);
    $(".filter_typ").on("changed.fu.selectlist", rebuildList);
    $(".filter_status").on("changed.fu.selectlist", rebuildList);
    $(".filter_abgelaufen").on("changed.fu.checkbox", rebuildList);
    $("#filter_titel").on("keyup change", rebuildList);

    $(document).on("change", ".tagsList .neu select", function() {
        const $select = $(this);
        const selected = $select.val();
        if (selected !== "") {
            const $newLi = $("<li class='tag'><span class='name'></span><a href='#' class='delete'>🗑</a></li>");
            $newLi.find(".name").text(selected);
            $newLi.data("tag", selected);
            $newLi.insertBefore($select.parents(".neu"));
            this.selectedIndex = 0;
        }
    });
    $(document).on("click", ".tagsList .delete", function(ev) {
        ev.preventDefault();
        ev.stopPropagation();
        $(this).parents("li").first().remove();
    });

    $('.eintrag_add_button').click(function () {
        $adderRow.removeClass('hidden');
        $adderRow.find("input").first().focus();
    });
    $('.adder-row .aktionCol button').click(function (ev) {
        ev.stopPropagation();
        ev.preventDefault();

        var $adderRow = $('.adder-row'),
            data = {},
            params = {};

        data['titel'] = $adderRow.find("input[name=titel]").val();

        data['tags'] = [];
        $adderRow.find(".tagsList li.tag").each(function() {
            data['tags'].push($(this).data("tag"));
        });

        data['notiz'] = $adderRow.find("textarea[name=notiz]").val();
        data['typ'] = $adderRow.find("input[name=typ]").val();
        data['status'] = $adderRow.find("input[name=status]").val();
        data['stadtraetinnen'] = [];
        $adderRow.find(".antragstellerin :checked").each(function () {
            data['stadtraetinnen'].push($(this).val());
        });
        var gestellt_am = $adderRow.find("input[name=gestellt_am]").val().split(".");
        if (gestellt_am.length === 3) {
            data['gestellt_am'] = gestellt_am[2] + '-' + gestellt_am[1] + '-' + gestellt_am[0];
        }
        var frist = $adderRow.find("input[name=bearbeitungsfrist]").val().split(".");
        if (frist.length === 3) {
            data['bearbeitungsfrist'] = frist[2] + '-' + frist[1] + '-' + frist[0];
        }

        params['antrag'] = data;
        params[$("head meta[name=csrf-param]").attr("content")] = $("head meta[name=csrf-token]").attr("content");
        $.post($adderRow.data("target"), params, function (ret) {
            $adderRow.after(ret['content']);
            $adderRow.hide();

            $adderRow.find("input[name=titel]").val("");
            $adderRow.find(".entertags").val("");
            $adderRow.find("textarea[name=notiz]").val("");
            $adderRow.find("input[name=status]").val("");
        });
    });


    $antragsliste.on('click', '.save-button', function () {
        var $row = $(this).parents("li").first(),
            data = {},
            params = {};

        data['tags'] = [];
        $row.find(".tagsList li.tag").each(function() {
            data['tags'].push($(this).data("tag"));
        });

        data['notiz'] = $row.find("textarea[name=notiz]").val();
        data['abgeschlossen'] = ($row.find("input[name=abgeschlossen]").prop("checked") ? 1 : 0);

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
            /*
            $newRow.find('.entertags').tagsinput({
                typeaheadjs: {
                    name: 'tagnames',
                    displayKey: 'name',
                    valueKey: 'name',
                    source: tagnames.ttAdapter()
                }
            });
             */

            $newRow.find(".aktionCol button").addClass("hidden");
            $newRow.find(".aktionCol .saved").removeClass("hidden");
            window.setTimeout(function () {
                $newRow.find(".aktionCol button").removeClass("hidden");
                $newRow.find(".aktionCol .saved").addClass("hidden");
            }, 1000);
        });
    });

    $antragsliste.on('click', '.del-button', function () {
        if (!window.confirm("Diesen Antrag wirklich löschen?")) {
            return;
        }
        var $row = $(this).parents("li").first(),
            params = {};
        params['antrag_id'] = $row.data("antrag-id");
        params[$("head meta[name=csrf-param]").attr("content")] = $("head meta[name=csrf-token]").attr("content");
        $.post($(this).data("target"), params, function (ret) {
            if (ret['success']) {
                $row.remove();
            } else {
                alert(ret['error']);
            }
        });
    });
});
