$(document).ready(function () {
    //1. GLOBAL TOGGLE INPUT (Tampil/Sembunyi Form)
    function handleToggle($el, isInitialLoad = false) {
        const targetId = $el.data("target");
        if (!targetId) return;

        let val = $el.val();
        let isShow = false;

        // Ambil custom trigger dari attribute, jika tidak ada default ke 'yes'
        const customTrigger = $el.data("trigger")
            ? $el.data("trigger").toString().toLowerCase()
            : "yes";
        const globalTriggers = [
            "3pl",
            "lainnya",
            "other",
            "pkp",
            "pma",
            "perorangan",
        ];

        if ($el.is(":checkbox")) {
            isShow = $el.is(":checked");
        } else if ($el.is(":radio")) {
            if ($el.is(":checked")) {
                if ($el.data("trigger")) {
                    isShow = val.toLowerCase() === customTrigger;
                } else {
                    isShow =
                        val.toLowerCase() === "yes" ||
                        globalTriggers.includes(val.toLowerCase());
                }
            } else {
                isShow = false;
            }
        }

        const $target = $(targetId);

        if (isShow) {
            if (isInitialLoad) {
                $target.removeClass("d-none").show();
            } else {
                $target
                    .removeClass("d-none")
                    .hide()
                    .slideDown(250, function () {
                        $(this)
                            .find('input[type="text"], textarea')
                            .first()
                            .focus();
                    });
            }
        } else {
            if (isInitialLoad) {
                $target.addClass("d-none").hide();
            } else {
                $target.slideUp(250, function () {
                    $(this).addClass("d-none");
                    $(this)
                        .find(
                            'input[type="text"], input[type="file"], input[type="number"], textarea',
                        )
                        .val("");
                    $(this).find(".custom-file-label").text("Pilih file...");
                    $(this)
                        .find("select")
                        .val("")
                        .trigger("change.select2")
                        .trigger("change");
                });
            }
        }
    }

    // Listener saat ada perubahan manual
    $(document).on("change", ".toggle-input", function () {
        handleToggle($(this));
    });

    // Jalankan otomatis saat halaman di-load
    $(".toggle-input").each(function () {
        const $el = $(this);
        if ($el.is(":radio")) {
            if ($el.is(":checked")) handleToggle($el, true);
        } else if ($el.is(":checkbox")) {
            handleToggle($el, true);
        } else {
            // Untuk Select
            handleToggle($el, true);
        }
    });

    //2. GLOBAL REPEATER (Tabel Dinamis)

    $(document).on("click", ".btn-add-repeater", function () {
        const targetBody = $(this).data("target-tbody");
        const templateId = $(this).data("template");

        // Gunakan timestamp atau jumlah baris untuk index unik agar tidak bentrok
        const index = $(targetBody + " tr").length + Date.now();

        // Ambil template HTML
        let templateHtml = $(templateId).html();

        // Replace placeholder __INDEX__
        templateHtml = templateHtml.replace(/__INDEX__/g, index);

        const $newRow = $(templateHtml).hide();
        $(targetBody).append($newRow);
        $newRow.fadeIn(300);

        updateRepeaterNumbers(targetBody);
    });

    // Hapus Baris
    $(document).on("click", ".btn-remove-repeater", function () {
        const $tbody = $(this).closest("tbody");
        const tbodyId = "#" + $tbody.attr("id");

        $(this)
            .closest("tr")
            .fadeOut(200, function () {
                $(this).remove();
                updateRepeaterNumbers(tbodyId);
            });
    });

    // Fungsi Update Nomor Urut (Kolom dengan class .row-number)
    function updateRepeaterNumbers(tbodySelector) {
        $(tbodySelector + " tr").each(function (index) {
            $(this)
                .find(".row-number")
                .text(index + 1);
        });
    }
});
