<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Student Resource Exchange System — an organised academic resource library.">
    <title>Student Resource Exchange</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --burgundy: #5D0F15;
            --burgundy-dark: #39070B;
            --burgundy-soft: #7B2028;
            --cream: #FFFFCD;
            --cream-light: #FFFEE8;
            --blue-grey: #A5B9C6;
            --blue-pale: #EAF1F4;
            --ink: #351519;
            --muted: #6E5559;
            --white: #FFFFFF;
            --line: #D7E1E5;
            --radius-lg: 30px;
            --radius-md: 22px;
            --radius-sm: 15px;
            --shadow: 0 18px 48px rgba(93, 15, 21, .14);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; overflow-x: hidden; background: var(--cream-light); color: var(--ink); font-family: "DM Sans", Arial, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1120px, calc(100% - 40px)); margin: 0 auto; }

        .hero { position: relative; overflow: hidden; min-height: 760px; padding-bottom: 72px; background: linear-gradient(132deg, #FFFFCD 0%, #F9F6D6 52%, #E4EEF1 100%); }
        .blob { position: absolute; border-radius: 50%; pointer-events: none; }
        .blob-one { width: 385px; height: 385px; top: -135px; right: -115px; background: rgba(93, 15, 21, .13); }
        .blob-two { width: 305px; height: 305px; bottom: -135px; left: -110px; background: rgba(165, 185, 198, .67); }
        .blob-three { width: 142px; height: 142px; top: 160px; left: 6%; border: 22px solid rgba(93, 15, 21, .11); background: transparent; }

        .nav { position: relative; z-index: 3; display: flex; align-items: center; justify-content: space-between; gap: 22px; top: 20px; padding: 15px 19px; border: 1px solid rgba(255, 255, 255, .88); border-radius: 999px; background: rgba(255, 255, 253, .80); box-shadow: 0 10px 28px rgba(93, 15, 21, .09); backdrop-filter: blur(12px); }
        .brand { display: inline-flex; align-items: center; gap: 10px; color: var(--burgundy); font-size: .82rem; font-weight: 700; letter-spacing: .05em; }
        .brand-mark { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 12px; background: var(--burgundy); color: var(--cream); font-family: "Playfair Display", serif; font-size: .86rem; font-weight: 800; }
        .nav-links { display: flex; align-items: center; gap: 7px; color: var(--muted); font-size: .9rem; font-weight: 500; }
        .nav-links a { padding: 9px 13px; border-radius: 999px; transition: color .2s ease, background .2s ease, transform .2s ease; }
        .nav-links a:hover { background: var(--blue-pale); color: var(--burgundy); transform: translateY(-2px); }

        .hero-content { position: relative; z-index: 1; display: grid; grid-template-columns: 1.05fr .95fr; align-items: center; gap: 64px; min-height: 670px; padding: 80px 0 35px; }
        .eyebrow { display: inline-flex; margin: 0 0 18px; padding: 8px 13px; border-radius: 999px; background: var(--white); color: var(--burgundy); font-size: .73rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; box-shadow: 0 7px 18px rgba(93, 15, 21, .07); }
        h1, h2, h3 { font-family: "Playfair Display", Georgia, serif; }
        h1 { max-width: 670px; margin: 0; color: var(--burgundy-dark); font-size: clamp(3rem, 6vw, 5.8rem); line-height: .98; letter-spacing: -.065em; }
        h1 span { color: var(--burgundy-soft); font-style: italic; }
        .hero-copy { max-width: 555px; margin: 25px 0 30px; color: var(--muted); font-size: 1.06rem; line-height: 1.75; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 50px; padding: 0 21px; border-radius: 999px; font-size: .92rem; font-weight: 700; transition: transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .button:hover { box-shadow: 0 13px 26px rgba(93, 15, 21, .19); transform: translateY(-3px); }
        .button:active { transform: scale(.97); }
        .primary { background: var(--burgundy); color: var(--cream); }
        .primary:hover { background: var(--burgundy-soft); }
        .secondary { border: 1px solid var(--blue-grey); background: rgba(255,255,255,.66); color: var(--burgundy); }
        .secondary:hover { background: var(--blue-pale); }

        .resource-preview { position: relative; overflow: hidden; padding: 26px; border: 1px solid rgba(255,255,255,.90); border-radius: var(--radius-lg); background: rgba(255,255,253,.78); box-shadow: var(--shadow); backdrop-filter: blur(11px); }
        .resource-preview::before { position: absolute; width: 165px; height: 165px; top: -85px; right: -62px; border-radius: 50%; background: rgba(165,185,198,.40); content: ""; }
        .preview-head { position: relative; display: flex; align-items: center; justify-content: space-between; padding-bottom: 18px; border-bottom: 1px solid var(--line); }
        .preview-head strong { font-family: "Playfair Display", serif; font-size: 1.05rem; } .preview-head span { padding: 6px 10px; border-radius: 999px; background: var(--blue-pale); color: #496778; font-size: .68rem; font-weight: 700; }
        .preview-item { display: grid; grid-template-columns: 42px 1fr; align-items: center; gap: 13px; padding: 16px 0; border-bottom: 1px solid var(--line); }
        .preview-item:last-of-type { border-bottom: 0; }
        .file-icon { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 13px; background: var(--blue-pale); color: var(--burgundy); font-size: .68rem; font-weight: 700; }
        .preview-item b { display: block; font-size: .9rem; } .preview-item small { display: block; margin-top: 4px; color: var(--muted); font-size: .76rem; }
        .preview-note { margin: 12px 0 0; padding: 12px 14px; border-radius: var(--radius-sm); background: var(--cream); color: var(--burgundy); font-size: .79rem; line-height: 1.48; }

        section { padding: 105px 0; }
        .tag { color: var(--burgundy); font-size: .74rem; font-weight: 700; letter-spacing: .11em; text-transform: uppercase; }
        h2 { margin: 12px 0 17px; color: var(--burgundy-dark); font-size: clamp(2.25rem, 4.4vw, 3.72rem); line-height: 1; letter-spacing: -.055em; }
        .section-copy { max-width: 610px; margin: 0; color: var(--muted); font-size: 1.02rem; line-height: 1.75; }
        .about { display: grid; grid-template-columns: .91fr 1.09fr; gap: 68px; align-items: center; }
        .about-shape { position: relative; min-height: 350px; overflow: hidden; border-radius: var(--radius-lg); background: linear-gradient(145deg, var(--burgundy), #812731); box-shadow: var(--shadow); }
        .about-shape::before, .about-shape::after { position: absolute; border-radius: 50%; content: ""; }
        .about-shape::before { width: 235px; height: 235px; top: -82px; right: -45px; background: var(--blue-grey); }
        .about-shape::after { width: 185px; height: 185px; bottom: -92px; left: -44px; background: var(--cream); }
        .about-shape .initials { position: absolute; z-index: 1; top: 50%; left: 50%; color: var(--cream); font-family: "Playfair Display", serif; font-size: 4.5rem; font-weight: 800; letter-spacing: -.12em; transform: translate(-50%, -50%); }
        .about-shape .caption { position: absolute; z-index: 1; bottom: 26px; left: 28px; color: rgba(255,255,205,.88); font-size: .72rem; font-weight: 700; letter-spacing: .11em; }

        .steps-section { background: var(--blue-pale); }
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 45px; }
        .step { min-height: 245px; padding: 28px; border: 1px solid rgba(165,185,198,.35); border-radius: var(--radius-md); background: rgba(255,255,255,.9); box-shadow: 0 8px 20px rgba(93,15,21,.04); transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; }
        .step:hover { border-color: var(--blue-grey); box-shadow: var(--shadow); transform: translateY(-8px); }
        .number { display: grid; width: 41px; height: 41px; place-items: center; border-radius: 13px; background: var(--burgundy); color: var(--cream); font-size: .78rem; font-weight: 700; }
        .step h3 { margin: 42px 0 10px; color: var(--burgundy); font-size: 1.4rem; letter-spacing: -.03em; } .step p { margin: 0; color: var(--muted); font-size: .94rem; line-height: 1.62; }

        .cta { text-align: center; } .cta-box { position: relative; overflow: hidden; padding: 76px 25px; border-radius: var(--radius-lg); background: linear-gradient(132deg, var(--burgundy), #74222C); color: white; box-shadow: var(--shadow); }
        .cta-box::before, .cta-box::after { position: absolute; border-radius: 50%; content: ""; opacity: .35; } .cta-box::before { width: 235px; height: 235px; top: -112px; left: -80px; background: var(--blue-grey); } .cta-box::after { width: 190px; height: 190px; right: -68px; bottom: -96px; background: var(--cream); }
        .cta-content { position: relative; z-index: 1; max-width: 625px; margin: auto; } .cta .tag { color: var(--blue-grey); } .cta h2 { margin-top: 12px; color: var(--cream); } .cta .section-copy { margin: 0 auto 28px; color: rgba(255,255,205,.82); } .cta .primary { background: var(--cream); color: var(--burgundy); } .cta .primary:hover { background: var(--blue-grey); }
        footer { padding: 27px 0; background: var(--cream); color: var(--muted); font-size: .79rem; } footer .container { display: flex; justify-content: space-between; gap: 18px; }
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .65s cubic-bezier(.2,.8,.2,1), transform .65s cubic-bezier(.2,.8,.2,1); } .reveal.visible { opacity: 1; transform: translateY(0); }
        @media (max-width: 820px) { .hero-content, .about { grid-template-columns: 1fr; gap: 45px; } .hero-content { min-height: auto; padding-top: 110px; } .resource-preview { max-width: 540px; } .steps { grid-template-columns: 1fr; } section { padding: 78px 0; } }
        @media (max-width: 560px) { .container { width: min(100% - 28px, 1120px); } .nav { align-items: flex-start; flex-direction: column; padding: 16px 18px; border-radius: var(--radius-md); } .nav-links { flex-wrap: wrap; } h1 { font-size: clamp(2.75rem, 14vw, 4.4rem); } .hero { min-height: auto; } .hero-content { padding-bottom: 65px; } .about-shape { min-height: 275px; } .cta-box { padding: 58px 20px; } footer .container { flex-direction: column; } }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <header class="hero" id="home">
        <span class="blob blob-one"></span><span class="blob blob-two"></span><span class="blob blob-three"></span>
        <nav class="container nav" aria-label="Main navigation">
            <a class="brand" href="#home"><span class="brand-mark">SR</span>RESOURCE EXCHANGE</a>
            <div class="nav-links"><a href="#about">About</a><a href="#how-it-works">How it works</a><a href="login.php">Log in</a><a class="button primary" href="register.php?new=1">Register</a></div>
        </nav>
        <div class="container hero-content">
            <div>
                <p class="eyebrow">A shared academic library</p>
                <h1>Learn together. <span>Share smarter.</span></h1>
                <p class="hero-copy">Student Resource Exchange is a welcoming place to submit, review, and find helpful academic material. It keeps study resources organised and ready when students need them.</p>
                <div class="actions"><a class="button primary" href="login.php">Log in to exchange</a><a class="button secondary" href="register.php?new=1">Create an account</a></div>
            </div>
            <aside class="resource-preview" aria-label="Example resource library preview">
                <div class="preview-head"><strong>Resource library</strong><span>REVIEWED</span></div>
                <div class="preview-item"><span class="file-icon">PDF</span><div><b>Mathematics past paper</b><small>Grade 12 · Past paper</small></div></div>
                <div class="preview-item"><span class="file-icon">DOC</span><div><b>Computer Science notes</b><small>College · Lecture notes</small></div></div>
                <div class="preview-item"><span class="file-icon">PDF</span><div><b>Biology revision guide</b><small>Grade 11 · Study guide</small></div></div>
                <p class="preview-note">Every uploaded resource waits for administrator review before it is shared.</p>
            </aside>
        </div>
    </header>
    <main>
        <section id="about"><div class="container about reveal"><div class="about-shape" aria-hidden="true"><span class="initials">SR</span><span class="caption">SHARE · REVIEW · LEARN</span></div><div><span class="tag">About the website</span><h2>Study resources, in one clear place.</h2><p class="section-copy">This website helps students move useful files away from disorganised group chats and into a structured academic resource library. Students can share their study guides, notes, and past papers; administrators can review each submission before it becomes available.</p></div></div></section>
        <section class="steps-section" id="how-it-works"><div class="container"><div class="reveal"><span class="tag">How it works</span><h2>A simple learning cycle.</h2><p class="section-copy">The website has a clear process that supports both student contribution and quality control.</p></div><div class="steps"><article class="step reveal"><span class="number">01</span><h3>Upload</h3><p>Students submit a PDF or DOCX file and add a short description.</p></article><article class="step reveal"><span class="number">02</span><h3>Review</h3><p>An administrator checks the resource and approves or rejects it.</p></article><article class="step reveal"><span class="number">03</span><h3>Learn</h3><p>Approved study resources become available for students to use.</p></article></div></div></section>
        <section class="cta"><div class="container"><div class="cta-box reveal"><div class="cta-content"><span class="tag">Ready to contribute?</span><h2>Help make learning easier.</h2><p class="section-copy">Open the student page and submit an academic resource for review.</p><a class="button primary" href="register.php?new=1">Create an account</a></div></div></div></section>
    </main>
    <footer><div class="container"><span>Student Resource Exchange System</span><span>Academic resources, organised with care.</span></div></footer>
    <script>
        const observer = new IntersectionObserver((entries) => { entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add('visible'); observer.unobserve(entry.target); } }); }, { threshold: 0.15 });
        document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));
    </script>
</body>
</html>
