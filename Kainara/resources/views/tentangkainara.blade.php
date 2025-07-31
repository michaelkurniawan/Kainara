<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- <link rel="stylesheet" href="css/styletentangkainara.css"> -->
  <!-- <link rel="stylesheet" href="{{ asset('css/styletentangkainara.css') }}"> -->
  <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #F8F5F0;
      color: #333;
    }

    .gambar-kainara {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
        height: 1082.1px;
        object-fit: cover;
    }

    .title-penjelasan {
        width: 100%;
        position: relative;
        max-width: 100%;
        overflow: hidden;
        max-height: 100%;
        object-fit: cover;
        margin-top: -100vw;
    }

    .rounded.mx-auto.d-block{
      bottom: 5vw;
    }

    .judul-penjelasan{
      position: relative; /* atau absolute tergantung konteks */
      top: -150px; /* bisa sesuaikan angka negatifnya */
      margin-bottom: 0;
    }

    .card-img-cendrawasih{
      width: 18vw;
      align-self: center;
    }

    section.hero-section.container {
      background-image: url('css/backgroundbawah-tentangkainara.png');
      background-color: rgba(255, 255, 255, 0.7); /* putih dengan transparansi */      
      background-size: cover;
      /* background-position: center; */
      background-blend-mode: lighten;
      min-width: 100vw;
      /* min-height: 50vw; */
      padding: 10vw;
    }

    section.hero-section.container h1 {
      text-align: center;
      margin-bottom: 2vw;
    }

    section.hero-section.container p {
      text-align: center;
      padding: 1vw 9vw 0vw 10vw;
    }

    .custom-bg{
      top: -8vw;
    }

  </style>  
</head>
<body>

  <section class="text-center position-relative">
    <img src="css/atastentangkainara.svg" alt="Ilustrasi Kainara" class="img-fluid hero-image">
    <img src="css/titlepenjelasanlogo.png" alt="Title Penjelasan Logo" class="judul-penjelasan">

   <!-- Story Behind the Logo -->
    <div class="container text-center">
      <div class="row g-4">
        <div class="col-md-4 custom-bg">
          <div class="card p-4 custom-bg shadow-sm">
            <img src="css/cendrawasih.png" class="card-img-cendrawasih" alt="Cenderawasih">
            <div class="card-body">
              <h5 class="card-title">Cenderawasih</h5>
              <p class="card-text">The bird of paradise is one of the many animals carved into batik motifs, especially Papuan batik, 
                because this bird is considered a bird of heaven and has a strong philosophical meaning in Papuan culture. 
                In addition, the beautiful and colorful feathers of the bird of paradise are an inspiration for batik artists to 
                create unique and interesting motifs.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 custom-bg">
          <div class="card p-4 custom-bg shadow-sm">
            <img src="css/kainara.png" class="card-img-kainara" alt="Logo Kainara">
            <div class="card-body">
              <!-- <h5 class="card-title kainara-title">Kainara</h5> -->
              <p class="card-text">"Kainara" is a combination of two words <br><em>kain</em> and <em>nara</em></br></p>
              <p class="text-start">Kain = Represents the main product of this start-up, namely typical Indonesian fabrics such as batik, woven fabrics, lurik, and so on.</p>
              <p class="text-start">Nara = Taken from the word ‘Nusantara’. Inspired by Sanskrit, which can mean “story” or “human”.</p>
              <p class="card-text">It can be interpreted as <em>"kain yang bercerita"</em> or <em>"kain yang punya jiwa dan koneksi dengan manusia"</em>.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 custom-bg shadow-sm">
            <img src="css/tangancanting.png" class="card-img-canting" alt="Canting Batik">
            <div class="card-body">
              <h5 class="card-title">Canting Batik</h5>
              <p class="card-text">An essential tool in the creation of hand-drawn batik, used to apply hot wax onto fabric to form intricate batik patterns.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- What is Kainara Section -->
 
  <section class="hero-section container">
     <h1>What is the<em> “Kainara” </em>??</h1>
    <p>
      Kainara is a digital platform that connects the beauty of traditional Indonesian textiles with the modern world through technology.
      We offer authentic fabrics—such as batik, tenun, and lurik—sourced directly from local artisans and small businesses across Indonesia.
    </p>
    <p>
      More than just a marketplace, Kainara is a collaborative space where designers and artisans create unique, meaningful, and sustainable fashion.
      We promote inclusive economic growth, empower women through technology, and support environmentally responsible production.
    </p>
    <p>
      Kainara celebrates Indonesia’s cultural heritage by telling the story behind every piece of fabric—blending tradition with innovation
      to shape a more equitable and sustainable future for fashion.
    </p>
  </section>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>