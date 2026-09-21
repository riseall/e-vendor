var __ = window.__ || function (key) { return key; };

var fixedStepsBefore = [
    {
        id: "step-1",
        icon: "fas fa-th-large",
        title: __("category"),
    },
    {
        id: "step-2",
        icon: "fas fa-building",
        title: __("general_info"),
    },
    {
        id: "step-3",
        icon: "fas fa-wallet",
        title: __("payment"),
    },
    {
        id: "step-4",
        icon: "fas fa-award",
        title: __("commitment"),
    },
    {
        id: "step-5",
        icon: "fas fa-clipboard-list",
        title: __("other_info"),
    },
    {
        id: "step-6",
        icon: "fas fa-map-marker-alt",
        title: __("local_vendor_special"),
    },
    {
        id: "step-7",
        icon: "fas fa-boxes",
        title: __("product"),
    },
    {
        id: "step-8",
        icon: "fas fa-file-upload",
        title: __("document"),
    },
];

// Dynamic steps — muncul sesuai kategori yang dipilih
var categorySteps = {
    1: {
        id: "cat-1",
        icon: "flaticon2-box-1",
        title: __("raw_material"),
        dynamic: true,
    },
    2: {
        id: "cat-2",
        icon: "flaticon2-settings",
        title: __("technical_varia"),
        dynamic: true,
    },
    3: {
        id: "cat-3",
        icon: "fas fa-shipping-fast",
        title: __("transporter"),
        dynamic: true,
    },
    4: {
        id: "cat-4",
        icon: "fas fa-city",
        title: __("contractor"),
        dynamic: true,
    },
    5: {
        id: "cat-5",
        icon: "flaticon2-analytics",
        title: __("testing"),
        dynamic: true,
    },
    6: {
        id: "cat-6",
        icon: "fas fa-clinic-medical",
        title: __("facility"),
        dynamic: true,
    },
    7: {
        id: "cat-7",
        icon: "fas fa-people-carry",
        title: __("training"),
        dynamic: true,
    },
    8: {
        id: "cat-8",
        icon: "fas fa-bullhorn",
        title: __("advertising"),
        dynamic: true,
    },
};

// ============================================
// STATE
// ============================================
var activeSteps = [];
var currentIndex = 0;

// ============================================
// BUILD ACTIVE STEPS
// ============================================
function buildActiveSteps() {
    var currentStepId = activeSteps[currentIndex]
        ? activeSteps[currentIndex].id
        : null;
    var hasProductCategory = $(".category-checkbox:checked[value='1']").length > 0;
    var dynamicSteps = [];
    $(".category-checkbox:checked").each(function () {
        var id = parseInt($(this).val());
        if (categorySteps[id]) dynamicSteps.push(categorySteps[id]);
    });

    var fixedSteps = fixedStepsBefore.filter(function (step) {
        return step.id !== "step-7" || hasProductCategory;
    });

    activeSteps = fixedSteps.concat(dynamicSteps);

    if (currentStepId) {
        var nextIndex = activeSteps.findIndex(function (step) {
            return step.id === currentStepId;
        });
        currentIndex = nextIndex >= 0 ? nextIndex : Math.min(currentIndex, activeSteps.length - 1);
    }
}

// ============================================
// RENDER NAV
// ============================================
function renderNav() {
    var html = "";
    activeSteps.forEach(function (step, i) {
        var isDone = i < currentIndex;
        var isActive = i === currentIndex;
        var dynClass = step.dynamic ? " dynamic" : "";
        var stateClass = isActive ? " active" : isDone ? " done" : "";

        html +=
            '<div class="wz-nav-item' +
            dynClass +
            stateClass +
            '" data-index="' +
            i +
            '" style="cursor: pointer;">';
        html += '  <div class="wz-nav-step">';
        html +=
            '    <div class="wz-nav-icon"><i class="' +
            step.icon +
            '"></i></div>';
        html += '    <div class="wz-nav-label">' + step.title + "</div>";
        html += "  </div>";
        html += "</div>";

        if (i < activeSteps.length - 1) {
            html +=
                '<div class="wz-nav-line' +
                (isDone ? " done" : "") +
                '"></div>';
        }
    });
    $("#wzNav").html(html);
}

// ============================================
// RENDER CONTENT
// Sembunyikan semua, tampilkan step aktif
// ============================================
function renderContent() {
    $("[data-step-id]").hide();
    var stepId = activeSteps[currentIndex].id;
    $('[data-step-id="' + stepId + '"]').show();
    if (stepId === "step-7" && typeof window.refreshProductTable === "function") {
        window.refreshProductTable();
    }

    $("#current_step").val(currentIndex + 1);
    $("#btnPrev").toggle(currentIndex > 0);
    $("#btnNext").toggle(currentIndex < activeSteps.length - 1);
    $("#btnSubmit").toggle(currentIndex === activeSteps.length - 1);
}

// ============================================
// INIT
// ============================================
function init() {
    buildActiveSteps();
    renderNav();
    renderContent();
}

init();

// ============================================
// CLICK NAVIGATION
// ============================================
$("#wzNav").on("click", ".wz-nav-item", function () {
    var targetIndex = $(this).data("index");

    // Jika klik ke step yang sama, abaikan
    if (targetIndex === currentIndex) return;

    // Jika step-1 belum selesai (kategori belum dipilih), hanya izinkan navigasi di fixed steps
    if (currentIndex === 0 && targetIndex > 0) {
        // Cek apakah kategori sudah dipilih
        if ($(".category-checkbox:checked").length === 0) {
            Swal.fire({
                title: __("select_category"),
                text: __("select_at_least_one_category"),
                icon: "warning",
                confirmButtonText: __("ok"),
            });
            return;
        }
        // Rebuild activeSteps karena kategori sudah dipilih
        buildActiveSteps();
    }

    // Set currentIndex ke target
    currentIndex = targetIndex;
    renderNav();
    renderContent();
});

// ============================================
// NAVIGASI
// ============================================
$("#btnNext").on("click", function () {
    // Validasi step kategori
    if (activeSteps[currentIndex].id === "step-1") {
        if ($(".category-checkbox:checked").length === 0) {
            Swal.fire({
                title: __("select_category"),
                text: __("select_at_least_one_category"),
                icon: "warning",
                confirmButtonText: __("ok"),
            });
            return;
        }
        $("#errKategori").addClass("d-none");
        // Rebuild karena user baru konfirmasi pilihan kategori
        buildActiveSteps();
    }
    if (currentIndex < activeSteps.length - 1) {
        currentIndex++;
        renderNav();
        renderContent();
    }
});

$("#btnPrev").on("click", function () {
    if (currentIndex > 0) {
        currentIndex--;
        renderNav();
        renderContent();
    }
});
