@extends('user.layout.home', [
    'title' => 'Hubungi Kami',
    'subtitle' => 'Layanan bantuan dan informasi kemitraan vendor PT Phapros Tbk.'
])

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 text-center features feature-primary feature-clean">
                <div class="icons text-center mx-auto">
                    <i class="fas fa-phone-alt rounded h3 mb-0"></i>
                </div>
                <div class="content mt-4">
                    <h5 class="title font-weight-bold">Telepon</h5>
                    <p class="text-muted">Layanan informasi pengadaan dan permohonan vendor</p>
                    <a href="tel:+62247607330" class="read-more font-weight-bold">+62 24 7607330</a>
                </div>
            </div>
        </div><!--end col-->

        <div class="col-md-4 mt-4 mt-sm-0 pt-2 pt-sm-0">
            <div class="card border-0 text-center features feature-primary feature-clean">
                <div class="icons text-center mx-auto">
                    <i class="fas fa-envelope rounded h3 mb-0"></i>
                </div>
                <div class="content mt-4">
                    <h5 class="title font-weight-bold">Email</h5>
                    <p class="text-muted">Kirimkan pertanyaan atau informasi kelengkapan berkas</p>
                    <a href="mailto:procurement@phapros.co.id" class="read-more font-weight-bold">procurement@phapros.co.id</a>
                </div>
            </div>
        </div><!--end col-->

        <div class="col-md-4 mt-4 mt-sm-0 pt-2 pt-sm-0">
            <div class="card border-0 text-center features feature-primary feature-clean">
                <div class="icons text-center mx-auto">
                    <i class="fas fa-map-marker-alt rounded h3 mb-0"></i>
                </div>
                <div class="content mt-4">
                    <h5 class="title font-weight-bold">Kantor &amp; Pabrik</h5>
                    <p class="text-muted">Jl. Simongan No. 131, Bongsari, Semarang Barat, <br>Kota Semarang, Jawa Tengah 50148</p>
                    <a href="https://maps.google.com/?q=PT+Phapros+Tbk+Semarang" target="_blank" class="read-more font-weight-bold">Lihat di Google Maps</a>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->

    <div class="row mt-5 pt-2">
        <div class="col-12">
            <div class="card map border-0 rounded overflow-hidden shadow">
                <div class="card-body p-0">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.2078693457193!2d110.3905542!3d-6.9847721!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b49339e7631%3A0x7d61184ffbe328ea!2sPT%20Phapros%20Tbk!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        style="border:0" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->
@endsection
