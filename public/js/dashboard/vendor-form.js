$(document).ready(function () {
    function getMaxFileSize($input) {
        const inputLimit = Number($input.data("max-file-kb"));
        const formLimit = Number($input.closest("form").data("max-file-kb"));
        const maxFileKb = inputLimit || formLimit;

        return maxFileKb > 0 ? maxFileKb * 1024 : null;
    }

    function formatFileSize(bytes) {
        return bytes >= 1024 * 1024
            ? (bytes / (1024 * 1024)).toFixed(0) + " MB"
            : Math.ceil(bytes / 1024) + " KB";
    }

    function getFileFeedbackContainer($input) {
        const $customFile = $input.closest(".custom-file");

        if ($customFile.length) {
            return $customFile;
        }

        const $uploadButton = $input.closest(".btn-upload");

        return $uploadButton.length ? $uploadButton.parent() : $input.parent();
    }

    function clearFileSizeError($input) {
        $input.removeClass("is-invalid");
        $input.closest(".custom-file").find(".custom-file-label").removeClass("border-danger");
        getFileFeedbackContainer($input)
            .find(".file-size-feedback")
            .remove();
    }

    function showFileSizeError($input, maxBytes) {
        const message =
            "Ukuran file maksimal " + formatFileSize(maxBytes) + ".";
        const $container = getFileFeedbackContainer($input);

        clearFileSizeError($input);
        $input.addClass("is-invalid");
        $input
            .closest(".custom-file")
            .find(".custom-file-label")
            .addClass("border-danger")
            .text("Pilih file...");
        $container.append(
            $("<div>", {
                class: "invalid-feedback d-block file-size-feedback",
                text: message,
            }),
        );

        Swal.fire({
            icon: "error",
            title: "File Terlalu Besar",
            text: message,
        });
    }

    $(document).on("change.vendorFileSize", 'input[type="file"]', function (event) {
        const $input = $(this);
        const file = this.files && this.files[0];
        const maxBytes = getMaxFileSize($input);

        clearFileSizeError($input);

        if (!file || !maxBytes || file.size <= maxBytes) {
            return;
        }

        event.stopImmediatePropagation();
        $input.val("");
        showFileSizeError($input, maxBytes);
    });

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
        
        // Initialize datepickers if any exist in the new row
        if ($.fn.datepicker) {
            $newRow.find('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                orientation: 'top left'
            });
        }

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

    // Initialize global datepickers on load
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            orientation: 'top left'
        });
    }
});
