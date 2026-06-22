<?php
// index.php  –  halaman utama (menggantikan index.html)
// Semua HTML/CSS/JS asli dipertahankan; hanya data yang dimuat dari DB

session_start();
require_once __DIR__ . '/includes/config.php';

$pdo = getDB();
$cfg = getPengaturan();

// Ambil produk aktif, urut sesuai kolom urutan
$stmt = $pdo->query("SELECT * FROM produk WHERE aktif = TRUE ORDER BY urutan ASC");
$produkList = $stmt->fetchAll();

// Proses form kontak / tanya produk (POST)
$msgSuccess = '';
$msgError   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_kontak'])) {
    $nama     = trim($_POST['nama']     ?? '');
    $no_hp    = trim($_POST['no_hp']    ?? '');
    $email    = trim($_POST['email']    ?? '');
    $pesan    = trim($_POST['pesan']    ?? '');
    $produkId = !empty($_POST['produk_id']) ? (int)$_POST['produk_id'] : null;

    if ($nama === '' || $pesan === '') {
        $msgError = 'Nama dan pesan wajib diisi.';
    } else {
        $ins = $pdo->prepare("INSERT INTO pesan_kontak (nama, no_hp, email, pesan, produk_id) VALUES (?,?,?,?,?)");
        $ins->execute([$nama, $no_hp, $email, $pesan, $produkId]);
        $msgSuccess = 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.';
    }
}
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <title><?= htmlspecialchars($cfg['nama_perusahaan'] ?? 'PT Cipta Pesona Teknik') ?> - Landing Page</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="author" content="Masmut Dev">
        <meta name="viewport" content="width=device-width,initial-scale=1.0" />
        <meta name="description" content="<?= htmlspecialchars($cfg['meta_description'] ?? '') ?>">
        <!-- favicon icon -->
        <link rel="shortcut icon" href="assets/images/logo.png">
        <link rel="apple-touch-icon" href="assets/images/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/images/apple-touch-icon-114x114.png">
        <!-- google fonts preconnect -->
        <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- style sheets and font icons  -->
        <link rel="stylesheet" href="assets/css/vendors.min.css"/>
        <link rel="stylesheet" href="assets/css/icon.min.css"/>
        <link rel="stylesheet" href="assets/css/style.css"/>
        <link rel="stylesheet" href="assets/css/responsive.css"/>
        <link rel="stylesheet" href="assets/custom/hotel-and-resort.css" />
    </head>
    <body data-mobile-nav-style="classic">
        <!-- start header -->
        <header>
            <nav class="navbar navbar-expand-lg header-light bg-white center-logo header-reverse">
                <div class="container-fluid">
                    <div class="col-auto col-xl-2 col-lg-1 menu-logo">
                        <div class="d-none d-xl-block">
                            <a href="" class="mb-0 pb-0 fw-bold text-dark fs-20"><?= htmlspecialchars($cfg['nama_perusahaan'] ?? 'PT Cahaya Teknik') ?></a>
                        </div> 
                        <a class="navbar-brand" href="">
                            <img src="assets/images/logo.png" data-at2x="assets/images/logo@2x.png" alt="" class="default-logo">
                            <img src="assets/images/logo.png" data-at2x="assets/images/logo@2x.png" alt="" class="alt-logo">
                            <img src="assets/images/logo.png" data-at2x="assets/images/logo@2x.png" alt="" class="mobile-logo">
                        </a>   
                    </div>
                    <div class="col-auto col-xl-8 col-lg-10 menu-order">
                        <button class="navbar-toggler float-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
                            <span class="navbar-toggler-line"></span>
                            <span class="navbar-toggler-line"></span>
                            <span class="navbar-toggler-line"></span>
                            <span class="navbar-toggler-line"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">  
                            <ul class="navbar-nav navbar-left justify-content-end"> 
                                <li class="nav-item"><a href="" class="nav-link">Home</a></li>
                            </ul>
                            <ul class="navbar-nav navbar-right justify-content-start"> 
                                <li class="nav-item"><a href="#produk" class="nav-link">Produk</a></li>
                                <li class="nav-item"><a href="#kontak" class="nav-link">Kontak</a></li>
                            </ul> 
                        </div>
                    </div> 
                    <div class="col-auto col-xl-2 col-lg-1 text-end">
                        <div class="d-none d-xl-flex align-items-center widget-text fw-600">
                            <i class="bi bi-whatsapp text-base-color me-10px"></i>
                            <a href="https://wa.me/<?= htmlspecialchars($cfg['no_whatsapp'] ?? '6285774918809') ?>">Pesan Sekarang</a>
                        </div>  
                    </div>
                </div>
            </nav>
        </header>
        <!-- end header -->

        <!-- start banner slider -->
        <section class="p-0 top-space-margin full-screen md-h-600px sm-h-500px border-top border-4 border-color-base-color position-relative" data-parallax-background-ratio="0.3" style="background-image: url('<?= htmlspecialchars(urlBanner($cfg['banner_url'] ?? null)) ?>')">
            <div class="opacity-light bg-black"></div>
            <div class="container h-100 position-relative">
                <div class="row align-items-center h-100 justify-content-center">
                    <div class="col-md-10 position-relative text-white d-flex flex-column justify-content-center text-center h-100" data-anime='{ "el": "childs", "translateY": [-15, 0], "perspective": [1200,1200], "scale": [1.1, 1], "rotateX": [50, 0], "opacity": [0,1], "duration": 600, "delay": 100, "staggervalue": 300, "easing": "easeOutQuad" }'> 
                        <h5 class="alt-font fw-400 mb-20px text-shadow-double-large"></h5> 
                        <div class="fs-100 lg-fs-100 md-fs-120 sm-fs-100 xs-fs-60 fw-700 mb-20px ls-minus-8px md-ls-minus-4px xs-ls-minus-2px text-shadow-double-large"><?= htmlspecialchars($cfg['nama_perusahaan'] ?? 'PT CIPTA PESONA TEKNIK') ?></div>
                        <div class="mb-30px"> 
                            <a href="https://wa.me/<?= htmlspecialchars($cfg['no_whatsapp'] ?? '6285774918809') ?>" class="btn btn-extra-large btn-switch-text btn-white fw-700 btn-round-edge btn-box-shadow">
                                <span>
                                    <span class="btn-double-text" data-text="Whatsapp">Pesan Sekarang</span>
                                    <span><i class="fa-solid fa-arrow-right fs-14"></i></span>
                                </span>
                            </a>
                        </div> 
                        <div class="position-absolute sm-position-relative bottom-80px lg-bottom-50px sm-bottom-0px left-0px right-0px d-flex justify-content-center align-items-center">
                            <div class="fs-22 fw-500">"<?= htmlspecialchars($cfg['tagline'] ?? '') ?>" </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end banner slider --> 

        <!-- start section fitur -->
        <section class="bg-very-light-gray half-section ps-6 pe-6">
            <div class="container-fluid"> 
                <div class="row row-cols-1 row-cols-lg-4 row-cols-md-2 justify-content-center" data-anime='{ "el": "childs", "translateX": [-15, 0], "opacity": [0,1], "duration": 600, "delay": 0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                    <div class="col icon-with-text-style-10 border-end border-1 sm-border-end-0 border-color-transparent-base-color md-mb-50px">
                        <div class="feature-box ps-8 pe-8 xl-ps-5 xl-pe-5">
                            <div class="feature-box-icon feature-box-icon-rounded w-120px h-120px rounded-circle mb-20px">
                                <i class="line-icon-Medal-2 icon-extra-large text-base-color"></i>
                            </div>
                            <div class="feature-box-content last-paragraph-no-margin">
                                <span class="alt-font text-dark-gray fs-22 ls-0px">High Quality</span>
                            </div>
                        </div>
                    </div>
                    <div class="col icon-with-text-style-10 border-end border-1 md-border-end-0 border-color-transparent-base-color md-mb-50px">
                        <div class="feature-box ps-8 pe-8 xl-ps-5 xl-pe-5">
                            <div class="feature-box-icon feature-box-icon-rounded w-120px h-120px rounded-circle mb-20px">
                                <i class="line-icon-Moustache-Smiley icon-extra-large text-base-color"></i>
                            </div>
                            <div class="feature-box-content last-paragraph-no-margin">
                                <span class="alt-font text-dark-gray fs-22 ls-0px">Best Price</span>
                            </div>
                        </div>
                    </div>
                    <div class="col icon-with-text-style-10 border-end border-1 sm-border-end-0 border-color-transparent-base-color md-mb-50px">
                        <div class="feature-box ps-8 pe-8 xl-ps-5 xl-pe-5">
                            <div class="feature-box-icon feature-box-icon-rounded w-120px h-120px rounded-circle mb-20px">
                                <i class="line-icon-Ship icon-extra-large text-base-color"></i>
                            </div>
                            <div class="feature-box-content last-paragraph-no-margin">
                                <span class="alt-font text-dark-gray fs-22 ls-0px">Fast Delivery</span>
                            </div>
                        </div>
                    </div>
                    <div class="col icon-with-text-style-10">
                        <div class="feature-box ps-8 pe-8 xl-ps-5 xl-pe-5">
                            <div class="feature-box-icon feature-box-icon-rounded w-120px h-120px rounded-circle mb-20px">
                                <i class="line-icon-Life-Safer icon-extra-large text-base-color"></i>
                            </div>
                            <div class="feature-box-content last-paragraph-no-margin">
                                <span class="alt-font text-dark-gray fs-22 ls-0px">Fashionable</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end section fitur -->

        <!-- start section produk -->
        <section id="produk" class="big-section bg-gradient-very-light-gray ps-7 pe-7 xxl-ps-3 xxl-pe-3 xs-px-0">
            <div class="container-fluid">
                <div class="row justify-content-center mb-2">
                    <div class="col-xl-5 col-lg-7 col-md-8 text-center" data-anime='{ "opacity": [0,1], "duration": 800, "delay": 0, "staggervalue": 300, "easing": "easeOutQuad" }'>
                        <span class="fw-600 ls-1px fs-16 alt-font d-inline-block text-uppercase mb-5px text-base-color">New Article</span>
                        <h2 class="text-dark-gray fw-700 ls-minus-2px">Product</h2>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-xl-4 row-cols-md-2 row-cols-sm-2 justify-content-center" data-anime='{ "el": "childs", "translateX": [30, 0], "opacity": [0,1], "duration": 800, "delay": 200, "staggervalue": 300, "easing": "easeOutQuad" }'>
                    <?php foreach ($produkList as $p): 
                        $gambarPath = urlGambarProduk($p['gambar']);
                        $waMsg = urlencode('Halo, saya ingin bertanya tentang produk: ' . $p['nama']);
                        $waLink = 'https://wa.me/' . ($cfg['no_whatsapp'] ?? '6285774918809') . '?text=' . $waMsg;
                    ?>
                    <!-- start interactive banner item -->
                    <div class="col interactive-banner-style-05 lg-mb-30px position-relative z-index-1">
                        <div class="atropos" data-atropos data-atropos-perspective="1450">
                            <a href="<?= $waLink ?>" target="_blank" class="position-absolute z-index-1 top-0px left-0px h-100 w-100"></a>
                            <div class="atropos-scale">
                                <div class="atropos-rotate">
                                    <div class="atropos-inner">
                                        <figure class="m-0 hover-box border-radius-8px overflow-hidden position-relative" data-atropos-offset="3">
                                            <img class="w-100" src="<?= htmlspecialchars($gambarPath) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" />
                                            <figcaption class="d-flex flex-column align-items-start justify-content-center position-absolute left-0px top-0px w-100 h-100 z-index-1 p-15 xl-p-12 last-paragraph-no-margin">
                                                <div class="mb-auto bg-base-color fw-500 text-white text-uppercase border-radius-30px ps-20px pe-20px fs-13"><?= formatRupiah((float)$p['harga']) ?></div>
                                                <div class="w-100 text-white fw-600 fs-16 mt-auto"><?= htmlspecialchars($p['nama']) ?></div>
                                                <?php if (!empty($p['deskripsi'])): ?>
                                                <div class="text-white fs-13 opacity-8"><?= htmlspecialchars($p['deskripsi']) ?></div>
                                                <?php endif; ?>
                                                <div class="position-absolute left-0px top-0px w-100 h-100 bg-gradient-gray-light-dark-transparent z-index-minus-1 opacity-9"></div>
                                                <div class="box-overlay bg-gradient-gray-light-dark-transparent z-index-minus-1"></div>
                                            </figcaption>
                                        </figure>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <!-- end interactive banner item --> 
                    <?php endforeach; ?>
                </div> 
            </div> 
        </section>
        <!-- end section produk -->

        <!-- start section kontak -->
        <section id="kontak" class="bg-very-light-gray half-section">
            <div class="container">
                <div class="row justify-content-center mb-4">
                    <div class="col-xl-5 col-lg-7 col-md-8 text-center">
                        <span class="fw-600 ls-1px fs-16 alt-font d-inline-block text-uppercase mb-5px text-base-color">Hubungi Kami</span>
                        <h2 class="text-dark-gray fw-700 ls-minus-2px">Tanya / Pesan Produk</h2>
                    </div>
                </div>
                <?php if ($msgSuccess): ?>
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-6">
                        <div class="alert alert-success text-center"><?= htmlspecialchars($msgSuccess) ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($msgError): ?>
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-6">
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($msgError) ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <form method="post" action="#kontak">
                            <input type="hidden" name="form_kontak" value="1">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="nama" placeholder="Nama Anda *" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="no_hp" placeholder="No. HP / WhatsApp">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email (opsional)">
                            </div>
                            <div class="mb-3">
                                <select class="form-control" name="produk_id">
                                    <option value="">-- Pilih Produk (opsional) --</option>
                                    <?php foreach ($produkList as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="pesan" rows="4" placeholder="Pesan / Pertanyaan Anda *" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-base-color btn-medium fw-700 btn-round-edge w-100">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- end section kontak -->

        <!-- start section social -->
        <section class="bg-very-light-gray pb-0">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-12 col-sm-12 text-center elements-social social-icon-style-03">
                        <ul class="extra-large-icon">
                            <li><a class="instagram" href="<?= htmlspecialchars($cfg['instagram'] ?? '#') ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- start footer -->
        <footer class="bg-very-light-gray pb-50px pt-2 xs-pb-30px background-repeat background-position-center sm-background-image-none">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-sm-6 text-center text-sm-start last-paragraph-no-margin fs-15 order-3 order-md-1">
                        <p><?= htmlspecialchars($cfg['copyright'] ?? '© Copyright 2024 PT Cipta Pesona Teknik') ?></p>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end footer -->

        <!-- start scroll progress -->
        <div class="scroll-progress d-none d-xxl-block">
            <a href="#" class="scroll-top" aria-label="scroll">
                <span class="scroll-text">Scroll</span><span class="scroll-line"><span class="scroll-point"></span></span>
            </a>
        </div>
        <!-- end scroll progress -->

        <!-- javascript libraries -->
        <script type="text/javascript" src="assets/js/jquery.js"></script>
        <script type="text/javascript" src="assets/js/vendors.min.js"></script>
        <script type="text/javascript" src="assets/js/main.js"></script>
    </body>
</html>
