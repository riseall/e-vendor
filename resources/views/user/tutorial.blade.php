@extends('user.layout.home', ['title' => 'Tutorial'])

@push('styles')
    <style>
        /* ── Design Tokens ──────────────────────────────────────────── */
        :root {
            --tut-ink: #1e1b3a;
            --tut-violet: #6d4aff;
            --tut-violet-deep: #4b2ee0;
            --tut-amber: #ff9f43;
            --tut-amber-deep: #f57c1f;
            --tut-mist: #f5f3ff;
            --tut-paper: #ffffff;
            --tut-line: #e7e3fb;
            --tut-muted: #6b6584;
        }

        .tut-page {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* ── Page intro / eyebrow ─────────────────────────────────────── */
        .tut-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--tut-violet-deep);
            background: var(--tut-mist);
            border: 1px solid var(--tut-line);
            border-radius: 99px;
            padding: .35rem .9rem .35rem .7rem;
        }

        .tut-eyebrow .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--tut-amber);
        }

        .tut-heading {
            font-family: 'Poppins', 'Inter', sans-serif;
            font-weight: 700;
            font-size: clamp(1.6rem, 2.4vw, 2.1rem);
            color: var(--tut-ink);
            letter-spacing: -.01em;
            line-height: 1.2;
        }

        .tut-subheading {
            color: var(--tut-muted);
            font-size: .98rem;
            max-width: 620px;
        }

        /* ── Shared panel shell ───────────────────────────────────────── */
        .tut-panel {
            background: var(--tut-paper);
            border: 1px solid var(--tut-line);
            border-radius: 22px;
            box-shadow: 0 18px 40px -22px rgba(76, 46, 224, .25);
            overflow: hidden;
            height: 100%;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .tut-panel:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 48px -20px rgba(76, 46, 224, .32);
        }

        /* ── Video panel ──────────────────────────────────────────────── */
        .tut-panel-video .tut-panel-head {
            padding: 1.75rem 1.75rem 1.1rem;
        }

        .tut-tag {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--tut-violet-deep);
            background: var(--tut-mist);
            border-radius: 99px;
            padding: .3rem .75rem;
            margin-bottom: .85rem;
        }

        .tut-panel-title {
            font-family: 'Poppins', 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--tut-ink);
            margin-bottom: .5rem;
        }

        .tut-panel-desc {
            color: var(--tut-muted);
            font-size: .92rem;
            line-height: 1.55;
            margin-bottom: 0;
        }

        .tut-video-frame {
            position: relative;
            margin: 0 1.75rem 1.75rem;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px -12px rgba(30, 27, 58, .35);
        }

        .tut-video-frame iframe {
            width: 100%;
            border: 0;
        }

        .tut-video-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, .08);
            pointer-events: none;
        }

        /* ── Manual panel ─────────────────────────────────────────────── */
        .tut-panel-manual {
            background: linear-gradient(165deg, #2a1f6b 0%, #4b2ee0 55%, #6d4aff 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .tut-panel-manual .tut-panel-body {
            padding: 2.25rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        .tut-manual-icon {
            width: 84px;
            height: 84px;
            border-radius: 22px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.4rem;
            backdrop-filter: blur(4px);
        }

        .tut-manual-icon i {
            font-size: 38px;
            color: var(--tut-amber);
        }

        .tut-panel-manual .tut-panel-title {
            color: #fff;
        }

        .tut-panel-manual .tut-panel-desc {
            color: rgba(255, 255, 255, .78);
        }

        .tut-manual-meta {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: 1.1rem 0 1.5rem;
            font-size: .75rem;
            color: rgba(255, 255, 255, .65);
        }

        .tut-manual-meta .sep {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .4);
        }

        /* ── Buttons ──────────────────────────────────────────────────── */
        .tut-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            font-weight: 600;
            font-size: .92rem;
            padding: .72rem 1.2rem;
            border-radius: 12px;
            text-decoration: none;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .tut-btn-amber {
            background: var(--tut-amber);
            color: #2a1500;
            box-shadow: 0 10px 22px -8px rgba(245, 124, 31, .55);
        }

        .tut-btn-amber:hover {
            background: var(--tut-amber-deep);
            color: #2a1500;
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -8px rgba(245, 124, 31, .6);
        }

        .tut-btn-ghost {
            background: rgba(255, 255, 255, .08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .28);
        }

        .tut-btn-ghost:hover {
            background: rgba(255, 255, 255, .16);
            color: #fff;
            transform: translateY(-2px);
        }

        .tut-btn-group {
            display: flex;
            flex-direction: column;
            gap: .65rem;
            width: 100%;
            max-width: 280px;
        }

        /* ── Responsive ───────────────────────────────────────────────── */
        @media (max-width: 991.98px) {

            .tut-panel-video .tut-panel-head,
            .tut-video-frame {
                margin-left: 1.25rem;
                margin-right: 1.25rem;
            }

            .tut-panel-video .tut-panel-head {
                padding-left: 0;
                padding-right: 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container tut-page py-5">

        {{-- ───── Intro ───── --}}
        <div class="row justify-content-center mb-4">
            <div class="col-lg-9 text-center">
                <span class="tut-eyebrow"><span class="dot"></span> Pusat Bantuan E-Vendor</span>
                <h1 class="tut-heading mt-3 mb-2">Belajar prosesnya, pilih caranya sendiri</h1>
                <p class="tut-subheading mx-auto">
                    Tonton video singkat atau baca manual lengkap — keduanya membahas alur yang sama,
                    dari registrasi vendor sampai penagihan invoice.
                </p>
            </div>
        </div>

        {{-- ───── Two Paths ───── --}}
        <div class="row align-items-stretch g-4 mt-2">

            {{-- Path 1: Watch --}}
            <div class="col-lg-7">
                <div class="tut-panel tut-panel-video">
                    <div class="tut-panel-head">
                        <span class="tut-tag"><i class="uil uil-play-circle"></i> Tonton</span>
                        <h2 class="tut-panel-title">Panduan Video</h2>
                        <p class="tut-panel-desc">
                            Walkthrough singkat yang menjelaskan alur pengadaan end-to-end di platform kami,
                            langsung dari layar — cocok kalau kamu lebih suka melihat prosesnya berjalan.
                        </p>
                    </div>
                    <div class="tut-video-frame ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/watch?v=TA4CC9v4DUE" title="E-Vendor Tutorial Video"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            {{-- Path 2: Read --}}
            <div class="col-lg-5">
                <div class="tut-panel tut-panel-manual">
                    <div class="tut-panel-body">
                        <div class="tut-manual-icon">
                            <i class="uil uil-book-open"></i>
                        </div>
                        <h2 class="tut-panel-title">Buku Manual Pengguna</h2>
                        <p class="tut-panel-desc">
                            Lebih suka membaca? Unduh panduan PDF lengkap, langkah demi langkah —
                            dari registrasi sampai penagihan invoice.
                        </p>
                        <div class="tut-manual-meta">
                            <span><i class="uil uil-file-alt"></i> Format PDF</span>
                            <span class="sep"></span>
                            <span>Lengkap &amp; terstruktur</span>
                        </div>

                        <div class="tut-btn-group mt-auto">
                            <a href="{{ asset('files/manual-book.pdf') }}" target="_blank" class="tut-btn tut-btn-amber">
                                <i class="uil uil-eye"></i> Baca Online
                            </a>
                            <a href="{{ asset('files/manual-book.pdf') }}" download class="tut-btn tut-btn-ghost">
                                <i class="uil uil-download-alt"></i> Unduh PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
